<?php
/**
 * Single material — kept thin per the plan: a focused page around the one
 * download/open CTA, not a full sales-page layout like courses get.
 */
get_header();

while (have_posts()) : the_post();
	$material_id    = get_the_ID();
	$type           = get_post_meta($material_id, '_sci_material_type', true) ?: 'other';
	$link           = sci_material_link($material_id);
	$is_tool        = $type === 'tool';
	$price          = get_post_meta($material_id, '_sci_price', true);
	$price_original = get_post_meta($material_id, '_sci_price_original', true);
?>

<header class="page-hero" style="padding-bottom:48px;">
	<div class="container">
		<div class="breadcrumb">
			<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'sco-investor'); ?></a><span>/</span>
			<a href="<?php echo esc_url(sci_materials_url()); ?>"><?php esc_html_e('Materials', 'sco-investor'); ?></a><span>/</span>
			<span><?php the_title(); ?></span>
		</div>
		<h1 style="max-width:780px;margin:18px auto;"><?php the_title(); ?></h1>
	</div>
</header>

<section class="section section--tight">
	<div class="container" style="max-width:760px;">
		<div class="glass" style="padding:28px 32px;display:flex;gap:18px;align-items:center;flex-wrap:wrap;justify-content:space-between;margin-bottom:32px;">
			<div style="display:flex;gap:16px;align-items:center;">
				<div class="material-icon<?php echo has_post_thumbnail($material_id) ? ' material-icon--photo' : ''; ?>">
					<?php if (has_post_thumbnail($material_id)) : ?>
						<?php echo get_the_post_thumbnail($material_id, 'thumbnail'); ?>
					<?php else : ?>
						<?php echo sci_material_icon($type); ?>
					<?php endif; ?>
				</div>
				<span class="badge<?php echo $is_tool ? ' badge-gold' : ''; ?>"><?php echo esc_html(sci_material_badge($material_id)); ?></span>
			</div>
			<?php get_template_part('template-parts/price', null, ['price' => $price, 'price_original' => $price_original]); ?>
			<?php echo sci_material_cta_html($material_id, $link, $type, '', false); ?>
		</div>

		<?php if (get_the_content()) : ?>
			<div class="post-content reveal">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
