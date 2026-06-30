<?php
/**
 * Members management admin page.
 *
 * List view  → /wp-admin/admin.php?page=sci-members
 * Detail view → /wp-admin/admin.php?page=sci-members&user_id=X
 *
 * The detail view lets the owner see exactly which courses/materials a
 * member owns (via purchase or subscription) and grant/revoke one-click
 * manual access without touching WooCommerce orders.
 */

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'sci_register_members_page');
function sci_register_members_page() {
	add_menu_page(
		__('SCI Members', 'sco-investor'),
		__('Members', 'sco-investor'),
		'manage_options',
		'sci-members',
		'sci_render_members_page',
		'dashicons-groups',
		4
	);
}

function sci_render_members_page() {
	if (!current_user_can('manage_options')) {
		wp_die(esc_html__('You do not have permission to view this page.', 'sco-investor'));
	}

	// CSV export — output before any HTML
	if (isset($_GET['export']) && $_GET['export'] === 'csv' && check_admin_referer('sci_export_csv', 'sci_csv_nonce')) {
		sci_members_export_csv();
		return;
	}

	// Single-user detail view
	$user_id = isset($_GET['user_id']) ? absint($_GET['user_id']) : 0;
	if ($user_id) {
		sci_render_member_detail($user_id);
		return;
	}

	// — List view ————————————————————————————————————————————
	$per_page     = 30;
	$current_page = max(1, absint($_GET['paged'] ?? 1));
	$search       = sanitize_text_field($_GET['s'] ?? '');
	$role_filter  = sanitize_text_field($_GET['role'] ?? '');

	$user_args = [
		'number'       => $per_page,
		'offset'       => ($current_page - 1) * $per_page,
		'orderby'      => 'registered',
		'order'        => 'DESC',
		'count_total'  => true,
	];
	if ($search) {
		$user_args['search']         = '*' . $search . '*';
		$user_args['search_columns'] = ['user_login', 'user_email', 'display_name', 'user_nicename'];
	}
	if ($role_filter) {
		$user_args['role'] = $role_filter;
	}

	$user_query  = new WP_User_Query($user_args);
	$users       = $user_query->get_results();
	$total       = $user_query->get_total();
	$total_pages = ceil($total / $per_page);

	$export_url = wp_nonce_url(
		add_query_arg(['page' => 'sci-members', 'export' => 'csv'], admin_url('admin.php')),
		'sci_export_csv',
		'sci_csv_nonce'
	);

	$has_subs = function_exists('wcs_get_users_subscriptions');
	?>
	<div class="wrap sci-members-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e('Members', 'sco-investor'); ?></h1>
	<a href="<?php echo esc_url(admin_url('user-new.php')); ?>" class="page-title-action"><?php esc_html_e('Add User', 'sco-investor'); ?></a>
	<a href="<?php echo esc_url($export_url); ?>" class="page-title-action"><?php esc_html_e('Export CSV', 'sco-investor'); ?></a>
	<hr class="wp-header-end">

	<?php if (!sci_has_woocommerce()) : ?>
	<div class="notice notice-warning inline" style="margin:12px 0;"><p><?php esc_html_e('Install WooCommerce to see purchase data, order counts, and subscription status alongside member accounts.', 'sco-investor'); ?></p></div>
	<?php endif; ?>

	<form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" style="margin-bottom:12px;">
		<input type="hidden" name="page" value="sci-members">
		<div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
			<input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Search by name or email…', 'sco-investor'); ?>" style="min-width:240px;">
			<select name="role">
				<option value=""><?php esc_html_e('All roles', 'sco-investor'); ?></option>
				<?php
				$all_roles = wp_roles()->get_names();
				foreach ($all_roles as $slug => $name) :
					?>
					<option value="<?php echo esc_attr($slug); ?>" <?php selected($role_filter, $slug); ?>><?php echo esc_html(translate_user_role($name)); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="button"><?php esc_html_e('Filter', 'sco-investor'); ?></button>
			<?php if ($search || $role_filter) : ?>
				<a href="<?php echo esc_url(admin_url('admin.php?page=sci-members')); ?>" class="button-link"><?php esc_html_e('Clear', 'sco-investor'); ?></a>
			<?php endif; ?>
		</div>
	</form>

	<p class="description"><?php printf(esc_html__('%d user(s) found', 'sco-investor'), $total); ?></p>

	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width:32px;">#</th>
				<th><?php esc_html_e('Name', 'sco-investor'); ?></th>
				<th><?php esc_html_e('Email', 'sco-investor'); ?></th>
				<th><?php esc_html_e('Joined', 'sco-investor'); ?></th>
				<th><?php esc_html_e('Role', 'sco-investor'); ?></th>
				<?php if (sci_has_woocommerce()) : ?>
				<th><?php esc_html_e('Orders', 'sco-investor'); ?></th>
				<?php endif; ?>
				<?php if ($has_subs) : ?>
				<th><?php esc_html_e('Subscription', 'sco-investor'); ?></th>
				<?php endif; ?>
				<th style="width:120px;"><?php esc_html_e('Access', 'sco-investor'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($users)) : ?>
			<tr><td colspan="8"><em><?php esc_html_e('No members found.', 'sco-investor'); ?></em></td></tr>
		<?php endif; ?>
		<?php foreach ($users as $i => $user) :
			$row_num      = ($current_page - 1) * $per_page + $i + 1;
			$detail_url   = admin_url('admin.php?page=sci-members&user_id=' . $user->ID);
			$edit_url     = admin_url('user-edit.php?user_id=' . $user->ID);
			$order_count  = sci_has_woocommerce() ? wc_get_customer_order_count($user->ID) : 0;
			$has_active_sub = false;
			if ($has_subs) {
				$subs = wcs_get_users_subscriptions($user->ID);
				foreach ($subs as $sub) {
					if ($sub->get_status() === 'active') { $has_active_sub = true; break; }
				}
			}
			$role_names = array_map('translate_user_role', array_map(fn($r) => wp_roles()->get_names()[$r] ?? $r, $user->roles));
			?>
			<tr>
				<td><?php echo esc_html($row_num); ?></td>
				<td>
					<strong><a href="<?php echo esc_url($detail_url); ?>"><?php echo esc_html($user->display_name ?: $user->user_login); ?></a></strong>
					<div class="row-actions">
						<a href="<?php echo esc_url($detail_url); ?>"><?php esc_html_e('Manage Access', 'sco-investor'); ?></a> |
						<a href="<?php echo esc_url($edit_url); ?>"><?php esc_html_e('Edit Profile', 'sco-investor'); ?></a>
					</div>
				</td>
				<td><?php echo esc_html($user->user_email); ?></td>
				<td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($user->user_registered))); ?></td>
				<td><?php echo esc_html(implode(', ', $role_names)); ?></td>
				<?php if (sci_has_woocommerce()) : ?>
				<td>
					<?php echo esc_html($order_count); ?>
					<?php if ($order_count > 0) : ?>
					<a href="<?php echo esc_url(admin_url('edit.php?post_type=shop_order&_customer_user=' . $user->ID)); ?>" style="font-size:11px;display:block;"><?php esc_html_e('view', 'sco-investor'); ?></a>
					<?php endif; ?>
				</td>
				<?php endif; ?>
				<?php if ($has_subs) : ?>
				<td><?php echo $has_active_sub ? '<span style="color:#00a32a;font-weight:600;">✓ ' . esc_html__('Active', 'sco-investor') . '</span>' : '<span class="description">' . esc_html__('None', 'sco-investor') . '</span>'; ?></td>
				<?php endif; ?>
				<td><a href="<?php echo esc_url($detail_url); ?>" class="button button-small"><?php esc_html_e('Details', 'sco-investor'); ?></a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($total_pages > 1) : ?>
	<div class="tablenav bottom"><div class="tablenav-pages">
		<?php echo paginate_links([
			'base'      => add_query_arg('paged', '%#%'),
			'format'    => '',
			'current'   => $current_page,
			'total'     => $total_pages,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
		]); ?>
	</div></div>
	<?php endif; ?>
	</div>
	<?php
}

