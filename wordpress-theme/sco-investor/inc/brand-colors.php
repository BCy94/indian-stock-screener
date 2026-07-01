<?php
/**
 * Turns the Brand Colors Customizer settings (inc/customizer.php) into an
 * actual CSS override. The owner only ever picks one accent color; its
 * hover-state and translucent-background variants are derived here so the
 * three always stay visually coordinated instead of asking a non-designer
 * to hand-match three separate hex codes.
 */

if (!defined('ABSPATH')) exit;

/**
 * #abc or #aabbcc -> [r, g, b]. Falls back to the theme's default bronze
 * on anything malformed — sanitize_hex_color() already guards what gets
 * saved, this is just defense in depth for a stray get_theme_mod() call.
 */
function sci_hex_to_rgb($hex) {
	$hex = ltrim((string) $hex, '#');
	if (strlen($hex) === 3) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
		return [201, 162, 75];
	}
	return array_map('hexdec', str_split($hex, 2));
}

/**
 * Darkens a hex color by $percent — used to derive the accent's hover/dim
 * shade from the single color the owner actually picks.
 */
function sci_darken_hex($hex, $percent = 18) {
	[$r, $g, $b] = sci_hex_to_rgb($hex);
	$factor = 1 - ($percent / 100);
	return sprintf(
		'#%02x%02x%02x',
		(int) max(0, round($r * $factor)),
		(int) max(0, round($g * $factor)),
		(int) max(0, round($b * $factor))
	);
}

/**
 * Overrides the :root / [data-theme="light"] custom properties defined in
 * assets/css/main.css. Printed after the main stylesheet so the cascade
 * favors these values; always output (even at defaults) so there's a
 * single, predictable source of truth instead of two code paths.
 */
function sci_brand_colors_css() {
	$accent   = get_theme_mod('sci_color_accent', '#C9A24B');
	$up       = get_theme_mod('sci_color_up', '#3F8F5F');
	$down     = get_theme_mod('sci_color_down', '#B0473E');
	$dark_bg  = get_theme_mod('sci_color_dark_bg', '#14171F');
	$light_bg = get_theme_mod('sci_color_light_bg', '#F6F1E7');

	[$ar, $ag, $ab] = sci_hex_to_rgb($accent);
	$accent_dim  = sci_darken_hex($accent, 18);
	$accent_soft = sprintf('rgba(%d, %d, %d, 0.14)', $ar, $ag, $ab);

	echo '<style id="sci-brand-colors">';
	printf(
		':root{--accent:%1$s;--accent-dim:%2$s;--accent-soft:%3$s;--up:%4$s;--down:%5$s;--bg:%6$s;}',
		esc_html($accent),
		esc_html($accent_dim),
		esc_html($accent_soft),
		esc_html($up),
		esc_html($down),
		esc_html($dark_bg)
	);
	printf('[data-theme="light"]{--bg:%s;}', esc_html($light_bg));
	echo '</style>' . "\n";
}
add_action('wp_head', 'sci_brand_colors_css', 20);
