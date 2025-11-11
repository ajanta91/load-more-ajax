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
class LMA_Products extends Widget_Base {

    public function get_name() {
        return 'lma-products';
    }

    public function get_title() {
        return esc_html__('WooCommerce Products [LMA]', 'load-more-ajax-lite');
    }

    public function get_icon() {
        return 'eicon-products';
    }

    public function get_style_depends() {
        if (\Elementor\Plugin::$instance->preview->is_preview_mode()) {
            return ['lma-woocommerce'];
        } else {
            $settings = $this->get_settings_for_display();
            $styles = ['lma-woocommerce'];

            if ($settings['layout'] == '1') {
                $styles[] = 'load-more-ajax-lite';
            } elseif ($settings['layout'] == '2') {
                $styles[] = 'load-more-ajax-lite-s2';
            } elseif ($settings['layout'] == '3') {
                $styles[] = 'load-more-ajax-lite-s3';
            }

            return $styles;
        }
    }

    public function get_script_depends() {
        return ['lma-woocommerce-js'];
    }

    public function get_categories() {
        return ['load_more_ajax-elements'];
    }

    protected function register_controls() {

        // Layout Settings
        $this->start_controls_section('section_layout', [
            'label' => esc_html__('Layout', 'load-more-ajax-lite'),
        ]);

        $this->add_control('layout', [
            'label' => esc_html__('Product Style', 'load-more-ajax-lite'),
            'type' => Controls_Manager::SELECT,
            'default' => '1',
            'options' => [
                '1' => esc_html__('Layout 1', 'load-more-ajax-lite'),
                '2' => esc_html__('Layout 2', 'load-more-ajax-lite'),
                '3' => esc_html__('Layout 3', 'load-more-ajax-lite')
            ],
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

        $this->end_controls_section();

        // Style Tab
        $this->add_style_controls();
    }

    protected function add_style_controls() {
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
    }

    protected function get_product_categories() {
        $categories = [];

        if (function_exists('lma_get_product_categories')) {
            $terms = lma_get_product_categories();
            foreach ($terms as $term) {
                $categories[$term->slug] = $term->name;
            }
        }

        return $categories;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // Check if WooCommerce is active
        if (!function_exists('lma_is_woocommerce_active') || !lma_is_woocommerce_active()) {
            echo '<div class="lma-notice">' . esc_html__('WooCommerce is not active.', 'load-more-ajax-lite') . '</div>';
            return;
        }

        // Build shortcode attributes
        $atts = [
            'posts_per_page' => $settings['per_page'],
            'style' => $settings['layout'],
            'column' => $settings['product_column'],
            'filter' => $settings['enable_filter'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'featured' => $settings['featured_products'],
            'on_sale' => $settings['on_sale_products'],
            'show_rating' => $settings['show_rating'],
            'show_price' => $settings['show_price'],
            'show_cart_button' => $settings['show_cart_button'],
            'show_sale_badge' => $settings['show_sale_badge'],
            'enable_sort' => $settings['enable_sort'],
        ];

        if (!empty($settings['selected_categories'])) {
            $atts['include'] = implode(',', $settings['selected_categories']);
        }

        // Render using shortcode
        echo do_shortcode('[lma_products ' . $this->build_shortcode_atts($atts) . ']');
    }

    protected function build_shortcode_atts($atts) {
        $output = [];
        foreach ($atts as $key => $value) {
            if (!empty($value) || $value === '0') {
                $output[] = $key . '="' . esc_attr($value) . '"';
            }
        }
        return implode(' ', $output);
    }
}
