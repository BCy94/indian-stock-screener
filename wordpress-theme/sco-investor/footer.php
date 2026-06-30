</main>

<?php $sci_form_flag = isset($_GET['sci_form']) ? sanitize_text_field(wp_unslash($_GET['sci_form'])) : ''; ?>

<footer class="site-footer">
	<div class="container">
		<div class="footer-grid">
			<div class="footer-brand">
				<?php sci_site_brand(); ?>
				<p><?php echo esc_html(get_theme_mod('sci_footer_tagline', 'Honest, practical investing education for Indian markets — courses, research materials and tools built from real portfolio experience, not hype.')); ?></p>
				<div class="social-row">
					<a href="<?php echo esc_url(sci_social_url('twitter') ?: '#'); ?>" aria-label="Twitter"><svg viewBox="0 0 24 24" fill="none"><path d="M22 5.9c-.8.35-1.6.6-2.5.7.9-.55 1.6-1.4 1.9-2.4-.85.5-1.8.85-2.8 1a4 4 0 00-6.8 3.6c-3.2-.15-6-1.7-7.9-4.1-.35.6-.5 1.3-.5 2 0 1.4.7 2.6 1.8 3.3-.7 0-1.3-.2-1.9-.5 0 1.95 1.4 3.6 3.2 4-.35.1-.7.15-1.1.15-.25 0-.5 0-.75-.07.5 1.6 2 2.75 3.7 2.78A8.1 8.1 0 012 19.5 11.4 11.4 0 008.3 21c7.5 0 11.6-6.3 11.6-11.7v-.55c.8-.55 1.5-1.3 2.1-2.15z" fill="currentColor"/></svg></a>
					<a href="<?php echo esc_url(sci_social_url('youtube') ?: '#'); ?>" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none"><rect x="2" y="5" width="20" height="14" rx="4" stroke="currentColor" stroke-width="1.8"/><path d="M10 9.5l5 2.5-5 2.5v-5z" fill="currentColor"/></svg></a>
					<a href="<?php echo esc_url(sci_social_url('instagram') ?: '#'); ?>" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.8"/><circle cx="17.2" cy="6.8" r="1.1" fill="currentColor"/></svg></a>
					<a href="<?php echo esc_url(sci_social_url('telegram') ?: '#'); ?>" aria-label="Telegram"><svg viewBox="0 0 24 24" fill="none"><path d="M21 4L3 11.5l6 2M21 4L17 21l-8-7.5M21 4L9 13.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
				</div>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e('Explore', 'sco-investor'); ?></h4>
				<?php
				if (has_nav_menu('footer-explore')) {
					wp_nav_menu([
						'theme_location' => 'footer-explore',
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'depth'          => 1,
					]);
				} else {
					sci_footer_explore_fallback();
				}
				?>
			</div>
			<div class="footer-col">
				<h4><?php esc_html_e('Support', 'sco-investor'); ?></h4>
				<?php
				if (has_nav_menu('footer-support')) {
					wp_nav_menu([
						'theme_location' => 'footer-support',
						'container'      => false,
						'items_wrap'     => '<ul>%3$s</ul>',
						'depth'          => 1,
					]);
				} else {
					sci_footer_support_fallback();
				}
				?>
			</div>
			<div class="footer-col" id="newsletter-form-footer">
				<h4><?php echo esc_html(get_theme_mod('sci_newsletter_heading', 'Stay Updated')); ?></h4>
				<p class="muted" style="font-size:14.5px;margin-bottom:4px;"><?php echo esc_html(get_theme_mod('sci_newsletter_tagline', 'Market insights and new course drops in your inbox.')); ?></p>
				<form class="newsletter-form" data-sci-ajax-form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
					<input type="hidden" name="action" value="sci_newsletter_submit">
					<input type="hidden" name="sci_form_anchor" value="newsletter-form-footer">
					<?php wp_nonce_field('sci_newsletter_submit', 'sci_newsletter_nonce'); ?>
					<input type="email" name="sci_newsletter_email" placeholder="you@email.com" required>
					<button class="btn btn-primary btn-sm" type="submit"><?php esc_html_e('Join', 'sco-investor'); ?></button>
				</form>
				<?php if ('newsletter_success' === $sci_form_flag) : ?>
					<div class="form-msg show"><?php esc_html_e("You're on the list!", 'sco-investor'); ?></div>
				<?php elseif ('newsletter_error' === $sci_form_flag) : ?>
					<div class="form-msg show is-error"><?php esc_html_e('Newsletter signups are not live yet — check back soon.', 'sco-investor'); ?></div>
				<?php else : ?>
					<div class="form-msg"></div>
				<?php endif; ?>
			</div>
		</div>
		<div class="footer-bottom">
			<?php
			$footer_copyright = get_theme_mod('sci_footer_copyright', '');
			if ($footer_copyright) {
				echo '<span>' . esc_html($footer_copyright) . '</span>';
			} else {
				echo '<span>&copy; ' . esc_html(date_i18n('Y')) . ' ';
				bloginfo('name');
				echo '. ' . esc_html__('All rights reserved. Educational content only — not investment advice.', 'sco-investor') . '</span>';
			}
			?>
			<div class="legal-links">
				<?php
				the_privacy_policy_link('', '');
				sci_legal_link('terms-of-service', __('Terms', 'sco-investor'));
				sci_legal_link('disclaimer', __('Disclaimer', 'sco-investor'));
				?>
			</div>
		</div>
	</div>
</footer>

<button class="back-to-top glass" aria-label="<?php esc_attr_e('Back to top', 'sco-investor'); ?>">
	<svg viewBox="0 0 24 24" fill="none"><path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
