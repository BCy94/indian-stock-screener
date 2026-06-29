<?php
/**
 * Accordion Item (child) render. question/answer are plain block attributes
 * (not markup-sourced), so inc/schema.php's FAQPage extraction can read the
 * exact same values via parse_blocks() with zero markup-scraping. The answer
 * supports basic rich text (bold/links) so wp_kses_post() is used instead of
 * esc_html().
 */

if (!defined('ABSPATH')) exit;

$question = $attributes['question'] ?? '';
$answer   = $attributes['answer'] ?? '';
$is_open  = !empty($attributes['openByDefault']);

$item_class = 'accordion-item' . ($is_open ? ' open' : '');
$wrapper_attributes = get_block_wrapper_attributes(['class' => $item_class]);
$body_id = wp_unique_id('sci-accordion-body-');
?>
<div <?php echo $wrapper_attributes; ?>>
	<button class="accordion-head" type="button" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($body_id); ?>">
		<?php echo esc_html($question); ?> <span class="plus">+</span>
	</button>
	<div class="accordion-body" id="<?php echo esc_attr($body_id); ?>" aria-hidden="<?php echo $is_open ? 'false' : 'true'; ?>">
		<p><?php echo wp_kses_post($answer); ?></p>
	</div>
</div>
