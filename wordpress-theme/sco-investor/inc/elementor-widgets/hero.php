<?php
if (!defined('ABSPATH')) exit;
if (!class_exists('\Elementor\Widget_Base')) return;

class SCI_Widget_Hero extends \Elementor\Widget_Base {

	public function get_name()       { return 'sci_hero'; }
	public function get_title()      { return __('SCI — Hero Section', 'sco-investor'); }
	public function get_categories() { return ['sci']; }
	public function get_icon()       { return 'eicon-call-to-action'; }
	public function get_keywords()   { return ['hero', 'banner', 'heading', 'cta', 'sci']; }

	protected function register_controls() {

		// ── Headline ──────────────────────────────────────────────────────
		$this->start_controls_section('section_headline', [
			'label' => __('Headline', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('eyebrow', [
			'label'       => __('Eyebrow (small text above heading)', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __('So Called Investor', 'sco-investor'),
			'placeholder' => __('e.g. Trusted by 2,000+ investors', 'sco-investor'),
		]);

		$this->add_control('heading', [
			'label'       => __('Main Heading', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXTAREA,
			'default'     => __('Master the Indian Stock Market', 'sco-investor'),
			'rows'        => 3,
		]);

		$this->add_control('heading_accent', [
			'label'       => __('Gold Accent Word (must appear in heading)', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::TEXT,
			'default'     => __('Stock Market', 'sco-investor'),
			'description' => __('One or more words from the heading above that should appear in gold. Must match exactly.', 'sco-investor'),
		]);

		$this->add_control('lead', [
			'label'   => __('Lead Paragraph', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::TEXTAREA,
			'default' => __('Practical, no-hype investing education built from real portfolio experience — courses, research tools and resources for every stage of your journey.', 'sco-investor'),
			'rows'    => 3,
		]);

		$this->end_controls_section();

		// ── Buttons ───────────────────────────────────────────────────────
		$this->start_controls_section('section_buttons', [
			'label' => __('Buttons', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('cta1_text', [
			'label'   => __('Primary Button Text', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __('Explore Courses', 'sco-investor'),
		]);

		$this->add_control('cta1_url', [
			'label'   => __('Primary Button URL', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => ['url' => '/courses/'],
		]);

		$this->add_control('cta2_text', [
			'label'   => __('Secondary Button Text', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::TEXT,
			'default' => __('Free Resources', 'sco-investor'),
		]);

		$this->add_control('cta2_url', [
			'label'   => __('Secondary Button URL', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::URL,
			'default' => ['url' => '/materials/'],
		]);

		$this->end_controls_section();

		// ── Trust Badges ──────────────────────────────────────────────────
		$this->start_controls_section('section_trust', [
			'label' => __('Trust Badges', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('trust_note', [
			'type' => \Elementor\Controls_Manager::RAW_HTML,
			'raw'  => '<p style="color:#555;font-size:12px;">' . esc_html__('Three small trust signals shown below the buttons (e.g. "2,000+ Students", "No fluff", "Risk-free — Free content to start"). Leave blank to hide.', 'sco-investor') . '</p>',
		]);

		foreach ([1, 2, 3] as $n) {
			$defaults = [
				1 => __('2,000+ Students', 'sco-investor'),
				2 => __('SEBI-registered guidance only', 'sco-investor'),
				3 => __('Free content to start', 'sco-investor'),
			];
			$this->add_control("trust_{$n}", [
				'label'   => sprintf(__('Badge %d', 'sco-investor'), $n),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $defaults[$n],
			]);
		}

		$this->end_controls_section();

		// ── Background Image ──────────────────────────────────────────────
		$this->start_controls_section('section_bg', [
			'label' => __('Background Image', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		]);

		$this->add_control('hero_image', [
			'label'   => __('Hero Image (optional — decorative panel on large screens)', 'sco-investor'),
			'type'    => \Elementor\Controls_Manager::MEDIA,
			'default' => ['url' => ''],
		]);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		// Build heading HTML — accent word gets gold colour
		$raw_heading = esc_html($s['heading']);
		$accent      = $s['heading_accent'] ? esc_html($s['heading_accent']) : '';
		if ($accent && str_contains($raw_heading, $accent)) {
			$heading_html = str_replace($accent, '<span class="text-accent">' . $accent . '</span>', $raw_heading);
		} else {
			$heading_html = $raw_heading;
		}

		$cta1_url = !empty($s['cta1_url']['url']) ? esc_url($s['cta1_url']['url']) : '#';
		$cta2_url = !empty($s['cta2_url']['url']) ? esc_url($s['cta2_url']['url']) : '#';

		$badges = array_filter([$s['trust_1'] ?? '', $s['trust_2'] ?? '', $s['trust_3'] ?? '']);

		$img_url = !empty($s['hero_image']['url']) ? esc_url($s['hero_image']['url']) : '';
		?>
		<section class="hero sci-elementor-hero">
			<div class="container">
				<div class="hero-body">
					<?php if (!empty($s['eyebrow'])) : ?>
						<span class="eyebrow"><?php echo esc_html($s['eyebrow']); ?></span>
					<?php endif; ?>
					<h1 class="hero-title"><?php echo $heading_html; // already escaped above ?></h1>
					<?php if (!empty($s['lead'])) : ?>
						<p class="hero-lead"><?php echo esc_html($s['lead']); ?></p>
					<?php endif; ?>
					<div class="hero-ctas">
						<?php if (!empty($s['cta1_text'])) : ?>
							<a href="<?php echo $cta1_url; ?>" class="btn btn-primary"><?php echo esc_html($s['cta1_text']); ?></a>
						<?php endif; ?>
						<?php if (!empty($s['cta2_text'])) : ?>
							<a href="<?php echo $cta2_url; ?>" class="btn btn-ghost"><?php echo esc_html($s['cta2_text']); ?></a>
						<?php endif; ?>
					</div>
					<?php if ($badges) : ?>
						<div class="hero-trust">
							<?php foreach ($badges as $badge) : ?>
								<span><svg viewBox="0 0 20 20" fill="none" style="width:14px;height:14px;color:var(--accent);"><path d="M10 1.5l2.39 4.84 5.35.78-3.87 3.77.91 5.32L10 13.77l-4.78 2.44.91-5.32L2.26 7.12l5.35-.78L10 1.5z" fill="currentColor"/></svg><?php echo esc_html($badge); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ($img_url) : ?>
					<div class="hero-visual">
						<img src="<?php echo $img_url; ?>" alt="" loading="eager" style="border-radius:12px;max-width:100%;height:auto;">
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
