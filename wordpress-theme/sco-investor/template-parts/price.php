<?php
/**
 * Shared price display — course cards, the single-course sidebar, material
 * cards and single-material all render through this one partial so the
 * "looks like other shopping sites" treatment (discount badge, struck-
 * through original price) only has to be built once. Pass $args['price']
 * and $args['price_original'] (raw post-meta strings, '' if unset);
 * $args['center'] => true for the single-page sidebar layout, and
 * $args['note'] => true to add the one-time/tax caption under the price.
 * Prints nothing at all when no price is set, same as before this partial
 * existed.
 */

if (!defined('ABSPATH')) exit;

$price          = $args['price'] ?? '';
$price_original = $args['price_original'] ?? '';
$center         = !empty($args['center']);
$note           = !empty($args['note']);

if ($price === '') return;

$has_old  = $price_original !== '' && (float) $price_original > (float) $price;
$discount = $has_old ? sci_discount_percent($price, $price_original) : 0;
?>
<div class="price-block<?php echo $center ? ' price-block--center' : ''; ?>">
	<div class="price-row">
		<span class="price"><?php if ($has_old) : ?><span class="old"><?php echo esc_html(sci_format_inr($price_original)); ?></span><?php endif; ?><?php echo esc_html(sci_format_inr($price)); ?></span>
		<?php if ($discount > 0) : ?><span class="price-discount"><?php echo esc_html(sprintf(__('%d%% off', 'sco-investor'), $discount)); ?></span><?php endif; ?>
	</div>
	<?php if ($note) : ?><p class="price-note"><?php esc_html_e('One-time payment · inclusive of all taxes', 'sco-investor'); ?></p><?php endif; ?>
</div>
