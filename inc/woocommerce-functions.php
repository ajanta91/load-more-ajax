<?php
/**
 * WooCommerce Functions for Load More Ajax
 *
 * Handles WooCommerce product loading and filtering
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if WooCommerce is active
 */
function lma_is_woocommerce_active() {
    return class_exists('WooCommerce');
}

/**
 * Get WooCommerce product categories for filter
 */
function lma_get_product_categories() {
    if (!lma_is_woocommerce_active()) {
        return [];
    }

    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);

    return is_wp_error($categories) ? [] : $categories;
}

/**
 * Get product price HTML
 */
function lma_get_product_price($product_id) {
    if (!lma_is_woocommerce_active()) {
        return '';
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    return $product->get_price_html();
}

/**
 * Get product rating HTML
 */
function lma_get_product_rating($product_id) {
    if (!lma_is_woocommerce_active()) {
        return '';
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    $rating_count = $product->get_rating_count();
    $average = $product->get_average_rating();

    if ($rating_count > 0) {
        return wc_get_rating_html($average, $rating_count);
    }

    return '';
}

/**
 * Get add to cart button HTML
 */
function lma_get_add_to_cart_button($product_id) {
    if (!lma_is_woocommerce_active()) {
        return '';
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    $button_text = $product->add_to_cart_text();
    $button_url = $product->add_to_cart_url();
    $product_type = $product->get_type();

    $classes = implode(' ', array_filter([
        'button',
        'product_type_' . $product_type,
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
        $product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
    ]));

    return sprintf(
        '<a href="%s" data-quantity="1" class="%s" data-product_id="%s" data-product_sku="%s" aria-label="%s" rel="nofollow">%s</a>',
        esc_url($button_url),
        esc_attr($classes),
        esc_attr($product_id),
        esc_attr($product->get_sku()),
        esc_attr($product->add_to_cart_description()),
        esc_html($button_text)
    );
}

/**
 * Get product categories for a product
 */
function lma_get_product_categories_html($product_id) {
    if (!lma_is_woocommerce_active()) {
        return '';
    }

    $categories = get_the_terms($product_id, 'product_cat');
    $cat_html = '';

    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $cat_html .= '<a href="' . esc_url(get_term_link($category->term_id, 'product_cat')) . '" class="product_category">' . esc_html($category->name) . '</a>';
        }
    }

    return $cat_html;
}

/**
 * Get product sale badge
 */
function lma_get_product_sale_badge($product_id) {
    if (!lma_is_woocommerce_active()) {
        return '';
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    if ($product->is_on_sale()) {
        return '<span class="onsale">' . esc_html__('Sale!', 'load-more-ajax-lite') . '</span>';
    }

    return '';
}

/**
 * Get product stock status
 */
function lma_get_product_stock_status($product_id) {
    if (!lma_is_woocommerce_active()) {
        return '';
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return '';
    }

    $availability = $product->get_availability();

    if (!empty($availability['availability'])) {
        return '<span class="stock ' . esc_attr($availability['class']) . '">' . esc_html($availability['availability']) . '</span>';
    }

    return '';
}

/**
 * WooCommerce Product Ajax Handler
 */
add_action('wp_ajax_nopriv_lma_load_products', 'lma_load_products_ajax');
add_action('wp_ajax_lma_load_products', 'lma_load_products_ajax');

function lma_load_products_ajax() {
    try {
        // Security checks
        if (class_exists('LMA_Security')) {
            if (!LMA_Security::check_rate_limit('lma_load_products', 120)) {
                LMA_Security::log_security_event('rate_limit', 'Rate limit exceeded for lma_load_products');
                wp_send_json_error(['error' => true, 'message' => esc_html__('Too many requests. Please wait.', 'load-more-ajax-lite')]);
            }

            // Verify nonce - make it optional for backward compatibility
            $nonce_provided = isset($_POST['nonce']) && !empty($_POST['nonce']);
            if ($nonce_provided && !LMA_Security::verify_ajax_nonce('load_more_ajax_nonce', 'nonce')) {
                LMA_Security::log_security_event('invalid_nonce', 'Invalid nonce for lma_load_products');
                wp_send_json_error(['error' => true, 'message' => esc_html__('Security check failed.', 'load-more-ajax-lite')]);
            }
        }

        if (!lma_is_woocommerce_active()) {
            wp_send_json_error(['error' => true, 'message' => esc_html__('WooCommerce is not active.', 'load-more-ajax-lite')]);
        }

        // Validate and sanitize inputs
        if (class_exists('LMA_Security')) {
            $order = LMA_Security::validate_numeric($_POST['order'] ?? 1, 1, 999, 1);
            $limit = LMA_Security::validate_numeric($_POST['limit'] ?? 6, 1, 50, 6);
            $cat = LMA_Security::validate_category_ids($_POST['cate'] ?? '', 'product_cat');
            $block_style = LMA_Security::validate_numeric($_POST['block_style'] ?? 1, 1, 3, 1);
        } else {
            // Fallback validation
            $order = intval($_POST['order'] ?? 1);
            $limit = intval($_POST['limit'] ?? 6);
            $cat = sanitize_text_field($_POST['cate'] ?? '');
            $block_style = intval($_POST['block_style'] ?? 1);
        }

        $image_size = sanitize_text_field($_POST['column'] ?? 'woocommerce_thumbnail');
        $sort_by = sanitize_text_field($_POST['sort_by'] ?? 'date');
        $sort_order = sanitize_text_field($_POST['sort_order'] ?? 'DESC');
        $featured = sanitize_text_field($_POST['featured'] ?? 'false');
        $on_sale = sanitize_text_field($_POST['on_sale'] ?? 'false');
        $min_price = isset($_POST['min_price']) ? floatval($_POST['min_price']) : 0;
        $max_price = isset($_POST['max_price']) ? floatval($_POST['max_price']) : 0;

        // Build query arguments
        $args = [
            'post_type' => 'product',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'paged' => $order,
            'tax_query' => [
                [
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => ['exclude-from-catalog', 'exclude-from-search'],
                    'operator' => 'NOT IN',
                ],
            ],
        ];

        // Add sorting
        switch ($sort_by) {
            case 'price':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'popularity':
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'rating':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'title':
                $args['orderby'] = 'title';
                break;
            case 'menu_order':
                $args['orderby'] = 'menu_order title';
                break;
            case 'rand':
                $args['orderby'] = 'rand';
                break;
            default:
                $args['orderby'] = 'date';
        }
        $args['order'] = in_array($sort_order, ['ASC', 'DESC']) ? $sort_order : 'DESC';

        // Add category filter
        if (!empty($cat)) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => explode(',', $cat),
                'operator' => 'IN',
            ];
        }

        // Featured products filter
        if ($featured === 'true') {
            $args['tax_query'][] = [
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'featured',
            ];
        }

        // On sale products filter
        if ($on_sale === 'true') {
            $args['post__in'] = array_merge([0], wc_get_product_ids_on_sale());
        }

        // Price filter
        if ($min_price > 0 || $max_price > 0) {
            $args['meta_query'] = isset($args['meta_query']) ? $args['meta_query'] : [];

            if ($min_price > 0 && $max_price > 0) {
                $args['meta_query'][] = [
                    'key' => '_price',
                    'value' => [$min_price, $max_price],
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                ];
            } elseif ($min_price > 0) {
                $args['meta_query'][] = [
                    'key' => '_price',
                    'value' => $min_price,
                    'compare' => '>=',
                    'type' => 'NUMERIC',
                ];
            } elseif ($max_price > 0) {
                $args['meta_query'][] = [
                    'key' => '_price',
                    'value' => $max_price,
                    'compare' => '<=',
                    'type' => 'NUMERIC',
                ];
            }
        }

        // Execute query
        $query = new WP_Query($args);

        $productdata = [
            'products' => [],
            'pagination' => [
                'current_page' => $order,
                'next_page' => $order + 1,
                'total_pages' => $query->max_num_pages,
                'total_products' => $query->found_posts,
                'has_more' => ($order < $query->max_num_pages),
            ],
            'meta' => [
                'block_style' => $block_style,
                'limit' => $limit,
                'showing' => sprintf(
                    esc_html__('Showing %d-%d of %d products', 'load-more-ajax-lite'),
                    (($order - 1) * $limit) + 1,
                    min($order * $limit, $query->found_posts),
                    $query->found_posts
                ),
            ],
        ];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $product;

                if (!$product) {
                    $product = wc_get_product(get_the_ID());
                }

                $product_data = [
                    'id' => get_the_ID(),
                    'class' => implode(' ', get_post_class()),
                    'title' => get_the_title(),
                    'permalink' => esc_url(get_the_permalink()),
                    'thumbnail' => esc_url(get_the_post_thumbnail_url(get_the_ID(), $image_size)),
                    'thumbnail_alt' => get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true),
                    'categories' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content(lma_get_product_categories_html(get_the_ID())) : lma_get_product_categories_html(get_the_ID()),
                    'price' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content($product->get_price_html()) : $product->get_price_html(),
                    'rating' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content(lma_get_product_rating(get_the_ID())) : lma_get_product_rating(get_the_ID()),
                    'add_to_cart' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content(lma_get_add_to_cart_button(get_the_ID())) : lma_get_add_to_cart_button(get_the_ID()),
                    'sale_badge' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content(lma_get_product_sale_badge(get_the_ID())) : lma_get_product_sale_badge(get_the_ID()),
                    'stock_status' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content(lma_get_product_stock_status(get_the_ID())) : lma_get_product_stock_status(get_the_ID()),
                    'short_description' => esc_html(wp_trim_words(get_the_excerpt(), 15, '...')),
                    'on_sale' => $product->is_on_sale(),
                    'featured' => $product->is_featured(),
                    'product_type' => $product->get_type(),
                    'block_style' => $block_style,
                ];

                // Apply filters for extensibility
                $product_data = apply_filters('lma_ajax_product_data', $product_data, $product, $args);

                $productdata['products'][] = $product_data;
            }
            wp_reset_postdata();
        }

        // Backward compatibility
        $productdata['paged'] = $order + 1;
        $productdata['limit'] = $limit;
        $productdata['block_style'] = esc_html($block_style);

        // Apply filters to final data
        $productdata = apply_filters('lma_ajax_product_response_data', $productdata, $args);

        wp_send_json_success($productdata);

    } catch (Exception $e) {
        // Log error
        if (class_exists('LMA_Security')) {
            LMA_Security::log_security_event('ajax_error', 'Product Ajax request failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        } else {
            error_log('Load More Ajax WooCommerce Error: ' . $e->getMessage());
        }

        wp_send_json_error([
            'error' => true,
            'message' => esc_html__('Something went wrong. Please try again.', 'load-more-ajax-lite'),
        ]);
    }
}
