<?php
/**
 * Materials archive ← materials.html. Filter pills are hardcoded to the
 * 3 buckets the static design ships (sheet/pdf/tool) rather than built
 * from sci_material_types(), since "excel" folds into "sheet" and "other"
 * intentionally has no pill of its own — see sci_material_filter_bucket().
 */

get_header();
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php esc_html_e('Materials', 'sco-investor'); ?></span></div>
		<div class="eyebrow center" style="margin:0 auto;"><span class="dot"></span> <?php esc_html_e('Free Forever', 'sco-investor'); ?></div>
		<h1><?php esc_html_e('Super Investor', 'sco-investor'); ?><br><span class="text-accent"><?php esc_html_e('Materials Library', 'sco-investor'); ?></span></h1>
		<p><?php esc_html_e('Templates, checklists, reports and tools — free to download, no signup gimmicks, no spam.', 'sco-investor'); ?></p>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<div class="filter-row">
			<button class="filter-pill active" data-filter="all"><?php esc_html_e('All', 'sco-investor'); ?></button>
			<button class="filter-pill" data-filter="sheet"><?php esc_html_e('Spreadsheets', 'sco-investor'); ?></button>
			<button class="filter-pill" data-filter="pdf"><?php esc_html_e('PDF Guides', 'sco-investor'); ?></button>
			<button class="filter-pill" data-filter="tool"><?php esc_html_e('Tools', 'sco-investor'); ?></button>
		</div>

		<?php if (have_posts()) : ?>
			<div class="card-grid card-grid--2 reveal-stagger">
				<?php while (have_posts()) : the_post(); ?>
					<?php get_template_part('template-parts/card-material'); ?>
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

<section class="section">
	<div class="container">
		<div class="cta-band glass reveal">
			<h2><?php esc_html_e('Want the deep-dive version?', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('Our paid courses go far beyond these free resources with full video walkthroughs and live examples.', 'sco-investor'); ?></p>
			<div class="hero-actions" style="justify-content:center;">
				<a href="<?php echo esc_url(sci_courses_url()); ?>" class="btn btn-primary"><?php esc_html_e('See Courses', 'sco-investor'); ?></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
