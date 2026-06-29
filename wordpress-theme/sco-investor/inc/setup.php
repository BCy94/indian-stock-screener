<?php
/**
 * Core theme setup: supports, nav menus, asset enqueueing.
 */

if (!defined('ABSPATH')) exit;

function sci_setup() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
	add_theme_support('responsive-embeds');
	add_theme_support('align-wide');
	add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style']);
	add_theme_support('custom-logo', [
		'height'      => 40,
		'width'       => 160,
		'flex-height' => true,
		'flex-width'  => true,
	]);
	add_theme_support('editor-styles');
	add_editor_style(['assets/css/editor-style.css', 'assets/css/blocks.css']);

	register_nav_menus([
		'primary'        => __('Primary Navigation', 'sco-investor'),
		'footer-explore' => __('Footer — Explore', 'sco-investor'),
		'footer-support' => __('Footer — Support', 'sco-investor'),
	]);
}
add_action('after_setup_theme', 'sci_setup');

function sci_content_width() {
	$GLOBALS['content_width'] = apply_filters('sci_content_width', 760);
}
add_action('after_setup_theme', 'sci_content_width', 0);

function sci_enqueue_assets() {
	wp_enqueue_style('sco-investor-style', get_stylesheet_uri(), [], SCI_VERSION);
	wp_enqueue_style('sci-fonts', SCI_THEME_URI . '/assets/css/fonts.css', [], SCI_VERSION);
	wp_enqueue_style('sci-main', SCI_THEME_URI . '/assets/css/main.css', ['sco-investor-style', 'sci-fonts'], SCI_VERSION);
	wp_enqueue_style('sci-blocks', SCI_THEME_URI . '/assets/css/blocks.css', ['sci-main'], SCI_VERSION);
	wp_enqueue_script('sci-main', SCI_THEME_URI . '/assets/js/main.js', [], SCI_VERSION, true);
}
add_action('wp_enqueue_scripts', 'sci_enqueue_assets');

/**
 * Mirrors wp_nav_menu()'s "current-menu-item" detection onto the <a>
 * itself (the theme's CSS styles a.active, not li.current-menu-item).
 */
function sci_nav_link_active_class($atts, $item) {
	$current = array_intersect(
		['current-menu-item', 'current_page_item', 'current-menu-parent', 'current_page_parent'],
		$item->classes
	);
	if (!empty($current)) {
		$atts['class'] = isset($atts['class']) ? trim($atts['class'] . ' active') : 'active';
	}
	return $atts;
}
add_filter('nav_menu_link_attributes', 'sci_nav_link_active_class', 10, 2);

/**
 * Renders the primary menu as bare <a> tags with no <li> wrapper,
 * matching the design's flat nav-links / mobile-panel link lists.
 */
class SCI_Walker_Nav_Flat extends Walker_Nav_Menu {
	public function start_lvl(&$output, $depth = 0, $args = null) {}
	public function end_lvl(&$output, $depth = 0, $args = null) {}

	public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
		$classes   = empty($item->classes) ? [] : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$atts = [
			'href'  => !empty($item->url) ? $item->url : '',
			'class' => trim(implode(' ', array_filter($classes))),
		];
		$atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

		$attributes = '';
		foreach ($atts as $key => $value) {
			if ($value === '' || $value === null) continue;
			$value       = ('href' === $key) ? esc_url($value) : esc_attr($value);
			$attributes .= ' ' . $key . '="' . $value . '"';
		}

		$title   = apply_filters('the_title', $item->title, $item->ID);
		$output .= '<a' . $attributes . '>' . $title . '</a>';
	}

	public function end_el(&$output, $item, $depth = 0, $args = null) {}
}

/**
 * Explicitly allowlists AI crawlers in robots.txt alongside regular
 * search engines — none are blocked by default, but an explicit Allow
 * avoids ambiguity for crawlers that respect named user-agent blocks.
 * Skipped entirely when the site has "discourage search engines"
 * checked (Settings > Reading), matching that setting's own intent.
 */
function sci_allow_ai_crawlers($output, $public) {
	if (!$public) return $output;
	return $output . "\nUser-agent: GPTBot\nAllow: /\n\nUser-agent: ClaudeBot\nAllow: /\n\nUser-agent: Google-Extended\nAllow: /\n";
}
add_filter('robots_txt', 'sci_allow_ai_crawlers', 10, 2);
