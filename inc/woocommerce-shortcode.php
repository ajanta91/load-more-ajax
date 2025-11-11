<?php
/**
 * WooCommerce Product Load More Shortcode
 *
 * Usage: [lma_products posts_per_page="6" style="1" column="3" filter="true"]
 */

if (!defined('ABSPATH')) {
    exit;
}

function lma_products_shortcode($atts) {
    // Check if WooCommerce is active
    if (!lma_is_woocommerce_active()) {
        return '<div class="lma-notice">' . esc_html__('WooCommerce is not active. Please install and activate WooCommerce to use this feature.', 'load-more-ajax-lite') . '</div>';
    }

    $attributes = shortcode_atts([
        'posts_per_page' => 6,
        'include' => '',
        'exclude' => '',
        'filter' => 'true',
        'style' => '1',
        'column' => '3',
        'infinite_scroll' => 'false',
        'enable_search' => 'false',
        'enable_sort' => 'false',
        'animation' => 'true',
        'show_count' => 'true',
        'orderby' => 'date',
        'order' => 'DESC',
        'featured' => 'false',
        'on_sale' => 'false',
        'show_rating' => 'true',
        'show_price' => 'true',
        'show_cart_button' => 'true',
        'show_sale_badge' => 'true',
    ], $atts);

    ob_start();

    $include = !empty($atts['include']) ? $atts['include'] : '';
    $exclude = !empty($atts['exclude']) ? $atts['exclude'] : '';
    $filter = !empty($atts['filter']) ? $atts['filter'] : 'true';
    $style = !empty($atts['style']) ? $atts['style'] : '1';
    $column = !empty($atts['column']) ? $atts['column'] : '3';
    $infinite_scroll = !empty($atts['infinite_scroll']) ? $atts['infinite_scroll'] : 'false';
    $enable_search = !empty($atts['enable_search']) ? $atts['enable_search'] : 'false';
    $enable_sort = !empty($atts['enable_sort']) ? $atts['enable_sort'] : 'false';
    $animation = !empty($atts['animation']) ? $atts['animation'] : 'true';
    $show_count = !empty($atts['show_count']) ? $atts['show_count'] : 'true';
    $orderby = !empty($atts['orderby']) ? $atts['orderby'] : 'date';
    $order = !empty($atts['order']) ? $atts['order'] : 'DESC';
    $featured = !empty($atts['featured']) ? $atts['featured'] : 'false';
    $on_sale = !empty($atts['on_sale']) ? $atts['on_sale'] : 'false';
    $show_rating = !empty($atts['show_rating']) ? $atts['show_rating'] : 'true';
    $show_price = !empty($atts['show_price']) ? $atts['show_price'] : 'true';
    $show_cart_button = !empty($atts['show_cart_button']) ? $atts['show_cart_button'] : 'true';
    $show_sale_badge = !empty($atts['show_sale_badge']) ? $atts['show_sale_badge'] : 'true';

    // Enqueue scripts and styles
    if ($style == '1') {
        wp_enqueue_style('load-more-ajax-lite');
    } elseif ($style == '2') {
        wp_enqueue_style('load-more-ajax-lite-s2');
    } elseif ($style == '3') {
        wp_enqueue_style('load-more-ajax-lite-s3');
    }
    wp_enqueue_style('lma-woocommerce');
    wp_enqueue_script('lma-woocommerce-js');

    // Column setup
    switch ($column) {
        case 'full':
            $wraper_class = 'full';
            break;
        case '2':
            $wraper_class = 'column_2';
            break;
        case '3':
            $wraper_class = 'column_3';
            break;
        case '4':
            $wraper_class = 'column_4';
            break;
        case '5':
            $wraper_class = 'column_5';
            break;
        default:
            $wraper_class = 'column_2';
            break;
    }

    $limit = !empty($atts['posts_per_page']) ? $atts['posts_per_page'] : '6';

    // Create wrapper with enhanced data attributes
    $block_classes = 'apl_block_wraper lma_block_style_' . esc_attr($style) . ' lma_products_block';
    $data_attributes = [
        'data-infinite-scroll' => esc_attr($infinite_scroll),
        'data-enable-search' => esc_attr($enable_search),
        'data-enable-sort' => esc_attr($enable_sort),
        'data-animation' => esc_attr($animation),
        'data-show-count' => esc_attr($show_count),
        'data-content-type' => 'products',
        'data-show-rating' => esc_attr($show_rating),
        'data-show-price' => esc_attr($show_price),
        'data-show-cart-button' => esc_attr($show_cart_button),
        'data-show-sale-badge' => esc_attr($show_sale_badge),
    ];

    echo '<div class="' . esc_attr($block_classes) . '" ' . implode(' ', array_map(function($key, $value) {
        return $key . '="' . $value . '"';
    }, array_keys($data_attributes), $data_attributes)) . '>';

    // Category filter
    if ($filter == 'true') { ?>
        <div class="cat_filter product_cat_filter">
            <?php
            $args = [
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            ];

            if (!empty($include)) {
                $args['include'] = $include;
            }
            if (!empty($exclude)) {
                $args['exclude'] = $exclude;
            }

            $categories = get_terms($args);

            $all_cat_id = '';
            if (is_array($categories) && !empty($categories)) {
                $cat_count = count($categories);
                $count = $cat_count - 1;
                foreach ($categories as $key => $value) {
                    $all_cat_id .= $key < $count ? $value->term_id . ',' : $value->term_id;
                }
            }

            echo '<div data-cateid="' . esc_attr($all_cat_id) . '" class="ajax_post_cat active">' . esc_html__('All Products', 'load-more-ajax-lite') . '</div>';

            if (is_array($categories)) {
                foreach ($categories as $cat) {
                    echo '<div data-cateid="' . esc_attr($cat->term_id) . '" data-filter="' . esc_attr($cat->slug) . '" class="ajax_post_cat">' . esc_html($cat->name) . '</div>';
                }
            }
            ?>
        </div>
    <?php }

    // Sort options
    if ($enable_sort == 'true') { ?>
        <div class="lma_sort_options">
            <select class="lma_product_sort" data-sort-type="products">
                <option value="date:DESC"><?php esc_html_e('Latest', 'load-more-ajax-lite'); ?></option>
                <option value="price:ASC"><?php esc_html_e('Price: Low to High', 'load-more-ajax-lite'); ?></option>
                <option value="price:DESC"><?php esc_html_e('Price: High to Low', 'load-more-ajax-lite'); ?></option>
                <option value="popularity:DESC"><?php esc_html_e('Popularity', 'load-more-ajax-lite'); ?></option>
                <option value="rating:DESC"><?php esc_html_e('Average Rating', 'load-more-ajax-lite'); ?></option>
                <option value="title:ASC"><?php esc_html_e('Name: A to Z', 'load-more-ajax-lite'); ?></option>
            </select>
        </div>
    <?php }

    // Products container
    echo '<div class="ajaxproduct_loader ' . esc_attr($wraper_class) . '"
        data-block_style="' . esc_attr($style) . '"
        data-column="' . esc_attr($wraper_class) . '"
        data-content_type="products"
        data-order="1"
        data-limit="' . esc_attr($limit) . '"
        data-cate=""
        data-orderby="' . esc_attr($orderby) . '"
        data-sort-order="' . esc_attr($order) . '"
        data-featured="' . esc_attr($featured) . '"
        data-on-sale="' . esc_attr($on_sale) . '"
        data-show-rating="' . esc_attr($show_rating) . '"
        data-show-price="' . esc_attr($show_price) . '"
        data-show-cart-button="' . esc_attr($show_cart_button) . '"
        data-show-sale-badge="' . esc_attr($show_sale_badge) . '"></div>';

    echo '<div class="load_more_wrapper"><button class="loadmore_ajax loadmore_products" type="button">' . esc_html__('Load More Products', 'load-more-ajax-lite') . '</button></div>';

    echo '</div>';

    return ob_get_clean();
}
add_shortcode('lma_products', 'lma_products_shortcode');
