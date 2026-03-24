<?php

namespace LMA\Widgets;

if (!defined('ABSPATH')) {
	exit;
}

use Elementor\{
	Widget_Base,
	Controls_Manager,
	Group_Control_Typography,
	Group_Control_Border,
	Group_Control_Box_Shadow,
	Group_Control_Background
};

/**
 * Get Post.
 *
 * Retrieve Hostim Post.
 *
 * @return string Hostim Post.
 * @since 1.0.0
 * @access public
 *
 */
class LMA_Blog extends Widget_Base
{

	public $base;

	public function get_name()
	{
		return 'lma-blog';
	}

	public function get_title()
	{
		return esc_html__('Blog Posts [LMA]', 'load-more-ajax');
	}

	public function get_icon()
	{
		return 'eicon-posts-grid';
	}

	public function get_script_depends()
	{
		// Elementor calls these methods at registration time, not per-instance,
		// so get_settings_for_display() returns defaults. Load all vendor scripts
		// and let the JS decide which to use based on data-block_style.
		return ['load-more-ajax', 'lma-masonry', 'lma-imagesloaded', 'lma-swiper'];
	}

	public function get_style_depends()
	{
		// Load all layout styles — Elementor resolves dependencies at registration,
		// not per widget instance, so conditional loading doesn't work here.
		return ['load-more-ajax', 'load-more-ajax-s2', 'load-more-ajax-s3', 'load-more-ajax-s4', 'load-more-ajax-s5', 'lma-swiper', 'fontawesome'];
	}

	public function get_categories()
	{
		return ['load_more_ajax-elements'];
	}

