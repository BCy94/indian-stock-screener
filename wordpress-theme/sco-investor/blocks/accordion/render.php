<?php
/**
 * Accordion (parent) render. $content is the already-rendered InnerBlocks
 * markup (one sci/accordion-item per Q&A) supplied by edit.js's save()
 * via <InnerBlocks.Content />. This wrapper only adds the data-accordion
 * hook view.js queries for, plus the .reveal scroll-in animation already
 * used sitewide.
 */

if (!defined('ABSPATH')) exit;

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'reveal']);
?>
<div <?php echo $wrapper_attributes; ?> data-accordion>
	<?php echo $content; ?>
</div>
