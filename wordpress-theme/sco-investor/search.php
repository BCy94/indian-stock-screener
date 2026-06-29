<?php
/**
 * Search results. Reuses the same blog-grid/content template part as
 * archive.php and home.php so results look identical to the regular
 * post grid, with searchform.php shown above to refine the query.
 */
get_header();
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php esc_html_e('Search', 'sco-investor'); ?></span></div>
		<h1><?php esc_html_e('Search Results', 'sco-investor'); ?></h1>
		<p>
			<?php if (have_posts()) : ?>
				<?php printf(
					esc_html(_n('%1$d result for "%2$s"', '%1$d results for "%2$s"', $wp_query->found_posts, 'sco-investor')),
					(int) $wp_query->found_posts,
					esc_html(get_search_query())
				); ?>
			<?php else : ?>
				<?php printf(esc_html__('No results for "%s".', 'sco-investor'), esc_html(get_search_query())); ?>
			<?php endif; ?>
		</p>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<div style="max-width:420px;margin:0 auto 40px;">
			<?php get_search_form(); ?>
		</div>

		<?php if (have_posts()) : ?>
			<div class="blog-grid reveal-stagger">
				<?php while (have_posts()) : the_post(); ?>
					<?php get_template_part('template-parts/content'); ?>
				<?php endwhile; ?>
			</div>

			<div class="pagination">
				<?php
				echo paginate_links([
					'prev_text' => __('← Previous', 'sco-investor'),
					'next_text' => __('Next →', 'sco-investor'),
				]);
				?>
			</div>
		<?php else : ?>
			<?php get_template_part('template-parts/content-none'); ?>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
