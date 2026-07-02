<?php
/**
 * Customizer: theme options the owner can set without touching code.
 */

if (!defined('ABSPATH')) exit;

function sci_sanitize_checkbox($value) {
	return (bool) $value;
}

function sci_customize_register($wp_customize) {
	/**
	 * Brand Colors — first section on purpose (priority 5): color is the
	 * first thing an owner reaches for when making the theme "theirs".
	 * Every control here maps to a CSS custom property consumed sitewide
	 * (buttons, links, accents, ticker up/down, backgrounds) — see
	 * inc/brand-colors.php for the actual override output. Only the
	 * accent color is picked directly; its darker hover-state and
	 * translucent-background variants are derived automatically so the
	 * owner never has to hand-pick three coordinated shades.
	 */
	$wp_customize->add_section('sci_brand_colors', [
		'title'       => __('Brand Colors', 'sco-investor'),
		'priority'    => 5,
		'description' => __('Change these and every button, link, highlight and price tag on the site updates to match — no code, no CSS editing.', 'sco-investor'),
	]);

	$color_controls = [
		'sci_color_accent'   => ['default' => '#C9A24B', 'label' => __('Accent / Brand Color', 'sco-investor'), 'description' => __('Used for buttons, links, badges and highlights sitewide.', 'sco-investor')],
		'sci_color_up'       => ['default' => '#3F8F5F', 'label' => __('Gain / Positive Color', 'sco-investor'), 'description' => __('Stock ticker and calculator "up"/positive values.', 'sco-investor')],
		'sci_color_down'     => ['default' => '#B0473E', 'label' => __('Loss / Negative Color', 'sco-investor'), 'description' => __('Stock ticker "down"/negative values.', 'sco-investor')],
		'sci_color_dark_bg'  => ['default' => '#14171F', 'label' => __('Dark Mode Background', 'sco-investor'), 'description' => __('Main background color in dark mode (default).', 'sco-investor')],
		'sci_color_light_bg' => ['default' => '#F6F1E7', 'label' => __('Light Mode Background', 'sco-investor'), 'description' => __('Main background color when a visitor switches to light mode.', 'sco-investor')],
	];
	foreach ($color_controls as $key => $cfg) {
		$wp_customize->add_setting($key, [
			'default'           => $cfg['default'],
			'sanitize_callback' => 'sanitize_hex_color',
			'transport'         => 'refresh',
		]);
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $key, [
			'section'     => 'sci_brand_colors',
			'label'       => $cfg['label'],
			'description' => $cfg['description'],
		]));
	}

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
	 * Homepage — Hero Section.
	 */
	$wp_customize->add_section('sci_hero', [
		'title'    => __('Homepage — Hero', 'sco-investor'),
		'priority' => 33,
	]);

	$hero_settings = [
		'sci_hero_eyebrow'      => ['label' => __('Eyebrow text (small label above heading)', 'sco-investor'), 'default' => 'Now enrolling — Summer 2026 cohort', 'type' => 'text'],
		'sci_hero_heading'      => ['label' => __('Main heading', 'sco-investor'),                            'default' => 'Invest with clarity, not noise.', 'type' => 'text'],
		'sci_hero_heading_accent' => ['label' => __('Accented word in heading (shown in gold)', 'sco-investor'), 'default' => 'clarity', 'type' => 'text'],
		'sci_hero_lead'         => ['label' => __('Lead paragraph', 'sco-investor'),                         'default' => 'Practical courses, research materials and tools for Indian equity investors — built from real portfolio experience, not recycled YouTube hype.', 'type' => 'textarea'],
		'sci_hero_cta1_text'    => ['label' => __('Primary button text', 'sco-investor'),                    'default' => 'Explore Courses', 'type' => 'text'],
		'sci_hero_cta1_url'     => ['label' => __('Primary button URL (leave blank to auto-detect courses page)', 'sco-investor'), 'default' => '', 'type' => 'url'],
		'sci_hero_cta2_text'    => ['label' => __('Secondary button text', 'sco-investor'),                  'default' => 'Browse Free Materials', 'type' => 'text'],
		'sci_hero_cta2_url'     => ['label' => __('Secondary button URL (leave blank to auto-detect materials page)', 'sco-investor'), 'default' => '', 'type' => 'url'],
		'sci_hero_trust_1'      => ['label' => __('Trust badge 1', 'sco-investor'),                         'default' => '★★★★★ 4.9/5 avg. rating', 'type' => 'text'],
		'sci_hero_trust_2'      => ['label' => __('Trust badge 2', 'sco-investor'),                         'default' => '10,000+ investors taught', 'type' => 'text'],
		'sci_hero_trust_3'      => ['label' => __('Trust badge 3', 'sco-investor'),                         'default' => 'Zero hype, 100% practical', 'type' => 'text'],
	];
	foreach ($hero_settings as $key => $cfg) {
		$sanitize = $cfg['type'] === 'url' ? 'sanitize_url' : ($cfg['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field');
		$wp_customize->add_setting($key, ['default' => $cfg['default'], 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
		$wp_customize->add_control($key, ['section' => 'sci_hero', 'label' => $cfg['label'], 'type' => $cfg['type']]);
	}

	/**
	 * Homepage — Stats Row.
	 */
	$wp_customize->add_section('sci_stats', [
		'title'    => __('Homepage — Stats Row', 'sco-investor'),
		'priority' => 34,
	]);

	$stats_defaults = [
		1 => ['count' => '10000', 'suffix' => '+',     'decimals' => '0', 'label' => 'Investors Taught'],
		2 => ['count' => '50',    'suffix' => '+',     'decimals' => '0', 'label' => 'Stocks Tracked Live'],
		3 => ['count' => '12',    'suffix' => '+ yrs', 'decimals' => '0', 'label' => 'Market Experience'],
		4 => ['count' => '4.9',   'suffix' => '/5',    'decimals' => '1', 'label' => 'Average Rating'],
	];
	foreach ($stats_defaults as $n => $d) {
		$wp_customize->add_setting("sci_stat_{$n}_count",    ['default' => $d['count'],    'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
		$wp_customize->add_setting("sci_stat_{$n}_suffix",   ['default' => $d['suffix'],   'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
		$wp_customize->add_setting("sci_stat_{$n}_decimals", ['default' => $d['decimals'], 'sanitize_callback' => 'absint',              'transport' => 'refresh']);
		$wp_customize->add_setting("sci_stat_{$n}_label",    ['default' => $d['label'],    'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
		/* translators: %d is the stat number (1–4) */
		$wp_customize->add_control("sci_stat_{$n}_count",    ['section' => 'sci_stats', 'label' => sprintf(__('Stat %d — Number', 'sco-investor'), $n), 'type' => 'text']);
		$wp_customize->add_control("sci_stat_{$n}_suffix",   ['section' => 'sci_stats', 'label' => sprintf(__('Stat %d — Suffix (e.g. + or /5)', 'sco-investor'), $n), 'type' => 'text']);
		$wp_customize->add_control("sci_stat_{$n}_decimals", ['section' => 'sci_stats', 'label' => sprintf(__('Stat %d — Decimal places (0, 1 or 2)', 'sco-investor'), $n), 'type' => 'number', 'input_attrs' => ['min' => 0, 'max' => 2]]);
		$wp_customize->add_control("sci_stat_{$n}_label",    ['section' => 'sci_stats', 'label' => sprintf(__('Stat %d — Label', 'sco-investor'), $n), 'type' => 'text']);
	}

	/**
	 * Homepage — Why Us / Features Section.
	 */
	$wp_customize->add_section('sci_features', [
		'title'    => __('Homepage — Why Us Section', 'sco-investor'),
		'priority' => 35,
	]);

	$features_text = [
		'sci_features_eyebrow'  => ['label' => __('Eyebrow text', 'sco-investor'),        'default' => 'Why So Called Investor',                                                                             'type' => 'text'],
		'sci_features_heading'  => ['label' => __('Section heading', 'sco-investor'),     'default' => 'Built different from typical "finfluencer" content',                                                  'type' => 'text'],
		'sci_features_desc'     => ['label' => __('Section description', 'sco-investor'), 'default' => 'No prediction calls, no paid pumps — just frameworks you can actually apply to your own portfolio.',  'type' => 'textarea'],
		'sci_feature_1_title'   => ['label' => __('Card 1 — Title', 'sco-investor'),      'default' => 'Practical Curriculum',                                                                                'type' => 'text'],
		'sci_feature_1_desc'    => ['label' => __('Card 1 — Description', 'sco-investor'),'default' => 'Step-by-step frameworks for fundamental and technical analysis you can use the same day you learn them.', 'type' => 'textarea'],
		'sci_feature_2_title'   => ['label' => __('Card 2 — Title', 'sco-investor'),      'default' => 'Real Portfolio Breakdowns',                                                                           'type' => 'text'],
		'sci_feature_2_desc'    => ['label' => __('Card 2 — Description', 'sco-investor'),'default' => 'Live case studies and teardowns of actual Indian companies — the good, the bad, and the red flags.',  'type' => 'textarea'],
		'sci_feature_3_title'   => ['label' => __('Card 3 — Title', 'sco-investor'),      'default' => 'Lifetime Access & Community',                                                                         'type' => 'text'],
		'sci_feature_3_desc'    => ['label' => __('Card 3 — Description', 'sco-investor'),'default' => 'One-time purchase, lifetime updates, plus a private community to ask questions and track progress together.', 'type' => 'textarea'],
	];
	foreach ($features_text as $key => $cfg) {
		$sanitize = $cfg['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field';
		$wp_customize->add_setting($key, ['default' => $cfg['default'], 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
		$wp_customize->add_control($key, ['section' => 'sci_features', 'label' => $cfg['label'], 'type' => $cfg['type']]);
	}

	/**
	 * Homepage — Testimonials Section heading.
	 */
	$wp_customize->add_section('sci_testimonials_section', [
		'title'    => __('Homepage — Testimonials Section', 'sco-investor'),
		'priority' => 36,
	]);
	$wp_customize->add_setting('sci_testi_eyebrow', ['default' => 'Student Feedback', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
	$wp_customize->add_control('sci_testi_eyebrow', ['section' => 'sci_testimonials_section', 'label' => __('Eyebrow text', 'sco-investor'), 'type' => 'text']);
	$wp_customize->add_setting('sci_testi_heading', ['default' => 'What investors say', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
	$wp_customize->add_control('sci_testi_heading', ['section' => 'sci_testimonials_section', 'label' => __('Section heading', 'sco-investor'), 'type' => 'text']);
	$wp_customize->add_setting('sci_testi_desc', ['default' => '', 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh']);
	$wp_customize->add_control('sci_testi_desc', ['section' => 'sci_testimonials_section', 'label' => __('Section description (optional)', 'sco-investor'), 'type' => 'textarea']);

	/**
	 * Homepage — CTA Band.
	 */
	$wp_customize->add_section('sci_cta_band', [
		'title'    => __('Homepage — CTA Band', 'sco-investor'),
		'priority' => 37,
	]);
	$cta_settings = [
		'sci_cta_heading'   => ['label' => __('Heading', 'sco-investor'),                             'default' => 'Ready to invest with confidence?',                                           'type' => 'text'],
		'sci_cta_desc'      => ['label' => __('Description', 'sco-investor'),                         'default' => 'Join thousands of Indian investors learning to read businesses, not just stock tickers.', 'type' => 'textarea'],
		'sci_cta_btn1_text' => ['label' => __('Button 1 text', 'sco-investor'),                       'default' => 'Explore Courses',                                                            'type' => 'text'],
		'sci_cta_btn1_url'  => ['label' => __('Button 1 URL (blank = courses archive)', 'sco-investor'), 'default' => '',                                                                        'type' => 'url'],
		'sci_cta_btn2_text' => ['label' => __('Button 2 text', 'sco-investor'),                       'default' => 'Talk to Us',                                                                 'type' => 'text'],
		'sci_cta_btn2_url'  => ['label' => __('Button 2 URL (blank = contact page)', 'sco-investor'),  'default' => '',                                                                          'type' => 'url'],
	];
	foreach ($cta_settings as $key => $cfg) {
		$sanitize = $cfg['type'] === 'url' ? 'sanitize_url' : ($cfg['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field');
		$wp_customize->add_setting($key, ['default' => $cfg['default'], 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
		$wp_customize->add_control($key, ['section' => 'sci_cta_band', 'label' => $cfg['label'], 'type' => $cfg['type']]);
	}

	/**
	 * Footer content.
	 */
	$wp_customize->add_section('sci_footer', [
		'title'    => __('Footer', 'sco-investor'),
		'priority' => 38,
	]);
	$footer_settings = [
		'sci_footer_tagline'      => ['label' => __('Brand tagline (below logo)', 'sco-investor'),      'default' => 'Honest, practical investing education for Indian markets — courses, research materials and tools built from real portfolio experience, not hype.', 'type' => 'textarea'],
		'sci_footer_copyright'    => ['label' => __('Copyright text (leave blank to auto-generate)', 'sco-investor'), 'default' => '', 'type' => 'text'],
		'sci_newsletter_heading'  => ['label' => __('Newsletter column heading', 'sco-investor'),       'default' => 'Stay Updated', 'type' => 'text'],
		'sci_newsletter_tagline'  => ['label' => __('Newsletter column tagline', 'sco-investor'),       'default' => 'Market insights and new course drops in your inbox.', 'type' => 'text'],
	];
	foreach ($footer_settings as $key => $cfg) {
		$sanitize = $cfg['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field';
		$wp_customize->add_setting($key, ['default' => $cfg['default'], 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
		$wp_customize->add_control($key, ['section' => 'sci_footer', 'label' => $cfg['label'], 'type' => $cfg['type']]);
	}

	/**
	 * Images — visual elements the owner can swap without touching code or
	 * hiring a designer. Both optional; the theme's existing built-in
	 * visuals keep showing until something is uploaded here.
	 */
	$wp_customize->add_section('sci_images', [
		'title'    => __('Images', 'sco-investor'),
		'priority' => 39,
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

	/**
	 * Archive Pages — the Courses and Materials listings are auto-generated
	 * from published posts (not a single Page an owner can open in
	 * Elementor), so their heading text lives here instead. The card grid
	 * itself always reflects live Course/Material content and can't be
	 * hidden/reordered from this panel — only the text above it.
	 */
	$wp_customize->add_section('sci_archive_pages', [
		'title'    => __('Archive Pages (Courses/Materials)', 'sco-investor'),
		'priority' => 45,
	]);

	$archive_fields = [
		'sci_courses_archive_eyebrow'    => ['default' => '', 'label' => __('Courses — Eyebrow (blank = auto "N Courses · Lifetime Access")', 'sco-investor'), 'type' => 'text'],
		'sci_courses_archive_heading'    => ['default' => 'Courses that turn theory into portfolio decisions', 'label' => __('Courses — Heading', 'sco-investor'), 'type' => 'text'],
		'sci_courses_archive_accent'     => ['default' => 'portfolio decisions', 'label' => __('Courses — Gold Accent Words (must match heading exactly)', 'sco-investor'), 'type' => 'text'],
		'sci_courses_archive_desc'       => ['default' => 'Self-paced video courses with downloadable worksheets, quizzes and lifetime updates. One-time payment, no recurring fees.', 'label' => __('Courses — Description', 'sco-investor'), 'type' => 'textarea'],
		'sci_materials_archive_eyebrow'  => ['default' => 'Free Forever', 'label' => __('Materials — Eyebrow', 'sco-investor'), 'type' => 'text'],
		'sci_materials_archive_heading'  => ['default' => 'Super Investor Materials Library', 'label' => __('Materials — Heading', 'sco-investor'), 'type' => 'text'],
		'sci_materials_archive_accent'   => ['default' => 'Materials Library', 'label' => __('Materials — Gold Accent Words (must match heading exactly)', 'sco-investor'), 'type' => 'text'],
		'sci_materials_archive_desc'     => ['default' => 'Templates, checklists, reports and tools — free to download, no signup gimmicks, no spam.', 'label' => __('Materials — Description', 'sco-investor'), 'type' => 'textarea'],
	];
	foreach ($archive_fields as $key => $cfg) {
		$sanitize = $cfg['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field';
		$wp_customize->add_setting($key, ['default' => $cfg['default'], 'sanitize_callback' => $sanitize, 'transport' => 'refresh']);
		$wp_customize->add_control($key, ['section' => 'sci_archive_pages', 'label' => $cfg['label'], 'type' => $cfg['type']]);
	}
}
add_action('customize_register', 'sci_customize_register');
