<?php
/**
 * WooCommerce Load More Ajax - Usage Examples
 *
 * This file contains example implementations for the WooCommerce
 * Load More Ajax feature. Copy and paste these examples into your
 * theme files where needed.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Example 1: Basic Product Grid
 * Display a simple 3-column product grid with 6 products
 */
function lma_example_basic_products() {
    echo do_shortcode('[lma_products posts_per_page="6" column="3"]');
}

/**
 * Example 2: Featured Products Showcase
 * Display only featured products in a 4-column layout
 */
function lma_example_featured_products() {
    echo do_shortcode('[lma_products featured="true" column="4" posts_per_page="8" style="2"]');
}

/**
 * Example 3: Sale Products with Filter
 * Display on-sale products with category filter and sort options
 */
function lma_example_sale_products() {
    echo do_shortcode('[lma_products on_sale="true" filter="true" enable_sort="true" column="3"]');
}

/**
 * Example 4: Specific Categories
 * Display products from specific categories (replace IDs with your category IDs)
 */
function lma_example_category_products() {
    // Get your category IDs from WooCommerce > Products > Categories
    echo do_shortcode('[lma_products include="15,23,45" column="4" posts_per_page="12"]');
}

/**
 * Example 5: Full-Width Product List
 * Display products in a full-width layout (good for sidebars or narrow columns)
 */
function lma_example_fullwidth_products() {
    echo do_shortcode('[lma_products column="full" style="3" posts_per_page="5"]');
}

/**
 * Example 6: Latest Products with All Options
 * Display latest products with all features enabled
 */
function lma_example_latest_products_full() {
    $shortcode = '[lma_products
        posts_per_page="9"
        column="3"
        style="1"
        filter="true"
        enable_sort="true"
        enable_search="false"
        orderby="date"
        order="DESC"
        show_rating="true"
        show_price="true"
        show_cart_button="true"
        show_sale_badge="true"
        animation="true"
    ]';

    echo do_shortcode($shortcode);
}

/**
 * Example 7: Best Selling Products
 * Display products sorted by popularity
 */
function lma_example_bestselling_products() {
    echo do_shortcode('[lma_products orderby="popularity" order="DESC" column="4" posts_per_page="8"]');
}

/**
 * Example 8: Top Rated Products
 * Display products sorted by rating
 */
function lma_example_toprated_products() {
    echo do_shortcode('[lma_products orderby="rating" order="DESC" show_rating="true" column="3"]');
}

/**
 * Example 9: Products by Price (Low to High)
 * Display products sorted by price ascending
 */
function lma_example_cheapest_products() {
    echo do_shortcode('[lma_products orderby="price" order="ASC" column="4"]');
}

/**
 * Example 10: Products with Infinite Scroll
 * Enable infinite scroll for automatic loading
 */
function lma_example_infinite_scroll_products() {
    echo do_shortcode('[lma_products infinite_scroll="true" column="3" posts_per_page="6"]');
}

/**
 * Example 11: Minimal Product Display
 * Display products without ratings, sale badges, or add to cart
 */
function lma_example_minimal_products() {
    echo do_shortcode('[lma_products show_rating="false" show_cart_button="false" show_sale_badge="false" column="4"]');
}

/**
 * Example 12: Sidebar Widget Alternative
 * 2-column layout suitable for sidebars
 */
function lma_example_sidebar_products() {
    echo do_shortcode('[lma_products column="2" posts_per_page="4" style="2" filter="false"]');
}

/**
 * Example 13: Custom Hook Implementation
 * Add custom content before/after products using WordPress hooks
 */
function lma_example_with_custom_hooks() {
    ?>
    <div class="custom-products-section">
        <h2 class="section-title">Our Products</h2>
        <p class="section-description">Check out our latest collection</p>

        <?php echo do_shortcode('[lma_products column="3" posts_per_page="6"]'); ?>

        <div class="section-footer">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="view-all-btn">
                View All Products
            </a>
        </div>
    </div>
    <?php
}

