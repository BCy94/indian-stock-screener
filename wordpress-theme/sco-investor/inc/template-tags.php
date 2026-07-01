<?php
/**
 * Small template helpers shared across header, footer and page templates.
 */

if (!defined('ABSPATH')) exit;

/**
 * Outputs the site brand link: the owner's Custom Logo if one is set
 * in Appearance > Customize > Site Identity, otherwise the theme's
 * built-in mark + wordmark.
 */
function sci_site_brand() {
	$home = esc_url(home_url('/'));
	if (has_custom_logo()) {
		printf('<a href="%s" class="brand">', $home);
		the_custom_logo();
		echo '</a>';
		return;
	}
	printf(
		'<a href="%s" class="brand"><span class="brand-mark"><svg viewBox="0 0 24 24" fill="none"><path d="M3 17L9 11L13 15L21 7" stroke="#14171F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 7H21V13" stroke="#14171F" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="brand-name">%s<span>%s</span></span></a>',
		$home,
		esc_html__('So Called', 'sco-investor'),
		esc_html__('Investor', 'sco-investor')
	);
}

/**
 * Finds the first published Page assigned a given Page Template,
 * so links can survive the owner renaming/moving that page.
 */
function sci_find_page_by_template($template) {
	static $cache = [];
	if (array_key_exists($template, $cache)) {
		return $cache[$template];
	}
	$pages = get_posts([
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'meta_key'       => '_wp_page_template',
		'meta_value'     => $template,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	]);
	$cache[$template] = $pages ? get_permalink($pages[0]) : '';
	return $cache[$template];
}

function sci_contact_url() {
	return sci_find_page_by_template('page-contact.php') ?: home_url('/contact/');
}

function sci_calculator_url() {
	return sci_find_page_by_template('page-calculator.php') ?: home_url('/calculator/');
}

function sci_blog_url() {
	$page_for_posts = (int) get_option('page_for_posts');
	return $page_for_posts ? get_permalink($page_for_posts) : home_url('/blog/');
}

function sci_courses_url() {
	return get_post_type_archive_link('course') ?: home_url('/courses/');
}

function sci_materials_url() {
	return get_post_type_archive_link('material') ?: home_url('/materials/');
}

/**
 * Up to two initials from a display name, for the .avatar-ring fallback
 * used wherever no real author/profile photo exists.
 */
function sci_initials($name) {
	$words = preg_split('/\s+/', trim($name));
	$words = array_filter($words);
	if (!$words) return '';
	if (count($words) === 1) {
		return mb_strtoupper(mb_substr($words[0], 0, 2));
	}
	return mb_strtoupper(mb_substr(reset($words), 0, 1) . mb_substr(end($words), 0, 1));
}

/**
 * Estimated reading time in minutes, derived from word count
 * so it never needs manual upkeep per post.
 */
function sci_reading_time($post_id = null) {
	$post_id = $post_id ?: get_the_ID();
	$words   = str_word_count(wp_strip_all_tags(get_post_field('post_content', $post_id)));
	return max(1, (int) ceil($words / 200));
}

/**
 * Reads a Customizer on/off toggle. Centralized so templates never call
 * get_theme_mod() directly for a checkbox — one place to fix if the
 * storage format ever changes.
 */
function sci_show($mod, $default = true) {
	return (bool) get_theme_mod($mod, $default);
}

/**
 * Whether the Page assigned as the static homepage (Settings > Reading)
 * has been deliberately given its own content — either authored directly
 * (classic/block editor) or built with a page builder like Elementor.
 * front-page.php uses this to decide whether to defer entirely to that
 * content instead of rendering its own hardcoded hero/stats/etc. sections,
 * which is what makes the homepage genuinely rearrangeable with Elementor
 * (or any other page-content editor) instead of permanently fixed in PHP.
 * A fresh/untouched homepage Page has empty post_content, so this returns
 * false and today's default homepage design keeps rendering unchanged.
 */
function sci_page_has_custom_content($page_id) {
	if (!$page_id) return false;
	if (get_post_meta($page_id, '_elementor_edit_mode', true) === 'builder') return true;
	$content = get_post_field('post_content', $page_id);
	return trim(wp_strip_all_tags((string) $content)) !== '';
}

/**
 * The owner's free Stock Screener tool URL, set via Customizer.
 * Returns '' when not configured so callers can hide the link entirely
 * rather than pointing at a guessed/broken URL.
 */
function sci_screener_url() {
	$url = get_theme_mod('sci_screener_url', '');
	return $url ? esc_url($url) : '';
}

function sci_social_url($key) {
	$url = get_theme_mod('sci_social_' . $key, '');
	return $url ? esc_url($url) : '';
}

