<?php
/**
 * Registers the pattern category used by patterns/about-*.php. WordPress
 * auto-registers any .php file placed in /patterns/ with no functions.php
 * wiring required — this only adds the category those files declare via
 * their own "Categories:" header line.
 */

if (!defined('ABSPATH')) exit;

function sci_register_pattern_categories() {
	register_block_pattern_category('sco-investor-about', [
		'label' => __('About Page Sections', 'sco-investor'),
	]);
}
add_action('init', 'sci_register_pattern_categories');