/**
 * Example 14: Dynamic Category from URL
 * Display products from category based on URL parameter
 */
function lma_example_dynamic_category() {
    $category_id = isset($_GET['product_cat']) ? intval($_GET['product_cat']) : '';

    if ($category_id) {
        echo do_shortcode('[lma_products include="' . $category_id . '" filter="false"]');
    } else {
        echo do_shortcode('[lma_products filter="true"]');
    }
}

/**
 * Example 15: Programmatic Shortcode with Variables
 * Build shortcode dynamically with PHP variables
 */
function lma_example_dynamic_shortcode() {
    $columns = 4;
    $products_per_page = 8;
    $layout_style = 2;
    $show_filter = true;

    $atts = array(
        'column' => $columns,
        'posts_per_page' => $products_per_page,
        'style' => $layout_style,
        'filter' => $show_filter ? 'true' : 'false',
        'orderby' => 'date',
        'show_rating' => 'true'
    );

    $shortcode = '[lma_products';
    foreach ($atts as $key => $value) {
        $shortcode .= ' ' . $key . '="' . $value . '"';
    }
    $shortcode .= ']';

    echo do_shortcode($shortcode);
}

/**
 * Example 16: Conditional Display
 * Show different product layouts based on user role or other conditions
 */
function lma_example_conditional_products() {
    if (is_user_logged_in()) {
        // Show featured products to logged-in users
        echo do_shortcode('[lma_products featured="true" column="4"]');
    } else {
        // Show regular products to guests
        echo do_shortcode('[lma_products column="3"]');
    }
}

/**
 * Example 17: Homepage Product Sections
 * Create multiple product sections for homepage
 */
function lma_example_homepage_sections() {
    ?>
    <section class="featured-products">
        <h2>Featured Products</h2>
        <?php echo do_shortcode('[lma_products featured="true" column="4" posts_per_page="4" filter="false"]'); ?>
    </section>

    <section class="sale-products">
        <h2>Special Offers</h2>
        <?php echo do_shortcode('[lma_products on_sale="true" column="3" posts_per_page="6" filter="false"]'); ?>
    </section>

    <section class="new-products">
        <h2>New Arrivals</h2>
        <?php echo do_shortcode('[lma_products orderby="date" column="4" posts_per_page="8"]'); ?>
    </section>
    <?php
}

/**
 * HOW TO USE THESE EXAMPLES:
 *
 * 1. In Page/Post Content:
 *    Simply copy the shortcode text between the quotes and paste it into your page editor
 *
 * 2. In Theme Template Files:
 *    Copy the entire function and paste it into your theme's functions.php
 *    Then call it in your template: <?php lma_example_basic_products(); ?>
 *
 * 3. In Widgets:
 *    Use the shortcode in a Text or HTML widget
 *
 * 4. In Elementor:
 *    Use the Shortcode widget and paste the shortcode
 *    OR use the dedicated "WooCommerce Products [LMA]" widget
 *
 * 5. In Custom Code:
 *    Use do_shortcode() function as shown in the examples
 */

/**
 * FINDING YOUR CATEGORY IDs:
 *
 * 1. Go to WooCommerce > Products > Categories
 * 2. Hover over a category name
 * 3. Look at the URL in the browser status bar
 * 4. The ID is shown as: tag_ID=123 (where 123 is the ID)
 */

/**
 * CSS CUSTOMIZATION EXAMPLES:
 *
 * Add these to your theme's style.css or Customizer > Additional CSS
 */
/*
.lma_products_block {
    padding: 40px 0;
}

.lma_product_item {
    transition: transform 0.3s ease;
}

.lma_product_item:hover {
    transform: translateY(-5px);
}

.lma_product_title a {
    color: #2c3e50;
    font-weight: 600;
}

.lma_product_price {
    color: #e74c3c;
    font-size: 24px;
}

.lma_product_cart .button {
    background: #27ae60;
    padding: 12px 30px;
    border-radius: 25px;
}

.lma_product_cart .button:hover {
    background: #229954;
}
*/
