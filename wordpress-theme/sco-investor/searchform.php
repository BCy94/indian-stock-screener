<?php
/**
 * Overrides WordPress's default unstyled search form. Reuses the existing
 * newsletter-form flex layout (input + button) so it matches the design
 * system with zero new CSS.
 */
if (!defined('ABSPATH')) exit;
?>
<form role="search" method="get" class="newsletter-form" action="<?php echo esc_url(home_url('/')); ?>">
	<label class="screen-reader-text" for="sci-search-field"><?php esc_html_e('Search for:', 'sco-investor'); ?></label>
	<input type="search" id="sci-search-field" name="s" placeholder="<?php esc_attr_e('Search articles…', 'sco-investor'); ?>" value="<?php echo get_search_query(); ?>">
	<button type="submit" class="btn btn-primary btn-sm"><?php esc_html_e('Search', 'sco-investor'); ?></button>
</form>
