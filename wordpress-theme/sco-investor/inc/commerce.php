<?php
/**
 * WooCommerce integration. The theme is Woo-*compatible* — it links
 * courses/materials to products, gates access, and renders Buy Now
 * buttons — but it never embeds actual payment processing. Real money
 * handling is WooCommerce's job plus whichever gateway plugin (Razorpay,
 * Stripe, PayPal, etc.) the owner installs and configures with their own
 * merchant account. Every function here is dormant — zero behaviour
 * change from today — until WooCommerce is active AND a course/material
 * is deliberately linked to a product via the meta box dropdown.
 */

if (!defined('ABSPATH')) exit;

function sci_has_woocommerce() {
	return class_exists('WooCommerce');
}

/**
 * Declared unconditionally (safe no-op without WooCommerce) so that if
 * the owner installs WooCommerce *after* this theme is already active,
 * Woo doesn't show its own "theme doesn't support WooCommerce" notice —
 * that notice is based on this declaration existing, not on install order.
 */
function sci_woocommerce_setup() {
	add_theme_support('woocommerce');
	add_theme_support('wc-product-gallery-zoom');
	add_theme_support('wc-product-gallery-lightbox');
	add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'sci_woocommerce_setup');

/**
 * Wraps Woo's shop/single-product templates in the same .section/.container
 * chrome every other template on the site uses. Cart, Checkout and My
 * Account are normal Pages with shortcodes, so they already inherit that
 * wrapping from page.php — only the product templates need this.
 */
function sci_woocommerce_wrapper_start() {
	echo '<section class="section section--tight"><div class="container">';
}
add_action('woocommerce_before_main_content', 'sci_woocommerce_wrapper_start');

function sci_woocommerce_wrapper_end() {
	echo '</div></section>';
}
add_action('woocommerce_after_main_content', 'sci_woocommerce_wrapper_end');

/**
 * Baseline restyle of Woo's default markup (buttons, forms, product grid,
 * My Account tabs) with the theme's own CSS variables — only enqueued
 * when WooCommerce is actually active.
 */
function sci_woocommerce_assets() {
	if (!sci_has_woocommerce()) return;
	wp_enqueue_style('sci-woocommerce', SCI_THEME_URI . '/assets/css/woocommerce.css', ['sci-main'], SCI_VERSION);
}
add_action('wp_enqueue_scripts', 'sci_woocommerce_assets');

/**
 * The WooCommerce product ID linked to a course/material, or 0 if none.
 */
function sci_linked_product_id($post_id) {
	return (int) get_post_meta($post_id, '_sci_product_id', true);
}

/**
 * The linked WC_Product, but only when it's actually safe to sell right
 * now (Woo active, a product is linked, that product still exists and is
 * purchasable). Every caller can trust a non-null return is a real buy
 * target, with no further checking of its own.
 */
function sci_purchasable_product($post_id) {
	if (!sci_has_woocommerce()) return null;
	$product_id = sci_linked_product_id($post_id);
	if (!$product_id) return null;
	$product = wc_get_product($product_id);
	if (!$product || !$product->is_purchasable()) return null;
	return $product;
}

/**
 * Whether the logged-in user has actually bought a product. Cached per
 * request since the same product can be checked from several places
 * (gate check, CTA render, My Account listing) in one page load.
 */
function sci_user_has_purchased($product_id) {
	static $cache = [];
	if (!sci_has_woocommerce() || !is_user_logged_in()) return false;
	if (!isset($cache[$product_id])) {
		$user = wp_get_current_user();
		$cache[$product_id] = wc_customer_bought_product($user->user_email, $user->ID, $product_id);
	}
	return $cache[$product_id];
}

/**
 * The single access gate every course/material page, download link and
 * CTA button checks: open unless a purchasable product is actually
 * linked, in which case editors and buyers get through and everyone else
 * doesn't. A course/material with no linked product behaves exactly as
 * it does today — always accessible.
 */
function sci_user_can_access($post_id) {
	$product = sci_purchasable_product($post_id);
	if (!$product) return true;
	if (current_user_can('edit_post', $post_id)) return true;
	return sci_user_has_purchased($product->get_id());
}

function sci_buy_now_url($product_id) {
	return add_query_arg('sci_buy_now', (int) $product_id, home_url('/'));
}

function sci_download_url($material_id) {
	return add_query_arg('sci_download', (int) $material_id, home_url('/'));
}

/**
 * Full CTA markup for a course card/single page: the original disabled
 * "Enroll Soon" button when no purchasable product is linked (today's
 * exact behaviour), a real Buy Now button when one is linked and not yet
 * unlocked, or a link through to the course once access is granted —
 * "Go to Course" from a card, or a plain access note when already on the
 * course's own page (linking to itself would be pointless there).
 */
