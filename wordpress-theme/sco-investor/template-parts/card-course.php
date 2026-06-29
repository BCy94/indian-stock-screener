<?php
/**
 * Course card. Pass $args['cta'] = 'enroll' for the disabled "Enroll
 * Soon" button (used on the archive — no live payments yet), or omit
 * it / pass 'view' for a link to the single course page (homepage
 * previews). Expects the course Loop to already be on the current post.
 */

if (!defined('ABSPATH')) exit;

$course_id = get_the_ID();
$cta       = $args['cta'] ?? 'view';

$price          = get_post_meta($course_id, '_sci_price', true);
$price_original = get_post_meta($course_id, '_sci_price_original', true);
$weeks          = (int) get_post_meta($course_id, '_sci_duration_weeks', true);
$lessons        = (int) get_post_meta($course_id, '_sci_lesson_count', true);
$badge          = get_post_meta($course_id, '_sci_badge', true);
$level_label    = sci_course_level_label($course_id);
$data_level     = sci_course_level_data_attr($course_id);
?>
<article class="course-card glass tilt" data-level="<?php echo esc_attr($data_level); ?>">
	<div class="course-thumb">
		<?php if (has_post_thumbnail($course_id)) : ?>
			<?php echo get_the_post_thumbnail($course_id, 'medium'); ?>
		<?php else : ?>
			<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="2"/><path d="M10 8.5l6 3.5-6 3.5v-7z" fill="currentColor"/></svg>
		<?php endif; ?>
		<?php if ($badge) : ?>
			<span class="badge badge-gold badge-float"><?php echo esc_html($badge); ?></span>
		<?php endif; ?>
	</div>
	<div class="course-body">
		<?php if ($level_label) : ?><span class="course-level"><?php echo esc_html($level_label); ?></span><?php endif; ?>
		<h3><a href="<?php echo esc_url(get_permalink($course_id)); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
		<div class="course-meta">
			<?php if ($weeks > 0) : ?>
				<span><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3.5 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo esc_html(sprintf(_n('%d week', '%d weeks', $weeks, 'sco-investor'), $weeks)); ?></span>
			<?php endif; ?>
			<?php if ($lessons > 0) : ?>
				<span><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="9" r="5.5" stroke="currentColor" stroke-width="2"/><path d="M8.5 13.5L7 21l5-2.5L17 21l-1.5-7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> <?php echo esc_html(sprintf(__('%d+ lessons', 'sco-investor'), $lessons)); ?></span>
			<?php endif; ?>
		</div>
		<div class="course-foot">
			<?php if ($price !== '') : ?>
				<span class="price"><?php if ($price_original !== '') : ?><span class="old"><?php echo esc_html(sci_format_inr($price_original)); ?></span><?php endif; ?><?php echo esc_html(sci_format_inr($price)); ?></span>
			<?php endif; ?>
			<?php if ($cta === 'enroll') : ?>
				<button class="btn btn-primary btn-sm" disabled title="<?php esc_attr_e('Payments coming soon', 'sco-investor'); ?>"><?php esc_html_e('Enroll Soon', 'sco-investor'); ?></button>
			<?php else : ?>
				<a href="<?php echo esc_url(get_permalink($course_id)); ?>" class="btn btn-ghost btn-sm"><?php esc_html_e('View', 'sco-investor'); ?></a>
			<?php endif; ?>
		</div>
	</div>
</article>
