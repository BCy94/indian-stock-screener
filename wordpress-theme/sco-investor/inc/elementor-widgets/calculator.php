<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('\Elementor\Widget_Base')) return;

class SCI_Widget_Calculator extends \Elementor\Widget_Base {

	public function get_name()       { return 'sci_calculator'; }
	public function get_title()      { return __('SCI — Investment Calculator', 'sco-investor'); }
	public function get_categories() { return ['sci']; }
	public function get_icon()       { return 'eicon-price-table'; }
	public function get_keywords()   { return ['calculator', 'sip', 'investment', 'returns', 'sci']; }

	protected function register_controls() {

		$this->start_controls_section('section_defaults', [
			'label' => __('Default Values', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('defaults_note', [
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw'  => '<p style="color:#555;font-size:12px;">' . esc_html__('These are the starting values when a visitor first loads the calculator. They can drag the sliders to any amount they like.', 'sco-investor') . '</p>',
		]);

		$this->add_control('default_mode', [
			'label'   => __('Default Tab', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'sip',
			'options' => [
				'sip'     => __('SIP (monthly investment)', 'sco-investor'),
				'lumpsum' => __('Lumpsum (one-time investment)', 'sco-investor'),
			],
		]);

		$this->add_control('default_amount', [
			'label'   => __('Default Monthly SIP (₹)', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 10000,
			'min'     => 500,
			'max'     => 100000,
			'step'    => 500,
		]);

		$this->add_control('default_lumpsum', [
			'label'   => __('Default Lumpsum Amount (₹)', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 100000,
			'min'     => 5000,
			'max'     => 5000000,
			'step'    => 5000,
		]);

		$this->add_control('default_rate', [
			'label'   => __('Default Return Rate (% p.a.)', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 12,
			'min'     => 1,
			'max'     => 30,
			'step'    => 0.5,
		]);

		$this->add_control('default_years', [
			'label'   => __('Default Time Period (years)', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 10,
			'min'     => 1,
			'max'     => 35,
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$attributes = [
			'defaultMode'    => $s['default_mode'] ?? 'sip',
			'defaultAmount'  => (int) ($s['default_amount'] ?? 10000),
			'defaultLumpsum' => (int) ($s['default_lumpsum'] ?? 100000),
			'defaultRate'    => (float) ($s['default_rate'] ?? 12),
			'defaultYears'   => (int) ($s['default_years'] ?? 10),
		];

		// Include the block's render file — it uses $attributes which we set above.
		// get_block_wrapper_attributes() outputs a minimal wrapper class when called
		// outside a block render context, which is fine for this usage.
		$render = SCI_THEME_DIR . '/blocks/calculator/render.php';
		if (file_exists($render)) {
			include $render;
		}

		// Ensure the calculator's view.js is enqueued on the front end
		if (!is_admin()) {
			wp_enqueue_script(
				'sci-calculator-view',
				SCI_THEME_URI . '/blocks/calculator/view.js',
				[],
				SCI_VERSION,
				true
			);
		}
	}
}
