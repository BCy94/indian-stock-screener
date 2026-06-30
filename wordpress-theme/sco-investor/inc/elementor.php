<?php
/**
 * Elementor integration — loads only when Elementor plugin is active.
 *
 * Registers an "So Called Investor" widget category and six custom widgets
 * that bring the theme's dynamic content (courses, materials, testimonials,
 * stats counter, hero, calculator) into the Elementor drag-and-drop editor.
 * None of this loads at all when Elementor is not installed, so the theme
 * stays fully functional without it.
 */

if (!defined('ABSPATH')) exit;

add_action('elementor/loaded', 'sci_elementor_init');

function sci_elementor_init() {
	add_action('elementor/elements/categories_registered', 'sci_elementor_add_category');
	add_action('elementor/widgets/register',              'sci_elementor_register_widgets');
	add_action('elementor/frontend/after_enqueue_styles', 'sci_elementor_compat_styles');
	add_action('elementor/editor/after_enqueue_styles',   'sci_elementor_compat_styles');
}

function sci_elementor_add_category($manager) {
	$manager->add_category('sci', [
		'title' => __('So Called Investor', 'sco-investor'),
		'icon'  => 'eicon-posts-grid',
	]);
}

function sci_elementor_register_widgets($manager) {
	$dir = SCI_THEME_DIR . '/inc/elementor-widgets/';
	$map = [
		'hero'         => 'SCI_Widget_Hero',
		'stats'        => 'SCI_Widget_Stats',
		'courses'      => 'SCI_Widget_Courses',
		'materials'    => 'SCI_Widget_Materials',
		'testimonials' => 'SCI_Widget_Testimonials',
		'calculator'   => 'SCI_Widget_Calculator',
	];
	foreach ($map as $file => $class) {
		$path = $dir . $file . '.php';
		if (file_exists($path)) {
			require_once $path;
		}
		if (class_exists($class)) {
			$manager->register(new $class());
		}
	}
}

function sci_elementor_compat_styles() {
	wp_enqueue_style(
		'sci-elementor-compat',
		SCI_THEME_URI . '/assets/css/elementor-compat.css',
		['sci-main'],
		SCI_VERSION
	);
}
