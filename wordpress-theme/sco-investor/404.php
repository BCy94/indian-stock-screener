<?php
/**
 * 404 Not Found. No prototype equivalent — original on-brand design reusing
 * the existing page-hero/hero-actions primitives plus the searchform.php
 * box, so a lost visitor can search or jump back to a known-good page.
 */
get_header();
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php esc_html_e('404', 'sco-investor'); ?></span></div>
		<p class="has-bronze-color has-text-color has-xxx-large-font-size" style="font-family:var(--font-display);font-weight:700;line-height:1;margin:0;" aria-hidden="true">404</p>
		<h1><?php esc_html_e('Page Not Found', 'sco-investor'); ?></h1>
		<p><?php esc_html_e("The page you're looking for has moved or no longer exists. Try a search, or head back to somewhere useful below.", 'sco-investor'); ?></p>
	</div>
</header>

<section class="section section--tight">
	<div class="container">
		<div style="max-width:420px;margin:0 auto 40px;">
			<?php get_search_form(); ?>
		</div>

		<div class="hero-actions" style="justify-content:center;">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php esc_html_e('Back to Home', 'sco-investor'); ?></a>
			<a href="<?php echo esc_url(sci_blog_url()); ?>" class="btn btn-ghost"><?php esc_html_e('Read the Blog', 'sco-investor'); ?></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
