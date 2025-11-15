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
 * WooCommerce Products Widget
 *
 * Retrieve and display WooCommerce products with AJAX load more
 *
 * @since 1.0.0
 */
class LMA_Products extends Widget_Base
{

    public function get_name()
    {
        return 'lma-products';
    }

    public function get_title()
    {
        return esc_html__('WooCommerce Products [LMA]', 'load-more-ajax-lite');
    }

    public function get_icon()
    {
        return 'eicon-products';
    }

    public function get_style_depends()
    {
        return ['woocommerce-general'];
    }

    public function get_script_depends()
    {
        return ['lma-woocommerce-js'];
    }

    public function get_categories()
    {
        return ['load_more_ajax-elements'];
    }

    protected function register_controls()
    {

        // Layout Settings
        $this->start_controls_section('section_layout', [
            'label' => esc_html__('Layout', 'load-more-ajax-lite'),
        ]);

        $this->add_control('layout', [
            'label' => esc_html__('Product Style', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SELECT,
            'default' => '1',
            'options' => [
                '1' => esc_html__('Classic Layout', 'load-more-ajax-lite'),
                '2' => esc_html__('Modern Card Layout', 'load-more-ajax-lite'),
                '3' => esc_html__('Minimal Layout', 'load-more-ajax-lite')
            ],
        ]);

        $this->add_control('layout_description', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => '<div style="font-size: 12px; color: #666; margin-top: 5px;">
                <strong>Classic:</strong> Traditional product grid<br>
                <strong>Modern Card:</strong> Elegant cards with hover effects and action buttons<br>
                <strong>Minimal:</strong> Clean and simple design
            </div>',
            'content_classes' => 'elementor-descriptor',
        ]);

        $this->add_control('product_column', [
            'label' => esc_html__('Columns', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '2' => esc_html__('2 Columns', 'load-more-ajax-lite'),
                '3' => esc_html__('3 Columns', 'load-more-ajax-lite'),
                '4' => esc_html__('4 Columns', 'load-more-ajax-lite'),
                '5' => esc_html__('5 Columns', 'load-more-ajax-lite'),
                'full' => esc_html__('Full Width', 'load-more-ajax-lite'),
            ],
        ]);

        $this->end_controls_section();

        // Modern Layout Settings (Layout 2)
        $this->start_controls_section('section_modern_layout', [
            'label' => esc_html__('Modern Layout Settings', 'load-more-ajax-lite'),
            'condition' => [
                'layout' => '2',
            ],
        ]);

        $this->add_control('show_action_buttons', [
            'label' => esc_html__('Show Action Buttons', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
            'description' => esc_html__('Quick view, wishlist, and compare buttons on product hover', 'load-more-ajax-lite'),
        ]);

        $this->add_control('show_product_description', [
            'label' => esc_html__('Show Short Description', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('show_stock_status', [
            'label' => esc_html__('Show Stock Status', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('enable_hover_effects', [
            'label' => esc_html__('Enable Hover Effects', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Enable', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Disable', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
            'description' => esc_html__('Card elevation and image zoom effects on hover', 'load-more-ajax-lite'),
        ]);

        $this->add_control('card_border_radius', [
            'label' => esc_html__('Card Border Radius', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 30,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 15,
            ],
            'selectors' => [
                '{{WRAPPER}} .modern-product-card' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('image_height', [
            'label' => esc_html__('Product Image Height', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 200,
                    'max' => 400,
                    'step' => 10,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 280,
            ],
            'selectors' => [
                '{{WRAPPER}} .modern-product-image' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->end_controls_section();

        // Query Settings
        $this->start_controls_section('section_query', [
            'label' => esc_html__('Query Settings', 'load-more-ajax-lite'),
        ]);

        $this->add_control('per_page', [
            'label' => esc_html__('Products Per Page', 'load-more-ajax-lite'),
            'type' => Controls_Manager::NUMBER,
            'default' => 6
        ]);

        $this->add_control('orderby', [
            'label' => esc_html__('Order By', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'date' => esc_html__('Date', 'load-more-ajax-lite'),
                'price' => esc_html__('Price', 'load-more-ajax-lite'),
                'popularity' => esc_html__('Popularity', 'load-more-ajax-lite'),
                'rating' => esc_html__('Rating', 'load-more-ajax-lite'),
                'title' => esc_html__('Title', 'load-more-ajax-lite'),
                'menu_order' => esc_html__('Menu Order', 'load-more-ajax-lite'),
            ],
            'default' => 'date',
        ]);

        $this->add_control('order', [
            'label' => esc_html__('Sort Order', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'ASC' => esc_html__('Ascending', 'load-more-ajax-lite'),
                'DESC' => esc_html__('Descending', 'load-more-ajax-lite'),
            ],
            'default' => 'DESC',
        ]);

        $this->add_control('selected_categories', [
            'label' => esc_html__('Select Categories', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'label_block' => true,
            'options' => $this->get_product_categories(),
        ]);

        $this->add_control('featured_products', [
            'label' => esc_html__('Featured Products Only', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'load-more-ajax-lite'),
            'label_off' => esc_html__('No', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'false',
        ]);

        $this->add_control('on_sale_products', [
            'label' => esc_html__('On Sale Products Only', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'load-more-ajax-lite'),
            'label_off' => esc_html__('No', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'false',
        ]);

        $this->end_controls_section();

        // Display Settings
        $this->start_controls_section('section_display', [
            'label' => esc_html__('Display Settings', 'load-more-ajax-lite'),
        ]);

        $this->add_control('show_rating', [
            'label' => esc_html__('Show Rating', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('show_price', [
            'label' => esc_html__('Show Price', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('show_cart_button', [
            'label' => esc_html__('Show Add to Cart Button', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('show_sale_badge', [
            'label' => esc_html__('Show Sale Badge', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('enable_filter', [
            'label' => esc_html__('Enable Category Filter', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'load-more-ajax-lite'),
            'label_off' => esc_html__('No', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
        ]);

        $this->add_control('enable_sort', [
            'label' => esc_html__('Enable Sort Options', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'load-more-ajax-lite'),
            'label_off' => esc_html__('No', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'false',
        ]);

        $this->add_control('button_text', [
            'label' => esc_html__('Load More Button Text', 'load-more-ajax-lite'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Load More Products', 'load-more-ajax-lite'),
            'placeholder' => esc_html__('Enter button text', 'load-more-ajax-lite'),
        ]);

        $this->add_control('loading_text', [
            'label' => esc_html__('Loading Text', 'load-more-ajax-lite'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Loading...', 'load-more-ajax-lite'),
            'placeholder' => esc_html__('Enter loading text', 'load-more-ajax-lite'),
        ]);

        $this->add_control('no_more_text', [
            'label' => esc_html__('No More Products Text', 'load-more-ajax-lite'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('No More Products', 'load-more-ajax-lite'),
            'placeholder' => esc_html__('Enter no more text', 'load-more-ajax-lite'),
        ]);

        $this->add_control('show_count', [
            'label' => esc_html__('Show Product Count', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'load-more-ajax-lite'),
            'label_off' => esc_html__('Hide', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'false',
            'description' => esc_html__('Show "Showing X of Y products" text', 'load-more-ajax-lite'),
        ]);

        $this->add_control('enable_animation', [
            'label' => esc_html__('Enable Animation', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'load-more-ajax-lite'),
            'label_off' => esc_html__('No', 'load-more-ajax-lite'),
            'return_value' => 'true',
            'default' => 'true',
            'description' => esc_html__('Animate new products when they load', 'load-more-ajax-lite'),
        ]);

        $this->end_controls_section();

        // Style Tab
        $this->add_style_controls();
    }

    protected function add_style_controls()
    {
        // Product Title Style
        $this->start_controls_section('section_title_style', [
            'label' => esc_html__('Product Title', 'load-more-ajax-lite'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_color', [
            'label' => esc_html__('Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lma_product_title a' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .lma_product_title',
        ]);

        $this->end_controls_section();

        // Price Style
        $this->start_controls_section('section_price_style', [
            'label' => esc_html__('Price', 'load-more-ajax-lite'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('price_color', [
            'label' => esc_html__('Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lma_product_price' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'price_typography',
            'selector' => '{{WRAPPER}} .lma_product_price',
        ]);

        $this->end_controls_section();

        // Button Style
        $this->start_controls_section('section_button_style', [
            'label' => esc_html__('Add to Cart Button', 'load-more-ajax-lite'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab('tab_button_normal', [
            'label' => esc_html__('Normal', 'load-more-ajax-lite'),
        ]);

        $this->add_control('button_text_color', [
            'label' => esc_html__('Text Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lma_product_cart .button' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('button_bg_color', [
            'label' => esc_html__('Background Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lma_product_cart .button' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_button_hover', [
            'label' => esc_html__('Hover', 'load-more-ajax-lite'),
        ]);

        $this->add_control('button_hover_color', [
            'label' => esc_html__('Text Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lma_product_cart .button:hover' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('button_hover_bg_color', [
            'label' => esc_html__('Background Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .lma_product_cart .button:hover' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'button_typography',
            'selector' => '{{WRAPPER}} .lma_product_cart .button',
            'separator' => 'before',
        ]);

        $this->end_controls_section();

        // Load More Button Style
        $this->start_controls_section('section_load_more_style', [
            'label' => esc_html__('Load More Button', 'load-more-ajax-lite'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->start_controls_tabs('tabs_load_more_style');

        $this->start_controls_tab('tab_load_more_normal', [
            'label' => esc_html__('Normal', 'load-more-ajax-lite'),
        ]);

        $this->add_control('load_more_text_color', [
            'label' => esc_html__('Text Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .loadmore_products' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('load_more_bg_color', [
            'label' => esc_html__('Background Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .loadmore_products' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'load_more_border',
            'selector' => '{{WRAPPER}} .loadmore_products',
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tab_load_more_hover', [
            'label' => esc_html__('Hover', 'load-more-ajax-lite'),
        ]);

        $this->add_control('load_more_hover_color', [
            'label' => esc_html__('Text Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .loadmore_products:hover' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('load_more_hover_bg_color', [
            'label' => esc_html__('Background Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .loadmore_products:hover' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'load_more_typography',
            'selector' => '{{WRAPPER}} .loadmore_products',
            'separator' => 'before',
        ]);

        $this->add_control('load_more_padding', [
            'label' => esc_html__('Padding', 'load-more-ajax-lite'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .loadmore_products' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('load_more_margin', [
            'label' => esc_html__('Margin', 'load-more-ajax-lite'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .load_more_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control('load_more_align', [
            'label' => esc_html__('Alignment', 'load-more-ajax-lite'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'load-more-ajax-lite'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'load-more-ajax-lite'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'load-more-ajax-lite'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .load_more_wrapper' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // Product Container Style
        $this->start_controls_section('section_container_style', [
            'label' => esc_html__('Product Container', 'load-more-ajax-lite'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control('products_gap', [
            'label' => esc_html__('Products Gap', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'selectors' => [
                '{{WRAPPER}} .ajaxproduct_loader' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'container_border',
            'selector' => '{{WRAPPER}} .lma_product_item',
        ]);

        $this->add_control('container_border_radius', [
            'label' => esc_html__('Border Radius', 'load-more-ajax-lite'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .lma_product_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'container_shadow',
            'selector' => '{{WRAPPER}} .lma_product_item',
        ]);

        $this->end_controls_section();

        // Modern Layout Style Controls
        $this->start_controls_section('section_modern_style', [
            'label' => esc_html__('Modern Card Style', 'load-more-ajax-lite'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'layout' => '2',
            ],
        ]);

        $this->add_control('modern_card_background', [
            'label' => esc_html__('Card Background', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .modern-product-card' => 'background-color: {{VALUE}}',
            ],
        ]);

        $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'modern_card_shadow',
            'label' => esc_html__('Card Shadow', 'load-more-ajax-lite'),
            'selector' => '{{WRAPPER}} .modern-product-card',
            'fields_options' => [
                'box_shadow_type' => [
                    'default' => 'yes',
                ],
                'box_shadow' => [
                    'default' => [
                        'horizontal' => 0,
                        'vertical' => 4,
                        'blur' => 20,
                        'spread' => 0,
                        'color' => 'rgba(0,0,0,0.08)',
                    ],
                ],
            ],
        ]);

        $this->add_control('modern_hover_shadow', [
            'label' => esc_html__('Hover Shadow Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(0,0,0,0.15)',
            'selectors' => [
                '{{WRAPPER}} .modern-product-card:hover' => 'box-shadow: 0 20px 40px {{VALUE}}',
            ],
        ]);

        $this->add_control('sale_badge_heading', [
            'label' => esc_html__('Sale Badge', 'load-more-ajax-lite'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('sale_badge_bg_color', [
            'label' => esc_html__('Badge Background', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ff6b6b',
            'selectors' => [
                '{{WRAPPER}} .modern-sale-badge' => 'background: linear-gradient(135deg, {{VALUE}}, #ee5a52)',
            ],
        ]);

        $this->add_control('sale_badge_text_color', [
            'label' => esc_html__('Badge Text Color', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .modern-sale-badge' => 'color: {{VALUE}}',
            ],
        ]);

        $this->add_control('action_buttons_heading', [
            'label' => esc_html__('Action Buttons', 'load-more-ajax-lite'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'show_action_buttons' => 'true',
            ],
        ]);

        $this->add_control('action_button_bg', [
            'label' => esc_html__('Button Background', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(255,255,255,0.95)',
            'selectors' => [
                '{{WRAPPER}} .action-btn' => 'background: {{VALUE}}',
            ],
            'condition' => [
                'show_action_buttons' => 'true',
            ],
        ]);

        $this->add_control('action_button_hover_bg', [
            'label' => esc_html__('Button Hover Background', 'load-more-ajax-lite'),
            'type' => Controls_Manager::COLOR,
            'default' => '#667eea',
            'selectors' => [
                '{{WRAPPER}} .action-btn:hover' => 'background: linear-gradient(135deg, {{VALUE}} 0%, #764ba2 100%)',
            ],
            'condition' => [
                'show_action_buttons' => 'true',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function get_product_categories()
    {
        $categories = [];

        if (function_exists('lma_get_product_categories')) {
            $terms = lma_get_product_categories();
            foreach ($terms as $term) {
                $categories[$term->term_id] = $term->name;
            }
        }

        return $categories;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        // Check if WooCommerce is active
        if (!function_exists('lma_is_woocommerce_active') || !lma_is_woocommerce_active()) {
            echo '<div class="lma-notice">' . esc_html__('WooCommerce is not active.', 'load-more-ajax-lite') . '</div>';
            return;
        }

        // Enqueue necessary styles
        wp_enqueue_style('load-more-ajax-lite');
        
        // Enqueue modern layout styles for Layout 2
        if ($settings['layout'] === '2') {
            wp_enqueue_style('lma-modern-layout');
        }
        
        if (class_exists('WooCommerce')) {
            wp_enqueue_style('lma-woocommerce');
            wp_enqueue_script('lma-woocommerce-js');
        }

        // Map column settings
        $column_map = [
            '2' => 'column_2',
            '3' => 'column_3',
            '4' => 'column_4',
            '5' => 'column_5',
            'full' => 'column_full'
        ];

        $column_class = isset($column_map[$settings['product_column']]) ? $column_map[$settings['product_column']] : 'column_3';

        // Prepare category IDs if selected
        $category_ids = '';
        if (!empty($settings['selected_categories'])) {
            $category_ids = implode(',', $settings['selected_categories']);
        }

?>
        <div class="lma_products_block lma_block_style_<?php echo esc_attr($settings['layout']); ?>"
            data-layout="<?php echo esc_attr($settings['layout']); ?>"
            data-category="<?php echo esc_attr($category_ids); ?>"
            data-limit="<?php echo esc_attr($settings['per_page']); ?>"
            data-orderby="<?php echo esc_attr($settings['orderby']); ?>">

            <?php if ($settings['enable_filter'] == 'true'): ?>
                <div class="cat_filter product_cat_filter">
                    <div class="lma_productcategory">
                    <?php
                    $args = [
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                        'orderby' => 'name',
                        'order' => 'ASC',
                    ];

                    // If specific categories are selected, only show those
                    if (!empty($settings['selected_categories'])) {
                        $args['include'] = $settings['selected_categories'];
                    }

                    $categories = get_terms($args);

                    // Build "All" category IDs string
                    $all_cat_ids = '';
                    if (is_array($categories) && !empty($categories)) {
                        $cat_ids = array_map(function ($cat) {
                            return $cat->term_id;
                        }, $categories);
                        $all_cat_ids = implode(',', $cat_ids);
                    }

                    echo '<a href="#" data-cateid="' . esc_attr($all_cat_ids) . '" class="ajax_post_cat active">' . esc_html__('All Products', 'load-more-ajax-lite') . '</a>';

                    if (is_array($categories)) {
                        foreach ($categories as $cat) {
                            echo '<a href="' . esc_url(get_term_link($cat)) . '" data-cateid="' . esc_attr($cat->term_id) . '" data-filter="' . esc_attr($cat->slug) . '" class="ajax_post_cat">' . esc_html($cat->name) . '</a>';
                        }
                    }
                    echo '</div>'; // Close lma_productcategory div
                    if ($settings['enable_sort'] == 'true'): ?>
                        <div class="lma_product_sort_wrapper">
                            <select class="lma_product_sort">
                                <option value="date:DESC" <?php echo $settings['orderby'] == 'date' && $settings['order'] == 'DESC' ? ' selected' : ''; ?>><?php esc_html_e('Newest First', 'load-more-ajax-lite'); ?></option>
                                <option value="date:ASC" <?php echo $settings['orderby'] == 'date' && $settings['order'] == 'ASC' ? ' selected' : ''; ?>><?php esc_html_e('Oldest First', 'load-more-ajax-lite'); ?></option>
                                <option value="price:ASC" <?php echo $settings['orderby'] == 'price' && $settings['order'] == 'ASC' ? ' selected' : ''; ?>><?php esc_html_e('Price: Low to High', 'load-more-ajax-lite'); ?></option>
                                <option value="price:DESC" <?php echo $settings['orderby'] == 'price' && $settings['order'] == 'DESC' ? ' selected' : ''; ?>><?php esc_html_e('Price: High to Low', 'load-more-ajax-lite'); ?></option>
                                <option value="popularity:DESC" <?php echo $settings['orderby'] == 'popularity' ? ' selected' : ''; ?>><?php esc_html_e('Most Popular', 'load-more-ajax-lite'); ?></option>
                                <option value="rating:DESC" <?php echo $settings['orderby'] == 'rating' ? ' selected' : ''; ?>><?php esc_html_e('Highest Rated', 'load-more-ajax-lite'); ?></option>
                                <option value="title:ASC" <?php echo $settings['orderby'] == 'title' && $settings['order'] == 'ASC' ? ' selected' : ''; ?>><?php esc_html_e('Name: A to Z', 'load-more-ajax-lite'); ?></option>
                                <option value="title:DESC" <?php echo $settings['orderby'] == 'title' && $settings['order'] == 'DESC' ? ' selected' : ''; ?>><?php esc_html_e('Name: Z to A', 'load-more-ajax-lite'); ?></option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="ajaxproduct_loader <?php echo esc_attr($column_class); ?>"
                data-limit="<?php echo esc_attr($settings['per_page']); ?>"
                data-cate="<?php echo esc_attr($category_ids); ?>"
                data-block_style="<?php echo esc_attr($settings['layout']); ?>"
                data-column="<?php echo esc_attr($column_class); ?>"
                data-orderby="<?php echo esc_attr($settings['orderby']); ?>"
                data-sortOrder="<?php echo esc_attr($settings['order']); ?>"
                data-featured="<?php echo esc_attr($settings['featured_products']); ?>"
                data-onSale="<?php echo esc_attr($settings['on_sale_products']); ?>"
                data-showRating="<?php echo esc_attr($settings['show_rating']); ?>"
                data-showPrice="<?php echo esc_attr($settings['show_price']); ?>"
                data-showCartButton="<?php echo esc_attr($settings['show_cart_button']); ?>"
                data-showSaleBadge="<?php echo esc_attr($settings['show_sale_badge']); ?>"
                data-showCount="<?php echo esc_attr($settings['show_count']); ?>"
                data-enableAnimation="<?php echo esc_attr($settings['enable_animation']); ?>"
                data-showActionButtons="<?php echo esc_attr($settings['show_action_buttons'] ?? 'true'); ?>"
                data-showDescription="<?php echo esc_attr($settings['show_product_description'] ?? 'true'); ?>"
                data-showStockStatus="<?php echo esc_attr($settings['show_stock_status'] ?? 'true'); ?>"
                data-enableHoverEffects="<?php echo esc_attr($settings['enable_hover_effects'] ?? 'true'); ?>">
                <!-- Products will load here via AJAX -->
            </div>

            <?php if ($settings['show_count'] == 'true'): ?>
                <div class="lma_product_count" style="margin: 15px 0; text-align: center;">
                    <span class="lma_showing_text"></span>
                </div>
            <?php endif; ?>

            <div class="load_more_wrapper">
                <button class="loadmore_products" type="button"
                    data-button-text="<?php echo esc_attr($settings['button_text'] ?: 'Load More Products'); ?>"
                    data-loading-text="<?php echo esc_attr($settings['loading_text'] ?: 'Loading...'); ?>"
                    data-no-more-text="<?php echo esc_attr($settings['no_more_text'] ?: 'No More Products'); ?>">
                    <?php echo esc_html($settings['button_text'] ?: 'Load More Products'); ?>
                </button>
            </div>

        </div>
<?php
    }
}
