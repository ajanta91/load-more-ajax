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


		//========================== Button =========================//
		$this->start_controls_section( 'sec_buttons', [
			'label' => esc_html__( 'Buttons', 'load-more-ajax-lite' ),
			'condition' => [
				'layout' => '6'
			]
		] );

		$this->add_control( 'btn_label', [
			'label'   => esc_html__( 'Read More Button', 'load-more-ajax-lite' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'Explore More'
		] );


		$this->end_controls_section(); //End Button


		/*======================= Shape Images ============================*/
	    $shapes = new \Elementor\Repeater();
	    $this->start_controls_section(
		    'shape_image_sec',
		    [
			    'label' => __( 'Shape Image', 'load-more-ajax-lite' ),
				'condition' => [
					'layout' => '5'
				]
		    ]
	    );
	    $shapes->add_control(
		    'shape_img',
		    [
			    'label' => __( 'Choose Image', 'load-more-ajax-lite' ),
			    'type' => \Elementor\Controls_Manager::MEDIA,

		    ]
	    );
	    $shapes->add_responsive_control(
		    'horizontal_position',
		    [
			    'label' => __( 'Horizontal Position', 'load-more-ajax-lite' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1920,
					    'step' => 1,
				    ],
				    '%' => [
					    'min' => 0,
					    'max' => 100,
				    ],
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 50,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );
	    $shapes->add_responsive_control(
		    'vertical_position',
		    [
			    'label' => __( 'Vertical Position', 'load-more-ajax-lite' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px', '%' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 1920,
					    'step' => 1,
				    ],
				    '%' => [
					    'min' => 0,
					    'max' => 100,
				    ],
			    ],
			    'default' => [
				    'unit' => '%',
				    'size' => 50,
			    ],
			    'selectors' => [
				    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}{{UNIT}};',
			    ],
		    ]
	    );
		$shapes->add_control(
		    'shape_z_index',
		    [
			    'label' => __( 'Z-index', 'load-more-ajax-lite' ),
			    'type' => \Elementor\Controls_Manager::NUMBER,
				'step' => 1,
				'selectors' => [
				    '{{WRAPPER}} {{CURRENT_ITEM}}' => 'z-index: {{VALUE}};',
			    ],
		    ]
	    );
		$shapes->add_control( 'bland_mode', [
			'label'   => esc_html__( 'Bland Mode', 'load-more-ajax-lite' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'normal' 	=> esc_html__( 'Normal', 'load-more-ajax-lite' ),
				'multiply'	=> esc_html__( 'Multiply', 'load-more-ajax-lite' ),
				'screen'	=> esc_html__( 'Screen', 'load-more-ajax-lite' ),
				'overlay'	=> esc_html__( 'Overlay', 'load-more-ajax-lite' ),
				'darken'	=> esc_html__( 'Darken', 'load-more-ajax-lite' ),
				'lighten'	=> esc_html__( 'Lighten', 'load-more-ajax-lite' ),
				'color-dodge'=> esc_html__( 'Color-dodge', 'load-more-ajax-lite' ),
				'color-burn'=> esc_html__( 'Color-burn', 'load-more-ajax-lite' ),
				'difference'=> esc_html__( 'Difference', 'load-more-ajax-lite' ),
				'exclusion' => esc_html__( 'Exclusion', 'load-more-ajax-lite' ),
				'hue'		=> esc_html__( 'Hue', 'load-more-ajax-lite' ),
				'saturation'=> esc_html__( 'Saturation', 'load-more-ajax-lite' ),
				'color'		=> esc_html__( 'Color', 'load-more-ajax-lite' ),
				'luminosity'=> esc_html__( 'Luminosity', 'load-more-ajax-lite' ),
			],
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'mix-blend-mode: {{VALUE}};',
			],
		] );
		$shapes->add_control(
			'shape_blur',
			[
				'label' => esc_html__( 'Blur', 'load-more-ajax-lite' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'filter: blur( {{SIZE}}{{UNIT}} );',
				],
			]
		);

	    $this->add_control(
		    'shape_images',
		    [
			    'label' => __( 'Shape Image', 'load-more-ajax-lite' ),
			    'type' => \Elementor\Controls_Manager::REPEATER,
			    'fields' => $shapes->get_controls(),
			    'title_field' => '{{{ shape_img.alt }}}',
		    ]
	    );
	    $this->end_controls_section();

		/* Carousel Settings =================== */
		$this->start_controls_section(
		    'carousel_settings', [
			    'label' => __( 'Carousel Settings', 'load-more-ajax-lite' ),
				'condition' => [
					'layout' => ['2']
				]
		    ]
	    );
	    $this->add_control(
		    'show_items_desktop', [
			    'label'     => esc_html__( 'Display Items [Desktop]', 'load-more-ajax-lite' ),
			    'type'      => Controls_Manager::NUMBER,

		    ]
	    );
	    $this->add_control(
		    'show_items_tablet', [
			    'label'     => esc_html__( 'Display Items [Tablet]', 'load-more-ajax-lite' ),
			    'type'      => Controls_Manager::NUMBER,

		    ]
	    );
	    $this->add_control(
		    'show_items_mobile', [
			    'label'     => esc_html__( 'Display Items [Mobile]', 'load-more-ajax-lite' ),
			    'type'      => Controls_Manager::NUMBER,
                'default'	=> 1
		    ]
	    );
	    $this->add_control(
		    'item_space',
		    [
			    'label' => __( 'Item Space', 'load-more-ajax-lite' ),
			    'type' => Controls_Manager::SLIDER,
			    'size_units' => [ 'px' ],
			    'range' => [
				    'px' => [
					    'min' => 0,
					    'max' => 200,
					    'step' => 1,
				    ]
			    ],
			    'default' => [
				    'size' => 24,
			    ]
		    ]
	    );
	    $this->add_control(
		    'carousel_autoplay',
		    [
			    'label' => __( 'Auto Play', 'load-more-ajax-lite' ),
			    'type' => \Elementor\Controls_Manager::SWITCHER,
			    'label_on' => __( 'True', 'load-more-ajax-lite' ),
			    'label_off' => __( 'False', 'load-more-ajax-lite' ),
			    'return_value' => 'yes',
			    'default' => 'yes',
		    ]
	    );
	    $this->add_control(
		    'carousel_loop',
		    [
			    'label' => __( 'Loop', 'load-more-ajax-lite' ),
			    'type' => \Elementor\Controls_Manager::SWITCHER,
			    'label_on' => __( 'True', 'load-more-ajax-lite' ),
			    'label_off' => __( 'False', 'load-more-ajax-lite' ),
			    'return_value' => 'yes',
			    'default' => 'yes',
		    ]
	    );
	    $this->add_control(
		    'slide_speed', [
			    'label' => esc_html__( 'Slide Speed', 'load-more-ajax-lite' ),
			    'type' => Controls_Manager::NUMBER,
			    'min' => 0,
			    'max' => 5000,
			    'step' => 1,
                'default' => 500
		    ]
	    );
	    $this->end_controls_section();


		$this->start_controls_section( 'section_subtitle_style', [
			'label' => esc_html__( 'Title', 'load-more-ajax-lite' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'blog_title_color', [
			'label'     => __( 'Color', 'load-more-ajax-lite' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-grid .blog-content .entry-title a, 
                    {{WRAPPER}}  .single_blog_post .post_content h4 a' => 'color: {{VALUE}}',
			],
		] );

		$this->add_control( 'blog_title_color_hover', [
			'label'     => __( 'Hover Color', 'load-more-ajax-lite' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .post-grid .blog-content .entry-title a:hover, 
                    {{WRAPPER}}  .single_blog_post .post_content h4 a:hover' => 'color: {{VALUE}}',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => __( 'Typography', 'load-more-ajax-lite' ),
			'selector' => '{{WRAPPER}} .entry-title',
		] );

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
				'{{WRAPPER}} .blog-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'sec_padding', [
			'label'      => __( 'Padding', 'load-more-ajax-lite' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} .blog-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );
		$this->add_group_control( Group_Control_Background::get_type(), [
			'name' => 'background',
			'label' => esc_html__( 'Background', 'load-more-ajax-lite' ),
			'types' => [ 'classic', 'gradient' ],
			'selector' => '{{WRAPPER}} .blog-section',
		] );

		$this->add_control( 'bg_shape_left', [
			'label'   => __( 'Choose Left Shape Image', 'load-more-ajax-lite' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [
				'url' => plugin_dir_url( __DIR__ ) . 'assets/images/blog/circle-with-frame.png'
			],

		] );
		$this->add_control( 'bg_shape_right', [
			'label'   => __( 'Choose RIght Shape Image', 'load-more-ajax-lite' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [
				'url' => plugin_dir_url( __DIR__ ) . 'assets/images/blog/circle-blue.png'
			],

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
