<?php
/**
 * Calculator block render. The markup/data-attribute contract here
 * (data-calculator, data-calc-mode, data-calc-input, data-calc-slider,
 * data-calc-out) must exactly match what view.js queries — this is a
 * straight port of website/calculator.html's calculator markup, with the
 * four starting values now driven by block attributes instead of hardcoded.
 */

if (!defined('ABSPATH')) exit;

$mode           = $attributes['defaultMode'] ?? 'sip';
$amount         = $attributes['defaultAmount'] ?? 10000;
$lumpsum_amount = $attributes['defaultLumpsum'] ?? 100000;
$rate           = $attributes['defaultRate'] ?? 12;
$years          = $attributes['defaultYears'] ?? 10;
$is_sip         = $mode !== 'lumpsum';
$gradient_id    = wp_unique_id('calcChartFill-');

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'calc-block']);
?>
<div <?php echo $wrapper_attributes; ?> data-calculator>
	<div class="center" style="margin-bottom:32px;">
		<div class="calc-tabs">
			<button class="<?php echo $is_sip ? 'active' : ''; ?>" data-calc-mode="sip"><?php esc_html_e('SIP', 'sco-investor'); ?></button>
			<button class="<?php echo $is_sip ? '' : 'active'; ?>" data-calc-mode="lumpsum"><?php esc_html_e('Lumpsum', 'sco-investor'); ?></button>
		</div>
	</div>
	<div class="calc-layout">

		<div class="calc-panel glass">
			<div class="calc-field" data-calc-mode-field="sip" <?php echo $is_sip ? '' : 'hidden'; ?>>
				<div class="calc-field-head">
					<label><?php esc_html_e('Monthly Investment', 'sco-investor'); ?></label>
					<span>₹<input type="number" class="calc-val-input" data-calc-input="amount" value="<?php echo esc_attr($amount); ?>"></span>
				</div>
				<input type="range" class="calc-slider" data-calc-slider="amount" min="500" max="100000" step="500" value="<?php echo esc_attr($amount); ?>">
			</div>

			<div class="calc-field" data-calc-mode-field="lumpsum" <?php echo $is_sip ? 'hidden' : ''; ?>>
				<div class="calc-field-head">
					<label><?php esc_html_e('Lumpsum Amount', 'sco-investor'); ?></label>
					<span>₹<input type="number" class="calc-val-input" data-calc-input="lumpsum" value="<?php echo esc_attr($lumpsum_amount); ?>"></span>
				</div>
				<input type="range" class="calc-slider" data-calc-slider="lumpsum" min="5000" max="5000000" step="5000" value="<?php echo esc_attr($lumpsum_amount); ?>">
			</div>

			<div class="calc-field">
				<div class="calc-field-head">
					<label><?php esc_html_e('Expected Return (p.a.)', 'sco-investor'); ?></label>
					<span><input type="number" class="calc-val-input" data-calc-input="rate" value="<?php echo esc_attr($rate); ?>">%</span>
				</div>
				<input type="range" class="calc-slider" data-calc-slider="rate" min="1" max="30" step="0.5" value="<?php echo esc_attr($rate); ?>">
			</div>

			<div class="calc-field">
				<div class="calc-field-head">
					<label><?php esc_html_e('Time Period', 'sco-investor'); ?></label>
					<span><input type="number" class="calc-val-input" data-calc-input="years" value="<?php echo esc_attr($years); ?>"><?php esc_html_e('yrs', 'sco-investor'); ?></span>
				</div>
				<input type="range" class="calc-slider" data-calc-slider="years" min="1" max="35" step="1" value="<?php echo esc_attr($years); ?>">
			</div>
		</div>

		<div class="calc-results glass">
			<div class="calc-total">
				<div class="label"><?php esc_html_e('Total Value', 'sco-investor'); ?></div>
				<div class="amount" data-calc-out="total">₹0</div>
			</div>
			<div class="calc-breakdown">
				<div class="calc-row">
					<span><span class="swatch invested"></span> <?php esc_html_e('Invested Amount', 'sco-investor'); ?></span>
					<b data-calc-out="invested">₹0</b>
				</div>
				<div class="calc-row">
					<span><span class="swatch returns"></span> <?php esc_html_e('Est. Returns', 'sco-investor'); ?></span>
					<b data-calc-out="returns">₹0</b>
				</div>
				<div class="calc-bar"><span data-calc-out="bar" style="width:0%"></span></div>
			</div>
			<svg class="calc-chart" data-calc-out="chart" viewBox="0 0 300 140" preserveAspectRatio="none">
				<defs>
					<linearGradient id="<?php echo esc_attr($gradient_id); ?>" x1="0" y1="0" x2="0" y2="1">
						<stop offset="0%" stop-color="#C9A24B" stop-opacity="0.35"/>
						<stop offset="100%" stop-color="#C9A24B" stop-opacity="0"/>
					</linearGradient>
				</defs>
				<path class="fill" fill="url(#<?php echo esc_attr($gradient_id); ?>)" d=""/>
				<path class="line" d=""/>
			</svg>
		</div>

	</div>
	<p class="muted" style="text-align:center;font-size:13px;margin-top:20px;"><?php esc_html_e('Assumes a constant annual rate of return, compounded monthly. Actual market returns vary — this is an educational projection, not a guarantee.', 'sco-investor'); ?></p>
</div>