function sci_render_member_detail($user_id) {
	$user = get_user_by('id', $user_id);
	if (!$user) {
		echo '<div class="notice notice-error"><p>' . esc_html__('User not found.', 'sco-investor') . '</p></div>';
		return;
	}

	// Handle grant/revoke form submit
	if (isset($_POST['_sci_access_nonce']) && wp_verify_nonce($_POST['_sci_access_nonce'], 'sci_access_' . $user_id)) {
		if (!current_user_can('manage_options')) wp_die('Not allowed.');
		$post_id = absint($_POST['sci_post_id'] ?? 0);
		$action  = sanitize_text_field($_POST['sci_action'] ?? '');
		if ($post_id && in_array($action, ['grant', 'revoke'], true)) {
			if ($action === 'grant') {
				update_user_meta($user_id, '_sci_access_' . $post_id, '1');
				$notice = __('Access granted.', 'sco-investor');
			} else {
				delete_user_meta($user_id, '_sci_access_' . $post_id);
				$notice = __('Manual access revoked.', 'sco-investor');
			}
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notice) . '</p></div>';
		}
	}

	$back_url      = admin_url('admin.php?page=sci-members');
	$has_subs      = function_exists('wcs_get_users_subscriptions');
	$all_courses   = get_posts(['post_type' => 'course',   'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true]);
	$all_materials = get_posts(['post_type' => 'material', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true]);

	$role_names = array_map('translate_user_role', array_map(fn($r) => wp_roles()->get_names()[$r] ?? $r, $user->roles));

	// Subscription info
	$active_subs = [];
	if ($has_subs) {
		foreach (wcs_get_users_subscriptions($user_id) as $sub) {
			if ($sub->get_status() === 'active') $active_subs[] = $sub;
		}
	}
	?>
	<div class="wrap sci-members-wrap">
	<h1>
		<a href="<?php echo esc_url($back_url); ?>" style="font-size:14px;font-weight:normal;text-decoration:none;margin-right:12px;">← <?php esc_html_e('All Members', 'sco-investor'); ?></a>
		<?php echo esc_html($user->display_name ?: $user->user_login); ?>
	</h1>

	<div class="sci-member-grid">

		<!-- User info -->
		<div class="postbox">
			<div class="postbox-header"><h2 class="hndle"><?php esc_html_e('User Info', 'sco-investor'); ?></h2></div>
			<div class="inside">
				<table class="form-table" style="margin:0;">
					<tr><th><?php esc_html_e('Email', 'sco-investor'); ?></th><td><?php echo esc_html($user->user_email); ?></td></tr>
					<tr><th><?php esc_html_e('Registered', 'sco-investor'); ?></th><td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($user->user_registered))); ?></td></tr>
					<tr><th><?php esc_html_e('Role(s)', 'sco-investor'); ?></th><td><?php echo esc_html(implode(', ', $role_names)); ?></td></tr>
					<?php if (sci_has_woocommerce()) :
						$order_count = wc_get_customer_order_count($user_id); ?>
					<tr>
						<th><?php esc_html_e('Orders', 'sco-investor'); ?></th>
						<td><?php echo esc_html($order_count); ?>
							<?php if ($order_count > 0) : ?>
							&nbsp;<a href="<?php echo esc_url(admin_url('edit.php?post_type=shop_order&_customer_user=' . $user_id)); ?>"><?php esc_html_e('View orders →', 'sco-investor'); ?></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endif; ?>
					<?php if ($has_subs) : ?>
					<tr>
						<th><?php esc_html_e('Subscriptions', 'sco-investor'); ?></th>
						<td>
							<?php if ($active_subs) : ?>
								<?php foreach ($active_subs as $sub) : ?>
								<div style="color:#00a32a;font-weight:600;">✓ <?php echo esc_html($sub->get_formatted_order_total()); ?> &mdash; <?php esc_html_e('Active', 'sco-investor'); ?></div>
								<?php endforeach; ?>
							<?php else : ?>
								<span class="description"><?php esc_html_e('No active subscriptions', 'sco-investor'); ?></span>
							<?php endif; ?>
							<a href="<?php echo esc_url(admin_url('edit.php?post_type=shop_subscription&_customer_user=' . $user_id)); ?>" style="font-size:12px;display:block;margin-top:4px;"><?php esc_html_e('All subscriptions →', 'sco-investor'); ?></a>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				<p style="margin-top:12px;">
					<a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user_id)); ?>" class="button"><?php esc_html_e('Edit User Profile', 'sco-investor'); ?></a>
				</p>
			</div>
		</div>

		<!-- Course access -->
		<div class="postbox" style="grid-column: 1 / -1;">
			<div class="postbox-header"><h2 class="hndle">📚 <?php esc_html_e('Course Access', 'sco-investor'); ?></h2></div>
			<div class="inside" style="padding:0;">
				<table class="wp-list-table widefat fixed">
					<thead><tr>
						<th><?php esc_html_e('Course', 'sco-investor'); ?></th>
						<th style="width:130px;"><?php esc_html_e('Status', 'sco-investor'); ?></th>
						<th style="width:160px;"><?php esc_html_e('How granted', 'sco-investor'); ?></th>
						<th style="width:160px;"><?php esc_html_e('Action', 'sco-investor'); ?></th>
					</tr></thead>
					<tbody>
					<?php if (empty($all_courses)) : ?>
						<tr><td colspan="4"><em><?php esc_html_e('No courses yet. Add courses from the Courses menu.', 'sco-investor'); ?></em></td></tr>
					<?php endif; ?>
					<?php foreach ($all_courses as $course) :
						$product_id   = sci_linked_product_id($course->ID);
						$has_purchase = $product_id && sci_has_woocommerce() && wc_customer_bought_product($user->user_email, $user_id, $product_id);
						$has_sub      = !$has_purchase && $product_id && $has_subs && sci_subscription_grants_access($user_id, $product_id);
						$has_manual   = (bool) get_user_meta($user_id, '_sci_access_' . $course->ID, true);
						$has_access   = $has_purchase || $has_sub || $has_manual || !$product_id;
						?>
						<tr>
							<td><a href="<?php echo esc_url(get_edit_post_link($course->ID)); ?>"><?php echo esc_html($course->post_title); ?></a></td>
							<td>
								<?php if (!$product_id) : ?>
									<span style="color:#646970;"><?php esc_html_e('Free (no paywall)', 'sco-investor'); ?></span>
								<?php elseif ($has_access) : ?>
									<span style="color:#00a32a;font-weight:600;">✓ <?php esc_html_e('Has access', 'sco-investor'); ?></span>
								<?php else : ?>
									<span style="color:#c0392b;"><?php esc_html_e('No access', 'sco-investor'); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($has_purchase) esc_html_e('Purchased', 'sco-investor'); ?>
								<?php if ($has_sub) esc_html_e('Active subscription', 'sco-investor'); ?>
								<?php if ($has_manual) esc_html_e('Admin grant', 'sco-investor'); ?>
								<?php if (!$product_id) echo '—'; ?>
							</td>
							<td>
								<?php if ($product_id && !$has_purchase && !$has_sub) : ?>
									<form method="post" style="display:inline;">
										<?php wp_nonce_field('sci_access_' . $user_id, '_sci_access_nonce'); ?>
										<input type="hidden" name="sci_post_id" value="<?php echo esc_attr($course->ID); ?>">
										<?php if (!$has_manual) : ?>
											<input type="hidden" name="sci_action" value="grant">
											<button type="submit" class="button button-primary button-small"><?php esc_html_e('Grant Access', 'sco-investor'); ?></button>
										<?php else : ?>
											<input type="hidden" name="sci_action" value="revoke">
											<button type="submit" class="button button-small" style="color:#c0392b;"><?php esc_html_e('Revoke Grant', 'sco-investor'); ?></button>
										<?php endif; ?>
									</form>
								<?php elseif ($has_purchase || $has_sub) : ?>
									<span class="description"><?php esc_html_e('Via order/subscription', 'sco-investor'); ?></span>
								<?php else : ?>
									<span class="description">—</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Material access -->
		<div class="postbox" style="grid-column: 1 / -1;">
			<div class="postbox-header"><h2 class="hndle">📄 <?php esc_html_e('Material Access', 'sco-investor'); ?></h2></div>
			<div class="inside" style="padding:0;">
				<table class="wp-list-table widefat fixed">
					<thead><tr>
						<th><?php esc_html_e('Material', 'sco-investor'); ?></th>
						<th style="width:130px;"><?php esc_html_e('Status', 'sco-investor'); ?></th>
						<th style="width:160px;"><?php esc_html_e('How granted', 'sco-investor'); ?></th>
						<th style="width:160px;"><?php esc_html_e('Action', 'sco-investor'); ?></th>
					</tr></thead>
					<tbody>
					<?php if (empty($all_materials)) : ?>
						<tr><td colspan="4"><em><?php esc_html_e('No materials yet. Add materials from the Materials menu.', 'sco-investor'); ?></em></td></tr>
					<?php endif; ?>
					<?php foreach ($all_materials as $material) :
						$product_id   = sci_linked_product_id($material->ID);
						$has_purchase = $product_id && sci_has_woocommerce() && wc_customer_bought_product($user->user_email, $user_id, $product_id);
						$has_sub      = !$has_purchase && $product_id && $has_subs && sci_subscription_grants_access($user_id, $product_id);
						$has_manual   = (bool) get_user_meta($user_id, '_sci_access_' . $material->ID, true);
						$has_access   = $has_purchase || $has_sub || $has_manual || !$product_id;
						?>
						<tr>
							<td><a href="<?php echo esc_url(get_edit_post_link($material->ID)); ?>"><?php echo esc_html($material->post_title); ?></a></td>
							<td>
								<?php if (!$product_id) : ?>
									<span style="color:#646970;"><?php esc_html_e('Free', 'sco-investor'); ?></span>
								<?php elseif ($has_access) : ?>
									<span style="color:#00a32a;font-weight:600;">✓ <?php esc_html_e('Has access', 'sco-investor'); ?></span>
								<?php else : ?>
									<span style="color:#c0392b;"><?php esc_html_e('No access', 'sco-investor'); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($has_purchase) esc_html_e('Purchased', 'sco-investor'); ?>
								<?php if ($has_sub) esc_html_e('Active subscription', 'sco-investor'); ?>
								<?php if ($has_manual) esc_html_e('Admin grant', 'sco-investor'); ?>
								<?php if (!$product_id) echo '—'; ?>
							</td>
							<td>
								<?php if ($product_id && !$has_purchase && !$has_sub) : ?>
									<form method="post" style="display:inline;">
										<?php wp_nonce_field('sci_access_' . $user_id, '_sci_access_nonce'); ?>
										<input type="hidden" name="sci_post_id" value="<?php echo esc_attr($material->ID); ?>">
										<?php if (!$has_manual) : ?>
											<input type="hidden" name="sci_action" value="grant">
											<button type="submit" class="button button-primary button-small"><?php esc_html_e('Grant Access', 'sco-investor'); ?></button>
										<?php else : ?>
											<input type="hidden" name="sci_action" value="revoke">
											<button type="submit" class="button button-small" style="color:#c0392b;"><?php esc_html_e('Revoke Grant', 'sco-investor'); ?></button>
										<?php endif; ?>
									</form>
								<?php else : ?>
									<span class="description">—</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

	</div><!-- .sci-member-grid -->
	</div><!-- .wrap -->

	<style>
	.sci-member-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 16px; max-width: 1200px; }
	.sci-member-grid .postbox { margin: 0; }
	@media (max-width: 780px) { .sci-member-grid { grid-template-columns: 1fr; } }
	</style>
	<?php
}

/**
 * CSV export of all members with their purchase summary.
 */
function sci_members_export_csv() {
	if (!current_user_can('manage_options')) wp_die('Not allowed.');
	$users = get_users(['orderby' => 'registered', 'order' => 'DESC']);
	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename="sci-members-' . gmdate('Y-m-d') . '.csv"');
	header('Pragma: no-cache');
	$out = fopen('php://output', 'w');
	fputcsv($out, ['Name', 'Email', 'Registered', 'Role(s)', 'Total Orders', 'Subscriptions Active']);
	foreach ($users as $user) {
		$roles        = implode(', ', array_map('translate_user_role', array_map(fn($r) => wp_roles()->get_names()[$r] ?? $r, $user->roles)));
		$orders       = sci_has_woocommerce() ? wc_get_customer_order_count($user->ID) : 'N/A';
		$subs_active  = 0;
		if (function_exists('wcs_get_users_subscriptions')) {
			foreach (wcs_get_users_subscriptions($user->ID) as $s) {
				if ($s->get_status() === 'active') $subs_active++;
			}
		}
		fputcsv($out, [$user->display_name, $user->user_email, $user->user_registered, $roles, $orders, $subs_active]);
	}
	fclose($out);
	exit;
}

