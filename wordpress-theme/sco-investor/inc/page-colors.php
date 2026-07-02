<?php
/**
 * Per-page color override. Appearance → Customize → Brand Colors (see
 * inc/brand-colors.php) sets the sitewide default; this meta box lets the
 * owner override the accent/background just for one specific Page, Course
 * or Material — e.g. a seasonal landing page or a course with its own
 * branding — without touching the sitewide setting or any code.
 */

if (!defined('ABSPATH')) exit;

function sci_page_colors_meta_box() {
	add_meta_box(
		'sci_page_colors',
		__('Page Color Override', 'sco-investor'),
		'sci_render_page_colors_meta_box',
		['page', 'course', 'material'],
		'side',
		'default'
	);
}
add_action('add_meta_boxes', 'sci_page_colors_meta_box');

function sci_render_page_colors_meta_box($post) {
	wp_nonce_field('sci_save_page_colors', 'sci_page_colors_nonce');

	$accent = get_post_meta($post->ID, '_sci_page_accent_color', true);
	$bg     = get_post_meta($post->ID, '_sci_page_bg_color', true);
	?>
	<p class="description"><?php esc_html_e('Optional. Overrides the sitewide Brand Colors just for this page. Leave both blank to use the site default.', 'sco-investor'); ?></p>
	<p>
		<label for="sci_page_accent_color"><strong><?php esc_html_e('Accent Color', 'sco-investor'); ?></strong></label><br>
		<input type="text" id="sci_page_accent_color" name="sci_page_accent_color" value="<?php echo esc_attr($accent); ?>" class="sci-color-field" data-default-color="">
	</p>
	<p>
		<label for="sci_page_bg_color"><strong><?php esc_html_e('Background Color', 'sco-investor'); ?></strong></label><br>
		<input type="text" id="sci_page_bg_color" name="sci_page_bg_color" value="<?php echo esc_attr($bg); ?>" class="sci-color-field" data-default-color="">
	</p>
	<p><button type="button" class="button" id="sci_page_colors_reset"><?php esc_html_e('Clear Override (use site default)', 'sco-investor'); ?></button></p>
	<script>
	jQuery(function ($) {
		$('.sci-color-field').wpColorPicker();
		$('#sci_page_colors_reset').on('click', function () {
			$('#sci_page_accent_color').wpColorPicker('color', '');
			$('#sci_page_bg_color').wpColorPicker('color', '');
		});
	});
	</script>
	<?php
}

function sci_enqueue_page_colors_assets($hook) {
	if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
	if (!in_array(get_post_type(), ['page', 'course', 'material'], true)) return;
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'sci_enqueue_page_colors_assets');

function sci_save_page_colors($post_id) {
	if (!isset($_POST['sci_page_colors_nonce']) || !wp_verify_nonce($_POST['sci_page_colors_nonce'], 'sci_save_page_colors')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	if (isset($_POST['sci_page_accent_color'])) {
		$value = sanitize_hex_color(wp_unslash($_POST['sci_page_accent_color']));
		if ($value) {
			update_post_meta($post_id, '_sci_page_accent_color', $value);
		} else {
			delete_post_meta($post_id, '_sci_page_accent_color');
		}
	}
	if (isset($_POST['sci_page_bg_color'])) {
		$value = sanitize_hex_color(wp_unslash($_POST['sci_page_bg_color']));
		if ($value) {
			update_post_meta($post_id, '_sci_page_bg_color', $value);
		} else {
			delete_post_meta($post_id, '_sci_page_bg_color');
		}
	}
}
add_action('save_post_page', 'sci_save_page_colors');
add_action('save_post_course', 'sci_save_page_colors');
add_action('save_post_material', 'sci_save_page_colors');

/**
 * Scoped to body.postid-{$id}/body.page-id-{$id} (WordPress adds both
 * classes via body_class() for any singular page/post) so the override
 * only ever affects this one page — never bleeds into the rest of the
 * site. Reuses the same hex helpers as the sitewide Brand Colors
 * (inc/brand-colors.php) so a page override derives the same coordinated
 * hover/tint shades from a single picked color.
 */
function sci_page_color_css() {
	if (!is_singular(['page', 'course', 'material'])) return;
	$id = get_queried_object_id();
	if (!$id) return;

	$accent = get_post_meta($id, '_sci_page_accent_color', true);
	$bg     = get_post_meta($id, '_sci_page_bg_color', true);
	if (!$accent && !$bg) return;

	$decls = '';
	if ($accent) {
		[$r, $g, $b] = sci_hex_to_rgb($accent);
		$decls .= sprintf(
			'--accent:%1$s;--accent-dim:%2$s;--accent-soft:rgba(%3$d,%4$d,%5$d,0.14);',
			esc_html($accent),
			esc_html(sci_darken_hex($accent, 18)),
			$r, $g, $b
		);
	}
	if ($bg) {
		$decls .= sprintf('--bg:%s;', esc_html($bg));
	}

	printf('<style id="sci-page-colors">body.postid-%1$d,body.page-id-%1$d{%2$s}</style>' . "\n", (int) $id, $decls);
}
add_action('wp_head', 'sci_page_color_css', 21);
