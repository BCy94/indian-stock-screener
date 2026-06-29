<?php
/**
 * Native add_meta_box() UI for the course/material fields — no ACF or
 * other plugin dependency. Each save handler sanitizes $_POST itself
 * rather than relying on register_post_meta()'s sanitize_callback, which
 * only auto-applies through REST/the block editor, not classic meta boxes.
 */

if (!defined('ABSPATH')) exit;

function sci_add_meta_boxes() {
	add_meta_box('sci_course_details', __('Course Details', 'sco-investor'), 'sci_render_course_meta_box', 'course', 'normal', 'high');
	add_meta_box('sci_material_details', __('Material Details', 'sco-investor'), 'sci_render_material_meta_box', 'material', 'normal', 'high');
}
add_action('add_meta_boxes', 'sci_add_meta_boxes');

function sci_render_course_meta_box($post) {
	wp_nonce_field('sci_save_course_meta', 'sci_course_meta_nonce');

	$price          = get_post_meta($post->ID, '_sci_price', true);
	$price_original = get_post_meta($post->ID, '_sci_price_original', true);
	$weeks          = get_post_meta($post->ID, '_sci_duration_weeks', true);
	$lessons        = get_post_meta($post->ID, '_sci_lesson_count', true);
	$badge          = get_post_meta($post->ID, '_sci_badge', true);
	$level_label    = get_post_meta($post->ID, '_sci_level_label', true);
	?>
	<table class="form-table">
		<tr>
			<th><label for="sci_price"><?php esc_html_e('Price (₹)', 'sco-investor'); ?></label></th>
			<td><input type="number" step="0.01" min="0" id="sci_price" name="sci_price" value="<?php echo esc_attr($price); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="sci_price_original"><?php esc_html_e('Original Price (₹)', 'sco-investor'); ?></label></th>
			<td>
				<input type="number" step="0.01" min="0" id="sci_price_original" name="sci_price_original" value="<?php echo esc_attr($price_original); ?>" class="regular-text">
				<p class="description"><?php esc_html_e('Optional. Shown struck through next to the price, e.g. for a discount.', 'sco-investor'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_duration_weeks"><?php esc_html_e('Duration (weeks)', 'sco-investor'); ?></label></th>
			<td><input type="number" step="1" min="0" id="sci_duration_weeks" name="sci_duration_weeks" value="<?php echo esc_attr($weeks); ?>" class="small-text"></td>
		</tr>
		<tr>
			<th><label for="sci_lesson_count"><?php esc_html_e('Lesson Count', 'sco-investor'); ?></label></th>
			<td><input type="number" step="1" min="0" id="sci_lesson_count" name="sci_lesson_count" value="<?php echo esc_attr($lessons); ?>" class="small-text"></td>
		</tr>
		<tr>
			<th><label for="sci_level_label"><?php esc_html_e('Level Label', 'sco-investor'); ?></label></th>
			<td>
				<input type="text" id="sci_level_label" name="sci_level_label" value="<?php echo esc_attr($level_label); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g. Beginner → Pro', 'sco-investor'); ?>">
				<p class="description"><?php esc_html_e('Optional. Overrides the text shown on the card. Leave blank to show the Course Level(s) checked in the sidebar — checking all three shows "All Levels".', 'sco-investor'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_badge"><?php esc_html_e('Badge', 'sco-investor'); ?></label></th>
			<td>
				<input type="text" id="sci_badge" name="sci_badge" value="<?php echo esc_attr($badge); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g. Bestseller, Popular', 'sco-investor'); ?>">
				<p class="description"><?php esc_html_e('Optional. Leave blank to show no badge.', 'sco-investor'); ?></p>
			</td>
		</tr>
	</table>
	<p class="description"><?php esc_html_e('Set the Course Level(s) from the box in the sidebar.', 'sco-investor'); ?></p>
	<?php
}

function sci_render_material_meta_box($post) {
	wp_nonce_field('sci_save_material_meta', 'sci_material_meta_nonce');

	$type          = get_post_meta($post->ID, '_sci_material_type', true) ?: 'other';
	$file_id       = (int) get_post_meta($post->ID, '_sci_file', true);
	$external_url  = get_post_meta($post->ID, '_sci_external_url', true);
	$badge         = get_post_meta($post->ID, '_sci_badge', true);
	$file_name     = $file_id ? basename(get_attached_file($file_id)) : '';
	?>
	<table class="form-table">
		<tr>
			<th><label for="sci_material_type"><?php esc_html_e('Material Type', 'sco-investor'); ?></label></th>
			<td>
				<select id="sci_material_type" name="sci_material_type">
					<?php foreach (sci_material_types() as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="description"><?php esc_html_e('Controls the icon and default badge shown on the card.', 'sco-investor'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_file_button"><?php esc_html_e('File', 'sco-investor'); ?></label></th>
			<td>
				<input type="hidden" id="sci_file" name="sci_file" value="<?php echo esc_attr($file_id); ?>">
				<button type="button" class="button" id="sci_file_button"><?php esc_html_e('Select File', 'sco-investor'); ?></button>
				<button type="button" class="button-link" id="sci_file_remove" style="<?php echo $file_id ? '' : 'display:none;'; ?>margin-left:8px;color:#b32d2e;"><?php esc_html_e('Remove', 'sco-investor'); ?></button>
				<p class="description" id="sci_file_name"><?php echo esc_html($file_name); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_external_url"><?php esc_html_e('External URL', 'sco-investor'); ?></label></th>
			<td>
				<input type="url" id="sci_external_url" name="sci_external_url" value="<?php echo esc_attr($external_url); ?>" class="regular-text" placeholder="https://">
				<p class="description"><?php esc_html_e('Use this for Google Sheets, external tools, etc. If a File is also set above, the File is used instead.', 'sco-investor'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_badge"><?php esc_html_e('Badge', 'sco-investor'); ?></label></th>
			<td>
				<input type="text" id="sci_badge" name="sci_badge" value="<?php echo esc_attr($badge); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g. PDF · 6 pages', 'sco-investor'); ?>">
				<p class="description"><?php esc_html_e('Optional. Leave blank to use the Material Type label as the badge.', 'sco-investor'); ?></p>
			</td>
		</tr>
	</table>
	<script>
	(function(){
		var frame;
		var button = document.getElementById('sci_file_button');
		var removeBtn = document.getElementById('sci_file_remove');
		var input = document.getElementById('sci_file');
		var nameEl = document.getElementById('sci_file_name');

		button.addEventListener('click', function(e){
			e.preventDefault();
			if (frame) { frame.open(); return; }
			frame = wp.media({
				title: <?php echo wp_json_encode(__('Select a file', 'sco-investor')); ?>,
				multiple: false,
				library: { type: '' }
			});
			frame.on('select', function(){
				var attachment = frame.state().get('selection').first().toJSON();
				input.value = attachment.id;
				nameEl.textContent = attachment.filename || attachment.title || '';
				removeBtn.style.display = '';
			});
			frame.open();
		});

		removeBtn.addEventListener('click', function(e){
			e.preventDefault();
			input.value = '';
			nameEl.textContent = '';
			removeBtn.style.display = 'none';
		});
	})();
	</script>
	<?php
}

function sci_enqueue_meta_box_assets($hook) {
	if ($hook !== 'post.php' && $hook !== 'post-new.php') return;
	if (get_post_type() !== 'material') return;
	wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'sci_enqueue_meta_box_assets');

function sci_save_course_meta($post_id) {
	if (!isset($_POST['sci_course_meta_nonce']) || !wp_verify_nonce($_POST['sci_course_meta_nonce'], 'sci_save_course_meta')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	if (isset($_POST['sci_price'])) {
		$value = sanitize_text_field(wp_unslash($_POST['sci_price']));
		update_post_meta($post_id, '_sci_price', $value === '' ? '' : round((float) $value, 2));
	}
	if (isset($_POST['sci_price_original'])) {
		$value = sanitize_text_field(wp_unslash($_POST['sci_price_original']));
		update_post_meta($post_id, '_sci_price_original', $value === '' ? '' : round((float) $value, 2));
	}
	if (isset($_POST['sci_duration_weeks'])) {
		update_post_meta($post_id, '_sci_duration_weeks', absint($_POST['sci_duration_weeks']));
	}
	if (isset($_POST['sci_lesson_count'])) {
		update_post_meta($post_id, '_sci_lesson_count', absint($_POST['sci_lesson_count']));
	}
	if (isset($_POST['sci_level_label'])) {
		update_post_meta($post_id, '_sci_level_label', sanitize_text_field(wp_unslash($_POST['sci_level_label'])));
	}
	if (isset($_POST['sci_badge'])) {
		update_post_meta($post_id, '_sci_badge', sanitize_text_field(wp_unslash($_POST['sci_badge'])));
	}
}
add_action('save_post_course', 'sci_save_course_meta');

function sci_save_material_meta($post_id) {
	if (!isset($_POST['sci_material_meta_nonce']) || !wp_verify_nonce($_POST['sci_material_meta_nonce'], 'sci_save_material_meta')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	if (isset($_POST['sci_material_type'])) {
		$type = sanitize_text_field(wp_unslash($_POST['sci_material_type']));
		update_post_meta($post_id, '_sci_material_type', array_key_exists($type, sci_material_types()) ? $type : 'other');
	}
	if (isset($_POST['sci_file'])) {
		update_post_meta($post_id, '_sci_file', absint($_POST['sci_file']));
	}
	if (isset($_POST['sci_external_url'])) {
		update_post_meta($post_id, '_sci_external_url', sanitize_url(wp_unslash($_POST['sci_external_url'])));
	}
	if (isset($_POST['sci_badge'])) {
		update_post_meta($post_id, '_sci_badge', sanitize_text_field(wp_unslash($_POST['sci_badge'])));
	}
}
add_action('save_post_material', 'sci_save_material_meta');
