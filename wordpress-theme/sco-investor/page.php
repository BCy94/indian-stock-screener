<?php
/**
 * Generic Page template — any Page without a more specific Page Template
 * assigned (Contact and Calculator have their own). Plain content pages
 * (About, Terms, Disclaimer, Privacy Policy, etc.) render here.
 */
get_header();
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php the_title(); ?></span></div>
		<h1><?php the_title(); ?></h1>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<div style="max-width:760px;margin:0 auto;">
			<?php while (have_posts()) : the_post(); ?>
				<div class="post-content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
