<?php
/**
 * Testimonial CPT — student/customer reviews displayed on the homepage.
 * Title = person name. Quote, role/location, star rating, and initials
 * override are stored in meta fields so editing stays in a single, clean
 * form rather than the full Gutenberg editor.
 */

if (!defined('ABSPATH')) exit;

function sci_register_testimonial_cpt() {
	register_post_type('testimonial', [
		'labels' => [
			'name'               => __('Testimonials', 'sco-investor'),
			'singular_name'      => __('Testimonial', 'sco-investor'),
			'add_new_item'       => __('Add New Testimonial', 'sco-investor'),
			'edit_item'          => __('Edit Testimonial', 'sco-investor'),
			'new_item'           => __('New Testimonial', 'sco-investor'),
			'all_items'          => __('All Testimonials', 'sco-investor'),
			'not_found'          => __('No testimonials yet — add your first student review above.', 'sco-investor'),
			'featured_image'     => __('Photo (optional)', 'sco-investor'),
			'set_featured_image' => __('Set reviewer photo', 'sco-investor'),
		],
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_position'      => 9,
		'menu_icon'          => 'dashicons-format-quote',
		'show_in_rest'       => true,
		'supports'           => ['title', 'thumbnail', 'page-attributes'],
		// title = reviewer name; thumbnail = optional photo; page-attributes = order
	]);
}
add_action('init', 'sci_register_testimonial_cpt');

function sci_register_testimonial_meta() {
	$str = [
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => function () { return current_user_can('edit_posts'); },
	];
	$textarea          = $str;
	$textarea['sanitize_callback'] = 'sanitize_textarea_field';
	$int               = $str;
	$int['type']       = 'integer';
	$int['sanitize_callback'] = 'absint';

	register_post_meta('testimonial', '_sci_testi_quote', $textarea);
	register_post_meta('testimonial', '_sci_testi_role', $str);
	register_post_meta('testimonial', '_sci_testi_initials', $str);
	register_post_meta('testimonial', '_sci_testi_stars', $int);
}
add_action('init', 'sci_register_testimonial_meta');
