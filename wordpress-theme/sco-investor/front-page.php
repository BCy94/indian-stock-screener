<?php
/**
 * Static homepage. All text, stats, features, and CTA content is editable
 * via Appearance → Customize. Testimonials come from the Testimonials CPT.
 * Courses and materials are live WP_Query loops.
 */
get_header();

// ── Hero ──────────────────────────────────────────────────────────────────
$hero_eyebrow = get_theme_mod('sci_hero_eyebrow', 'Now enrolling — Summer 2026 cohort');
$hero_heading = esc_html(get_theme_mod('sci_hero_heading', 'Invest with clarity, not noise.'));
$hero_accent  = esc_html(get_theme_mod('sci_hero_heading_accent', 'clarity'));
$hero_lead    = get_theme_mod('sci_hero_lead', 'Practical courses, research materials and tools for Indian equity investors — built from real portfolio experience, not recycled YouTube hype.');
$hero_cta1_text = get_theme_mod('sci_hero_cta1_text', 'Explore Courses');
$hero_cta1_url  = get_theme_mod('sci_hero_cta1_url', '') ?: sci_courses_url();
$hero_cta2_text = get_theme_mod('sci_hero_cta2_text', 'Browse Free Materials');
$hero_cta2_url  = get_theme_mod('sci_hero_cta2_url', '') ?: sci_materials_url();
$hero_trust = [
	get_theme_mod('sci_hero_trust_1', '★★★★★ 4.9/5 avg. rating'),
	get_theme_mod('sci_hero_trust_2', '10,000+ investors taught'),
	get_theme_mod('sci_hero_trust_3', 'Zero hype, 100% practical'),
];

// Highlight the accent word inside the heading (both values already escaped)
if ($hero_accent && strpos($hero_heading, $hero_accent) !== false) {
	$hero_heading_html = str_replace($hero_accent, '<span class="text-accent">' . $hero_accent . '</span>', $hero_heading);
} else {
	$hero_heading_html = $hero_heading;
}
?>

<header class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<div class="eyebrow"><span class="dot"></span> <?php echo esc_html($hero_eyebrow); ?></div>
			<h1><?php echo $hero_heading_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — already escaped above ?></h1>
			<p class="lead"><?php echo esc_html($hero_lead); ?></p>
			<div class="hero-actions">
				<a href="<?php echo esc_url($hero_cta1_url); ?>" class="btn btn-primary"><?php echo esc_html($hero_cta1_text); ?></a>
				<a href="<?php echo esc_url($hero_cta2_url); ?>" class="btn btn-ghost"><?php echo esc_html($hero_cta2_text); ?></a>
			</div>
			<div class="hero-trust">
				<?php foreach (array_filter($hero_trust) as $badge) : ?>
					<span><?php echo esc_html($badge); ?></span>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="hero-visual">
			<?php $sci_hero_image_id = (int) get_theme_mod('sci_hero_image'); ?>
			<?php if ($sci_hero_image_id) : ?>
			<div class="hero-card hero-card--main hero-card--custom-image glass">
				<?php echo wp_get_attachment_image($sci_hero_image_id, 'large', false, ['class' => 'hero-custom-image', 'alt' => get_bloginfo('name')]); ?>
			</div>
			<?php else : ?>
			<div class="hero-card hero-card--main glass">
				<div class="eyebrow"><span class="dot"></span> NIFTY 50</div>
				<svg class="mini-chart" viewBox="0 0 300 120" preserveAspectRatio="none">
					<defs>
						<linearGradient id="chartFill" x1="0" y1="0" x2="0" y2="1">
							<stop offset="0%" stop-color="#C9A24B" stop-opacity="0.35"/>
							<stop offset="100%" stop-color="#C9A24B" stop-opacity="0"/>
						</linearGradient>
					</defs>
					<path class="fill" fill="url(#chartFill)" d="M0,95 C25,90 40,60 65,68 C90,76 105,45 130,50 C155,55 175,25 200,30 C225,35 250,15 275,18 L300,10 L300,120 L0,120 Z"/>
					<path class="line" d="M0,95 C25,90 40,60 65,68 C90,76 105,45 130,50 C155,55 175,25 200,30 C225,35 250,15 275,18 L300,10"/>
				</svg>
				<div class="kpi-row">
					<div class="kpi"><b class="text-accent">+18.4%</b><span><?php esc_html_e('YTD Return', 'sco-investor'); ?></span></div>
					<div class="kpi"><b>24,812</b><span><?php esc_html_e('Index Level', 'sco-investor'); ?></span></div>
					<div class="kpi"><b style="color:var(--up)">▲ 1.2%</b><span><?php esc_html_e('Today', 'sco-investor'); ?></span></div>
				</div>
			</div>
			<?php endif; ?>
			<div class="hero-card hero-card--chip1 glass">
				<div class="chip-row">
					<span class="chip-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M7 17L17 7M9 7h8v8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					<span><b>TATAMOTORS</b><span class="sub">+2.41% <?php esc_html_e('today', 'sco-investor'); ?></span></span>
				</div>
			</div>
			<div class="hero-card hero-card--chip2 glass">
				<div class="chip-row">
					<span class="chip-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="9" r="5.5" stroke="currentColor" stroke-width="2"/><path d="M8.5 13.5L7 21l5-2.5L17 21l-1.5-7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					<span><b><?php esc_html_e('Course Completed', 'sco-investor'); ?></b><span class="sub"><?php esc_html_e('Fundamental Analysis', 'sco-investor'); ?></span></span>
				</div>
			</div>
		</div>
	</div>
