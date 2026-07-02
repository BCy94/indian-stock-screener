<?php
/**
 * Template Name: Contact Page
 *
 * Assign this to any Page in Editor > Page Attributes to render the
 * Contact layout. The message form posts to inc/contact-form.php via
 * admin-post.php.
 */
get_header();

$sci_form_flag = isset($_GET['sci_form']) ? sanitize_text_field(wp_unslash($_GET['sci_form'])) : '';
?>

<header class="page-hero">
	<div class="container">
		<div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span><span><?php the_title(); ?></span></div>
		<div class="eyebrow center" style="margin:0 auto;"><span class="dot"></span> <?php esc_html_e('We reply within 24 hours', 'sco-investor'); ?></div>
		<h1><?php esc_html_e("Let's", 'sco-investor'); ?> <span class="text-accent"><?php esc_html_e('talk', 'sco-investor'); ?></span></h1>
		<p><?php esc_html_e("Questions about a course, a partnership idea, or feedback on the site — we'd love to hear from you.", 'sco-investor'); ?></p>
	</div>
</header>

<?php
/*
 * Anything the owner writes or builds (classic/block editor or Elementor)
 * in this Page's own content shows here, above the contact form — the
 * form and FAQ below stay fixed since they're working functionality, not
 * placeholder content, but this section is free to add to/rearrange.
 */
while (have_posts()) : the_post();
	if (trim(wp_strip_all_tags(get_the_content()))) :
		?>
		<section class="section section--tight">
			<div class="container" style="max-width:760px;">
				<div class="post-content"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endif;
endwhile;
?>

<section class="section section--tight">
	<div class="container contact-grid">

		<div class="reveal">
			<div class="contact-info-item glass" style="padding:24px; border-radius:var(--radius-lg); border:1px solid var(--surface-border); margin-bottom:16px;">
				<span class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3.5 6.5L12 13l8.5-6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
				<div><h4><?php esc_html_e('Email', 'sco-investor'); ?></h4><p>hello@socalledinvestor.com</p></div>
			</div>
			<div class="contact-info-item glass" style="padding:24px; border-radius:var(--radius-lg); border:1px solid var(--surface-border); margin-bottom:16px;">
				<span class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M21 12a8 8 0 10-3.5 6.6L21 20l-1-3.6A8 8 0 0021 12z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg></span>
				<div><h4><?php esc_html_e('Community', 'sco-investor'); ?></h4><p><?php esc_html_e('Telegram & Discord links coming soon', 'sco-investor'); ?></p></div>
			</div>
			<div class="contact-info-item glass" style="padding:24px; border-radius:var(--radius-lg); border:1px solid var(--surface-border);">
				<span class="feature-icon"><svg viewBox="0 0 24 24" fill="none"><path d="M12 21s7-6.6 7-11.5A7 7 0 005 9.5C5 14.4 12 21 12 21z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="2"/></svg></span>
				<div><h4><?php esc_html_e('Based in', 'sco-investor'); ?></h4><p><?php esc_html_e('India · Serving investors nationwide', 'sco-investor'); ?></p></div>
			</div>
		</div>

		<div class="glass reveal" style="padding:40px;" id="contact-form">
			<h3 style="margin-bottom:6px;"><?php esc_html_e('Send us a message', 'sco-investor'); ?></h3>
			<p class="muted" style="margin-bottom:24px; font-size:14.5px;"><?php esc_html_e("We read every message and reply within 24 hours.", 'sco-investor'); ?></p>
			<form data-sci-ajax-form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
				<input type="hidden" name="action" value="sci_contact_submit">
				<?php wp_nonce_field('sci_contact_submit', 'sci_contact_nonce'); ?>
				<div class="form-grid">
					<div class="form-field">
						<label for="name"><?php esc_html_e('Full Name', 'sco-investor'); ?></label>
						<input type="text" id="name" name="sci_name" placeholder="<?php esc_attr_e('Your name', 'sco-investor'); ?>" required>
					</div>
					<div class="form-field">
						<label for="email"><?php esc_html_e('Email Address', 'sco-investor'); ?></label>
						<input type="email" id="email" name="sci_email" placeholder="you@email.com" required>
					</div>
					<div class="form-field full">
						<label for="subject"><?php esc_html_e('Subject', 'sco-investor'); ?></label>
						<select id="subject" name="sci_subject">
							<option><?php esc_html_e('Course Question', 'sco-investor'); ?></option>
							<option><?php esc_html_e('Partnership / Collaboration', 'sco-investor'); ?></option>
							<option><?php esc_html_e('Feedback on the Website', 'sco-investor'); ?></option>
							<option><?php esc_html_e('Something Else', 'sco-investor'); ?></option>
						</select>
					</div>
					<div class="form-field full">
						<label for="message"><?php esc_html_e('Message', 'sco-investor'); ?></label>
						<textarea id="message" name="sci_message" placeholder="<?php esc_attr_e('How can we help?', 'sco-investor'); ?>" required></textarea>
					</div>
				</div>
				<button type="submit" class="btn btn-primary btn-block"><?php esc_html_e('Send Message', 'sco-investor'); ?></button>
				<?php if ('contact_success' === $sci_form_flag) : ?>
					<div class="form-msg show"><?php esc_html_e("Thanks for reaching out! We'll reply within 24 hours.", 'sco-investor'); ?></div>
				<?php elseif ('contact_error' === $sci_form_flag) : ?>
					<div class="form-msg show is-error"><?php esc_html_e('Something went wrong — please check your details and try again.', 'sco-investor'); ?></div>
				<?php else : ?>
					<div class="form-msg"></div>
				<?php endif; ?>
			</form>
		</div>

	</div>
</section>

<section class="section">
	<div class="container" style="max-width:760px;">
		<div class="section-head reveal">
			<div class="eyebrow"><span class="dot"></span> FAQ</div>
			<h2><?php esc_html_e('Before you write in', 'sco-investor'); ?></h2>
		</div>
		<?php
		echo sci_render_accordion([
			[
				'question' => __('Do you offer personalised investment advice?', 'sco-investor'),
				'answer'   => __('No — we are an education platform, not a SEBI-registered advisory. All content is for learning purposes only.', 'sco-investor'),
				'open'     => true,
			],
			[
				'question' => __('Can I get a refund on a course?', 'sco-investor'),
				'answer'   => __("Our refund policy will be published alongside checkout when payments go live. Reach out and we'll sort it case-by-case for now.", 'sco-investor'),
				'open'     => false,
			],
			[
				'question' => __('Do you do collaborations or guest posts?', 'sco-investor'),
				'answer'   => __('Yes — select Partnership / Collaboration in the form above and tell us a bit about your idea.', 'sco-investor'),
				'open'     => false,
			],
		]);
		?>
	</div>
</section>

<?php get_footer(); ?>
