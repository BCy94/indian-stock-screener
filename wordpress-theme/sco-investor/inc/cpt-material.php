<?php
/**
 * Material CPT — free downloadable resources (sheets, PDFs, tools).
 * No taxonomy: _sci_material_type is a small fixed enum that drives the
 * icon, default badge label and filter-pill bucket all at once, kept as
 * post meta rather than a taxonomy since it's a single fixed choice per
 * item, not a multi-value classification.
 */

if (!defined('ABSPATH')) exit;

function sci_material_types() {
	return [
		'pdf'   => __('PDF Guide', 'sco-investor'),
		'sheet' => __('Google Sheet', 'sco-investor'),
		'excel' => __('Excel', 'sco-investor'),
		'tool'  => __('Web App / Tool', 'sco-investor'),
		'other' => __('Other', 'sco-investor'),
	];
}

function sci_register_material_cpt() {
	register_post_type('material', [
		'labels' => [
			'name'               => __('Materials', 'sco-investor'),
			'singular_name'      => __('Material', 'sco-investor'),
			'add_new_item'       => __('Add New Material', 'sco-investor'),
			'edit_item'          => __('Edit Material', 'sco-investor'),
			'new_item'           => __('New Material', 'sco-investor'),
			'view_item'          => __('View Material', 'sco-investor'),
			'all_items'          => __('All Materials', 'sco-investor'),
			'search_items'       => __('Search Materials', 'sco-investor'),
			'not_found'          => __('No materials found.', 'sco-investor'),
			'archives'           => __('Material Archives', 'sco-investor'),
			'featured_image'     => __('Material Thumbnail', 'sco-investor'),
			'set_featured_image' => __('Set material thumbnail', 'sco-investor'),
		],
		'public'        => true,
		'menu_position' => 6,
		'menu_icon'     => 'dashicons-media-document',
		'has_archive'   => 'materials',
		'rewrite'       => ['slug' => 'material', 'with_front' => false],
		'show_in_rest'  => true,
		'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'],
	]);
}
add_action('init', 'sci_register_material_cpt');

function sci_register_material_meta() {
	$auth_callback = function () {
		return current_user_can('edit_posts');
	};

	register_post_meta('material', '_sci_material_type', [
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'default'           => 'other',
		'sanitize_callback' => function ($value) {
			return array_key_exists($value, sci_material_types()) ? $value : 'other';
		},
		'auth_callback' => $auth_callback,
	]);

	register_post_meta('material', '_sci_file', [
		'type'              => 'integer',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'absint',
		'auth_callback'     => $auth_callback,
	]);

	register_post_meta('material', '_sci_external_url', [
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_url',
		'auth_callback'     => $auth_callback,
	]);

	register_post_meta('material', '_sci_badge', [
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => $auth_callback,
	]);
}
add_action('init', 'sci_register_material_meta');