</header>

<?php $sci_ticker_items = sci_show('sci_show_ticker') ? sci_get_ticker_items() : []; ?>
<?php if ($sci_ticker_items) : ?>
<div class="ticker-wrap">
	<?php get_template_part('template-parts/ticker', null, ['items' => $sci_ticker_items]); ?>
</div>
<?php endif; ?>

<?php if (sci_show('sci_show_stats')) : ?>
<?php
// ── Stats ─────────────────────────────────────────────────────────────────
$stats = [
	[
		'count'    => get_theme_mod('sci_stat_1_count',    '10000'),
		'suffix'   => get_theme_mod('sci_stat_1_suffix',   '+'),
		'decimals' => (int) get_theme_mod('sci_stat_1_decimals', 0),
		'label'    => get_theme_mod('sci_stat_1_label',    'Investors Taught'),
	],
	[
		'count'    => get_theme_mod('sci_stat_2_count',    '50'),
		'suffix'   => get_theme_mod('sci_stat_2_suffix',   '+'),
		'decimals' => (int) get_theme_mod('sci_stat_2_decimals', 0),
		'label'    => get_theme_mod('sci_stat_2_label',    'Stocks Tracked Live'),
	],
	[
		'count'    => get_theme_mod('sci_stat_3_count',    '12'),
		'suffix'   => get_theme_mod('sci_stat_3_suffix',   '+ yrs'),
		'decimals' => (int) get_theme_mod('sci_stat_3_decimals', 0),
		'label'    => get_theme_mod('sci_stat_3_label',    'Market Experience'),
	],
	[
		'count'    => get_theme_mod('sci_stat_4_count',    '4.9'),
		'suffix'   => get_theme_mod('sci_stat_4_suffix',   '/5'),
		'decimals' => (int) get_theme_mod('sci_stat_4_decimals', 1),
		'label'    => get_theme_mod('sci_stat_4_label',    'Average Rating'),
	],
];
?>
<section class="section section--tight">
	<div class="container">
		<div class="stats-row reveal-stagger">
			<?php foreach ($stats as $stat) : ?>
				<div class="stat-box glass">
					<div class="num"
						data-count="<?php echo esc_attr($stat['count']); ?>"
						data-suffix="<?php echo esc_attr($stat['suffix']); ?>"
						<?php if ($stat['decimals'] > 0) : ?>data-decimals="<?php echo esc_attr($stat['decimals']); ?>"<?php endif; ?>
					>0</div>
					<div class="label"><?php echo esc_html($stat['label']); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_features')) : ?>
