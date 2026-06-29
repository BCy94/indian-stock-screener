<?php
/**
 * Single blog post. comments_template() pulls in comments.php, which
 * brings its own on-brand styled markup instead of WordPress's unstyled
 * default.
 */
get_header();

while (have_posts()) : the_post();
	$cats     = get_the_category();
	$prev_post = get_previous_post();
	$next_post = get_next_post();
	$tags      = get_the_tags();
?>

<header class="page-hero" style="padding-bottom:48px;">
	<div class="container">
		<div class="breadcrumb">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span>
			<a href="<?php echo esc_url(sci_blog_url()); ?>"><?php esc_html_e('Blog', 'sco-investor'); ?></a><span>/</span>
			<span><?php echo $cats ? esc_html($cats[0]->name) : esc_html__('Blog', 'sco-investor'); ?></span>
		</div>
		<div class="eyebrow center" style="margin:0 auto;">
			<span class="dot"></span>
			<?php if ($cats) : ?><?php echo esc_html($cats[0]->name); ?> · <?php endif; ?>
			<?php printf(esc_html__('%d min read', 'sco-investor'), sci_reading_time()); ?>
		</div>
		<h1 style="max-width:780px;margin:18px auto;"><?php the_title(); ?></h1>
		<p><?php printf(esc_html__('Published %1$s · by %2$s', 'sco-investor'), esc_html(get_the_date()), esc_html(get_the_author())); ?></p>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<div class="post-layout<?php echo sci_show('sci_blog_show_sidebar') ? '' : ' post-layout--no-sidebar'; ?>">

			<article>
				<?php if (sci_show('sci_blog_show_featured_image')) : ?>
				<div class="post-cover glass flex-center">
					<?php if (has_post_thumbnail()) : ?>
						<?php the_post_thumbnail('large'); ?>
					<?php else : ?>
						<svg viewBox="0 0 24 24" fill="none" style="color:var(--accent);"><path d="M4 19h16M7 19V9M12 19V5M17 19v-7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="post-content reveal">
					<?php the_content(); ?>
				</div>

				<?php if ($tags) : ?>
				<div class="post-foot">
					<div class="tag-cloud">
						<?php foreach ($tags as $tag) : ?>
							<a href="<?php echo esc_url(get_tag_link($tag)); ?>">#<?php echo esc_html($tag->slug); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

				<?php if ($prev_post || $next_post) : ?>
				<div class="post-nav">
					<?php if ($prev_post) : ?>
						<a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="glass">
							<div class="dir">← <?php esc_html_e('Previous', 'sco-investor'); ?></div>
							<div><?php echo esc_html(get_the_title($prev_post)); ?></div>
						</a>
					<?php endif; ?>
					<?php if ($next_post) : ?>
						<a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="glass next">
							<div class="dir"><?php esc_html_e('Next', 'sco-investor'); ?> →</div>
							<div><?php echo esc_html(get_the_title($next_post)); ?></div>
						</a>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<?php comments_template(); ?>
			</article>

			<?php if (sci_show('sci_blog_show_sidebar')) : ?>
			<aside class="post-sidebar">
				<?php if (sci_show('sci_blog_show_author_box')) : ?>
				<div class="widget glass author-box">
					<span class="avatar-ring" style="width:52px;height:52px;font-size:17px;"><?php echo esc_html(sci_initials(get_the_author())); ?></span>
					<div>
						<div style="font-weight:700;"><?php the_author(); ?></div>
						<p><?php echo esc_html(get_the_author_meta('description') ?: __('Honest, practical investing education for Indian markets.', 'sco-investor')); ?></p>
					</div>
				</div>
				<?php endif; ?>

				<?php if (is_active_sidebar('sci-blog-sidebar')) :
					dynamic_sidebar('sci-blog-sidebar');
				else: ?>

					<?php
					$popular = new WP_Query([
						'post_type'           => 'post',
						'posts_per_page'      => 3,
						'post__not_in'        => [get_the_ID()],
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					]);
					if ($popular->have_posts()) :
					?>
					<div class="widget glass">
						<h4><?php esc_html_e('Popular Reading', 'sco-investor'); ?></h4>
						<?php while ($popular->have_posts()) : $popular->the_post(); ?>
							<div class="widget-post-row">
								<div class="post-thumb">
									<?php if (has_post_thumbnail()) : ?>
										<?php the_post_thumbnail('thumbnail'); ?>
									<?php else : ?>
										<svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 3a9 9 0 010 18" stroke="currentColor" stroke-width="2"/></svg>
									<?php endif; ?>
								</div>
								<div><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br><span><?php echo esc_html(get_the_date()); ?></span></div>
							</div>
						<?php endwhile; ?>
					</div>
					<?php
					endif;
					wp_reset_postdata();
					?>

					<?php $sidebar_cats = get_categories(['hide_empty' => true]); if ($sidebar_cats) : ?>
					<div class="widget glass">
						<h4><?php esc_html_e('Categories', 'sco-investor'); ?></h4>
						<div class="tag-cloud">
							<?php foreach ($sidebar_cats as $cat) : ?>
								<a href="<?php echo esc_url(get_category_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if (sci_screener_url()) : ?>
					<div class="widget glass">
						<h4><?php esc_html_e('Free Tool', 'sco-investor'); ?></h4>
						<p class="muted" style="font-size:14px;margin-bottom:16px;"><?php esc_html_e('Run the numbers from this article on real Nifty 50 stocks.', 'sco-investor'); ?></p>
						<a href="<?php echo esc_url(sci_screener_url()); ?>" class="btn btn-primary btn-block btn-sm"><?php esc_html_e('Open Stock Screener', 'sco-investor'); ?></a>
					</div>
					<?php endif; ?>

				<?php endif; ?>
			</aside>
			<?php endif; ?>

		</div>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