/**
 * Strips WordPress's default "Category:", "Tag:", "Author:" prefixes
 * so archive titles match the site's clean editorial heading style.
 */
add_filter('get_the_archive_title', function ($title) {
	return preg_replace('/^[^:]+:\s*/', '', $title);
});

/**
 * A plain Page found by slug, used for the small Terms/Disclaimer
 * footer links. Falls back to '#' until the owner creates that page.
 */
function sci_legal_link($slug, $label) {
	$page = get_page_by_path($slug);
	$url  = $page ? get_permalink($page) : '#';
	printf('<a href="%s">%s</a>', esc_url($url), esc_html($label));
}

/**
 * Primary nav fallback — only ever runs before the owner has assigned
 * a menu to the "primary" location in Appearance > Menus.
 */
function sci_primary_nav_fallback($mobile = false) {
	$links = [
		['url' => home_url('/'), 'label' => __('Home', 'sco-investor')],
		['url' => sci_courses_url(), 'label' => __('Courses', 'sco-investor')],
		['url' => sci_materials_url(), 'label' => __('Materials', 'sco-investor')],
		['url' => sci_blog_url(), 'label' => __('Blog', 'sco-investor')],
		['url' => sci_calculator_url(), 'label' => __('Calculator', 'sco-investor')],
		['url' => home_url('/about/'), 'label' => __('About', 'sco-investor')],
		['url' => sci_contact_url(), 'label' => __('Contact', 'sco-investor')],
	];
	if (!$mobile) echo '<div class="nav-links">';
	foreach ($links as $link) {
		printf('<a href="%s">%s</a>', esc_url($link['url']), esc_html($link['label']));
	}
	if ($mobile) {
		printf(
			'<a href="%s" class="btn btn-primary btn-block">%s</a>',
			esc_url(sci_contact_url()),
			esc_html__('Get Started', 'sco-investor')
		);
	}
	if (!$mobile) echo '</div>';
}

/**
 * Footer "Explore" column fallback — only runs before the owner has
 * assigned a menu to the "footer-explore" location.
 */
function sci_footer_explore_fallback() {
	$links = [
		['url' => sci_courses_url(), 'label' => __('Courses', 'sco-investor')],
		['url' => sci_materials_url(), 'label' => __('Materials', 'sco-investor')],
		['url' => sci_blog_url(), 'label' => __('Blog', 'sco-investor')],
		['url' => sci_calculator_url(), 'label' => __('Calculator', 'sco-investor')],
		['url' => home_url('/about/'), 'label' => __('About', 'sco-investor')],
	];
	echo '<ul>';
	foreach ($links as $link) {
		printf('<li><a href="%s">%s</a></li>', esc_url($link['url']), esc_html($link['label']));
	}
	if (sci_screener_url()) {
		printf('<li><a href="%s">%s</a></li>', sci_screener_url(), esc_html__('Stock Screener', 'sco-investor'));
	}
	echo '</ul>';
}

/**
 * Footer "Support" column fallback — only runs before the owner has
 * assigned a menu to the "footer-support" location.
 */
function sci_footer_support_fallback() {
	echo '<ul>';
	printf('<li><a href="%s">%s</a></li>', esc_url(sci_contact_url()), esc_html__('Contact Us', 'sco-investor'));
	printf('<li><a href="%s">%s</a></li>', esc_url(sci_contact_url()), esc_html__('FAQs', 'sco-investor'));
	echo '<li>';
	the_privacy_policy_link('', '');
	echo '</li><li>';
	sci_legal_link('terms-of-service', __('Terms of Service', 'sco-investor'));
	echo '</li>';
	echo '</ul>';
}

/**
 * Indian-style digit grouping (e.g. 1234567 -> "12,34,567") rather than
 * the Western 1,234,567 — this is a site for Indian equity investors and
 * every price in the static design was already written in that format.
 */
function sci_format_inr($amount) {
	$amount   = (float) $amount;
	$negative = $amount < 0;
	$amount   = abs($amount);
	$whole    = (int) $amount;
	$decimal  = round($amount - $whole, 2);

	$whole_str = (string) $whole;
	if (strlen($whole_str) > 3) {
		$last_three = substr($whole_str, -3);
		$rest       = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($whole_str, 0, -3));
		$whole_str  = $rest . ',' . $last_three;
	}

	if ($decimal > 0) {
		$whole_str .= '.' . str_pad((string) round($decimal * 100), 2, '0', STR_PAD_LEFT);
	}

	return ($negative ? '-' : '') . '₹' . $whole_str;
}

/**
 * Whole-number percent-off for a struck-through original price. Returns 0
 * (meaning: don't show a badge) when there's no real discount to report,
 * so callers can do a plain `if ($discount > 0)` without re-checking the
 * original price themselves.
 */