function sci_course_cta_html($course_id, $classes = 'btn btn-primary btn-sm', $on_course_page = false) {
	$product = sci_purchasable_product($course_id);

	if (!$product) {
		return sprintf(
			'<button class="%s" disabled title="%s">%s</button>',
			esc_attr($classes),
			esc_attr__('Payments coming soon', 'sco-investor'),
			esc_html__('Enroll Soon', 'sco-investor')
		);
	}

	if (sci_user_can_access($course_id)) {
		if ($on_course_page) {
			return sprintf('<p class="sci-access-note">%s</p>', esc_html__('✓ You have full access to this course.', 'sco-investor'));
		}
		return sprintf(
			'<a href="%s" class="%s">%s</a>',
			esc_url(get_permalink($course_id)),
			esc_attr($classes),
			esc_html__('Go to Course', 'sco-investor')
		);
	}

	return sprintf(
		'<a href="%s" class="%s">%s</a>',
		esc_url(sci_buy_now_url($product->get_id())),
		esc_attr($classes),
		esc_html__('Buy Now', 'sco-investor')
	);
}

/**
 * Full CTA markup for a material card/single page. Mirrors the exact
 * markup/classes the static templates used to build inline: a resolved
 * $link (today's Download/Open/Get Copy behaviour, unchanged for free or
 * already-unlocked materials), a Buy Now button when a purchasable
 * product is linked and not yet unlocked, or the original disabled
 * button as the last-resort fallback (e.g. incomplete data).
 */
function sci_material_cta_html($material_id, $link, $type, $size_class = '', $show_icon = false) {
	$classes_for = function ($base) use ($size_class) {
		return trim('btn ' . $base . ' ' . $size_class);
	};

	if ($link) {
		$base = $type === 'tool' ? 'btn-primary' : 'btn-ghost';
		$icon = ($show_icon && $type !== 'tool')
			? '<svg viewBox="0 0 24 24" fill="none" style="width:14px;height:14px;"><path d="M12 4v11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 19h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
			: '';
		return sprintf(
			'<a href="%s" class="%s">%s%s</a>',
			esc_url($link),
			esc_attr($classes_for($base)),
			$icon,
			esc_html(sci_material_cta_label($type))
		);
	}

	$product = sci_purchasable_product($material_id);
	if ($product && !sci_user_can_access($material_id)) {
		return sprintf(
			'<a href="%s" class="%s">%s</a>',
			esc_url(sci_buy_now_url($product->get_id())),
			esc_attr($classes_for('btn-primary')),
			esc_html__('Buy Now', 'sco-investor')
		);
	}

	return sprintf(
		'<button class="%s" disabled>%s</button>',
		esc_attr($classes_for('btn-ghost')),
		esc_html(sci_material_cta_label($type))
	);
}

/**
 * id => "Title — ₹price" pairs for the meta box's Linked Product
 * dropdown. Capped at 200 — plenty for a course/materials catalogue.
 */
function sci_get_products_for_select() {
	if (!sci_has_woocommerce()) return [];
	$products = get_posts([
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 200,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	]);
	$options = [];
	foreach ($products as $product_post) {
		$product = wc_get_product($product_post->ID);
		if (!$product) continue;
		$price = $product->get_price();
		$options[$product_post->ID] = $product_post->post_title . ($price !== '' ? ' — ' . sci_format_inr($price) : '');
	}
	return $options;
}

/**
 * Custom ?sci_download=ID query var for the secure download endpoint
 * below — registered so WordPress recognizes it regardless of the site's
 * permalink structure.
 */
function sci_register_download_query_var($vars) {
	$vars[] = 'sci_download';
	return $vars;
}
add_filter('query_vars', 'sci_register_download_query_var');

/**
 * Streams a material's real file only after sci_user_can_access() passes,
 * so the permanent, unauthenticated wp-content/uploads URL never has to
 * appear in page source for a paid-and-gated material — see the matching
 * change in sci_material_link(). Free/unlinked materials never hit this;
 * they keep using the direct attachment URL exactly as before.
 */
function sci_handle_download_request() {
	$material_id = (int) get_query_var('sci_download');
	if (!$material_id) return;

	if (get_post_type($material_id) !== 'material' || !sci_user_can_access($material_id)) {
		wp_die(esc_html__('You do not have access to this file. Please purchase it first.', 'sco-investor'), '', ['response' => 403]);
	}

	$file_id = (int) get_post_meta($material_id, '_sci_file', true);
	$path    = $file_id ? get_attached_file($file_id) : false;
	if (!$path || !file_exists($path)) {
		wp_die(esc_html__('File not found.', 'sco-investor'), '', ['response' => 404]);
	}

	$filetype = wp_check_filetype($path);

	while (ob_get_level()) ob_end_clean();
	nocache_headers();
	header('Content-Type: ' . ($filetype['type'] ?: 'application/octet-stream'));
	header('Content-Disposition: attachment; filename="' . basename($path) . '"');
	header('Content-Length: ' . filesize($path));
	readfile($path);
	exit;
}
add_action('template_redirect', 'sci_handle_download_request');

