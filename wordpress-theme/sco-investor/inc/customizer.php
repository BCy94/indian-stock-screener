<?php
/**
 * Customizer: theme options the owner can set without touching code.
 */

if (!defined('ABSPATH')) exit;

function sci_customize_register($wp_customize) {
	$wp_customize->add_section('sci_theme_options', [
		'title'    => __('So Called Investor — Theme Options', 'sco-investor'),
		'priority' => 30,
	]);

	$wp_customize->add_setting('sci_screener_url', [
		'default'           => '',
		'sanitize_callback' => 'sanitize_url',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control('sci_screener_url', [
		'section'     => 'sci_theme_options',
		'label'       => __('Stock Screener URL', 'sco-investor'),
		'description' => __('Link to your free stock screener tool. Leave blank to hide the link sitewide.', 'sco-investor'),
		'type'        => 'url',
	]);

	$socials = [
		'twitter'   => __('Twitter / X URL', 'sco-investor'),
		'youtube'   => __('YouTube URL', 'sco-investor'),
		'instagram' => __('Instagram URL', 'sco-investor'),
		'telegram'  => __('Telegram URL', 'sco-investor'),
	];
	foreach ($socials as $key => $label) {
		$setting = 'sci_social_' . $key;
		$wp_customize->add_setting($setting, [
			'default'           => '',
			'sanitize_callback' => 'sanitize_url',
			'transport'         => 'refresh',
		]);
		$wp_customize->add_control($setting, [
			'section' => 'sci_theme_options',
			'label'   => $label,
			'type'    => 'url',
		]);
	}
}
add_action('customize_register', 'sci_customize_register');