<?php
// ── Features / Why Us ─────────────────────────────────────────────────────
$feat_eyebrow = get_theme_mod('sci_features_eyebrow', 'Why So Called Investor');
$feat_heading = get_theme_mod('sci_features_heading', 'Built different from typical "finfluencer" content');
$feat_desc    = get_theme_mod('sci_features_desc',    'No prediction calls, no paid pumps — just frameworks you can actually apply to your own portfolio.');
$features = [
	[
		'icon'  => '<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/></svg>',
		'title' => get_theme_mod('sci_feature_1_title', 'Practical Curriculum'),
		'desc'  => get_theme_mod('sci_feature_1_desc',  'Step-by-step frameworks for fundamental and technical analysis you can use the same day you learn them.'),
	],
	[
		'icon'  => '<svg viewBox="0 0 24 24" fill="none"><path d="M12 3L3 8l9 5 9-5-9-5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M3 13l9 5 9-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'title' => get_theme_mod('sci_feature_2_title', 'Real Portfolio Breakdowns'),
		'desc'  => get_theme_mod('sci_feature_2_desc',  'Live case studies and teardowns of actual Indian companies — the good, the bad, and the red flags.'),
	],
	[
		'icon'  => '<svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="17.5" cy="9" r="2.6" stroke="currentColor" stroke-width="1.8"/><path d="M15.5 20c0-2.6 1.8-4.8 4-5.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
		'title' => get_theme_mod('sci_feature_3_title', 'Lifetime Access & Community'),
		'desc'  => get_theme_mod('sci_feature_3_desc',  'One-time purchase, lifetime updates, plus a private community to ask questions and track progress together.'),
	],
];
?>
<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php echo esc_html($feat_eyebrow); ?></div>
			<h2><?php echo esc_html($feat_heading); ?></h2>
			<?php if ($feat_desc) : ?><p><?php echo esc_html($feat_desc); ?></p><?php endif; ?>
		</div>
		<div class="feature-grid reveal-stagger">
			<?php foreach ($features as $feat) : ?>
			<div class="feature-card glass">
				<div class="feature-icon"><?php echo $feat['icon']; // phpcs:ignore — trusted inline SVG ?></div>
				<h3><?php echo esc_html($feat['title']); ?></h3>
				<p><?php echo esc_html($feat['desc']); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_courses_home')) : ?>
