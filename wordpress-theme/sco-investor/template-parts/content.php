<?php
/**
 * Post summary card used in the blog, archive and search loops.
 */
$cat_slugs = wp_get_post_terms(get_the_ID(), 'category', ['fields' => 'slugs']);
$data_level = (!is_wp_error($cat_slugs) && $cat_slugs) ? implode(' ', $cat_slugs) : '';
?>
<article <?php post_class('post-card glass'); ?> <?php if ($data_level) : ?>data-level="<?php echo esc_attr($data_level); ?>"<?php endif; ?>>
	<div class="post-thumb">
		<?php if (has_post_thumbnail()) : ?>
			<?php the_post_thumbnail('medium'); ?>
		<?php else : ?>
			<svg viewBox="0 0 24 24" fill="none"><path d="M3 17l5-5 4 4 8-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
		<?php endif; ?>
	</div>
	<div class="post-body">
		<div class="post-meta">
			<?php $cats = get_the_category(); if ($cats) : ?><span class="tag"><?php echo esc_html($cats[0]->name); ?></span><span>·</span><?php endif; ?>
			<span><?php echo esc_html(get_the_date()); ?></span>
		</div>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), get_theme_mod('sci_blog_excerpt_length', 22))); ?></p>
		<a href="<?php the_permalink(); ?>" class="post-readmore"><?php esc_html_e('Read more', 'sco-investor'); ?> →</a>
	</div>
</article>