/**
 * Buy Now: a single-item flow deliberately separate from Woo's native
 * ?add-to-cart=ID (which by default stays on the same page and is meant
 * for multi-item cart browsing). Empties the cart, adds the one product,
 * and goes straight to checkout. No nonce — consistent with how Woo's
 * own add-to-cart links work; adding something to one's own session cart
 * isn't a CSRF-sensitive action.
 */
function sci_handle_buy_now_request() {
	if (!sci_has_woocommerce() || empty($_GET['sci_buy_now'])) return;

	$product_id = absint($_GET['sci_buy_now']);
	$product    = $product_id ? wc_get_product($product_id) : false;
	if (!$product || !$product->is_purchasable()) return;

	WC()->cart->empty_cart();
	WC()->cart->add_to_cart($product_id);
	wp_safe_redirect(wc_get_checkout_url());
	exit;
}
add_action('template_redirect', 'sci_handle_buy_now_request');

/**
 * "My Courses" / "My Materials" tabs on WooCommerce's My Account page,
 * listing whatever the logged-in user has actually bought, rendered
 * through the same card partials as the rest of the site so they look
 * identical to the public archives. [woocommerce_my_account] already
 * handles the logged-out state for every endpoint, so no extra
 * is_user_logged_in() check is needed in the callbacks below.
 */
function sci_account_menu_items($items) {
	$logout = isset($items['customer-logout']) ? $items['customer-logout'] : null;
	unset($items['customer-logout']);
	$items['sci-courses']   = __('My Courses', 'sco-investor');
	$items['sci-materials'] = __('My Materials', 'sco-investor');
	if ($logout !== null) {
		$items['customer-logout'] = $logout;
	}
	return $items;
}
add_filter('woocommerce_account_menu_items', 'sci_account_menu_items');

function sci_account_endpoints() {
	if (!sci_has_woocommerce()) return;
	add_rewrite_endpoint('sci-courses', EP_ROOT | EP_PAGES);
	add_rewrite_endpoint('sci-materials', EP_ROOT | EP_PAGES);
}
add_action('init', 'sci_account_endpoints');

function sci_account_query_vars($vars) {
	$vars[] = 'sci-courses';
	$vars[] = 'sci-materials';
	return $vars;
}
add_filter('query_vars', 'sci_account_query_vars');

/**
 * IDs of published $post_type posts the current user owns: cross-
 * references every course/material's linked product against what
 * WooCommerce says they've actually bought.
 */
function sci_owned_post_ids($post_type) {
	if (!is_user_logged_in() || !sci_has_woocommerce()) return [];

	$candidates = get_posts([
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]);

	$owned = [];
	foreach ($candidates as $post_id) {
		$product_id = sci_linked_product_id($post_id);
		if ($product_id && sci_user_has_purchased($product_id)) {
			$owned[] = $post_id;
		}
	}
	return $owned;
}

function sci_render_account_endpoint($post_type, $card_partial) {
	$ids = sci_owned_post_ids($post_type);
	if (!$ids) {
		printf('<p>%s</p>', esc_html__('Nothing here yet — your purchases will show up automatically once you buy a course or material.', 'sco-investor'));
		return;
	}
	$query = new WP_Query([
		'post_type'      => $post_type,
		'post__in'       => $ids,
		'orderby'        => 'post__in',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	]);
	echo '<div class="card-grid">';
	while ($query->have_posts()) {
		$query->the_post();
		get_template_part($card_partial);
	}
	echo '</div>';
	wp_reset_postdata();
}

function sci_account_courses_content() {
	sci_render_account_endpoint('course', 'template-parts/card-course');
}
add_action('woocommerce_account_sci-courses_endpoint', 'sci_account_courses_content');

function sci_account_materials_content() {
	sci_render_account_endpoint('material', 'template-parts/card-material');
}
add_action('woocommerce_account_sci-materials_endpoint', 'sci_account_materials_content');

/**
 * The new My Account endpoints above are real rewrite rules — without a
 * flush they 404 until the owner happens to resave Settings > Permalinks.
 * Two triggers cover both realistic orderings: activating this theme
 * after WooCommerce already exists, and installing WooCommerce onto a
 * site where this theme is already active (the more likely real-world
 * order).
 */
function sci_flush_rewrites_on_theme_activation() {
	flush_rewrite_rules();
}
add_action('after_switch_theme', 'sci_flush_rewrites_on_theme_activation');

function sci_flush_rewrites_on_plugin_activation($plugin) {
	if ($plugin === 'woocommerce/woocommerce.php') {
		flush_rewrite_rules();
	}
}
add_action('activated_plugin', 'sci_flush_rewrites_on_plugin_activation');