function sci_discount_percent($price, $price_original) {
	$price          = (float) $price;
	$price_original = (float) $price_original;
	if ($price_original <= 0 || $price_original <= $price) return 0;
	return (int) round((($price_original - $price) / $price_original) * 100);
}

/**
 * Display label for a course's level: the owner's manual override if
 * set, else derived from the course_level terms actually checked —
 * all three checked reads as "All Levels" (mirrors the static design's
 * data-level="beginner intermediate advanced" technique for that case),
 * otherwise the term name(s) joined.
 */
function sci_course_level_label($course_id) {
	$override = get_post_meta($course_id, '_sci_level_label', true);
	if ($override) return $override;

	$terms = get_the_terms($course_id, 'course_level');
	if (!$terms || is_wp_error($terms)) return '';
	$names = wp_list_pluck($terms, 'name');
	return count($names) >= 3 ? __('All Levels', 'sco-investor') : implode(' / ', $names);
}

/**
 * Space-separated course_level slugs for the data-level attribute the
 * existing filter-pill JS in main.js already reads.
 */
function sci_course_level_data_attr($course_id) {
	$terms = get_the_terms($course_id, 'course_level');
	if (!$terms || is_wp_error($terms)) return '';
	return implode(' ', wp_list_pluck($terms, 'slug'));
}

/**
 * One representative inline-SVG icon per material type. The static
 * mockup used a different icon per item even within the same type;
 * tying the icon to the type instead means the owner gets a sensible
 * icon automatically from the Material Type dropdown — no icon-picker
 * field needed to stay "zero coding".
 */
function sci_material_icon($type) {
	$icons = [
		'pdf'   => '<svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'sheet' => '<svg viewBox="0 0 24 24" fill="none"><path d="M7 3h7l4 4v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 12h6M9 16h6M9 8h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'excel' => '<svg viewBox="0 0 24 24" fill="none"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>',
		'tool'  => '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="2"/><path d="M10 8.5l6 3.5-6 3.5v-7z" fill="currentColor"/></svg>',
		'other' => '<svg viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="9" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 11V7a4 4 0 018 0v4" stroke="currentColor" stroke-width="2"/></svg>',
	];
	return $icons[$type] ?? $icons['other'];
}

/**
 * Collapses the 5-value material type onto the 3 filter-pill buckets the
 * static design ships (sheet/pdf/tool) — "excel" folds into "sheet" and
 * "other" has no pill of its own, so it only ever shows under "All".
 */
function sci_material_filter_bucket($type) {
	return $type === 'excel' ? 'sheet' : $type;
}

/**
 * Badge text for a material: the owner's manual override if set,
 * otherwise the Material Type's default label.
 */
function sci_material_badge($material_id) {
	$badge = get_post_meta($material_id, '_sci_badge', true);
	if ($badge) return $badge;
	$type  = get_post_meta($material_id, '_sci_material_type', true) ?: 'other';
	$types = sci_material_types();
	return $types[$type] ?? $types['other'];
}

/**
 * Resolves the actual link target for a material: an uploaded File
 * takes priority over an External URL when both are set. Returns ''
 * when a purchasable product is linked and the current user hasn't
 * bought it (and can't edit the post) — see inc/commerce.php — so every
 * caller's existing "no link? show the disabled/locked state" branch
 * already does the right thing with zero changes on their end.
 */
function sci_material_link($material_id) {
	if (!sci_user_can_access($material_id)) {
		return '';
	}

	$file_id = (int) get_post_meta($material_id, '_sci_file', true);
	if ($file_id) {
		$url = wp_get_attachment_url($file_id);
		if ($url) {
			// Once a product is actually linked, route through the
			// access-checked download endpoint instead of handing out the
			// permanent, unauthenticated attachment URL — keeps a paid
			// file's real location out of the page source. Free/unlinked
			// materials keep today's direct URL untouched.
			return sci_linked_product_id($material_id) ? sci_download_url($material_id) : $url;
		}
	}
	$external = get_post_meta($material_id, '_sci_external_url', true);
	if ($external) return $external;

	// A "tool" material with no explicit URL is assumed to be the site's
	// own screener — falls back to the Customizer setting instead of
	// requiring the owner to duplicate that URL into this field too.
	$type = get_post_meta($material_id, '_sci_material_type', true);
	if ($type === 'tool') return sci_screener_url();

	return '';
}

/**
 * CTA label matches the static design's wording per type: you "Open" a
 * tool, "Get a Copy" of a Sheet (you can't download someone else's),
 * and "Download" anything else.
 */
function sci_material_cta_label($type) {
	if ($type === 'tool') return __('Open Tool', 'sco-investor');
	if ($type === 'sheet') return __('Get Copy', 'sco-investor');
	return __('Download', 'sco-investor');
}