	protected function register_controls()
	{

		$this->start_controls_section('section_tab', [
			'label' => esc_html__('Blog Post', 'load-more-ajax'),
		]);

		$this->add_control('layout', [
			'label' => esc_html__('Blog Style', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT,
			'default' => 1,
			'options' => [
				'1' => esc_html__('Layout 1', 'load-more-ajax'),
				'2' => esc_html__('layout 2', 'load-more-ajax'),
				'3' => esc_html__('Layout 3', 'load-more-ajax'),
				'4' => esc_html__('Masonry', 'load-more-ajax'),
				'5' => esc_html__('Carousel', 'load-more-ajax'),
			],
		]);

		$this->add_control('slides_per_view', [
			'label' => esc_html__('Slides Per View', 'load-more-ajax'),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => '3',
			'options' => ['1' => '1', '2' => '2', '3' => '3', '4' => '4'],
			'condition' => ['layout' => '5'],
		]);
		$this->add_control('show_arrows', [
			'label' => esc_html__('Show Arrows', 'load-more-ajax'),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'condition' => ['layout' => '5'],
		]);
		$this->add_control('show_dots', [
			'label' => esc_html__('Show Dots', 'load-more-ajax'),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'condition' => ['layout' => '5'],
		]);
		$this->add_control('show_autoplay', [
			'label' => esc_html__('Autoplay', 'load-more-ajax'),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'condition' => ['layout' => '5'],
		]);

		$this->end_controls_section();//End Blog Layout


		//=========================== Query Filter =====================//
		$this->start_controls_section('sec_filter', [
			'label' => esc_html__('Query Filter', 'load-more-ajax'),
		]);

		$post_types = get_post_types(['public' => true], 'objects');
		unset($post_types['attachment']);
		$pt_options = [];
		foreach ($post_types as $pt) {
			$pt_options[$pt->name] = $pt->labels->singular_name;
		}

		$this->add_control('lma_post_type', [
			'label' => esc_html__('Post Type', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT,
			'default' => 'post',
			'options' => $pt_options,
		]);

		$this->add_control('lma_taxonomy', [
			'label' => esc_html__('Taxonomy', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT,
			'default' => 'category',
			'options' => ['category' => 'Categories'],
		]);

		$this->add_control('lma_terms', [
			'label' => esc_html__('Select Terms', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'label_block' => true,
			'options' => categories_suggester(),
			'default' => [],
		]);

		$this->add_control('blog_column', [
			'label' => esc_html__('Blog Style', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT,
			'default' => '3',
			'options' => [
				'2' => esc_html__('2 Column', 'load-more-ajax'),
				'3' => esc_html__('3 Column', 'load-more-ajax'),
				'4' => esc_html__('4 Column', 'load-more-ajax'),
				'5' => esc_html__('5 Column', 'load-more-ajax'),
				'full' => esc_html__('Full Width', 'load-more-ajax'),
			],
		]);

		$this->add_control('per_page', [
			'label' => esc_html__('Posts Per Page', 'load-more-ajax'),
			'type' => Controls_Manager::NUMBER,
			'default' => '3'

		]);

		$this->add_control('order', [
			'label' => __('Sort Order', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'ASC' => esc_html__('Ascending', 'load-more-ajax'),
				'DESC' => esc_html__('Descending', 'load-more-ajax'),
			],
			'default' => 'DESC',
			'separator' => 'before',
			'description' => esc_html__("Select Ascending or Descending order. More at", 'load-more-ajax') . '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex</a>.',
		]);

		$this->add_control('selected_categories', [
			'label' => esc_html__('Select category', 'load-more-ajax'),
			'type' => Controls_Manager::SELECT2,
			'multiple' => true,
			'label_block' => true,
			'options' => categories_suggester(),
			'default' => '0'
		]);

		$this->add_control(
			'title_length',
			[
				'label' => esc_html__('Title Length', 'load-more-ajax'),
				'type' => \Elementor\Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label' => esc_html__('Excerpt Word Length', 'load-more-ajax'),
				'type' => \Elementor\Controls_Manager::NUMBER
			]
		);

		$this->end_controls_section();//End Query Filter


		//Categoory tab style =========================
		$this->start_controls_section('sec_cat_tab', [
			'label' => esc_html__('Category Tab Style', 'load-more-ajax'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);
		$this->start_controls_tabs('category_tabs');
		$this->start_controls_tab('cat_tab_normal', [
			'label' => __('Normal', 'load-more-ajax')
		]);
		$this->add_control('cat_item_color', [
			'label' => __('Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat' => 'color: {{VALUE}}',
			],
		]);
		$this->add_control('cat_item_bg', [
			'label' => __('Background', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat' => 'background: {{VALUE}}',
			],
		]);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_item_border',
				'label' => __('Border', 'load-more-ajax'),
				'selector' => '{{WRAPPER}} .cat_filter .ajax_post_cat',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('cat_tab_hover', [
			'label' => __('Hover', 'load-more-ajax')
		]);

		$this->add_control('cat_item_hover_color', [
			'label' => __('Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat:hover, {{WRAPPER}} .cat_filter .ajax_post_cat.active' => 'color: {{VALUE}}',
				'{{WRAPPER}} .cat_filter .ajax_post_cat:before' => 'background: {{VALUE}}',
			],
		]);
		$this->add_control('cat_item_hover_bg', [
			'label' => __('Background', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat:hover' => 'background: {{VALUE}}',
			],
		]);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'cat_typography',
			'label' => __('Typography', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .cat_filter .ajax_post_cat',
		]);
		$this->end_controls_section();


		// Section background ==============================
		$this->start_controls_section('lma_post_block_section', [
			'label' => __('Post Style', 'load-more-ajax'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control(
			'title_heading',
			[
				'label' => esc_html__('Title Style', 'load-more-ajax'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('title_color', [
			'label' => __('Title Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .apl_content_wraper .apl_post_title' => 'color: {{VALUE}}',
				'{{WRAPPER}} .apl_content_wraper .post_title a' => 'color: {{VALUE}}',
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'title_typography',
			'label' => __('Typography', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .apl_content_wraper .apl_post_title,{{WRAPPER}} .apl_content_wraper .post_title a',
		]);

		$this->add_control(
			'meta_heading',
			[
				'label' => esc_html__('Post Meta Style', 'load-more-ajax'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('meta_color', [
			'label' => __('Meta Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .apl_post_meta .apl_post_meta_item' => 'color: {{VALUE}}',
				'{{WRAPPER}} .apl_post_meta .apl_post_meta_item a' => 'color: {{VALUE}}'
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'meta_typography',
			'label' => __('Typography', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .apl_post_meta .apl_post_meta_item, {{WRAPPER}} .apl_post_meta .apl_post_meta_item a'
		]);

		$this->add_control(
			'content_heading',
			[
				'label' => esc_html__('Post Content Style', 'load-more-ajax'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('content_color', [
			'label' => __('Content Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .apl_post_wraper .apl_content_wraper p' => 'color: {{VALUE}}'
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'content_typography',
			'label' => __('Typography', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .apl_post_wraper .apl_content_wraper p'
		]);

		$this->end_controls_section();


		$this->start_controls_section('load_more_btn_style', [
			'label' => __('Button Style', 'load-more-ajax'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);

		$this->start_controls_tabs('tabs_button_style');
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __('Normal', 'load-more-ajax'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __('Color', 'load-more-ajax'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .apl_block_wraper button.loadmore_ajax' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __('Background Color', 'load-more-ajax'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apl_block_wraper button.loadmore_ajax' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __('Border', 'load-more-ajax'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __('Box Shadow', 'load-more-ajax'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __('Hover', 'load-more-ajax'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __('Color', 'load-more-ajax'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color_hover',
			[
				'label' => __('Background Color', 'load-more-ajax'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover' => 'background-color: {{VALUE}};'
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border_hover',
				'label' => __('Border', 'load-more-ajax'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_hover',
				'label' => __('Box Shadow', 'load-more-ajax'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'btn_typography',
			'label' => __('Typography', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax'
		]);
		$this->end_controls_section();


		// Arrow Style ==============================
		$this->start_controls_section('carousel_arrow_style', [
			'label' => __('Arrow Style', 'load-more-ajax'),
			'tab' => Controls_Manager::TAB_STYLE,
			'condition' => ['layout' => '5', 'show_arrows' => 'yes'],
		]);

		$this->add_responsive_control('arrow_size', [
			'label' => __('Size', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 20, 'max' => 60]],
			'default' => ['size' => 40, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('arrow_icon_size', [
			'label' => __('Icon Size', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 8, 'max' => 30]],
			'default' => ['size' => 16, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next::after, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev::after' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('arrow_border_radius', [
			'label' => __('Border Radius', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 0, 'max' => 50]],
			'default' => ['size' => 50, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev' => 'border-radius: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->start_controls_tabs('arrow_style_tabs');

		$this->start_controls_tab('arrow_tab_normal', [
			'label' => __('Normal', 'load-more-ajax'),
		]);
		$this->add_control('arrow_icon_color', [
			'label' => __('Icon Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next::after, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev::after' => 'color: {{VALUE}};',
			],
		]);
		$this->add_control('arrow_bg_color', [
			'label' => __('Background Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev' => 'background-color: {{VALUE}};',
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name' => 'arrow_border',
			'label' => __('Border', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .lma_block_style_5 .swiper-button-next, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev',
		]);
		$this->end_controls_tab();

		$this->start_controls_tab('arrow_tab_hover', [
			'label' => __('Hover', 'load-more-ajax'),
		]);
		$this->add_control('arrow_icon_hover_color', [
			'label' => __('Icon Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next:hover::after, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev:hover::after' => 'color: {{VALUE}};',
			],
		]);
		$this->add_control('arrow_bg_hover_color', [
			'label' => __('Background Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-button-next:hover, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
			],
		]);
		$this->add_group_control(Group_Control_Border::get_type(), [
			'name' => 'arrow_border_hover',
			'label' => __('Border', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .lma_block_style_5 .swiper-button-next:hover, {{WRAPPER}} .lma_block_style_5 .swiper-button-prev:hover',
		]);
		$this->end_controls_tab();

		$this->end_controls_tabs();
		$this->end_controls_section();

		// Dot Style ==============================
		$this->start_controls_section('carousel_dot_style', [
			'label' => __('Dot Style', 'load-more-ajax'),
			'tab' => Controls_Manager::TAB_STYLE,
			'condition' => ['layout' => '5', 'show_dots' => 'yes'],
		]);

		$this->add_responsive_control('dot_size', [
			'label' => __('Size', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 6, 'max' => 20]],
			'default' => ['size' => 10, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('dot_active_width', [
			'label' => __('Active Dot Width', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 10, 'max' => 40]],
			'default' => ['size' => 24, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('dot_border_radius', [
			'label' => __('Border Radius', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 0, 'max' => 20]],
			'default' => ['size' => 5, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('dot_spacing', [
			'label' => __('Spacing', 'load-more-ajax'),
			'type' => Controls_Manager::SLIDER,
			'range' => ['px' => ['min' => 0, 'max' => 20]],
			'default' => ['size' => 4, 'unit' => 'px'],
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('dot_color', [
			'label' => __('Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet' => 'background: {{VALUE}};',
			],
		]);

		$this->add_control('dot_active_color', [
			'label' => __('Active Color', 'load-more-ajax'),
			'type' => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Border::get_type(), [
			'name' => 'dot_border',
			'label' => __('Border', 'load-more-ajax'),
			'selector' => '{{WRAPPER}} .lma_block_style_5 .swiper-pagination-bullet',
		]);

		$this->end_controls_section();

		// Section background ==============================
		$this->start_controls_section('background_section', [
			'label' => __('Section Basckground', 'load-more-ajax'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('sec_margin', [
			'label' => __('Margin', 'load-more-ajax'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .lma_blog_section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('sec_padding', [
			'label' => __('Padding', 'load-more-ajax'),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors' => [
				'{{WRAPPER}} .lma_blog_section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
		$this->add_group_control(Group_Control_Background::get_type(), [
			'name' => 'background',
			'label' => esc_html__('Background', 'load-more-ajax'),
			'types' => ['classic', 'gradient'],
			'selector' => '{{WRAPPER}} .lma_blog_section',
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		extract($settings);

		$paged = 1;
		if (get_query_var('paged')) {
			$paged = get_query_var('paged');
		}
		if (get_query_var('page')) {
			$paged = get_query_var('page');
		}

		$lma_post_type = $settings['lma_post_type'] ?? 'post';
		$lma_taxonomy = $settings['lma_taxonomy'] ?? 'category';
		$lma_terms = $settings['lma_terms'] ?? [];

		$query['post_type'] = $lma_post_type;
		$query['order'] = $order;
		$query['post_status'] = 'publish';
		$query['posts_per_page'] = $per_page;
		if (!empty($lma_terms)) {
			$query['tax_query'] = array(
				array(
					'taxonomy' => $lma_taxonomy,
					'field' => 'slug',
					'terms' => $lma_terms,
				)
			);
		} elseif (!empty($selected_categories)) {
			$query['tax_query'] = array(
				array(
					'taxonomy' => $lma_taxonomy,
					'field' => 'slug',
					'terms' => $selected_categories,
				)
			);
		}
		$query['paged'] = $paged;

		$hostim_query = new \WP_Query($query);


		$slides_per_view = $settings['slides_per_view'] ?? '3';
		$show_arrows = $settings['show_arrows'] ?? 'yes';
		$show_dots = $settings['show_dots'] ?? 'yes';
		$show_autoplay = $settings['show_autoplay'] ?? 'yes';

		$lma_post_type_for_template = $lma_post_type;
		$lma_taxonomy_for_template = $lma_taxonomy;

		//====================== Template Parts ======================//
		require __DIR__ . '/templates/blog/blog-' . $layout . '.php';


	}
}
