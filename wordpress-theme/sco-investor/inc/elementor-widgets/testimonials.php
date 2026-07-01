<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('\Elementor\Widget_Base')) return;

class SCI_Widget_Testimonials extends \Elementor\Widget_Base {

	public function get_name()       { return 'sci_testimonials'; }
	public function get_title()      { return __('SCI — Testimonials', 'sco-investor'); }
	public function get_categories() { return ['sci']; }
	public function get_icon()       { return 'eicon-testimonial'; }
	public function get_keywords()   { return ['testimonials', 'reviews', 'social proof', 'sci']; }

	protected function register_controls() {

		$this->start_controls_section('section_heading', [
			'label' => __('Section Heading', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('eyebrow', [
			'label'       => __('Eyebrow', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __('STUDENT STORIES', 'sco-investor'),
			'placeholder' => __('Leave blank to hide', 'sco-investor'),
		]);

		$this->add_control('heading', [
			'label'       => __('Heading', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __('What Our Students Say', 'sco-investor'),
			'placeholder' => __('Leave blank to hide', 'sco-investor'),
		]);

		$this->end_controls_section();

		$this->start_controls_section('section_query', [
			'label' => __('Query', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('posts_per_page', [
			'label'   => __('Number of Testimonials', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 3,
			'min'     => 1,
			'max'     => 9,
		]);

		$this->add_control('columns', [
			'label'   => __('Columns', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '3',
			'options' => ['1' => '1', '2' => '2', '3' => '3'],
		]);

		$this->add_control('orderby', [
			'label'   => __('Order By', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'menu_order',
			'options' => [
				'menu_order' => __('Custom order (drag to reorder in wp-admin)', 'sco-investor'),
				'date'       => __('Newest first', 'sco-investor'),
				'rand'       => __('Random', 'sco-investor'),
			],
		]);

		$this->add_control('fallback_note', [
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw'  => '<p style="color:#555;font-size:12px;">' . sprintf(
				esc_html__('Testimonials are managed from %sTestimonials → Add New%s in the left admin menu. Set a photo via Featured Image, control display order with the Order field.', 'sco-investor'),
				'<strong>', '</strong>'
			) . '</p>',
		]);

		$this->end_controls_section();

		// ── Style tab ───────────────────────────────────────────────────────
		$this->start_controls_section('section_style', [
			'label' => __('Heading Style', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('style_eyebrow_color', [
			'label'     => __('Eyebrow Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .section-head .eyebrow' => 'color: {{VALUE}}'],
		]);

		$this->add_control('style_heading_color', [
			'label'     => __('Heading Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .section-head h2' => 'color: {{VALUE}}'],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$query = new WP_Query([
			'post_type'      => 'testimonial',
			'post_status'    => 'publish',
			'posts_per_page' => max(1, (int) $s['posts_per_page']),
			'orderby'        => $s['orderby'],
			'order'          => 'ASC',
			'no_found_rows'  => true,
		]);

		if (!empty($s['eyebrow']) || !empty($s['heading'])) : ?>
			<div class="section-head reveal">
				<?php if (!empty($s['eyebrow'])) : ?>
					<div class="eyebrow"><span class="dot"></span> <?php echo esc_html($s['eyebrow']); ?></div>
				<?php endif; ?>
				<?php if (!empty($s['heading'])) : ?>
					<h2><?php echo esc_html($s['heading']); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif;

		if (!$query->have_posts()) {
			if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
				echo '<p class="muted">' . esc_html__('No testimonials yet. Go to Testimonials → Add New in the left admin menu to add your first review.', 'sco-investor') . '</p>';
			}
			return;
		}

		$cols = absint($s['columns']) ?: 3;
		echo '<div class="testi-grid card-grid--' . esc_attr($cols) . '">';
		while ($query->have_posts()) {
			$query->the_post();
			get_template_part('template-parts/card-testimonial');
		}
		echo '</div>';
		wp_reset_postdata();
	}
}
