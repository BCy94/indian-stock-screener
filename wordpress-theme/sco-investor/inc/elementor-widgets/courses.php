<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('\Elementor\Widget_Base')) return;

class SCI_Widget_Courses extends \Elementor\Widget_Base {

	public function get_name()       { return 'sci_courses'; }
	public function get_title()      { return __('SCI — Courses Grid', 'sco-investor'); }
	public function get_categories() { return ['sci']; }
	public function get_icon()       { return 'eicon-posts-grid'; }
	public function get_keywords()   { return ['courses', 'grid', 'cards', 'sci', 'education']; }

	protected function register_controls() {

		// ── Heading ───────────────────────────────────────────────────────
		$this->start_controls_section('section_heading', [
			'label' => __('Section Heading', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('eyebrow', [
			'label'       => __('Eyebrow', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __('COURSES', 'sco-investor'),
			'placeholder' => __('Leave blank to hide', 'sco-investor'),
		]);

		$this->add_control('heading', [
			'label'       => __('Heading', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __('Build Real Investing Skills', 'sco-investor'),
			'placeholder' => __('Leave blank to hide', 'sco-investor'),
		]);

		$this->add_control('show_view_all', [
			'label'      => __('Show "View All" link', 'sco-investor'),
			'type'       => \Elementor\Controls_Manager::SWITCHER,
			'default'    => 'yes',
			'label_on'   => __('Yes', 'sco-investor'),
			'label_off'  => __('No', 'sco-investor'),
		]);

		$this->end_controls_section();

		// ── Query ─────────────────────────────────────────────────────────
		$this->start_controls_section('section_query', [
			'label' => __('Query', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('posts_per_page', [
			'label'   => __('Number of Courses', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 3,
			'min'     => 1,
			'max'     => 12,
		]);

		$this->add_control('columns', [
			'label'   => __('Columns', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '3',
			'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
		]);

		$this->add_control('featured_only', [
			'label'       => __('Featured Only', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::SWITCHER,
			'default'     => '',
			'label_on'    => __('Yes', 'sco-investor'),
			'label_off'   => __('No', 'sco-investor'),
			'description' => __('Show only courses with the "Featured on Homepage" checkbox ticked.', 'sco-investor'),
		]);

		// Build category options
		$cat_options = ['' => __('All Categories', 'sco-investor')];
		$cats = get_terms(['taxonomy' => 'course_category', 'hide_empty' => false]);
		if (!is_wp_error($cats)) {
			foreach ($cats as $cat) {
				$cat_options[$cat->slug] = $cat->name;
			}
		}
		$this->add_control('category', [
			'label'   => __('Filter by Category', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $cat_options,
		]);

		// Build level options
		$level_options = ['' => __('All Levels', 'sco-investor')];
		$levels = get_terms(['taxonomy' => 'course_level', 'hide_empty' => false]);
		if (!is_wp_error($levels)) {
			foreach ($levels as $level) {
				$level_options[$level->slug] = $level->name;
			}
		}
		$this->add_control('level', [
			'label'   => __('Filter by Level', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => '',
			'options' => $level_options,
		]);

		$this->add_control('orderby', [
			'label'   => __('Order By', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'date',
			'options' => [
				'date'       => __('Newest first', 'sco-investor'),
				'title'      => __('Title A–Z', 'sco-investor'),
				'menu_order' => __('Custom order (drag to reorder)', 'sco-investor'),
			],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$query_args = [
			'post_type'      => 'course',
			'post_status'    => 'publish',
			'posts_per_page' => max(1, (int) $s['posts_per_page']),
			'orderby'        => $s['orderby'],
			'order'          => $s['orderby'] === 'title' ? 'ASC' : 'DESC',
			'no_found_rows'  => true,
		];

		if ($s['featured_only'] === 'yes') {
			$query_args['meta_query'] = [
				['key' => '_sci_featured', 'value' => '1', 'compare' => '='],
			];
		}

		$tax_query = [];
		if (!empty($s['category'])) {
			$tax_query[] = ['taxonomy' => 'course_category', 'field' => 'slug', 'terms' => $s['category']];
		}
		if (!empty($s['level'])) {
			$tax_query[] = ['taxonomy' => 'course_level', 'field' => 'slug', 'terms' => $s['level']];
		}
		if ($tax_query) {
			$query_args['tax_query'] = $tax_query;
		}

		$query = new WP_Query($query_args);

		if (!empty($s['eyebrow']) || !empty($s['heading'])) : ?>
			<div class="section-header center" style="margin-bottom:2rem;">
				<?php if (!empty($s['eyebrow'])) : ?>
					<span class="eyebrow"><?php echo esc_html($s['eyebrow']); ?></span>
				<?php endif; ?>
				<?php if (!empty($s['heading'])) : ?>
					<h2><?php echo esc_html($s['heading']); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif;

		if (!$query->have_posts()) {
			echo '<p class="muted">' . esc_html__('No courses found. Add courses from the Courses menu in wp-admin.', 'sco-investor') . '</p>';
			return;
		}

		$cols = absint($s['columns']) ?: 3;
		echo '<div class="card-grid card-grid--' . esc_attr($cols) . '">';
		while ($query->have_posts()) {
			$query->the_post();
			get_template_part('template-parts/card-course', null, ['cta' => 'view']);
		}
		echo '</div>';
		wp_reset_postdata();

		if ($s['show_view_all'] === 'yes') {
			$archive = get_post_type_archive_link('course');
			if ($archive) {
				echo '<div class="center" style="margin-top:2rem;"><a href="' . esc_url($archive) . '" class="btn btn-ghost">' . esc_html__('View All Courses', 'sco-investor') . '</a></div>';
			}
		}
	}
}
