<?php
/**
 * Lightweight built-in SEO: meta description, Open Graph, Twitter Card.
 * The <title> tag and canonical <link> are left to core (title-tag
 * support + core's own rel_canonical()) rather than duplicated here.
 * Steps aside entirely if a dedicated SEO plugin is active, since two
 * sources of meta tags fighting each other is worse than none.
 */

if (!defined('ABSPATH')) exit;

if (defined('WPSEO_VERSION') || class_exists('RankMath') || defined('AIOSEO_VERSION')) {
	return;
}

function sci_seo_description() {
	if (is_singular()) {
		$post = get_queried_object();
		if (!($post instanceof WP_Post)) return '';
		$source = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 35);
		return wp_strip_all_tags($source);
	}
	if (is_category() || is_tag() || is_tax()) {
		$term_desc = term_description();
		return $term_desc ? wp_trim_words(wp_strip_all_tags($term_desc), 35) : '';
	}
	if (is_post_type_archive()) {
		$post_type = get_post_type_object(get_query_var('post_type'));
		return $post_type && !empty($post_type->description) ? $post_type->description : '';
	}
	return get_bloginfo('description');
}

function sci_seo_image() {
	if (is_singular() && has_post_thumbnail()) {
		$img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
		if ($img) return $img[0];
	}
	$default_share_id = (int) get_theme_mod('sci_default_share_image');
	if ($default_share_id) {
		$img = wp_get_attachment_image_src($default_share_id, 'large');
		if ($img) return $img[0];
	}
	$custom_logo_id = get_theme_mod('custom_logo');
	if ($custom_logo_id) {
		$img = wp_get_attachment_image_src($custom_logo_id, 'large');
		if ($img) return $img[0];
	}
	return '';
}

function sci_seo_url() {
	if (is_front_page()) return home_url('/');
	if (is_singular()) return get_permalink();
	if (is_category() || is_tag() || is_tax()) {
		$link = get_term_link(get_queried_object());
		return is_wp_error($link) ? home_url('/') : $link;
	}
	if (is_post_type_archive()) {
		return get_post_type_archive_link(get_query_var('post_type')) ?: home_url('/');
	}
	if (is_home()) {
		$page_for_posts = (int) get_option('page_for_posts');
		return $page_for_posts ? get_permalink($page_for_posts) : home_url('/');
	}
	global $wp;
	return home_url(add_query_arg([], $wp->request));
}

function sci_seo_meta_tags() {
	if (is_admin() || is_feed() || is_404() || is_search()) return;

	$title       = wp_get_document_title();
	$description = sci_seo_description();
	$image       = sci_seo_image();
	$url         = sci_seo_url();

	if ($description) {
		printf('<meta name="description" content="%s">' . "\n", esc_attr($description));
	}

	printf('<meta property="og:type" content="%s">' . "\n", is_singular('post') ? 'article' : 'website');
	printf('<meta property="og:title" content="%s">' . "\n", esc_attr($title));
	if ($description) {
		printf('<meta property="og:description" content="%s">' . "\n", esc_attr($description));
	}
	printf('<meta property="og:url" content="%s">' . "\n", esc_url($url));
	printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr(get_bloginfo('name')));
	if ($image) {
		printf('<meta property="og:image" content="%s">' . "\n", esc_url($image));
	}

	printf('<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary');
	printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($title));
	if ($description) {
		printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($description));
	}
	if ($image) {
		printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($image));
	}
}
add_action('wp_head', 'sci_seo_meta_tags', 1);
