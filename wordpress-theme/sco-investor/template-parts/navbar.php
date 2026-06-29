<?php
/**
 * Site navbar + mobile slide-down panel.
 */
?>
<nav class="navbar" id="navbar">
	<div class="container nav-inner">
		<?php sci_site_brand(); ?>

		<?php if (has_nav_menu('primary')) : ?>
			<div class="nav-links">
				<?php
				wp_nav_menu([
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'walker'         => new SCI_Walker_Nav_Flat(),
				]);
				?>
			</div>
		<?php else : ?>
			<?php sci_primary_nav_fallback(); ?>
		<?php endif; ?>

		<div class="nav-actions">
			<button class="theme-toggle" aria-label="<?php esc_attr_e('Toggle theme', 'sco-investor'); ?>">
				<svg class="icon-moon" viewBox="0 0 24 24" fill="none"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				<svg class="icon-sun" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
			</button>
			<a href="<?php echo esc_url(sci_contact_url()); ?>" class="btn btn-primary btn-sm"><?php esc_html_e('Get Started', 'sco-investor'); ?></a>
			<button class="nav-burger" aria-label="<?php esc_attr_e('Menu', 'sco-investor'); ?>" aria-expanded="false" aria-controls="mobile-panel"><span></span><span></span><span></span></button>
		</div>
	</div>
</nav>
<div class="mobile-panel" id="mobile-panel">
	<?php if (has_nav_menu('primary')) : ?>
		<?php
		wp_nav_menu([
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'walker'         => new SCI_Walker_Nav_Flat(),
		]);
		?>
		<a href="<?php echo esc_url(sci_contact_url()); ?>" class="btn btn-primary btn-block"><?php esc_html_e('Get Started', 'sco-investor'); ?></a>
	<?php else : ?>
		<?php sci_primary_nav_fallback(true); ?>
	<?php endif; ?>
</div>
