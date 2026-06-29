<?php
/**
 * Empty-state shown when a Loop (search, blog, archive) finds no posts.
 */
?>
<div class="glass" style="padding:48px 32px;text-align:center;">
	<h3><?php esc_html_e('Nothing here yet', 'sco-investor'); ?></h3>
	<?php if (is_search()) : ?>
		<p class="muted"><?php printf(esc_html__('No results for "%s". Try a different search term.', 'sco-investor'), esc_html(get_search_query())); ?></p>
	<?php else : ?>
		<p class="muted"><?php esc_html_e('New articles are on the way — check back soon.', 'sco-investor'); ?></p>
		<a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-sm"><?php esc_html_e('Back to Home', 'sco-investor'); ?></a>
	<?php endif; ?>
</div>
