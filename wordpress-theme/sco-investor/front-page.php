<?php
/**
 * The static homepage (Settings > Reading > "A static page" > Homepage).
 * Course/material preview cards are hardcoded for now — Phase 3 swaps them
 * for live WP_Query loops once the course/material post types exist.
 */
get_header();
?>

<header class="hero">
	<div class="container hero-grid">
		<div class="hero-copy">
			<div class="eyebrow"><span class="dot"></span> <?php esc_html_e('Now enrolling — Summer 2026 cohort', 'sco-investor'); ?></div>
			<h1><?php esc_html_e('Invest with', 'sco-investor'); ?> <span class="text-accent"><?php esc_html_e('clarity', 'sco-investor'); ?></span>,<br><?php esc_html_e('not noise.', 'sco-investor'); ?></h1>
			<p class="lead"><?php esc_html_e('Practical courses, research materials and tools for Indian equity investors — built from real portfolio experience, not recycled YouTube hype.', 'sco-investor'); ?></p>
			<div class="hero-actions">
				<a href="<?php echo esc_url(sci_courses_url()); ?>" class="btn btn-primary"><?php esc_html_e('Explore Courses', 'sco-investor'); ?></a>
				<a href="<?php echo esc_url(sci_materials_url()); ?>" class="btn btn-ghost"><?php esc_html_e('Browse Free Materials', 'sco-investor'); ?></a>
			</div>
			<div class="hero-trust">
				<span>★★★★★ <strong><?php esc_html_e('4.9/5', 'sco-investor'); ?></strong> <?php esc_html_e('avg. rating', 'sco-investor'); ?></span>
				<span><strong><?php esc_html_e('10,000+', 'sco-investor'); ?></strong> <?php esc_html_e('investors taught', 'sco-investor'); ?></span>
				<span><strong><?php esc_html_e('Zero', 'sco-investor'); ?></strong> <?php esc_html_e('hype, 100% practical', 'sco-investor'); ?></span>
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
<section class="section section--tight">
	<div class="container">
		<div class="stats-row reveal-stagger">
			<div class="stat-box glass"><div class="num" data-count="10000" data-suffix="+">0</div><div class="label"><?php esc_html_e('Investors Taught', 'sco-investor'); ?></div></div>
			<div class="stat-box glass"><div class="num" data-count="50" data-suffix="+">0</div><div class="label"><?php esc_html_e('Stocks Tracked Live', 'sco-investor'); ?></div></div>
			<div class="stat-box glass"><div class="num" data-count="12" data-suffix="+ yrs">0</div><div class="label"><?php esc_html_e('Market Experience', 'sco-investor'); ?></div></div>
			<div class="stat-box glass"><div class="num" data-count="4.9" data-decimals="1" data-suffix="/5">0</div><div class="label"><?php esc_html_e('Average Rating', 'sco-investor'); ?></div></div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_features')) : ?>
<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php esc_html_e('Why So Called Investor', 'sco-investor'); ?></div>
			<h2><?php esc_html_e('Built different from typical "finfluencer" content', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('No prediction calls, no paid pumps — just frameworks you can actually apply to your own portfolio.', 'sco-investor'); ?></p>
		</div>
		<div class="feature-grid reveal-stagger">
			<div class="feature-card glass">
				<div class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/></svg></div>
				<h3><?php esc_html_e('Practical Curriculum', 'sco-investor'); ?></h3>
				<p><?php esc_html_e('Step-by-step frameworks for fundamental and technical analysis you can use the same day you learn them.', 'sco-investor'); ?></p>
			</div>
			<div class="feature-card glass">
				<div class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3L3 8l9 5 9-5-9-5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M3 13l9 5 9-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
				<h3><?php esc_html_e('Real Portfolio Breakdowns', 'sco-investor'); ?></h3>
				<p><?php esc_html_e('Live case studies and teardowns of actual Indian companies — the good, the bad, and the red flags.', 'sco-investor'); ?></p>
			</div>
			<div class="feature-card glass">
				<div class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="17.5" cy="9" r="2.6" stroke="currentColor" stroke-width="1.8"/><path d="M15.5 20c0-2.6 1.8-4.8 4-5.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
				<h3><?php esc_html_e('Lifetime Access & Community', 'sco-investor'); ?></h3>
				<p><?php esc_html_e('One-time purchase, lifetime updates, plus a private community to ask questions and track progress together.', 'sco-investor'); ?></p>
			</div>
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
<section class="section">
	<div class="container">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php esc_html_e('Sample Feedback', 'sco-investor'); ?></div>
			<h2><?php esc_html_e('What investors say', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('Placeholder quotes — swap these for your real student testimonials before launch.', 'sco-investor'); ?></p>
		</div>
		<div class="card-grid reveal-stagger">
			<div class="testi-card glass">
				<div class="testi-stars">★★★★★</div>
				<p class="quote"><?php esc_html_e('"Finally a course that explains the \'why\' behind every ratio instead of just throwing formulas at you."', 'sco-investor'); ?></p>
				<div class="testi-person"><span class="avatar-ring">RA</span><div><div class="name"><?php esc_html_e('Rohit A.', 'sco-investor'); ?></div><div class="role"><?php esc_html_e('Working Professional, Pune', 'sco-investor'); ?></div></div></div>
			</div>
			<div class="testi-card glass">
				<div class="testi-stars">★★★★★</div>
				<p class="quote"><?php esc_html_e('"The portfolio breakdowns alone were worth the price. Practical, not theoretical."', 'sco-investor'); ?></p>
				<div class="testi-person"><span class="avatar-ring">SN</span><div><div class="name"><?php esc_html_e('Sneha N.', 'sco-investor'); ?></div><div class="role"><?php esc_html_e('First-time Investor', 'sco-investor'); ?></div></div></div>
			</div>
			<div class="testi-card glass">
				<div class="testi-stars">★★★★★</div>
				<p class="quote"><?php esc_html_e('"Free materials are better than most paid courses out there. The screener tool is a nice bonus."', 'sco-investor'); ?></p>
				<div class="testi-person"><span class="avatar-ring">VK</span><div><div class="name"><?php esc_html_e('Vikram K.', 'sco-investor'); ?></div><div class="role"><?php esc_html_e('Part-time Trader', 'sco-investor'); ?></div></div></div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if (sci_show('sci_show_cta_band')) : ?>
<section class="section section--tight">
	<div class="container">
		<div class="cta-band glass reveal">
			<h2><?php esc_html_e('Ready to invest with confidence?', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('Join thousands of Indian investors learning to read businesses, not just stock tickers.', 'sco-investor'); ?></p>
			<div class="hero-actions" style="justify-content:center;">
				<a href="<?php echo esc_url(sci_courses_url()); ?>" class="btn btn-primary"><?php esc_html_e('Explore Courses', 'sco-investor'); ?></a>
				<a href="<?php echo esc_url(sci_contact_url()); ?>" class="btn btn-ghost"><?php esc_html_e('Talk to Us', 'sco-investor'); ?></a>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
