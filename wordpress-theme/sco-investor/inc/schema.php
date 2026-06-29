<?php
/**
 * JSON-LD structured data, output as a single sitewide @graph.
 * Organization + BreadcrumbList run on every page; Article runs on single
 * posts; Course runs on single courses; FAQPage runs wherever a real
 * sci/accordion block exists in post_content. Course and FAQPage hook in
 * via the 'sci_schema_graph' filter rather than editing sci_schema_graph()
 * itself, so adding a new schema type never requires touching the core
 * graph builder below.
 */

if (!defined('ABSPATH')) exit;

if (defined('WPSEO_VERSION') || class_exists('RankMath') || defined('AIOSEO_VERSION')) {
	return;
}

function sci_schema_organization() {
	$logo = '';
	$custom_logo_id = get_theme_mod('custom_logo');
	if ($custom_logo_id) {
		$img = wp_get_attachment_image_src($custom_logo_id, 'medium');
		if ($img) $logo = $img[0];
	}

	$org = [
		'@type' => 'Organization',
		'@id'   => home_url('/#organization'),
		'name'  => get_bloginfo('name'),
		'url'   => home_url('/'),
	];
	if ($logo) $org['logo'] = $logo;

	$same_as = array_values(array_filter([
		sci_social_url('twitter'),
		sci_social_url('youtube'),
		sci_social_url('instagram'),
		sci_social_url('telegram'),
	]));
	if ($same_as) $org['sameAs'] = $same_as;

	return $org;
}

function sci_schema_breadcrumbs() {
	$items = [['name' => get_bloginfo('name'), 'item' => home_url('/')]];

	if (is_front_page()) {
		return null;
	} elseif (is_singular('post')) {
		$cats = get_the_category();
		if ($cats) {
			$cat_link = get_category_link($cats[0]);
			if (!is_wp_error($cat_link)) $items[] = ['name' => $cats[0]->name, 'item' => $cat_link];
		}
		$items[] = ['name' => get_the_title(), 'item' => get_permalink()];
	} elseif (is_singular()) {
		$items[] = ['name' => get_the_title(), 'item' => get_permalink()];
	} elseif (is_category() || is_tag() || is_tax()) {
		$term_link = get_term_link(get_queried_object());
		if (!is_wp_error($term_link)) $items[] = ['name' => single_term_title('', false), 'item' => $term_link];
	} elseif (is_post_type_archive()) {
		$items[] = ['name' => post_type_archive_title('', false), 'item' => get_post_type_archive_link(get_query_var('post_type'))];
	} elseif (is_search()) {
		/* translators: %s: search query */
		$items[] = ['name' => sprintf(__('Search results for "%s"', 'sco-investor'), get_search_query())];
	} elseif (is_404()) {
		return null;
	} elseif (is_home()) {
		$page_for_posts = (int) get_option('page_for_posts');
		if ($page_for_posts) $items[] = ['name' => get_the_title($page_for_posts), 'item' => get_permalink($page_for_posts)];
	}

	if (count($items) < 2) return null;

	$list = [];
	foreach ($items as $i => $item) {
		$entry = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
		];
		if (!empty($item['item'])) $entry['item'] = $item['item'];
		$list[] = $entry;
	}

	return [
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	];
}

function sci_schema_article() {
	if (!is_singular('post')) return null;

	$post_id = get_the_ID();
	$image   = has_post_thumbnail($post_id) ? wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'large') : null;

	$article = [
		'@type'            => 'Article',
		'headline'         => get_the_title(),
		'datePublished'    => get_the_date('c'),
		'dateModified'     => get_the_modified_date('c'),
		'mainEntityOfPage' => get_permalink(),
		'author'           => [
			'@type' => 'Person',
			'name'  => get_the_author(),
		],
		'publisher' => [
			'@id' => home_url('/#organization'),
		],
	];
	if ($image) $article['image'] = $image[0];

	return $article;
}

/**
 * Course schema — name/description/provider/image only. No pricing or
 * CourseInstance schema until real payments exist; shipping that now would
 * advertise an offer nobody can actually buy and trip Google's structured
 * data mismatch warning.
 */
function sci_schema_course($graph) {
	if (!is_singular('course')) return $graph;

	$course_id   = get_the_ID();
	$description = sci_seo_description();
	$image       = sci_seo_image();

	$course = [
		'@type'    => 'Course',
		'name'     => get_the_title($course_id),
		'url'      => get_permalink($course_id),
		'provider' => [
			'@id' => home_url('/#organization'),
		],
	];
	if ($description) $course['description'] = $description;
	if ($image) $course['image'] = $image;

	$graph[] = $course;
	return $graph;
}
add_filter('sci_schema_graph', 'sci_schema_course');

/**
 * FAQPage schema — built by walking the post's own parsed blocks for
 * sci/accordion-item children, so it always matches exactly what the
 * accordion block renders on the page. Only fires when a real sci/accordion
 * instance exists in this post's content; hardcoded template accordions
 * (e.g. the courses archive FAQ, rendered via sci_render_accordion()) have
 * no $post of their own to attach schema to and are skipped.
 */
function sci_schema_faq($graph) {
	if (!is_singular() || !has_block('sci/accordion')) return $graph;

	$questions = sci_schema_faq_collect(parse_blocks(get_post()->post_content));
	if (!$questions) return $graph;

	$graph[] = [
		'@type'      => 'FAQPage',
		'mainEntity' => $questions,
	];
	return $graph;
}
add_filter('sci_schema_graph', 'sci_schema_faq');

/**
 * Recursively walks parsed blocks — an accordion may sit inside a Group or
 * Columns block — collecting every sci/accordion-item's question/answer as
 * a schema.org Question entry.
 */
function sci_schema_faq_collect(array $blocks) {
	$questions = [];
	foreach ($blocks as $block) {
		if ($block['blockName'] === 'sci/accordion-item') {
			$question = trim(wp_strip_all_tags($block['attrs']['question'] ?? ''));
			$answer   = trim(wp_strip_all_tags($block['attrs']['answer'] ?? ''));
			if ($question && $answer) {
				$questions[] = [
					'@type'          => 'Question',
					'name'           => $question,
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => $answer,
					],
				];
			}
		}
		if (!empty($block['innerBlocks'])) {
			$questions = array_merge($questions, sci_schema_faq_collect($block['innerBlocks']));
		}
	}
	return $questions;
}

function sci_schema_graph() {
	if (is_admin() || is_feed() || is_404()) return [];

	$graph = [sci_schema_organization()];

	$breadcrumbs = sci_schema_breadcrumbs();
	if ($breadcrumbs) $graph[] = $breadcrumbs;

	$article = sci_schema_article();
	if ($article) $graph[] = $article;

	return apply_filters('sci_schema_graph', $graph);
}

function sci_schema_output() {
	$graph = sci_schema_graph();
	if (!$graph) return;

	echo '<script type="application/ld+json">' .
		wp_json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES) .
		'</script>' . "\n";
}
add_action('wp_footer', 'sci_schema_output');
