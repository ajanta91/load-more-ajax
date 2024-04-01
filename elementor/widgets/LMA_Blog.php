<?php

namespace LMA\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\{Widget_Base,
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
class LMA_Blog extends Widget_Base {

	public $base;

	public function get_name() {
		return 'lma-blog';
	}

	public function get_title() {
		return esc_html__( 'Blog Posts [LMA]', 'load-more-ajax-lite' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_style_depends() {
		if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			return [];
		} else {
			$settings = $this->get_settings_for_display();
			if ($settings['layout'] == '1') {
				return ['load-more-ajax-lite'];
			}
			elseif ($settings['layout'] == '2') {
				return ['load-more-ajax-lite-s2'];
			}
			elseif ($settings['layout'] == '3') {
				return ['load-more-ajax-lite-s3'];
			}
			return [];
		}
	}

	public function get_categories() {
		return ['load_more_ajax-elements' ];
	}

	protected function register_controls() {

		$this->start_controls_section( 'section_tab', [
			'label' => esc_html__( 'Blog Post', 'load-more-ajax-lite' ),
		] );

		$this->add_control( 'layout', [
			'label'   => esc_html__( 'Blog Style', 'load-more-ajax-lite' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 1,
			'options' => [
				'1' => esc_html__( 'Layout 1', 'load-more-ajax-lite' ),
				'2' => esc_html__( 'layout 2', 'load-more-ajax-lite' ),
				'3' => esc_html__( 'Layout 3', 'load-more-ajax-lite' )
			],
		] );

		$this->end_controls_section();//End Blog Layout


		//=========================== Query Filter =====================//
		$this->start_controls_section( 'sec_filter', [
			'label' => esc_html__( 'Query Filter', 'load-more-ajax-lite' ),
		] );
		$this->add_control('blog_column', [
			'label'   => esc_html__('Blog Style', 'load-more-ajax-lite'),
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => [
				'2' => esc_html__('2 Column', 'load-more-ajax-lite'),
				'3' => esc_html__('3 Column', 'load-more-ajax-lite'),
				'4' => esc_html__('4 Column', 'load-more-ajax-lite'),
				'5' => esc_html__('5 Column', 'load-more-ajax-lite'),
				'full' => esc_html__('Full Width', 'load-more-ajax-lite'),
			],
		]);

		$this->add_control( 'per_page', [
			'label'   => esc_html__( 'Posts Per Page', 'load-more-ajax-lite' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => '3'

		] );

		$this->add_control( 'order', [
			'label'       => __( 'Sort Order', 'load-more-ajax-lite' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => [
				'ASC'  => esc_html__( 'Ascending', 'load-more-ajax-lite' ),
				'DESC' => esc_html__( 'Descending', 'load-more-ajax-lite' ),
			],
			'default'     => 'DESC',
			'separator'   => 'before',
			'description' => esc_html__( "Select Ascending or Descending order. More at", 'load-more-ajax-lite' ) . '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex</a>.',
		] );

		$this->add_control( 'selected_categories', [
			'label'       => esc_html__( 'Select category', 'load-more-ajax-lite' ),
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'label_block' => true,
			'options'     => categories_suggester(),
			'default'     => '0'
		] );

		$this->add_control(
			'title_length', [
				'label' => esc_html__('Title Length', 'load-more-ajax-lite'),
				'type' => \Elementor\Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'excerpt_length', [
				'label' => esc_html__('Excerpt Word Length', 'load-more-ajax-lite'),
				'type' => \Elementor\Controls_Manager::NUMBER
			]
		);

		$this->end_controls_section();//End Query Filter


		//Categoory tab style =========================
		$this->start_controls_section( 'sec_cat_tab', [
			'label' => esc_html__( 'Category Tab Style', 'load-more-ajax-lite' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );
		$this->start_controls_tabs('category_tabs');
		$this->start_controls_tab('cat_tab_normal', [
			'label' => __('Normal', 'load-more-ajax-lite')
		]);
		$this->add_control('cat_item_color', [
			'label'     => __('Color', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat' => 'color: {{VALUE}}',
			],
		]);
		$this->add_control('cat_item_bg', [
			'label'     => __('Background', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat' => 'background: {{VALUE}}',
			],
		]);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_item_border',
				'label' => __('Border', 'load-more-ajax-lite'),
				'selector' => '{{WRAPPER}} .cat_filter .ajax_post_cat',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('cat_tab_hover', [
			'label' => __('Hover', 'load-more-ajax-lite')
		]);

		$this->add_control('cat_item_hover_color', [
			'label'     => __('Color', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat:hover, {{WRAPPER}} .cat_filter .ajax_post_cat.active' => 'color: {{VALUE}}',
				'{{WRAPPER}} .cat_filter .ajax_post_cat:before' => 'background: {{VALUE}}',
			],
		]);
		$this->add_control('cat_item_hover_bg', [
			'label'     => __('Background', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cat_filter .ajax_post_cat:hover' => 'background: {{VALUE}}',
			],
		]);
		$this->end_controls_tab();
		$this->end_controls_tabs();
	
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'cat_typography',
			'label'    => __( 'Typography', 'load-more-ajax-lite' ),
			'selector' => '{{WRAPPER}} .cat_filter .ajax_post_cat',
		] );
		$this->end_controls_section();


		// Section background ==============================
		$this->start_controls_section('lma_post_block_section', [
			'label' => __('Post Style', 'load-more-ajax-lite'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);
		$this->add_control(
			'title_heading',
			[
				'label' => esc_html__('Title Style', 'load-more-ajax-lite'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('title_color', [
			'label'     => __('Title Color', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .apl_content_wraper .apl_post_title' => 'color: {{VALUE}}',
				'{{WRAPPER}} .apl_content_wraper .post_title a' => 'color: {{VALUE}}',
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => __('Typography', 'load-more-ajax-lite'),
			'selector' => '{{WRAPPER}} .apl_content_wraper .apl_post_title,{{WRAPPER}} .apl_content_wraper .post_title a',
		]);

		$this->add_control(
			'meta_heading',
			[
				'label' => esc_html__('Post Meta Style', 'load-more-ajax-lite'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('meta_color', [
			'label'     => __('Meta Color', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .apl_post_meta .apl_post_meta_item' => 'color: {{VALUE}}',
				'{{WRAPPER}} .apl_post_meta .apl_post_meta_item a' => 'color: {{VALUE}}'
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'meta_typography',
			'label'    => __('Typography', 'load-more-ajax-lite'),
			'selector' => '{{WRAPPER}} .apl_post_meta .apl_post_meta_item, {{WRAPPER}} .apl_post_meta .apl_post_meta_item a'
		]);
		
		$this->add_control(
			'content_heading',
			[
				'label' => esc_html__('Post Content Style', 'load-more-ajax-lite'),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('content_color', [
			'label'     => __('Content Color', 'load-more-ajax-lite'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .apl_post_wraper .apl_content_wraper p' => 'color: {{VALUE}}'
			],
		]);
		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'content_typography',
			'label'    => __('Typography', 'load-more-ajax-lite'),
			'selector' => '{{WRAPPER}} .apl_post_wraper .apl_content_wraper p'
		]);

		$this->end_controls_section();


		$this->start_controls_section('load_more_btn_style', [
			'label' => __('Button Style', 'load-more-ajax-lite'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->start_controls_tabs('tabs_button_style');
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __('Normal', 'load-more-ajax-lite'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __('Color', 'load-more-ajax-lite'),
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
				'label' => __('Background Color', 'load-more-ajax-lite'),
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
				'label' => __('Border', 'load-more-ajax-lite'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __('Box Shadow', 'load-more-ajax-lite'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __('Hover', 'load-more-ajax-lite'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __('Color', 'load-more-ajax-lite'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color_hover',
			[
				'label' => __('Background Color', 'load-more-ajax-lite'),
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
				'label' => __('Border', 'load-more-ajax-lite'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover'
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_hover',
				'label' => __('Box Shadow', 'load-more-ajax-lite'),
				'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'btn_typography',
			'label'    => __('Typography', 'load-more-ajax-lite'),
			'selector' => '{{WRAPPER}} .apl_block_wraper button.loadmore_ajax'
		]);
		$this->end_controls_section();


		// Section background ==============================
		$this->start_controls_section( 'background_section', [
			'label' => __( 'Section Basckground', 'load-more-ajax-lite' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'sec_margin', [
			'label'      => __( 'Margin', 'load-more-ajax-lite' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .lma_blog_section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'sec_padding', [
			'label'      => __( 'Padding', 'load-more-ajax-lite' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .lma_blog_section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'background',
			'label' => esc_html__( 'Background', 'load-more-ajax-lite' ),
			'types' => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .lma_blog_section',
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		extract( $settings );

		$paged = 1;
		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		}
		if ( get_query_var( 'page' ) ) {
			$paged = get_query_var( 'page' );
		}

		$query['post_type'] 	= 'post';
		$query['order'] 		= $order;
		$query['post_status'] 	= 'publish';
		$query['posts_per_page']= $per_page;
		if( !empty( $selected_categories ) ){
			$query['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $selected_categories,
				)
			);
		}
		$query['paged'] = $paged;

		$hostim_query = new \WP_Query( $query );


		//====================== Template Parts ======================//
		require __DIR__ . '/templates/blog/blog-' . $layout . '.php';


	}
}
