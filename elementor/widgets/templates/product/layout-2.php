<?php
/**
 * Product Layout 2 - Modern Design Template
 * 
 * This template displays products in a modern card layout
 * matching the demo structure from lma-product-demo.html
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get product data from the AJAX response
$products = $productdata['products'] ?? [];
$total_products = $productdata['total_products'] ?? 0;
$has_more = $productdata['has_more'] ?? false;
$layout = $productdata['block_style'] ?? '2';

if (empty($products)) {
    echo '<div class="lma-no-products">No products found.</div>';
    return;
}

foreach ($products as $product) {
    $product_id = $product['id'] ?? 0;
    $title = $product['title'] ?? '';
    $permalink = $product['permalink'] ?? '#';
    $thumbnail = $product['thumbnail'] ?? '';
    $thumbnail_alt = $product['thumbnail_alt'] ?? $title;
    $categories = $product['categories'] ?? '';
    $price = $product['price'] ?? '';
    $regular_price = $product['regular_price'] ?? '';
    $sale_price = $product['sale_price'] ?? '';
    $rating = $product['rating'] ?? '';
    $rating_count = $product['rating_count'] ?? 0;
    $average_rating = $product['average_rating'] ?? 0;
    $add_to_cart = $product['add_to_cart'] ?? '';
    $sale_badge = $product['sale_badge'] ?? '';
    $on_sale = $product['on_sale'] ?? false;
    $featured = $product['featured'] ?? false;
    $stock_status = $product['stock_status'] ?? '';
    $short_description = $product['short_description'] ?? '';
    ?>
    
    <div class="lma_product_item" data-product-id="<?php echo esc_attr($product_id); ?>">
        <div class="lma_product_thumb">
            <?php if ($thumbnail): ?>
                <a href="<?php echo esc_url($permalink); ?>">
                    <img src="<?php echo esc_url($thumbnail); ?>" 
                         alt="<?php echo esc_attr($thumbnail_alt); ?>" 
                         loading="lazy">
                </a>
            <?php endif; ?>
            
            <?php if ($on_sale): ?>
                <span class="lma_sale_badge">
                    SALE!
                </span>
            <?php endif; ?>
            
            <!-- Product Action Buttons -->
            <div class="product-actions">
                <button class="action-btn" 
                        title="<?php esc_attr_e('Quick View', 'load-more-ajax-lite'); ?>"
                        data-product-id="<?php echo esc_attr($product_id); ?>">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn" 
                        title="<?php esc_attr_e('Add to Wishlist', 'load-more-ajax-lite'); ?>"
                        data-product-id="<?php echo esc_attr($product_id); ?>">
                    <i class="fas fa-heart"></i>
                </button>
                <button class="action-btn" 
                        title="<?php esc_attr_e('Compare', 'load-more-ajax-lite'); ?>"
                        data-product-id="<?php echo esc_attr($product_id); ?>">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
        </div>
        
        <div class="lma_product_content">
            <?php if ($categories): ?>
                <div class="lma_product_categories">
                    <?php echo wp_kses_post($categories); ?>
                </div>
            <?php endif; ?>
            
            <div class="lma_product_title">
                <a href="<?php echo esc_url($permalink); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </div>
            
            <?php if ($rating): ?>
                <div class="lma_product_rating">
                    <?php echo wp_kses_post($rating); ?>
                    <?php if ($rating_count > 0): ?>
                        <span class="rating-text">(<?php echo esc_html($rating_count); ?> reviews)</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($regular_price || $sale_price): ?>
                <div class="lma_product_price">
                    <?php if ($on_sale && $regular_price && $sale_price): ?>
                        <span class="price-original"><?php echo wp_kses_post($regular_price); ?></span>
                        <?php echo wp_kses_post($sale_price); ?>
                    <?php else: ?>
                        <?php echo wp_kses_post($price); ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($add_to_cart): ?>
                <div class="lma_product_cart">
                    <?php echo wp_kses_post($add_to_cart); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php
}
