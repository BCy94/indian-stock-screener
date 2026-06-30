<?php
/**
 * Native add_meta_box() UI for the course/material fields — no ACF or
 * other plugin dependency. Each save handler sanitizes $_POST itself
 * rather than relying on register_post_meta()'s sanitize_callback, which
 * only auto-applies through REST/the block editor, not classic meta boxes.
 */

if (!defined('ABSPATH')) exit;

function sci_add_meta_boxes() {
	add_meta_box('sci_course_details',       __('Course Details', 'sco-investor'),      'sci_render_course_meta_box',       'course',       'normal', 'high');
	add_meta_box('sci_material_details',     __('Material Details', 'sco-investor'),    'sci_render_material_meta_box',     'material',     'normal', 'high');
	add_meta_box('sci_testimonial_details',  __('Testimonial Details', 'sco-investor'), 'sci_render_testimonial_meta_box',  'testimonial',  'normal', 'high');
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
	$linked_product = (int) get_post_meta($post->ID, '_sci_product_id', true);
	$featured       = (bool) get_post_meta($post->ID, '_sci_featured', true);
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
		<tr>
			<th><label for="sci_product_id"><?php esc_html_e('Linked Product (WooCommerce)', 'sco-investor'); ?></label></th>
			<td>
				<?php if (sci_has_woocommerce()) : ?>
					<select id="sci_product_id" name="sci_product_id">
						<option value="0"><?php esc_html_e('— None (no payment required) —', 'sco-investor'); ?></option>
						<?php foreach (sci_get_products_for_select() as $product_id => $product_label) : ?>
							<option value="<?php echo esc_attr($product_id); ?>" <?php selected($linked_product, $product_id); ?>><?php echo esc_html($product_label); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e('Selling this course? Link it to a WooCommerce product to show a real Buy Now button and unlock content automatically on purchase.', 'sco-investor'); ?></p>
				<?php else : ?>
					<p class="description"><?php esc_html_e('Install and activate WooCommerce to sell this course with a real payment gateway (Razorpay, Stripe, PayPal, etc.).', 'sco-investor'); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Featured on Homepage', 'sco-investor'); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sci_featured" value="1" <?php checked($featured); ?>>
					<?php esc_html_e('Show this course in the featured/homepage course section', 'sco-investor'); ?>
				</label>
			</td>
		</tr>
	</table>
	<p class="description"><?php esc_html_e('Set the Course Level(s) from the box in the sidebar.', 'sco-investor'); ?></p>
	<?php
}

