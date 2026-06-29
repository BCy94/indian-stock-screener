<?php
/**
 * Material card. Expects the material Loop to already be on the current
 * post. The icon/badge/CTA styling all key off the Material Type field;
 * a type icon is the default visual, but uploading a Featured Image for
 * an individual material (Customizer-free, just the normal post editor)
 * swaps the icon for that photo, same opt-in pattern as course cards.
 */

if (!defined('ABSPATH')) exit;

$material_id     = get_the_ID();
$type            = get_post_meta($material_id, '_sci_material_type', true) ?: 'other';
$link            = sci_material_link($material_id);
$is_tool         = $type === 'tool';
$has_thumb       = has_post_thumbnail($material_id);
$price           = get_post_meta($material_id, '_sci_price', true);
$price_original  = get_post_meta($material_id, '_sci_price_original', true);
?>
<article class="material-card glass" data-level="<?php echo esc_attr(sci_material_filter_bucket($type)); ?>">
	<div class="material-icon<?php echo $has_thumb ? ' material-icon--photo' : ''; ?>">
		<?php if ($has_thumb) : ?>
			<?php echo get_the_post_thumbnail($material_id, 'thumbnail'); ?>
		<?php else : ?>
			<?php echo sci_material_icon($type); ?>
		<?php endif; ?>
	</div>
	<div>
		<h3><a href="<?php echo esc_url(get_permalink($material_id)); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
		<?php get_template_part('template-parts/price', null, ['price' => $price, 'price_original' => $price_original]); ?>
		<div class="material-foot">
			<span class="badge<?php echo $is_tool ? ' badge-gold' : ''; ?>"><?php echo esc_html(sci_material_badge($material_id)); ?></span>
			<?php echo sci_material_cta_html($material_id, $link, $type, 'btn-sm', true); ?>
		</div>
	</div>
</article>
