<?php
/**
 * Customizer: theme options the owner can set without touching code.
 */

if (!defined('ABSPATH')) exit;

function sci_sanitize_checkbox($value) {
	return (bool) $value;
}

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

	/**
	 * Homepage Sections — every block on the front page can be switched off
	 * without touching code. All default to "on" so a fresh install (and
	 * every site that existed before these controls did) looks unchanged
	 * until the owner actually unchecks something.
	 */
	$wp_customize->add_section('sci_homepage_sections', [
		'title'    => __('Homepage Sections', 'sco-investor'),
		'priority' => 31,
	]);

	$homepage_toggles = [
		'sci_show_ticker'         => __('Stock ticker tape', 'sco-investor'),
		'sci_show_stats'          => __('Stats row (investors taught, etc.)', 'sco-investor'),
		'sci_show_features'       => __('"Why So Called Investor" section', 'sco-investor'),
		'sci_show_courses_home'   => __('Courses preview', 'sco-investor'),
		'sci_show_materials_home' => __('Materials preview', 'sco-investor'),
		'sci_show_testimonials'   => __('Testimonials', 'sco-investor'),
		'sci_show_cta_band'       => __('Bottom call-to-action band', 'sco-investor'),
	];
	foreach ($homepage_toggles as $key => $label) {
		$wp_customize->add_setting($key, [
			'default'           => true,
			'sanitize_callback' => 'sci_sanitize_checkbox',
			'transport'         => 'refresh',
		]);
		$wp_customize->add_control($key, [
			'section' => 'sci_homepage_sections',
			'label'   => $label,
			'type'    => 'checkbox',
		]);
	}

	/**
	 * Blog Settings.
	 */
	$wp_customize->add_section('sci_blog_settings', [
		'title'    => __('Blog Settings', 'sco-investor'),
		'priority' => 32,
	]);

	$blog_toggles = [
		'sci_blog_show_sidebar'        => __('Show sidebar on blog posts', 'sco-investor'),
		'sci_blog_show_author_box'     => __('Show author box on blog posts', 'sco-investor'),
		'sci_blog_show_featured_image' => __('Show featured image on blog posts', 'sco-investor'),
	];
	foreach ($blog_toggles as $key => $label) {
		$wp_customize->add_setting($key, [
			'default'           => true,
			'sanitize_callback' => 'sci_sanitize_checkbox',
			'transport'         => 'refresh',
		]);
		$wp_customize->add_control($key, [
			'section' => 'sci_blog_settings',
			'label'   => $label,
			'type'    => 'checkbox',
		]);
	}

	$wp_customize->add_setting('sci_blog_excerpt_length', [
		'default'           => 22,
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control('sci_blog_excerpt_length', [
		'section'     => 'sci_blog_settings',
		'label'       => __('Excerpt length (words)', 'sco-investor'),
		'description' => __('How much of each post to show in blog listing cards.', 'sco-investor'),
		'type'        => 'number',
		'input_attrs' => ['min' => 5, 'max' => 100],
	]);

	/**
	 * Images — visual elements the owner can swap without touching code or
	 * hiring a designer. Both optional; the theme's existing built-in
	 * visuals keep showing until something is uploaded here.
	 */
	$wp_customize->add_section('sci_images', [
		'title'    => __('Images', 'sco-investor'),
		'priority' => 33,
	]);

	$wp_customize->add_setting('sci_hero_image', [
		'default'           => '',
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'sci_hero_image', [
		'section'     => 'sci_images',
		'label'       => __('Homepage Hero Image', 'sco-investor'),
		'description' => __('Optional. Replaces the animated chart graphic in the homepage hero with your own image.', 'sco-investor'),
		'mime_type'   => 'image',
	]));

	$wp_customize->add_setting('sci_default_share_image', [
		'default'           => '',
		'sanitize_callback' => 'absint',
		'transport'         => 'refresh',
	]);
	$wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'sci_default_share_image', [
		'section'     => 'sci_images',
		'label'       => __('Default Social Share Image', 'sco-investor'),
		'description' => __('Used for Facebook/Twitter/etc. link previews on any page that has no featured image of its own.', 'sco-investor'),
		'mime_type'   => 'image',
	]));
}
add_action('customize_register', 'sci_customize_register');
