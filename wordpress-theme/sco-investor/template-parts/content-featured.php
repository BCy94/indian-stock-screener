<?php
/**
 * Large featured-post card atop the blog grid (the sticky post,
 * or the latest post when nothing is pinned).
 */
?>
<article <?php post_class('blog-featured glass'); ?>>
	<div class="post-thumb">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('medium_large'); ?>
		<?php else : ?>
			<svg viewBox="0 0 24 24" fill="none"><path d="M4 19h16M7 19V9M12 19V5M17 19v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		<?php endif; ?>
	</div>
	<div class="post-body">
		<div class="post-meta">
			<?php $cats = get_the_category(); if ($cats) : ?><span class="tag"><?php echo esc_html($cats[0]->name); ?></span><span>·</span><?php endif; ?>
			<span><?php echo esc_html(get_the_date()); ?></span>
			<span>·</span>
			<span><?php printf(esc_html__('%d min read', 'sco-investor'), sci_reading_time()); ?></span>
		</div>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 32)); ?></p>
		<a href="<?php the_permalink(); ?>" class="post-readmore"><?php esc_html_e('Read the full piece', 'sco-investor'); ?> <svg viewBox="0 0 24 24" fill="none" style="width:14px;height:14px;"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
	</div>
</article>
