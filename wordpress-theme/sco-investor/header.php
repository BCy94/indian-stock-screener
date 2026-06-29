<!DOCTYPE html>
<html lang="<?php echo esc_attr(str_replace('_', '-', get_locale())); ?>">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script>(function(){try{var t=localStorage.getItem('sci-theme');if(t==='light')document.documentElement.setAttribute('data-theme','light');}catch(e){}})();</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'sco-investor'); ?></a>

<div class="grid-overlay"></div>

<?php get_template_part('template-parts/navbar'); ?>

<main id="main">
