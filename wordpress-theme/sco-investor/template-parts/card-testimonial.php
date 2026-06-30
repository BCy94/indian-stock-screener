<?php
/**
 * Single testimonial card. Expects the testimonial Loop to be on the
 * current post. Uses the reviewer's photo if one is set, otherwise falls
 * back to the auto-generated initials ring.
 */

if (!defined('ABSPATH')) exit;

$post_id  = get_the_ID();
$quote    = get_post_meta($post_id, '_sci_testi_quote', true);
$role     = get_post_meta($post_id, '_sci_testi_role', true);
$stars    = max(1, min(5, (int) get_post_meta($post_id, '_sci_testi_stars', true) ?: 5));
$initials = get_post_meta($post_id, '_sci_testi_initials', true) ?: sci_initials(get_the_title());

$stars_html = str_repeat('★', $stars);
?>
<div class="testi-card glass">
	<div class="testi-stars"><?php echo esc_html($stars_html); ?></div>
	<?php if ($quote) : ?>
		<p class="quote">&ldquo;<?php echo esc_html($quote); ?>&rdquo;</p>
	<?php endif; ?>
	<div class="testi-person">
		<?php if (has_post_thumbnail($post_id)) : ?>
			<div class="avatar-ring avatar-ring--img"><?php echo get_the_post_thumbnail($post_id, [48, 48], ['alt' => esc_attr(get_the_title())]); ?></div>
		<?php else : ?>
			<span class="avatar-ring"><?php echo esc_html($initials); ?></span>
		<?php endif; ?>
		<div>
			<div class="name"><?php echo esc_html(get_the_title()); ?></div>
			<?php if ($role) : ?><div class="role"><?php echo esc_html($role); ?></div><?php endif; ?>
		</div>
	</div>
</div>
