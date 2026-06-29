<?php
/**
 * Contact + newsletter form handlers. Hand-rolled instead of pulling in a
 * forms plugin since the markup already exists in page-contact.php /
 * footer.php / home.php — this just gives those forms a real backend.
 */

if (!defined('ABSPATH')) exit;

/**
 * Replies to a form submission and always terminates the request: AJAX
 * callers (fetch() sending X-Requested-With) get JSON back; plain POSTs
 * (JS disabled) get redirected to the referring page with a status flag
 * so the page can render its own static notice — the message text never
 * round-trips through the URL.
 */
function sci_form_respond($success, $form_id, $message, $anchor = '') {
	$requested_with = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
		? sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REQUESTED_WITH']))
		: '';

	if (strtolower($requested_with) === 'xmlhttprequest') {
		if ($success) {
			wp_send_json_success(['message' => $message]);
		}
		wp_send_json_error(['message' => $message]);
	}

	$referer = remove_query_arg('sci_form', wp_get_referer() ?: home_url('/'));
	$flag    = $form_id . '_' . ($success ? 'success' : 'error');
	$anchor  = $anchor ?: ($form_id . '-form');
	wp_safe_redirect(add_query_arg('sci_form', $flag, $referer) . '#' . $anchor);
	exit;
}

function sci_contact_form_handle() {
	if (!isset($_POST['sci_contact_nonce']) || !wp_verify_nonce($_POST['sci_contact_nonce'], 'sci_contact_submit')) {
		sci_form_respond(false, 'contact', __('Your session expired — please refresh and try again.', 'sco-investor'));
	}

	$name    = isset($_POST['sci_name']) ? sanitize_text_field(wp_unslash($_POST['sci_name'])) : '';
	$email   = isset($_POST['sci_email']) ? sanitize_email(wp_unslash($_POST['sci_email'])) : '';
	$subject = isset($_POST['sci_subject']) ? sanitize_text_field(wp_unslash($_POST['sci_subject'])) : '';
	$message = isset($_POST['sci_message']) ? sanitize_textarea_field(wp_unslash($_POST['sci_message'])) : '';

	if (!$name || !$message || !is_email($email)) {
		sci_form_respond(false, 'contact', __('Please fill in your name, a valid email and a message.', 'sco-investor'));
	}

	$body = sprintf(
		"%s\n\n%s: %s\n%s: %s\n",
		$message,
		__('Name', 'sco-investor'),
		$name,
		__('Email', 'sco-investor'),
		$email
	);

	$sent = wp_mail(
		get_option('admin_email'),
		sprintf('[%s] %s', get_bloginfo('name'), $subject ?: __('Website Contact', 'sco-investor')),
		$body,
		['Reply-To: ' . $name . ' <' . $email . '>']
	);

	if (!$sent) {
		sci_form_respond(false, 'contact', __('Something went wrong sending your message — please email us directly instead.', 'sco-investor'));
	}

	sci_form_respond(true, 'contact', __("Thanks for reaching out! We'll reply within 24 hours.", 'sco-investor'));
}
add_action('admin_post_sci_contact_submit', 'sci_contact_form_handle');
add_action('admin_post_nopriv_sci_contact_submit', 'sci_contact_form_handle');

/**
 * No email service provider is wired up yet. apply_filters() defaults to
 * false (an honest "not live yet" message rather than a fake success)
 * until a future snippet adds, e.g.:
 *   add_filter('sci_newsletter_handler', function ($result, $email) { ... }, 10, 2);
 * returning true on success or a WP_Error on failure.
 */
function sci_newsletter_form_handle() {
	/*
	 * The newsletter signup form is repeated in footer.php (every page) and
	 * home.php's CTA band, so each instance points back at its own element
	 * id via this hidden field — otherwise a no-JS redirect couldn't know
	 * which of the two forms on a given page to scroll back to.
	 */
	$anchor = isset($_POST['sci_form_anchor']) ? sanitize_html_class(wp_unslash($_POST['sci_form_anchor'])) : 'newsletter-form';

	if (!isset($_POST['sci_newsletter_nonce']) || !wp_verify_nonce($_POST['sci_newsletter_nonce'], 'sci_newsletter_submit')) {
		sci_form_respond(false, 'newsletter', __('Your session expired — please refresh and try again.', 'sco-investor'), $anchor);
	}

	$email = isset($_POST['sci_newsletter_email']) ? sanitize_email(wp_unslash($_POST['sci_newsletter_email'])) : '';

	if (!is_email($email)) {
		sci_form_respond(false, 'newsletter', __('Please enter a valid email address.', 'sco-investor'), $anchor);
	}

	$result = apply_filters('sci_newsletter_handler', false, $email);

	if (is_wp_error($result)) {
		sci_form_respond(false, 'newsletter', $result->get_error_message(), $anchor);
	}

	if (!$result) {
		sci_form_respond(false, 'newsletter', __('Newsletter signups are not live yet — check back soon.', 'sco-investor'), $anchor);
	}

	sci_form_respond(true, 'newsletter', __("You're on the list!", 'sco-investor'), $anchor);
}
add_action('admin_post_sci_newsletter_submit', 'sci_newsletter_form_handle');
add_action('admin_post_nopriv_sci_newsletter_submit', 'sci_newsletter_form_handle');