function sci_render_material_meta_box($post) {
	wp_nonce_field('sci_save_material_meta', 'sci_material_meta_nonce');

	$type           = get_post_meta($post->ID, '_sci_material_type', true) ?: 'other';
	$file_id        = (int) get_post_meta($post->ID, '_sci_file', true);
	$external_url   = get_post_meta($post->ID, '_sci_external_url', true);
	$badge          = get_post_meta($post->ID, '_sci_badge', true);
	$price          = get_post_meta($post->ID, '_sci_price', true);
	$price_original = get_post_meta($post->ID, '_sci_price_original', true);
	$linked_product = (int) get_post_meta($post->ID, '_sci_product_id', true);
	$featured       = (bool) get_post_meta($post->ID, '_sci_featured', true);
	$file_name      = $file_id ? basename(get_attached_file($file_id)) : '';
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
			<th><label for="sci_price"><?php esc_html_e('Price (₹)', 'sco-investor'); ?></label></th>
			<td>
				<input type="number" step="0.01" min="0" id="sci_price" name="sci_price" value="<?php echo esc_attr($price); ?>" class="regular-text">
				<p class="description"><?php esc_html_e('Optional. Leave blank to keep this a free download.', 'sco-investor'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_price_original"><?php esc_html_e('Original Price (₹)', 'sco-investor'); ?></label></th>
			<td>
				<input type="number" step="0.01" min="0" id="sci_price_original" name="sci_price_original" value="<?php echo esc_attr($price_original); ?>" class="regular-text">
				<p class="description"><?php esc_html_e('Optional. Shown struck through next to the price, e.g. for a discount.', 'sco-investor'); ?></p>
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
		<tr>
			<th><label for="sci_product_id"><?php esc_html_e('Linked Product (WooCommerce)', 'sco-investor'); ?></label></th>
			<td>
				<?php if (sci_has_woocommerce()) : ?>
					<select id="sci_product_id" name="sci_product_id">
						<option value="0"><?php esc_html_e('— None (free download) —', 'sco-investor'); ?></option>
						<?php foreach (sci_get_products_for_select() as $product_id => $product_label) : ?>
							<option value="<?php echo esc_attr($product_id); ?>" <?php selected($linked_product, $product_id); ?>><?php echo esc_html($product_label); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e('Selling this material? Link it to a WooCommerce product to show a real Buy Now button and gate the file/link until purchase.', 'sco-investor'); ?></p>
				<?php else : ?>
					<p class="description"><?php esc_html_e('Install and activate WooCommerce to sell this with a real payment gateway (Razorpay, Stripe, PayPal, etc.).', 'sco-investor'); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e('Featured on Homepage', 'sco-investor'); ?></th>
			<td>
				<label>
					<input type="checkbox" name="sci_featured" value="1" <?php checked($featured); ?>>
					<?php esc_html_e('Show this material in the featured/homepage materials section', 'sco-investor'); ?>
				</label>
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

function sci_render_testimonial_meta_box($post) {
	wp_nonce_field('sci_save_testimonial_meta', 'sci_testimonial_meta_nonce');

	$quote    = get_post_meta($post->ID, '_sci_testi_quote', true);
	$role     = get_post_meta($post->ID, '_sci_testi_role', true);
	$stars    = (int) get_post_meta($post->ID, '_sci_testi_stars', true) ?: 5;
	$initials = get_post_meta($post->ID, '_sci_testi_initials', true);
	?>
	<p class="description" style="margin-bottom:12px;"><?php esc_html_e('The Title field above is the reviewer\'s name. Fill in their quote and details below.', 'sco-investor'); ?></p>
	<table class="form-table">
		<tr>
			<th><label for="sci_testi_quote"><?php esc_html_e('Quote', 'sco-investor'); ?></label></th>
			<td>
				<textarea id="sci_testi_quote" name="sci_testi_quote" rows="4" class="large-text"><?php echo esc_textarea($quote); ?></textarea>
				<p class="description"><?php esc_html_e('The review text. Do not include quotation marks — the theme adds them automatically.', 'sco-investor'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="sci_testi_role"><?php esc_html_e('Role / Location', 'sco-investor'); ?></label></th>
			<td>
				<input type="text" id="sci_testi_role" name="sci_testi_role" value="<?php echo esc_attr($role); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g. Working Professional, Pune', 'sco-investor'); ?>">
			</td>
		</tr>
		<tr>
			<th><label for="sci_testi_stars"><?php esc_html_e('Star Rating', 'sco-investor'); ?></label></th>
			<td>
				<select id="sci_testi_stars" name="sci_testi_stars">
					<?php for ($i = 5; $i >= 1; $i--) : ?>
						<option value="<?php echo esc_attr($i); ?>" <?php selected($stars, $i); ?>><?php echo esc_html(str_repeat('★', $i) . ' (' . $i . ')'); ?></option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="sci_testi_initials"><?php esc_html_e('Initials Override', 'sco-investor'); ?></label></th>
			<td>
				<input type="text" id="sci_testi_initials" name="sci_testi_initials" value="<?php echo esc_attr($initials); ?>" class="small-text" maxlength="3" placeholder="AB">
				<p class="description"><?php esc_html_e('Optional. Leave blank to auto-generate from the name above. Shown in the avatar circle when no photo is set.', 'sco-investor'); ?></p>
			</td>
		</tr>
	</table>
	<p class="description"><?php esc_html_e('To show a photo, set a Featured Image in the sidebar. To control display order, change the Order field under Page Attributes.', 'sco-investor'); ?></p>
	<?php
}

function sci_save_testimonial_meta($post_id) {
	if (!isset($_POST['sci_testimonial_meta_nonce']) || !wp_verify_nonce($_POST['sci_testimonial_meta_nonce'], 'sci_save_testimonial_meta')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	if (isset($_POST['sci_testi_quote'])) {
		update_post_meta($post_id, '_sci_testi_quote', sanitize_textarea_field(wp_unslash($_POST['sci_testi_quote'])));
	}
	if (isset($_POST['sci_testi_role'])) {
		update_post_meta($post_id, '_sci_testi_role', sanitize_text_field(wp_unslash($_POST['sci_testi_role'])));
	}
	if (isset($_POST['sci_testi_stars'])) {
		update_post_meta($post_id, '_sci_testi_stars', min(5, max(1, absint($_POST['sci_testi_stars']))));
	}
	if (isset($_POST['sci_testi_initials'])) {
		update_post_meta($post_id, '_sci_testi_initials', sanitize_text_field(wp_unslash($_POST['sci_testi_initials'])));
	}
}
add_action('save_post_testimonial', 'sci_save_testimonial_meta');

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
	if (isset($_POST['sci_product_id'])) {
		update_post_meta($post_id, '_sci_product_id', absint($_POST['sci_product_id']));
	}
	update_post_meta($post_id, '_sci_featured', isset($_POST['sci_featured']) ? '1' : '');
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
	if (isset($_POST['sci_price'])) {
		$value = sanitize_text_field(wp_unslash($_POST['sci_price']));
		update_post_meta($post_id, '_sci_price', $value === '' ? '' : round((float) $value, 2));
	}
	if (isset($_POST['sci_price_original'])) {
		$value = sanitize_text_field(wp_unslash($_POST['sci_price_original']));
		update_post_meta($post_id, '_sci_price_original', $value === '' ? '' : round((float) $value, 2));
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
	if (isset($_POST['sci_product_id'])) {
		update_post_meta($post_id, '_sci_product_id', absint($_POST['sci_product_id']));
	}
	update_post_meta($post_id, '_sci_featured', isset($_POST['sci_featured']) ? '1' : '');
}
add_action('save_post_material', 'sci_save_material_meta');
