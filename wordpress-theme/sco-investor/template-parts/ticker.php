<?php
/**
 * Renders .ticker-track from the items the caller already fetched via
 * sci_get_ticker_items() (so the caller can decide whether to print the
 * .ticker-wrap chrome at all when the list is empty). The list is doubled
 * because the CSS marquee animation (ticker-scroll, translateX(-50%))
 * needs two back-to-back copies to loop without a visible seam — the
 * same trick the old JS buildTicker() used.
 */

if (!defined('ABSPATH')) exit;

$sci_ticker_items = $args['items'] ?? [];
if (!$sci_ticker_items) return;

$sci_ticker_items = array_merge($sci_ticker_items, $sci_ticker_items);
?>
<div class="ticker-track">
	<?php foreach ($sci_ticker_items as $sci_ticker_item) :
		$sci_ticker_up = $sci_ticker_item['change'] >= 0;
	?>
		<div class="ticker-item">
			<span class="sym"><?php echo esc_html($sci_ticker_item['symbol']); ?></span>
			<span>₹<?php echo esc_html(number_format($sci_ticker_item['price'], 2)); ?></span>
			<span class="chg <?php echo $sci_ticker_up ? 'up' : 'down'; ?>"><?php echo $sci_ticker_up ? '▲' : '▼'; ?> <?php echo esc_html(number_format(abs($sci_ticker_item['change']), 2)); ?>%</span>
		</div>
	<?php endforeach; ?>
</div>
