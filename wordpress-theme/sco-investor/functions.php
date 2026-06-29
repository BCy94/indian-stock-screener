<?php
/**
 * So Called Investor theme bootstrap.
 */

if (!defined('ABSPATH')) exit;

define('SCI_VERSION', '1.0.0');
define('SCI_THEME_DIR', get_template_directory());
define('SCI_THEME_URI', get_template_directory_uri());

require SCI_THEME_DIR . '/inc/setup.php';
require SCI_THEME_DIR . '/inc/customizer.php';
require SCI_THEME_DIR . '/inc/template-tags.php';
require SCI_THEME_DIR . '/inc/seo.php';
require SCI_THEME_DIR . '/inc/schema.php';
require SCI_THEME_DIR . '/inc/cpt-course.php';
require SCI_THEME_DIR . '/inc/cpt-material.php';
require SCI_THEME_DIR . '/inc/cpt-ticker.php';
require SCI_THEME_DIR . '/inc/commerce.php';
require SCI_THEME_DIR . '/inc/meta-boxes.php';
require SCI_THEME_DIR . '/inc/blocks.php';
require SCI_THEME_DIR . '/inc/block-styles.php';
require SCI_THEME_DIR . '/inc/block-patterns.php';
require SCI_THEME_DIR . '/inc/contact-form.php';
require SCI_THEME_DIR . '/inc/widgets.php';
