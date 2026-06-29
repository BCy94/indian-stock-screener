<?php
/**
 * Single course. No static-HTML equivalent existed for this page — it
 * extends the same price/meta layout used in template-parts/card-course.php
 * into a full page, reusing the same meta fields, helpers and CSS classes
 * (.price, .course-meta) so it stays visually consistent with the cards.
 */
get_header();

while (have_posts()) : the_post();
	$course_id      = get_the_ID();
	$price          = get_post_meta($course_id, '_sci_price', true);
	$price_original = get_post_meta($course_id, '_sci_price_original', true);
	$weeks          = (int) get_post_meta($course_id, '_sci_duration_weeks', true);
	$lessons        = (int) get_post_meta($course_id, '_sci_lesson_count', true);
	$level_label    = sci_course_level_label($course_id);
?>

<header class="page-hero" style="padding-bottom:48px;">
	<div class="container">
		<div class="breadcrumb">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span>
			<a href="<?php echo esc_url(sci_courses_url()); ?>"><?php esc_html_e('Courses', 'sco-investor'); ?></a><span>/</span>
			<span><?php the_title(); ?></span>
		</div>
		<div class="eyebrow center" style="margin:0 auto;">
			<span class="dot"></span>
			<?php if ($level_label) : ?><?php echo esc_html($level_label); ?> · <?php endif; ?>
			<?php esc_html_e('Lifetime Access', 'sco-investor'); ?>
		</div>
		<h1 style="max-width:780px;margin:18px auto;"><?php the_title(); ?></h1>
		<?php if (get_the_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<div class="post-layout">

			<article>
				<div class="post-cover glass flex-center">
					<?php if (has_post_thumbnail()) : ?>
						<?php the_post_thumbnail('large'); ?>
					<?php else : ?>
						<svg viewBox="0 0 24 24" fill="none" style="color:var(--accent);"><circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="2"/><path d="M10 8.5l6 3.5-6 3.5v-7z" fill="currentColor"/></svg>
					<?php endif; ?>
				</div>

				<div class="post-content reveal">
					<?php if (sci_user_can_access($course_id)) : ?>
						<?php the_content(); ?>
					<?php else : ?>
						<?php if (get_the_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
						<div class="glass" style="padding:28px;text-align:center;">
							<p style="font-weight:600;margin-bottom:16px;"><?php esc_html_e('Enroll to unlock the full course content.', 'sco-investor'); ?></p>
							<?php echo sci_course_cta_html($course_id, 'btn btn-primary'); ?>
						</div>
					<?php endif; ?>
				</div>
			</article>

			<aside class="post-sidebar">
				<div class="widget glass" style="text-align:center;">
					<?php get_template_part('template-parts/price', null, ['price' => $price, 'price_original' => $price_original, 'center' => true, 'note' => true]); ?>
					<div style="margin-top:14px;"><?php echo sci_course_cta_html($course_id, 'btn btn-primary btn-block', true); ?></div>
					<div class="course-meta" style="justify-content:center;margin-top:18px;">
						<?php if ($weeks > 0) : ?>
							<span><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo esc_html(sprintf(_n('%d week', '%d weeks', $weeks, 'sco-investor'), $weeks)); ?></span>
						<?php endif; ?>
						<?php if ($lessons > 0) : ?>
							<span><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="9" r="5.5" stroke="currentColor" stroke-width="2"/><path d="M8.5 13.5L7 21l5-2.5L17 21l-1.5-7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo esc_html(sprintf(__('%d+ lessons', 'sco-investor'), $lessons)); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<?php
				$related = new WP_Query([
					'post_type'           => 'course',
					'posts_per_page'      => 3,
					'post__not_in'        => [$course_id],
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				]);
				if ($related->have_posts()) :
				?>
				<div class="widget glass">
					<h4><?php esc_html_e('More Courses', 'sco-investor'); ?></h4>
					<?php while ($related->have_posts()) : $related->the_post(); ?>
						<div class="widget-post-row">
							<div class="post-thumb">
								<?php if (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('thumbnail'); ?>
								<?php else : ?>
									<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M10 8.5l6 3.5-6 3.5v-7z" fill="currentColor"/></svg>
								<?php endif; ?>
							</div>
							<div><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
						</div>
					<?php endwhile; ?>
				</div>
				<?php
				endif;
				wp_reset_postdata();
				?>

				<?php if (sci_screener_url()) : ?>
				<div class="widget glass">
					<h4><?php esc_html_e('Free Tool', 'sco-investor'); ?></h4>
					<p class="muted" style="font-size:14px;margin-bottom:16px;"><?php esc_html_e('Practice what you learn here on real Nifty 50 stocks.', 'sco-investor'); ?></p>
					<a href="<?php echo esc_url(sci_screener_url()); ?>" class="btn btn-primary btn-block btn-sm"><?php esc_html_e('Open Stock Screener', 'sco-investor'); ?></a>
				</div>
				<?php endif; ?>
			</aside>

		</div>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
