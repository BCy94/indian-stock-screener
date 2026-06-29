<?php
/**
 * Courses archive ← courses.html. Filter pills are client-side (existing
 * main.js data-level/data-filter mechanic) and built from whichever
 * course_level terms actually exist, so a level the owner adds later shows
 * up here with zero template changes. This archive has no editable
 * post_content to attach a real sci/accordion instance to, so the FAQ
 * copy stays hardcoded here — but it's rendered through sci_render_accordion()
 * (sci/accordion + sci/accordion-item via render_block()) rather than
 * duplicated as raw HTML, so it gets the same markup and toggle behaviour
 * as every owner-editable accordion on the site.
 */

get_header();

$levels       = get_terms(['taxonomy' => 'course_level', 'hide_empty' => false]);
$course_count = wp_count_posts('course')->publish;
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php esc_html_e('Courses', 'sco-investor'); ?></span></div>
		<div class="eyebrow center" style="margin:0 auto;"><span class="dot"></span> <?php echo esc_html(sprintf(_n('%d Course · Lifetime Access', '%d Courses · Lifetime Access', $course_count, 'sco-investor'), $course_count)); ?></div>
		<h1><?php esc_html_e('Courses that turn theory', 'sco-investor'); ?><br><?php esc_html_e('into', 'sco-investor'); ?> <span class="text-accent"><?php esc_html_e('portfolio decisions', 'sco-investor'); ?></span></h1>
		<p><?php esc_html_e('Self-paced video courses with downloadable worksheets, quizzes and lifetime updates. One-time payment, no recurring fees.', 'sco-investor'); ?></p>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<?php if (!is_wp_error($levels) && $levels) : ?>
			<div class="filter-row">
				<button class="filter-pill active" data-filter="all"><?php esc_html_e('All Courses', 'sco-investor'); ?></button>
				<?php foreach ($levels as $level) : ?>
					<button class="filter-pill" data-filter="<?php echo esc_attr($level->slug); ?>"><?php echo esc_html($level->name); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (have_posts()) : ?>
			<div class="card-grid reveal-stagger">
				<?php while (have_posts()) : the_post(); ?>
					<?php get_template_part('template-parts/card-course', null, ['cta' => 'enroll']); ?>
				<?php endwhile; ?>
			</div>

			<div class="pagination">
				<?php
				echo paginate_links([
					'prev_text' => __('← Previous', 'sco-investor'),
					'next_text' => __('Next →', 'sco-investor'),
				]);
				?>
			</div>
		<?php else : ?>
			<?php get_template_part('template-parts/content-none'); ?>
		<?php endif; ?>
	</div>
</section>

<!-- FAQ -->
<section class="section">
	<div class="container" style="max-width:760px;">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> <?php esc_html_e('FAQ', 'sco-investor'); ?></div>
			<h2><?php esc_html_e('Common questions', 'sco-investor'); ?></h2>
		</div>
		<?php
		echo sci_render_accordion([
			[
				'question' => __('Do I get lifetime access?', 'sco-investor'),
				'answer'   => __('Yes — every course is a one-time purchase with lifetime access to all future updates and added lessons.', 'sco-investor'),
				'open'     => true,
			],
			sci_has_woocommerce() ? [
				'question' => __('How do payments work?', 'sco-investor'),
				'answer'   => __('Checkout is secure and handled by WooCommerce — pay by card, UPI or netbanking depending on what your payment provider supports. Access unlocks automatically the moment your payment is confirmed.', 'sco-investor'),
				'open'     => false,
			] : [
				'question' => __('When will online payments be enabled?', 'sco-investor'),
				'answer'   => __("We're finalizing secure checkout with automatic payment verification. Enrollment will open as soon as it's live — join the newsletter to get notified first.", 'sco-investor'),
				'open'     => false,
			],
			[
				'question' => __('Are these courses suitable for complete beginners?', 'sco-investor'),
				'answer'   => __('Yes. Each course lists its level clearly, and beginner courses start from the absolute basics with no prior market knowledge assumed.', 'sco-investor'),
				'open'     => false,
			],
			[
				'question' => __('Is this investment advice?', 'sco-investor'),
				'answer'   => __('No. All content is for educational purposes only. Please do your own research or consult a SEBI-registered advisor before investing.', 'sco-investor'),
				'open'     => false,
			],
		]);
		?>
	</div>
</section>

<section class="section section--tight">
	<div class="container">
		<div class="cta-band glass reveal">
			<h2><?php esc_html_e('Not sure which course fits you?', 'sco-investor'); ?></h2>
			<p><?php esc_html_e("Tell us your experience level and goals — we'll point you to the right starting course.", 'sco-investor'); ?></p>
			<div class="hero-actions" style="justify-content:center;">
				<a href="<?php echo esc_url(sci_contact_url()); ?>" class="btn btn-primary"><?php esc_html_e('Ask Us', 'sco-investor'); ?></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
