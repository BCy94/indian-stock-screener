<?php
/**
 * Fallback for category/tag/date/author archives. The main blog
 * listing lives in home.php — this covers everything else.
 */
get_header();
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php echo esc_html(get_the_archive_title()); ?></span></div>
		<h1><?php echo wp_kses_post(get_the_archive_title()); ?></h1>
		<?php $desc = get_the_archive_description(); if ($desc) : ?>
			<p><?php echo wp_kses_post($desc); ?></p>
		<?php endif; ?>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
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
