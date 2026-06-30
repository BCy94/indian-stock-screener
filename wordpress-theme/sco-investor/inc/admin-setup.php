<?php
/**
 * SCI Setup Guide — an admin page that walks the site owner through
 * every configuration step: Customizer, menus, WooCommerce payments,
 * My Account page, and content security. Answers the question "where do
 * I configure payments / profile / etc." without requiring any code.
 */

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'sci_register_setup_page');
function sci_register_setup_page() {
	add_menu_page(
		__('SCI Setup Guide', 'sco-investor'),
		__('SCI Setup', 'sco-investor'),
		'manage_options',
		'sci-setup',
		'sci_render_setup_page',
		'dashicons-welcome-learn-more',
		3
	);
}

function sci_render_setup_page() {
	$has_woo = sci_has_woocommerce();
	$course_count    = wp_count_posts('course')->publish ?? 0;
	$material_count  = wp_count_posts('material')->publish ?? 0;
	$testi_count     = wp_count_posts('testimonial')->publish ?? 0;
	$has_primary_nav = has_nav_menu('primary');

	$woo_installed_class   = $has_woo ? 'sci-step--done' : 'sci-step--todo';
	$woo_installed_badge   = $has_woo ? __('✓ Installed', 'sco-investor') : __('Not installed yet', 'sco-investor');
	?>
	<div class="wrap sci-setup-wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e('So Called Investor — Setup Guide', 'sco-investor'); ?></h1>
		<p class="sci-setup-intro"><?php esc_html_e('Everything you need to configure your site — no coding required. Work through the sections below from top to bottom on a fresh install.', 'sco-investor'); ?></p>

		<div class="sci-setup-grid">

			<!-- APPEARANCE -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">🎨 <?php esc_html_e('1 — Appearance & Content', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('All text, colours, images and section toggles are controlled from Appearance → Customize. No PHP editing needed.', 'sco-investor'); ?></p>
					<table class="widefat striped" style="margin-bottom:12px;">
						<tbody>
							<tr>
								<td><strong><?php esc_html_e('Homepage — Hero', 'sco-investor'); ?></strong><br><small><?php esc_html_e('Heading, lead text, CTA buttons, trust badges', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_hero')); ?>" class="button"><?php esc_html_e('Edit Hero', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Homepage — Stats Row', 'sco-investor'); ?></strong><br><small><?php esc_html_e('The 4 animated numbers below the hero', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_stats')); ?>" class="button"><?php esc_html_e('Edit Stats', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Homepage — Why Us Section', 'sco-investor'); ?></strong><br><small><?php esc_html_e('3 feature cards and their headings', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_features')); ?>" class="button"><?php esc_html_e('Edit Features', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Homepage — CTA Band', 'sco-investor'); ?></strong><br><small><?php esc_html_e('Bottom call-to-action strip', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_cta_band')); ?>" class="button"><?php esc_html_e('Edit CTA', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Homepage — Section Toggles', 'sco-investor'); ?></strong><br><small><?php esc_html_e('Show or hide each homepage section with one click', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_homepage_sections')); ?>" class="button"><?php esc_html_e('Toggle Sections', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Footer', 'sco-investor'); ?></strong><br><small><?php esc_html_e('Tagline, copyright text, newsletter wording, social links', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_footer')); ?>" class="button"><?php esc_html_e('Edit Footer', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Logo & Site Identity', 'sco-investor'); ?></strong><br><small><?php esc_html_e('Upload your own logo, set the site name, tagline', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=title_tagline')); ?>" class="button"><?php esc_html_e('Set Logo', 'sco-investor'); ?></a></td>
							</tr>
							<tr>
								<td><strong><?php esc_html_e('Navigation Menus', 'sco-investor'); ?></strong><br><small><?php esc_html_e('Primary nav, footer Explore and Support columns', 'sco-investor'); ?></small></td>
								<td><a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="button"><?php esc_html_e('Edit Menus', 'sco-investor'); ?></a></td>
							</tr>
						</tbody>
					</table>

					<h4 style="margin-top:16px;"><?php esc_html_e('Your content', 'sco-investor'); ?></h4>
					<p>
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=course')); ?>" class="button button-primary"><?php esc_html_e('Add Course', 'sco-investor'); ?></a>
						<a href="<?php echo esc_url(admin_url('edit.php?post_type=course')); ?>" class="button" style="margin-left:6px;"><?php printf(esc_html__('All Courses (%d)', 'sco-investor'), $course_count); ?></a>
						&nbsp;
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=material')); ?>" class="button button-primary" style="margin-left:12px;"><?php esc_html_e('Add Material', 'sco-investor'); ?></a>
						<a href="<?php echo esc_url(admin_url('edit.php?post_type=material')); ?>" class="button" style="margin-left:6px;"><?php printf(esc_html__('All Materials (%d)', 'sco-investor'), $material_count); ?></a>
						&nbsp;
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=testimonial')); ?>" class="button button-primary" style="margin-left:12px;"><?php esc_html_e('Add Testimonial', 'sco-investor'); ?></a>
						<a href="<?php echo esc_url(admin_url('edit.php?post_type=testimonial')); ?>" class="button" style="margin-left:6px;"><?php printf(esc_html__('Testimonials (%d)', 'sco-investor'), $testi_count); ?></a>
					</p>
				</div>
			</div>

			<!-- PAYMENTS -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">💳 <?php esc_html_e('2 — Payments & Checkout', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('The theme is built to work with WooCommerce for all payments. You pick the payment gateway (Razorpay, Stripe, PayPal, CCAvenue, etc.) — the theme handles gating content automatically once a course or material is linked to a product.', 'sco-investor'); ?></p>

					<ol class="sci-steps">
						<li class="<?php echo esc_attr($woo_installed_class); ?>">
							<strong><?php esc_html_e('Install WooCommerce', 'sco-investor'); ?></strong>
							<span class="sci-badge"><?php echo esc_html($woo_installed_badge); ?></span><br>
							<?php if (!$has_woo) : ?>
								<a href="<?php echo esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')); ?>" class="button button-primary"><?php esc_html_e('Install WooCommerce', 'sco-investor'); ?></a>
							<?php else : ?>
								<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings')); ?>" class="button"><?php esc_html_e('WooCommerce Settings', 'sco-investor'); ?></a>
							<?php endif; ?>
						</li>

						<li class="sci-step--todo">
							<strong><?php esc_html_e('Install a Payment Gateway Plugin', 'sco-investor'); ?></strong><br>
							<span class="description"><?php esc_html_e('For Indian payments (UPI, cards, net banking): install Razorpay for WooCommerce. For international: Stripe for WooCommerce. After installing, go to WooCommerce → Settings → Payments to activate and enter your API keys.', 'sco-investor'); ?></span><br>
							<div style="margin-top:8px;">
								<a href="<?php echo esc_url(admin_url('plugin-install.php?s=razorpay+woocommerce&tab=search&type=term')); ?>" class="button" style="margin-top:4px;"><?php esc_html_e('Find Razorpay Plugin', 'sco-investor'); ?></a>
								<a href="<?php echo esc_url(admin_url('plugin-install.php?s=stripe+woocommerce&tab=search&type=term')); ?>" class="button" style="margin-top:4px;margin-left:6px;"><?php esc_html_e('Find Stripe Plugin', 'sco-investor'); ?></a>
								<?php if ($has_woo) : ?>
								<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=checkout')); ?>" class="button button-primary" style="margin-top:4px;margin-left:6px;"><?php esc_html_e('Configure Payments', 'sco-investor'); ?></a>
								<?php endif; ?>
							</div>
						</li>

						<li class="sci-step--todo">
							<strong><?php esc_html_e('Create a WooCommerce Product for each paid course/material', 'sco-investor'); ?></strong><br>
							<span class="description"><?php esc_html_e('Go to Products → Add New. Set the product name (e.g. "Fundamental Analysis Course"), set a price, and publish. You don\'t need to configure shipping — these are digital goods, so mark it as "Virtual".', 'sco-investor'); ?></span><br>
							<?php if ($has_woo) : ?>
								<a href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>" class="button button-primary" style="margin-top:8px;"><?php esc_html_e('Add Product', 'sco-investor'); ?></a>
							<?php endif; ?>
						</li>

						<li class="sci-step--todo">
							<strong><?php esc_html_e('Link the product to your course or material', 'sco-investor'); ?></strong><br>
							<span class="description"><?php esc_html_e('Edit a Course or Material in wp-admin. In the "Course Details" (or "Material Details") box, find the "Linked Product (WooCommerce)" dropdown and select the product you just created. Save. Done — the theme now shows a real Buy Now button and automatically unlocks the course for anyone who completes purchase.', 'sco-investor'); ?></span><br>
							<div style="margin-top:8px;">
								<a href="<?php echo esc_url(admin_url('edit.php?post_type=course')); ?>" class="button" style="margin-top:4px;"><?php esc_html_e('Go to Courses', 'sco-investor'); ?></a>
								<a href="<?php echo esc_url(admin_url('edit.php?post_type=material')); ?>" class="button" style="margin-top:4px;margin-left:6px;"><?php esc_html_e('Go to Materials', 'sco-investor'); ?></a>
							</div>
						</li>
					</ol>
				</div>
			</div>

			<!-- USER PROFILE / MY ACCOUNT -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">👤 <?php esc_html_e('3 — User Profile & My Account', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<?php if ($has_woo) : ?>
						<p><?php esc_html_e('WooCommerce automatically creates a "My Account" page when you run its setup wizard. This page gives every registered user a profile, order history, and the theme automatically adds two extra tabs:', 'sco-investor'); ?></p>
						<ul>
							<li>📚 <strong><?php esc_html_e('My Courses', 'sco-investor'); ?></strong> — <?php esc_html_e('lists every course the user has purchased', 'sco-investor'); ?></li>
							<li>📄 <strong><?php esc_html_e('My Materials', 'sco-investor'); ?></strong> — <?php esc_html_e('lists every paid material the user has purchased', 'sco-investor'); ?></li>
						</ul>
						<?php
						$myaccount_id = (int) get_option('woocommerce_myaccount_page_id');
						if ($myaccount_id) :
							$myaccount_url = get_permalink($myaccount_id);
						?>
							<p>
								<a href="<?php echo esc_url($myaccount_url); ?>" class="button button-primary" target="_blank"><?php esc_html_e('View My Account Page', 'sco-investor'); ?></a>
								<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=account')); ?>" class="button" style="margin-left:6px;"><?php esc_html_e('Account Settings', 'sco-investor'); ?></a>
							</p>
						<?php else : ?>
							<div class="notice notice-warning inline"><p><?php esc_html_e('WooCommerce My Account page is not set yet. Run the WooCommerce setup wizard or go to WooCommerce → Settings → Advanced → Page setup to create it.', 'sco-investor'); ?></p></div>
							<p><a href="<?php echo esc_url(admin_url('admin.php?page=wc-setup')); ?>" class="button button-primary"><?php esc_html_e('Run WooCommerce Setup Wizard', 'sco-investor'); ?></a></p>
						<?php endif; ?>
					<?php else : ?>
						<p><?php esc_html_e('User profiles and My Account pages are handled by WooCommerce. Install and activate WooCommerce (Step 1 above) to unlock profile pages, purchase history, and the My Courses / My Materials tabs for your users.', 'sco-investor'); ?></p>
						<p><a href="<?php echo esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')); ?>" class="button button-primary"><?php esc_html_e('Install WooCommerce', 'sco-investor'); ?></a></p>
					<?php endif; ?>
				</div>
			</div>

			<!-- CONTENT SECURITY -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">🔒 <?php esc_html_e('4 — Content Security', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('The theme automatically protects paid content once you link a course or material to a WooCommerce product:', 'sco-investor'); ?></p>
					<ul>
						<li>✓ <?php esc_html_e('Course pages show a "Buy Now" button — the full content is visible only after purchase.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Material download links are hidden — clicking "Buy Now" goes straight to checkout.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Paid file downloads go through a secure endpoint — the real file URL never appears in page source.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Admins and editors always see everything, regardless of purchase status.', 'sco-investor'); ?></li>
					</ul>
					<p class="description"><?php esc_html_e('Courses and materials with no linked product remain freely accessible — perfect for your free preview content.', 'sco-investor'); ?></p>
				</div>
			</div>

			<!-- SEO & AI -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">📊 <?php esc_html_e('5 — SEO & AI Crawler Access', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<ul>
						<li>✓ <?php esc_html_e('Automatic meta description, Open Graph (Facebook/WhatsApp) and Twitter Card tags on every page.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('JSON-LD structured data: Organization, BreadcrumbList, Article (blog posts), Course (course pages), FAQPage (calculator page).', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Default social share image — set it in Appearance → Customize → Images.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('WordPress sitemap at /wp-sitemap.xml — automatically updated as you publish content.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('AI crawlers explicitly allowed: GPTBot (ChatGPT), ClaudeBot (Claude), Google-Extended (Gemini) all have Allow: / in robots.txt.', 'sco-investor'); ?></li>
					</ul>
					<p class="description"><?php esc_html_e('If you install Yoast SEO, RankMath or All-in-One SEO, the theme\'s built-in SEO module automatically steps aside so they don\'t conflict.', 'sco-investor'); ?></p>
					<p><a href="<?php echo esc_url(home_url('/wp-sitemap.xml')); ?>" target="_blank" class="button"><?php esc_html_e('View Sitemap', 'sco-investor'); ?></a></p>
				</div>
			</div>

			<!-- STOCK TICKER -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">📈 <?php esc_html_e('6 — Stock Ticker Tape', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('The scrolling ticker that runs below the hero is fully editable from the WordPress admin — no code, no external data feed required.', 'sco-investor'); ?></p>
					<ul>
						<li><?php esc_html_e('Go to Stock Tickers in the left admin menu.', 'sco-investor'); ?></li>
						<li><?php esc_html_e('Each ticker item has: Symbol (e.g. TATAMOTORS), Price, Change % (positive or negative), and optional full company name.', 'sco-investor'); ?></li>
						<li><?php esc_html_e('Update prices manually or set up an automation tool to update them periodically.', 'sco-investor'); ?></li>
						<li><?php esc_html_e('Toggle the ticker on/off from Appearance → Customize → Homepage Sections.', 'sco-investor'); ?></li>
					</ul>
					<p>
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=sci_ticker')); ?>" class="button button-primary"><?php esc_html_e('Add Ticker Item', 'sco-investor'); ?></a>
						<a href="<?php echo esc_url(admin_url('edit.php?post_type=sci_ticker')); ?>" class="button" style="margin-left:6px;"><?php esc_html_e('Manage Ticker', 'sco-investor'); ?></a>
					</p>
				</div>
			</div>

			<!-- MEMBERS -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">👥 <?php esc_html_e('7 — Members & Access Control', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('View all registered users, see which courses and materials each person has access to, and manually grant or revoke access without requiring a purchase.', 'sco-investor'); ?></p>
					<ul>
						<li>✓ <?php esc_html_e('List all members with their registration date, role, order count, and subscription status.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Click any member to see a full access report — which courses/materials they own and how (purchase, subscription, or admin grant).', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Grant access to any course or material with one click — useful for comps, beta testers, or support resolutions.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Revoke manually-granted access at any time without touching their purchase history.', 'sco-investor'); ?></li>
						<li>✓ <?php esc_html_e('Export the full member list as a CSV file for reporting or email campaigns.', 'sco-investor'); ?></li>
					</ul>
					<p>
						<a href="<?php echo esc_url(admin_url('admin.php?page=sci-members')); ?>" class="button button-primary"><?php esc_html_e('View Members', 'sco-investor'); ?></a>
					</p>
				</div>
			</div>

			<!-- COUPONS -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">🏷️ <?php esc_html_e('8 — Discount Coupons', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<?php if ($has_woo) : ?>
						<p><?php esc_html_e('Create discount coupons for your courses and materials from WooCommerce\'s built-in coupon system. No extra plugin needed.', 'sco-investor'); ?></p>
						<ol class="sci-steps">
							<li class="sci-step--todo">
								<strong><?php esc_html_e('Go to Marketing → Coupons', 'sco-investor'); ?></strong><br>
								<span class="description"><?php esc_html_e('(In older WooCommerce versions this is WooCommerce → Coupons.)', 'sco-investor'); ?></span><br>
								<a href="<?php echo esc_url(admin_url('edit.php?post_type=shop_coupon')); ?>" class="button button-primary" style="margin-top:8px;"><?php esc_html_e('Go to Coupons', 'sco-investor'); ?></a>
							</li>
							<li class="sci-step--todo">
								<strong><?php esc_html_e('Click "Add coupon" and enter a code', 'sco-investor'); ?></strong><br>
								<span class="description"><?php esc_html_e('Use any code — e.g. LAUNCH50, WELCOME20. Share this code with your students and they enter it at checkout.', 'sco-investor'); ?></span>
							</li>
							<li class="sci-step--todo">
								<strong><?php esc_html_e('Choose the discount type', 'sco-investor'); ?></strong><br>
								<span class="description">
									<?php esc_html_e('"Percentage discount" (e.g. 20% off) or "Fixed cart discount" (e.g. ₹500 off). For courses, "Percentage" is usually easier since the price can change.', 'sco-investor'); ?>
								</span>
							</li>
							<li class="sci-step--todo">
								<strong><?php esc_html_e('Set usage limits', 'sco-investor'); ?></strong><br>
								<span class="description"><?php esc_html_e('Under "Usage limits": set "Usage limit per user: 1" so each person can only use the coupon once. Set a total usage limit if it\'s for a limited-time offer.', 'sco-investor'); ?></span>
							</li>
							<li class="sci-step--todo">
								<strong><?php esc_html_e('Restrict to specific products (optional)', 'sco-investor'); ?></strong><br>
								<span class="description"><?php esc_html_e('Under "Usage restriction": add the specific WooCommerce products this coupon applies to. Leave blank to allow on any product.', 'sco-investor'); ?></span>
							</li>
						</ol>
					<?php else : ?>
						<p><?php esc_html_e('Coupon management is built into WooCommerce. Install and activate WooCommerce (Step 2 above) to create percentage or fixed-amount discount codes for your courses and materials.', 'sco-investor'); ?></p>
						<p><a href="<?php echo esc_url(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term')); ?>" class="button button-primary"><?php esc_html_e('Install WooCommerce', 'sco-investor'); ?></a></p>
					<?php endif; ?>
				</div>
			</div>

			<!-- SUBSCRIPTIONS -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">🔁 <?php esc_html_e('9 — Recurring Subscriptions', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('Want to sell monthly/yearly access to your content instead of (or in addition to) one-time purchases? You need the WooCommerce Subscriptions plugin.', 'sco-investor'); ?></p>
					<?php
					$has_wcs = function_exists('wcs_get_users_subscriptions');
					if ($has_wcs) :
					?>
						<div class="notice notice-success inline"><p>✓ <?php esc_html_e('WooCommerce Subscriptions is active. When a user holds an active subscription that includes a linked product, the theme automatically unlocks that course or material for them — no manual steps needed.', 'sco-investor'); ?></p></div>
					<?php else : ?>
						<div class="notice notice-info inline"><p><?php esc_html_e('WooCommerce Subscriptions is not yet active. Without it, all sales are one-time purchases.', 'sco-investor'); ?></p></div>
					<?php endif; ?>
					<ol class="sci-steps">
						<li class="<?php echo $has_wcs ? 'sci-step--done' : 'sci-step--todo'; ?>">
							<strong><?php esc_html_e('Install WooCommerce Subscriptions', 'sco-investor'); ?></strong>
							<?php if ($has_wcs) : ?>
								<span class="sci-badge"><?php esc_html_e('✓ Active', 'sco-investor'); ?></span>
							<?php endif; ?>
							<br>
							<span class="description"><?php esc_html_e('WooCommerce Subscriptions is a paid plugin from WooCommerce.com (~$199/yr). It is the most reliable, widely-used subscription engine for WooCommerce. Purchase, download, and install it as a normal WordPress plugin.', 'sco-investor'); ?></span>
						</li>
						<li class="sci-step--todo">
							<strong><?php esc_html_e('Create a Subscription product', 'sco-investor'); ?></strong><br>
							<span class="description"><?php esc_html_e('After installing: go to Products → Add New → set product type to "Simple subscription". Set the billing interval (e.g. monthly), price, and trial period if any. Mark it as Virtual.', 'sco-investor'); ?></span>
						</li>
						<li class="sci-step--todo">
							<strong><?php esc_html_e('Link the subscription product to your courses/materials', 'sco-investor'); ?></strong><br>
							<span class="description"><?php esc_html_e('Edit a Course or Material → "Linked Product" dropdown → select the subscription product. The theme will automatically grant access to all users with an active subscription to that product.', 'sco-investor'); ?></span>
						</li>
					</ol>
				</div>
			</div>

			<!-- BLOG -->
			<div class="postbox">
				<div class="postbox-header"><h2 class="hndle">✍️ <?php esc_html_e('10 — Blog & Reading Settings', 'sco-investor'); ?></h2></div>
				<div class="inside">
					<p><?php esc_html_e('Set your homepage to the static front page and a separate page as your Blog listing:', 'sco-investor'); ?></p>
					<ol>
						<li><?php esc_html_e('Go to Settings → Reading.', 'sco-investor'); ?></li>
						<li><?php esc_html_e('Set "Your homepage displays" to "A static page".', 'sco-investor'); ?></li>
						<li><?php esc_html_e('Set "Homepage" to the page you want as your main page.', 'sco-investor'); ?></li>
						<li><?php esc_html_e('Set "Posts page" to any page you create for your Blog listing (slug: blog).', 'sco-investor'); ?></li>
					</ol>
					<p>
						<a href="<?php echo esc_url(admin_url('options-reading.php')); ?>" class="button button-primary"><?php esc_html_e('Reading Settings', 'sco-investor'); ?></a>
						<a href="<?php echo esc_url(admin_url('customize.php?autofocus[section]=sci_blog_settings')); ?>" class="button" style="margin-left:6px;"><?php esc_html_e('Blog Display Options', 'sco-investor'); ?></a>
					</p>
				</div>
			</div>

		</div>
	</div>

	<style>
	.sci-setup-wrap { max-width: 1100px; }
	.sci-setup-intro { font-size: 15px; color: #3c434a; margin-bottom: 20px; }
	.sci-setup-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
	@media (max-width: 900px) { .sci-setup-grid { grid-template-columns: 1fr; } }
	.sci-setup-grid .postbox { margin: 0; }
	.sci-setup-grid .postbox-header { background: #f6f7f7; border-bottom: 1px solid #dcdcde; }
	.sci-steps { margin: 0; padding: 0; list-style: none; counter-reset: sci-step; }
	.sci-steps > li { position: relative; padding: 12px 12px 12px 44px; border-left: 3px solid #dcdcde; margin-bottom: 12px; background: #f9f9f9; border-radius: 4px; }
	.sci-steps > li::before { counter-increment: sci-step; content: counter(sci-step); position: absolute; left: 12px; top: 12px; background: #646970; color: #fff; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; }
	.sci-step--done { border-left-color: #00a32a !important; background: #f0fdf4 !important; }
	.sci-step--done::before { background: #00a32a !important; }
	.sci-badge { display: inline-block; font-size: 12px; padding: 2px 8px; border-radius: 10px; background: #dcdcde; color: #3c434a; margin-left: 8px; vertical-align: middle; }
	.sci-step--done .sci-badge { background: #d1fae5; color: #065f46; }
	.sci-setup-grid ul li { margin-bottom: 6px; }
	</style>
	<?php
}
