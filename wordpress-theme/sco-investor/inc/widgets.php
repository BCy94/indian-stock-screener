<?php
/**
 * Blog post sidebar widget area. Registered so the owner can add/reorder
 * widgets from Appearance > Widgets with zero coding; single.php falls
 * back to its existing hardcoded Popular Reading / Categories / Screener
 * CTA blocks whenever no widget has been added yet, so a fresh install
 * still looks fully featured.
 */

if (!defined('ABSPATH')) exit;

function sci_register_widget_areas() {
	register_sidebar([
		'name'          => __('Blog Sidebar', 'sco-investor'),
		'id'            => 'sci-blog-sidebar',
		'description'   => __('Shown on single blog posts, below the author box.', 'sco-investor'),
		'before_widget' => '<div class="widget glass %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	]);
}
add_action('widgets_init', 'sci_register_widget_areas');
