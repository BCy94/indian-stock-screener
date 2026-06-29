<?php
/**
 * The blog listing — WordPress's template for whichever Page is set as
 * "Posts page" in Settings > Reading (falls back to index.php otherwise).
 */
get_header();

$featured_post = null;
if (!is_paged() && have_posts()) {
	$featured_post = $wp_query->posts[0] ?? null;
}

$sci_form_flag = isset($_GET['sci_form']) ? sanitize_text_field(wp_unslash($_GET['sci_form'])) : '';
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php esc_html_e('Blog', 'sco-investor'); ?></span></div>
		<div class="eyebrow center" style="margin:0 auto;"><span class="dot"></span> <?php esc_html_e('Insights & Analysis', 'sco-investor'); ?></div>
		<h1><?php esc_html_e('The', 'sco-investor'); ?> <span class="text-accent"><?php esc_html_e('Journal', 'sco-investor'); ?></span></h1>
		<p><?php esc_html_e('Market notes, portfolio breakdowns and practical frameworks — written plainly, updated regularly.', 'sco-investor'); ?></p>
	</div>
</header>

<section class="section section--tight">
	<div class="container">

		<?php $categories = get_categories(['hide_empty' => true]); if ($categories) : ?>
		<div class="filter-row">
			<button class="filter-pill active" data-filter="all"><?php esc_html_e('All', 'sco-investor'); ?></button>
			<?php foreach ($categories as $cat) : ?>
				<button class="filter-pill" data-filter="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<?php if ($featured_post) :
			$post = $featured_post;
			setup_postdata($post);
			get_template_part('template-parts/content-featured');
		endif; ?>

		<?php
		$grid_count = $wp_query->post_count - ($featured_post ? 1 : 0);
		if ($grid_count > 0) :
		?>
			<div class="blog-grid reveal-stagger">
				<?php while (have_posts()) : the_post();
					if ($featured_post && get_the_ID() === $featured_post->ID) continue;
					get_template_part('template-parts/content');
				endwhile; ?>
			</div>

			<div class="pagination">
				<?php
				echo paginate_links([
					'prev_text' => __('← Previous', 'sco-investor'),
					'next_text' => __('Next →', 'sco-investor'),
				]);
				?>
			</div>
		<?php elseif (!$featured_post) : ?>
			<?php get_template_part('template-parts/content-none'); ?>
		<?php endif;
		wp_reset_postdata();
		?>

	</div>
</section>

<section class="section section--tight">
	<div class="container">
		<div class="cta-band glass reveal" id="newsletter-form-home">
			<h2><?php esc_html_e('Get new articles in your inbox', 'sco-investor'); ?></h2>
			<p><?php esc_html_e("One email when something's genuinely worth reading — no daily noise, no spam.", 'sco-investor'); ?></p>
			<form class="newsletter-form" data-sci-ajax-form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="max-width:420px;margin:0 auto;">
				<input type="hidden" name="action" value="sci_newsletter_submit">
				<input type="hidden" name="sci_form_anchor" value="newsletter-form-home">
				<?php wp_nonce_field('sci_newsletter_submit', 'sci_newsletter_nonce'); ?>
				<input type="email" name="sci_newsletter_email" placeholder="you@email.com" required>
				<button class="btn btn-primary" type="submit"><?php esc_html_e('Subscribe', 'sco-investor'); ?></button>
			</form>
			<?php if ('newsletter_success' === $sci_form_flag) : ?>
				<div class="form-msg show" style="text-align:center;"><?php esc_html_e("You're subscribed!", 'sco-investor'); ?></div>
			<?php elseif ('newsletter_error' === $sci_form_flag) : ?>
				<div class="form-msg show is-error" style="text-align:center;"><?php esc_html_e('Newsletter signups are not live yet — check back soon.', 'sco-investor'); ?></div>
			<?php else : ?>
				<div class="form-msg" style="text-align:center;"></div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
