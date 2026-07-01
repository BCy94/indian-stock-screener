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

		// ── Style tab ───────────────────────────────────────────────────────
		$this->start_controls_section('section_style_text', [
			'label' => __('Text Colors', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('style_eyebrow_color', [
			'label'     => __('Eyebrow Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .hero-copy .eyebrow' => 'color: {{VALUE}}'],
		]);

		$this->add_control('style_heading_color', [
			'label'     => __('Heading Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .hero-copy h1' => 'color: {{VALUE}}'],
		]);

		$this->add_control('style_accent_color', [
			'label'       => __('Accent Word Color', 'sco-investor'),
			'type'        => \Elementor\Controls_Manager::COLOR,
			'description' => __('Leave blank to use the sitewide Brand Color (Customize → Brand Colors).', 'sco-investor'),
			'selectors'   => ['{{WRAPPER}} .hero-copy h1 .text-accent' => 'color: {{VALUE}}'],
		]);

		$this->add_control('style_lead_color', [
			'label'     => __('Lead Text Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .hero-copy p.lead' => 'color: {{VALUE}}'],
		]);

		$this->end_controls_section();

		$this->start_controls_section('section_style_section', [
			'label' => __('Section', 'sco-investor'),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('style_bg_color', [
			'label'     => __('Background Color', 'sco-investor'),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => ['{{WRAPPER}} .hero' => 'background-color: {{VALUE}}'],
		]);

		$this->add_responsive_control('style_padding', [
			'label'      => __('Section Padding', 'sco-investor'),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => ['px', 'em', '%'],
			'selectors'  => ['{{WRAPPER}} .hero' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
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

		// Reuses the exact classes main.css already styles for the native
		// homepage hero (.hero / .hero-grid / .hero-copy / .lead / .hero-actions
		// / .hero-visual / .hero-card) so this widget looks identical instead
		// of rendering as bare, unstyled markup.
		?>
		<section class="hero sci-elementor-hero">
			<div class="container <?php echo $img_url ? 'hero-grid' : ''; ?>">
				<div class="hero-copy">
					<?php if (!empty($s['eyebrow'])) : ?>
						<div class="eyebrow"><span class="dot"></span> <?php echo esc_html($s['eyebrow']); ?></div>
					<?php endif; ?>
					<h1><?php echo $heading_html; // already escaped above ?></h1>
					<?php if (!empty($s['lead'])) : ?>
						<p class="lead"><?php echo esc_html($s['lead']); ?></p>
					<?php endif; ?>
					<div class="hero-actions">
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
						<div class="hero-card hero-card--main hero-card--custom-image glass">
							<img src="<?php echo $img_url; ?>" alt="" loading="eager" class="hero-custom-image">
						</div>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
