<?php
/**
 * Template Name: Calculator Page
 *
 * Assign this to a Page in Editor > Page Attributes. The template only
 * provides the hero header and closing CTA band — the calculator widget
 * and FAQ are the sci/calculator and sci/accordion blocks, placed in the
 * Page content itself, so the owner can edit/reorder/restyle them with no
 * code (see the Calculator page seeded during setup for an example).
 */
get_header();
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php the_title(); ?></span></div>
		<div class="eyebrow center" style="margin:0 auto;"><span class="dot"></span> <?php esc_html_e('Free Tool', 'sco-investor'); ?></div>
		<h1 style="max-width:680px;margin:18px auto;"><?php the_title(); ?></h1>
		<?php if (get_the_excerpt()) : ?><p style="max-width:560px;margin:0 auto;"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
	</div>
</header>

<section class="section section--tight">
	<div class="container" style="max-width:980px;">
		<?php while (have_posts()) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
	</div>
</section>

<section class="section section--tight">
	<div class="container">
		<div class="cta-band glass reveal">
			<h2><?php esc_html_e('Want to know how to act on these numbers?', 'sco-investor'); ?></h2>
			<p><?php esc_html_e('The calculator tells you where compounding can take you. Our courses teach you how to pick what to actually put in it.', 'sco-investor'); ?></p>
			<div class="hero-actions" style="justify-content:center;">
				<a href="<?php echo esc_url(sci_courses_url()); ?>" class="btn btn-primary"><?php esc_html_e('Browse Courses', 'sco-investor'); ?></a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
