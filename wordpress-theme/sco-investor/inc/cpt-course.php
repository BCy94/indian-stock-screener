<?php
/**
 * Course CPT + course_level taxonomy. Slug/rewrite are kept distinct from
 * WooCommerce's "product" CPT (archive at /courses/, singular at /course/)
 * so a future "course becomes a sellable product" migration never collides
 * with a WooCommerce install on the same site.
 */

if (!defined('ABSPATH')) exit;

function sci_register_course_cpt() {
	register_post_type('course', [
		'labels' => [
			'name'               => __('Courses', 'sco-investor'),
			'singular_name'      => __('Course', 'sco-investor'),
			'add_new_item'       => __('Add New Course', 'sco-investor'),
			'edit_item'          => __('Edit Course', 'sco-investor'),
			'new_item'           => __('New Course', 'sco-investor'),
			'view_item'          => __('View Course', 'sco-investor'),
			'all_items'          => __('All Courses', 'sco-investor'),
			'search_items'       => __('Search Courses', 'sco-investor'),
			'not_found'          => __('No courses found.', 'sco-investor'),
			'archives'           => __('Course Archives', 'sco-investor'),
			'featured_image'     => __('Course Thumbnail', 'sco-investor'),
			'set_featured_image' => __('Set course thumbnail', 'sco-investor'),
		],
		'public'        => true,
		'menu_position' => 5,
		'menu_icon'     => 'dashicons-welcome-learn-more',
		'has_archive'   => 'courses',
		'rewrite'       => ['slug' => 'course', 'with_front' => false],
		'show_in_rest'  => true,
		'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
	]);

	register_taxonomy('course_level', ['course'], [
		'labels' => [
			'name'          => __('Course Levels', 'sco-investor'),
			'singular_name' => __('Course Level', 'sco-investor'),
			'all_items'     => __('All Levels', 'sco-investor'),
		],
		// Hierarchical purely to get the checkbox-style admin UI (like
		// Categories) instead of the freeform tag/comma UI — there's a
		// fixed, curated set of levels and no actual parent/child terms
		// are ever created.
		'hierarchical'      => true,
		'public'            => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => ['slug' => 'course-level'],
	]);
}
add_action('init', 'sci_register_course_cpt');

/**
 * Seeds the three real levels so the taxonomy is ready to use immediately
 * — zero manual setup before the owner can assign a level to a course.
 * "All Levels" is deliberately not a 4th term: a course meant for every
 * level gets all three terms checked instead, so it still matches each
 * individual filter pill (the static design's own data-level="beginner
 * intermediate advanced" technique) — a standalone term would match none
 * of them. The display label for that case is handled separately by
 * sci_course_level_label().
 */
function sci_seed_course_levels() {
	foreach (['Beginner', 'Intermediate', 'Advanced'] as $name) {
		if (!term_exists($name, 'course_level')) {
			wp_insert_term($name, 'course_level');
		}
	}
}
add_action('after_switch_theme', 'sci_seed_course_levels');

function sci_register_course_meta() {
	$string_field = [
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => function () {
			return current_user_can('edit_posts');
		},
	];
	$number_field           = $string_field;
	$number_field['type']   = 'number';
	$number_field['sanitize_callback'] = function ($value) {
		return $value === '' ? '' : round((float) $value, 2);
	};
	$integer_field         = $string_field;
	$integer_field['type'] = 'integer';
	$integer_field['sanitize_callback'] = 'absint';

	register_post_meta('course', '_sci_price', $number_field);
	register_post_meta('course', '_sci_price_original', $number_field);
	register_post_meta('course', '_sci_duration_weeks', $integer_field);
	register_post_meta('course', '_sci_lesson_count', $integer_field);
	register_post_meta('course', '_sci_badge', $string_field);
	register_post_meta('course', '_sci_level_label', $string_field);
}
add_action('init', 'sci_register_course_meta');
