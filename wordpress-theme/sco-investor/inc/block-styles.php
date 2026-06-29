<?php
/**
 * Custom Block Styles — the no-code way an editor applies the site's
 * existing .glass / .btn-primary surface treatments to ordinary core
 * blocks. CSS lives in assets/css/blocks.css, scoped to the standard
 * .wp-block-{name}.is-style-{slug} selectors core block styles use.
 */

if (!defined('ABSPATH')) exit;

function sci_register_block_styles() {
	register_block_style('core/group', [
		'name'  => 'glass-card',
		'label' => __('Glass Card', 'sco-investor'),
	]);

	register_block_style('core/button', [
		'name'  => 'bronze',
		'label' => __('Bronze', 'sco-investor'),
	]);
}
add_action('init', 'sci_register_block_styles');