<section class="section" id="courses">
	<div class="container">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php esc_html_e('Courses', 'sco-investor'); ?></div>
			<h2><?php esc_html_e('Learn the way real investors actually think', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('Sample of our catalog — full pricing and curriculum on the courses page.', 'sco-investor'); ?></p>
		</div>
		<div class="card-grid reveal-stagger">
			<?php
			$home_courses = new WP_Query([
				'post_type'           => 'course',
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			]);
			if ($home_courses->have_posts()) :
				while ($home_courses->have_posts()) : $home_courses->the_post();
					get_template_part('template-parts/card-course', null, ['cta' => 'view']);
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p class="muted"><?php esc_html_e('Courses coming soon — add your first Course from the WordPress admin.', 'sco-investor'); ?></p>
			<?php endif; ?>
		</div>
		<div class="center" style="margin-top:48px;"><a href="<?php echo esc_url(sci_courses_url()); ?>" class="btn btn-primary"><?php esc_html_e('View All Courses', 'sco-investor'); ?></a></div>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_materials_home')) : ?>
<section class="section" id="materials">
	<div class="container">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php esc_html_e('Free Resources', 'sco-investor'); ?></div>
			<h2><?php esc_html_e('Super Investor Materials Library', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('Templates, checklists and tools — free to download, no signup gimmicks.', 'sco-investor'); ?></p>
		</div>
		<div class="card-grid card-grid--2 reveal-stagger">
			<?php
			$home_materials = new WP_Query([
				'post_type'           => 'material',
				'posts_per_page'      => 4,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			]);
			if ($home_materials->have_posts()) :
				while ($home_materials->have_posts()) : $home_materials->the_post();
					get_template_part('template-parts/card-material');
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p class="muted"><?php esc_html_e('Materials coming soon — add your first Material from the WordPress admin.', 'sco-investor'); ?></p>
			<?php endif; ?>
		</div>
		<div class="center" style="margin-top:48px;"><a href="<?php echo esc_url(sci_materials_url()); ?>" class="btn btn-primary"><?php esc_html_e('Browse All Materials', 'sco-investor'); ?></a></div>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_testimonials')) : ?>
<?php
// ── Testimonials ──────────────────────────────────────────────────────────
$testi_eyebrow = get_theme_mod('sci_testi_eyebrow', 'Student Feedback');
$testi_heading = get_theme_mod('sci_testi_heading', 'What investors say');
$testi_desc    = get_theme_mod('sci_testi_desc', '');

$testi_query = new WP_Query([
	'post_type'      => 'testimonial',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'no_found_rows'  => true,
]);
$has_testimonials = $testi_query->have_posts();
?>
<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php echo esc_html($testi_eyebrow); ?></div>
			<h2><?php echo esc_html($testi_heading); ?></h2>
			<?php if ($testi_desc) : ?><p><?php echo esc_html($testi_desc); ?></p><?php endif; ?>
		</div>
		<div class="card-grid reveal-stagger">
			<?php if ($has_testimonials) : ?>
				<?php while ($testi_query->have_posts()) : $testi_query->the_post(); ?>
					<?php get_template_part('template-parts/card-testimonial'); ?>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="testi-card glass"><div class="testi-stars">★★★★★</div><p class="quote">&ldquo;<?php esc_html_e('Finally a course that explains the \'why\' behind every ratio instead of just throwing formulas at you.', 'sco-investor'); ?>&rdquo;</p><div class="testi-person"><span class="avatar-ring">RA</span><div><div class="name"><?php esc_html_e('Rohit A.', 'sco-investor'); ?></div><div class="role"><?php esc_html_e('Working Professional, Pune', 'sco-investor'); ?></div></div></div></div>
				<div class="testi-card glass"><div class="testi-stars">★★★★★</div><p class="quote">&ldquo;<?php esc_html_e('The portfolio breakdowns alone were worth the price. Practical, not theoretical.', 'sco-investor'); ?>&rdquo;</p><div class="testi-person"><span class="avatar-ring">SN</span><div><div class="name"><?php esc_html_e('Sneha N.', 'sco-investor'); ?></div><div class="role"><?php esc_html_e('First-time Investor', 'sco-investor'); ?></div></div></div></div>
				<div class="testi-card glass"><div class="testi-stars">★★★★★</div><p class="quote">&ldquo;<?php esc_html_e('Free materials are better than most paid courses out there. The screener tool is a nice bonus.', 'sco-investor'); ?>&rdquo;</p><div class="testi-person"><span class="avatar-ring">VK</span><div><div class="name"><?php esc_html_e('Vikram K.', 'sco-investor'); ?></div><div class="role"><?php esc_html_e('Part-time Trader', 'sco-investor'); ?></div></div></div></div>
			<?php endif; ?>
		</div>
		<?php if (!$has_testimonials && current_user_can('edit_posts')) : ?>
			<p class="muted" style="text-align:center;margin-top:16px;"><?php printf(esc_html__('These are placeholder testimonials. %s to replace them with real student reviews.', 'sco-investor'), '<a href="' . esc_url(admin_url('post-new.php?post_type=testimonial')) . '">' . esc_html__('Add real testimonials', 'sco-investor') . '</a>'); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_cta_band')) : ?>
<?php
// ── CTA Band ──────────────────────────────────────────────────────────────
$cta_heading   = get_theme_mod('sci_cta_heading',   'Ready to invest with confidence?');
$cta_desc      = get_theme_mod('sci_cta_desc',      'Join thousands of Indian investors learning to read businesses, not just stock tickers.');
$cta_btn1_text = get_theme_mod('sci_cta_btn1_text', 'Explore Courses');
$cta_btn1_url  = get_theme_mod('sci_cta_btn1_url',  '') ?: sci_courses_url();
$cta_btn2_text = get_theme_mod('sci_cta_btn2_text', 'Talk to Us');
$cta_btn2_url  = get_theme_mod('sci_cta_btn2_url',  '') ?: sci_contact_url();
?>
<section class="section section--tight">
	<div class="container">
		<div class="cta-band glass reveal">
			<h2><?php echo esc_html($cta_heading); ?></h2>
			<?php if ($cta_desc) : ?><p><?php echo esc_html($cta_desc); ?></p><?php endif; ?>
			<div class="hero-actions" style="justify-content:center;">
				<a href="<?php echo esc_url($cta_btn1_url); ?>" class="btn btn-primary"><?php echo esc_html($cta_btn1_text); ?></a>
				<a href="<?php echo esc_url($cta_btn2_url); ?>" class="btn btn-ghost"><?php echo esc_html($cta_btn2_text); ?></a>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
