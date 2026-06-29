<?php
/**
 * Material card. Expects the material Loop to already be on the current
 * post. The icon/badge/CTA styling all key off the Material Type field
 * rather than a featured image — the static design always shows a type
 * icon here, never a photo, so there's no thumbnail fallback to handle.
 */

if (!defined('ABSPATH')) exit;

$material_id = get_the_ID();
$type        = get_post_meta($material_id, '_sci_material_type', true) ?: 'other';
$link        = sci_material_link($material_id);
$is_tool     = $type === 'tool';
?>
<article class="material-card glass" data-level="<?php echo esc_attr(sci_material_filter_bucket($type)); ?>">
	<div class="material-icon"><?php echo sci_material_icon($type); ?></div>
	<div>
		<h3><a href="<?php echo esc_url(get_permalink($material_id)); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
		<div class="material-foot">
			<span class="badge<?php echo $is_tool ? ' badge-gold' : ''; ?>"><?php echo esc_html(sci_material_badge($material_id)); ?></span>
			<?php if ($link) : ?>
				<a href="<?php echo esc_url($link); ?>" class="btn <?php echo $is_tool ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">
					<?php if (!$is_tool) : ?>
						<svg viewBox="0 0 24 24" fill="none" style="width:14px;height:14px;"><path d="M12 4v11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 19h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
					<?php endif; ?>
					<?php echo esc_html(sci_material_cta_label($type)); ?>
				</a>
			<?php else : ?>
				<button class="btn btn-ghost btn-sm" disabled><?php echo esc_html(sci_material_cta_label($type)); ?></button>
			<?php endif; ?>
		</div>
	</div>
</article>
