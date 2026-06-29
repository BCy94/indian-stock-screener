<?php
/**
 * Comments + comment form for single posts. No prototype equivalent — the
 * static HTML never had comments, so this is original markup built mostly
 * from existing primitives (.author-box for the meta row, .avatar-ring for
 * initials, .glass for cards, .form-field for inputs) plus a small set of
 * new comment-specific rules in main.css for list/thread structure.
 */

if (post_password_required()) {
	return;
}

if (!function_exists('sci_comment_callback')) {
	function sci_comment_callback($comment, $args, $depth) {
		if (in_array(get_comment_type(), ['pingback', 'trackback'], true)) {
			?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class('glass comment-item comment-item--ping'); ?>>
				<?php esc_html_e('Pingback:', 'sco-investor'); ?> <?php comment_author_link(); ?> <?php edit_comment_link(__('(Edit)', 'sco-investor')); ?>
			</li>
			<?php
			return;
		}
		?>
		<li id="comment-<?php comment_ID(); ?>" <?php comment_class('glass comment-item'); ?>>
			<div class="author-box">
				<span class="avatar-ring"><?php echo esc_html(sci_initials(get_comment_author())); ?></span>
				<div>
					<div class="comment-author"><?php comment_author(); ?></div>
					<div class="comment-date">
						<a href="<?php echo esc_url(get_comment_link($comment)); ?>">
							<time datetime="<?php comment_time('c'); ?>"><?php printf(esc_html__('%1$s at %2$s', 'sco-investor'), get_comment_date(), get_comment_time()); ?></time>
						</a>
					</div>
				</div>
			</div>

			<?php if ('0' === $comment->comment_approved) : ?>
				<p class="comment-awaiting-moderation muted"><?php esc_html_e('Your comment is awaiting moderation.', 'sco-investor'); ?></p>
			<?php endif; ?>

			<div class="comment-content"><?php comment_text(); ?></div>

			<?php
			comment_reply_link(array_merge($args, [
				'depth'      => $depth,
				'max_depth'  => $args['max_depth'],
				'reply_text' => __('Reply', 'sco-investor'),
				'class'      => 'comment-reply-link',
			]));
			?>
		</li>
		<?php
	}
}

if (!function_exists('sci_comment_form_fields')) {
	function sci_comment_form_fields() {
		$commenter = wp_get_current_commenter();
		$req       = (bool) get_option('require_name_email');
		$aria_req  = $req ? " aria-required='true'" : '';
		$html_req  = $req ? ' required' : '';
		$mark      = $req ? ' <span class="required" aria-hidden="true">*</span>' : '';

		return [
			'author' => '<div class="form-field"><label for="author">' . esc_html__('Name', 'sco-investor') . $mark . '</label>'
				. '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245"' . $aria_req . $html_req . '></div>',
			'email' => '<div class="form-field"><label for="email">' . esc_html__('Email', 'sco-investor') . $mark . '</label>'
				. '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req . '></div>',
			'url' => '<div class="form-field"><label for="url">' . esc_html__('Website', 'sco-investor') . '</label>'
				. '<input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200"></div>',
		];
	}
}

if (!function_exists('sci_comment_field_markup')) {
	function sci_comment_field_markup() {
		return '<div class="form-field"><label for="comment">' . esc_html__('Comment', 'sco-investor') . ' <span class="required" aria-hidden="true">*</span></label>'
			. '<textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required></textarea></div>';
	}
}
?>

<div id="comments" class="comments-area">

	<?php if (have_comments()) : ?>
		<h3 class="comments-title">
			<?php
			$sci_comment_count = get_comments_number();
			printf(
				esc_html(_n('%s Comment', '%s Comments', $sci_comment_count, 'sco-investor')),
				esc_html(number_format_i18n($sci_comment_count))
			);
			?>
		</h3>

		<ul class="comment-list">
			<?php
			wp_list_comments([
				'style'    => 'ul',
				'callback' => 'sci_comment_callback',
			]);
			?>
		</ul>

		<?php
		the_comments_pagination([
			'prev_text' => __('← Previous', 'sco-investor'),
			'next_text' => __('Next →', 'sco-investor'),
		]);
		?>
	<?php endif; ?>

	<?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
		<p class="comments-closed muted"><?php esc_html_e('Comments are closed.', 'sco-investor'); ?></p>
	<?php endif; ?>

	<?php if (comments_open()) : ?>
		<div class="glass" style="padding:32px;">
			<?php
			comment_form([
				'class_submit'      => 'btn btn-primary btn-block',
				'label_submit'      => __('Post Comment', 'sco-investor'),
				'title_reply'       => __('Leave a Comment', 'sco-investor'),
				'title_reply_to'    => __('Leave a Reply to %s', 'sco-investor'),
				'cancel_reply_link' => __('Cancel Reply', 'sco-investor'),
				'fields'            => sci_comment_form_fields(),
				'comment_field'     => sci_comment_field_markup(),
			]);
			?>
		</div>
	<?php endif; ?>

</div>
