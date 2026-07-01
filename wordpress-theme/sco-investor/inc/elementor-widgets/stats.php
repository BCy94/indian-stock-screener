<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('\Elementor\Widget_Base')) return;

class SCI_Widget_Stats extends \Elementor\Widget_Base {

	public function get_name()       { return 'sci_stats'; }
	public function get_title()      { return __('SCI — Stats Counter Row', 'sco-investor'); }
	public function get_categories() { return ['sci']; }
	public function get_icon()       { return 'eicon-counter'; }
	public function get_keywords()   { return ['stats', 'counter', 'numbers', 'animated', 'sci']; }

	protected function register_controls() {

		$this->start_controls_section('section_stats', [
			'label' => __('Stats', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('stats_note', [
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw'  => '<p style="color:#555;font-size:12px;">' . esc_html__('Numbers animate up from 0 when the section scrolls into view. Leave a count blank to hide that stat.', 'sco-investor') . '</p>',
		]);

		$defaults = [
			1 => ['count' => 2000,  'suffix' => '+',  'decimals' => 0, 'label' => __('Students Enrolled', 'sco-investor')],
			2 => ['count' => 4.9,   'suffix' => '★',  'decimals' => 1, 'label' => __('Average Rating', 'sco-investor')],
			3 => ['count' => 15,    'suffix' => '+',  'decimals' => 0, 'label' => __('Courses & Resources', 'sco-investor')],
			4 => ['count' => 100,   'suffix' => '%',  'decimals' => 0, 'label' => __('Practical, Real-portfolio Based', 'sco-investor')],
		];

		for ($i = 1; $i <= 4; $i++) {
			$d = $defaults[$i];

			$this->add_control("stat_{$i}_heading", [
				'type' => \Elementor\Controls_Manager::HEADING,
				'label' => sprintf(__('Stat %d', 'sco-investor'), $i),
				'separator' => $i > 1 ? 'before' : 'none',
			]);

			$this->add_control("stat_{$i}_count", [
				'label'   => __('Number', 'sco-investor'),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => $d['count'],
				'min'     => 0,
				'step'    => 0.1,
			]);

			$this->add_control("stat_{$i}_suffix", [
				'label'   => __('Suffix (after number)', 'sco-investor'),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $d['suffix'],
			]);

			$this->add_control("stat_{$i}_decimals", [
				'label'   => __('Decimal places', 'sco-investor'),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => $d['decimals'],
				'min'     => 0,
				'max'     => 2,
			]);

			$this->add_control("stat_{$i}_label", [
				'label'   => __('Label (below number)', 'sco-investor'),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $d['label'],
			]);
		}

		$this->end_controls_section();

		// ── Style tab ───────────────────────────────────────────────────────
		$this->start_controls_section('section_style', [
			'label' => __('Style', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('style_number_color', [
			'label'       => __('Number Color', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::COLOR,
			'description' => __('Leave blank to use the sitewide Brand/Accent Color.', 'sco-investor'),
			'selectors'   => ['{{WRAPPER}} .stat-box .num' => 'color: {{VALUE}}'],
		]);

		$this->add_control('style_label_color', [
			'label'     => __('Label Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .stat-box .label' => 'color: {{VALUE}}'],
		]);

		$this->add_control('style_box_bg', [
			'label'     => __('Tile Background Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .stat-box' => 'background-color: {{VALUE}}'],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		// Reuses .stats-row / .stat-box / .num / .label — the exact classes
		// main.css styles for the native homepage stats section (card
		// background, gold number color, dim label) — so this widget
		// matches instead of rendering as bare, unstyled numbers.
		?>
		<div class="stats-row sci-elementor-stats">
		<?php for ($i = 1; $i <= 4; $i++) :
			$count    = $s["stat_{$i}_count"];
			$suffix   = $s["stat_{$i}_suffix"];
			$decimals = (int) ($s["stat_{$i}_decimals"] ?? 0);
			$label    = $s["stat_{$i}_label"];
			if ($count === '' && $count !== 0) continue;
			?>
			<div class="stat-box glass">
				<div class="num"
					data-count="<?php echo esc_attr($count); ?>"
					data-suffix="<?php echo esc_attr($suffix); ?>"
					data-decimals="<?php echo esc_attr($decimals); ?>"
				>0</div>
				<?php if ($label) : ?>
					<div class="label"><?php echo esc_html($label); ?></div>
				<?php endif; ?>
			</div>
		<?php endfor; ?>
		</div>
		<?php
	}
}
