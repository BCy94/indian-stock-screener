<?php
/**
 * Ticker Stock CPT — replaces the hardcoded TICKER_STOCKS array that used
 * to live in assets/js/main.js. Each post is one row in the scrolling
 * tape: post_title is the symbol, menu_order drives display order (the
 * "Order" field WordPress already gives any post type that supports
 * page-attributes — no custom drag-sort UI needed), and price/change live
 * in post meta. Not public: there's no single-ticker-item page, so it's
 * kept out of search, sitemaps and nav menus, but still fully editable
 * from its own wp-admin menu.
 */

if (!defined('ABSPATH')) exit;

function sci_register_ticker_cpt() {
	register_post_type('ticker_item', [
		'labels' => [
			'name'               => __('Ticker Stocks', 'sco-investor'),
			'singular_name'      => __('Ticker Stock', 'sco-investor'),
			'add_new_item'       => __('Add New Ticker Stock', 'sco-investor'),
			'edit_item'          => __('Edit Ticker Stock', 'sco-investor'),
			'new_item'           => __('New Ticker Stock', 'sco-investor'),
			'all_items'          => __('Ticker Stocks', 'sco-investor'),
			'search_items'       => __('Search Ticker Stocks', 'sco-investor'),
			'not_found'          => __('No ticker stocks found.', 'sco-investor'),
		],
		'public'        => false,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'menu_position' => 7,
		'menu_icon'     => 'dashicons-chart-line',
		'hierarchical'  => false,
		'show_in_rest'  => true,
		'supports'      => ['title', 'page-attributes', 'custom-fields'],
	]);
}
add_action('init', 'sci_register_ticker_cpt');

function sci_register_ticker_meta() {
	$auth_callback = function () {
		return current_user_can('edit_posts');
	};

	register_post_meta('ticker_item', '_sci_ticker_price', [
		'type'              => 'number',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => function ($value) {
			return $value === '' ? 0 : round((float) $value, 2);
		},
		'auth_callback' => $auth_callback,
	]);

	register_post_meta('ticker_item', '_sci_ticker_change', [
		'type'              => 'number',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => function ($value) {
			return $value === '' ? 0 : round((float) $value, 2);
		},
		'auth_callback' => $auth_callback,
	]);
}
add_action('init', 'sci_register_ticker_meta');

/**
 * Seeds the same 20 NSE symbols the static prototype hardcoded in JS, so
 * activating this theme looks unchanged on day one. Only runs while the
 * CPT is empty — never re-seeds over an owner's edits, additions or
 * deletions.
 */
function sci_seed_ticker_items() {
	$existing = get_posts([
		'post_type'      => 'ticker_item',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]);
	if ($existing) return;

	$stocks = [
		['RELIANCE', 2954.10, 1.24], ['TCS', 4102.55, -0.42], ['HDFCBANK', 1687.30, 0.85],
		['INFY', 1842.75, 1.63], ['ICICIBANK', 1264.90, -0.31], ['HINDUNILVR', 2398.20, 0.18],
		['SBIN', 832.45, 2.05], ['BHARTIARTL', 1598.60, 0.97], ['ITC', 468.35, -0.56],
		['LT', 3712.80, 1.12], ['KOTAKBANK', 1789.15, -0.22], ['AXISBANK', 1142.50, 0.64],
		['BAJFINANCE', 7245.90, 1.88], ['MARUTI', 12480.25, -0.71], ['ASIANPAINT', 2865.40, 0.39],
		['SUNPHARMA', 1789.60, 1.04], ['TITAN', 3542.15, -0.18], ['WIPRO', 562.80, 0.93],
		['ONGC', 268.45, -0.85], ['TATAMOTORS', 968.20, 2.41],
	];

	foreach ($stocks as $i => $stock) {
		$post_id = wp_insert_post([
			'post_type'   => 'ticker_item',
			'post_title'  => $stock[0],
			'post_status' => 'publish',
			'menu_order'  => $i,
		]);
		if ($post_id && !is_wp_error($post_id)) {
			update_post_meta($post_id, '_sci_ticker_price', $stock[1]);
			update_post_meta($post_id, '_sci_ticker_change', $stock[2]);
		}
	}
}
add_action('after_switch_theme', 'sci_seed_ticker_items');

/**
 * All published ticker stocks, owner-ordered, as plain arrays — kept out
 * of template-parts/ticker.php so the homepage can check for an empty
 * result before deciding whether to print the .ticker-wrap chrome at all.
 */
function sci_get_ticker_items() {
	$query = new WP_Query([
		'post_type'      => 'ticker_item',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	]);

	$items = [];
	foreach ($query->posts as $post) {
		$items[] = [
			'symbol' => get_the_title($post),
			'price'  => (float) get_post_meta($post->ID, '_sci_ticker_price', true),
			'change' => (float) get_post_meta($post->ID, '_sci_ticker_change', true),
		];
	}
	return $items;
}

function sci_add_ticker_meta_box() {
	add_meta_box('sci_ticker_details', __('Stock Details', 'sco-investor'), 'sci_render_ticker_meta_box', 'ticker_item', 'normal', 'high');
}
add_action('add_meta_boxes', 'sci_add_ticker_meta_box');

function sci_render_ticker_meta_box($post) {
	wp_nonce_field('sci_save_ticker_meta', 'sci_ticker_meta_nonce');

	$price  = get_post_meta($post->ID, '_sci_ticker_price', true);
	$change = get_post_meta($post->ID, '_sci_ticker_change', true);
	?>
	<table class="form-table">
		<tr>
			<th><label for="sci_ticker_price"><?php esc_html_e('Price (₹)', 'sco-investor'); ?></label></th>
			<td><input type="number" step="0.01" min="0" id="sci_ticker_price" name="sci_ticker_price" value="<?php echo esc_attr($price); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="sci_ticker_change"><?php esc_html_e('Change (%)', 'sco-investor'); ?></label></th>
			<td>
				<input type="number" step="0.01" id="sci_ticker_change" name="sci_ticker_change" value="<?php echo esc_attr($change); ?>" class="regular-text">
				<p class="description"><?php esc_html_e('Use a negative number for a fall, e.g. -0.85. Shown with ▲ (green) or ▼ (red) automatically.', 'sco-investor'); ?></p>
			</td>
		</tr>
	</table>
	<p class="description"><?php esc_html_e('The stock symbol is the post Title above. Set the display order from the "Order" box in the sidebar.', 'sco-investor'); ?></p>
	<?php
}

function sci_save_ticker_meta($post_id) {
	if (!isset($_POST['sci_ticker_meta_nonce']) || !wp_verify_nonce($_POST['sci_ticker_meta_nonce'], 'sci_save_ticker_meta')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	if (isset($_POST['sci_ticker_price'])) {
		$value = sanitize_text_field(wp_unslash($_POST['sci_ticker_price']));
		update_post_meta($post_id, '_sci_ticker_price', $value === '' ? 0 : round((float) $value, 2));
	}
	if (isset($_POST['sci_ticker_change'])) {
		$value = sanitize_text_field(wp_unslash($_POST['sci_ticker_change']));
		update_post_meta($post_id, '_sci_ticker_change', $value === '' ? 0 : round((float) $value, 2));
	}
}
add_action('save_post_ticker_item', 'sci_save_ticker_meta');
