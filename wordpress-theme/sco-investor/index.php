<?php
/**
 * Universal fallback template, required by WordPress. Every more
 * specific template (home.php, single.php, page.php, etc.) takes
 * priority over this one — it only renders when nothing else matches.
 */
get_header();
?>

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
