# WooCommerce Integration Guide

## Overview

The Load More Ajax plugin now includes comprehensive WooCommerce support, allowing you to create beautiful, AJAX-powered product grids with load more functionality.

## Features

- **AJAX Product Loading**: Load products dynamically without page refresh
- **Category Filtering**: Filter products by categories with smooth transitions
- **Product Sorting**: Sort by price, popularity, rating, date, and more
- **Multiple Layout Styles**: 3 pre-designed layouts to choose from
- **Responsive Grid**: 2, 3, 4, 5 column layouts or full-width
- **Product Information Display**:
  - Product images with hover effects
  - Star ratings
  - Pricing (including sale prices)
  - Add to cart buttons
  - Sale badges
  - Stock status
  - Product categories
  - Short descriptions
- **Special Filters**:
  - Featured products only
  - On-sale products only
  - Price range filtering
- **Performance Optimized**: Uses caching and rate limiting
- **Secure**: Built-in security checks and nonce verification

## Usage

### 1. Using Shortcode

#### Basic Usage
```
[lma_products]
```

#### With Parameters
```
[lma_products
    posts_per_page="6"
    style="1"
    column="3"
    filter="true"
    orderby="date"
    order="DESC"
    show_rating="true"
    show_price="true"
    show_cart_button="true"
]
```

#### Complete Shortcode Parameters

| Parameter | Default | Description | Values |
|-----------|---------|-------------|--------|
| `posts_per_page` | 6 | Number of products per page | Any number |
| `style` | 1 | Layout style | 1, 2, 3 |
| `column` | 3 | Number of columns | 2, 3, 4, 5, full |
| `filter` | true | Show category filter | true, false |
| `orderby` | date | Order products by | date, price, popularity, rating, title, menu_order, rand |
| `order` | DESC | Sort order | ASC, DESC |
| `featured` | false | Show only featured products | true, false |
| `on_sale` | false | Show only products on sale | true, false |
| `show_rating` | true | Display star ratings | true, false |
| `show_price` | true | Display product prices | true, false |
| `show_cart_button` | true | Display add to cart button | true, false |
| `show_sale_badge` | true | Display sale badges | true, false |
| `enable_search` | false | Enable search functionality | true, false |
| `enable_sort` | false | Enable sort dropdown | true, false |
| `animation` | true | Enable loading animations | true, false |
| `show_count` | true | Show product count | true, false |
| `infinite_scroll` | false | Enable infinite scroll | true, false |
| `include` | - | Include specific category IDs | Comma-separated IDs |
| `exclude` | - | Exclude specific category IDs | Comma-separated IDs |

### 2. Using Elementor Widget

1. Open your page in Elementor
2. Search for "WooCommerce Products [LMA]" widget
3. Drag and drop it to your page
4. Configure settings in the left panel:
   - **Layout**: Choose product style (1, 2, or 3)
   - **Columns**: Select grid columns
   - **Query Settings**:
     - Products per page
     - Order by
     - Sort order
     - Category selection
     - Featured/Sale filters
   - **Display Settings**:
     - Toggle rating display
     - Toggle price display
     - Toggle add to cart button
     - Toggle sale badge
     - Enable category filter
     - Enable sort options
   - **Style Tab**: Customize colors, typography, and spacing

### 3. PHP Implementation

```php
<?php
// Basic usage
echo do_shortcode('[lma_products]');

// With parameters
$atts = array(
    'posts_per_page' => 8,
    'style' => '2',
    'column' => '4',
    'orderby' => 'popularity',
    'featured' => 'true'
);

$shortcode = '[lma_products';
foreach ($atts as $key => $value) {
    $shortcode .= ' ' . $key . '="' . $value . '"';
}
$shortcode .= ']';

echo do_shortcode($shortcode);
?>
```

## Examples

### Example 1: Featured Products Grid
```
[lma_products
    featured="true"
    column="4"
    posts_per_page="8"
    style="2"
    filter="false"
]
```

### Example 2: Sale Products with Sort
```
[lma_products
    on_sale="true"
    column="3"
    enable_sort="true"
    show_sale_badge="true"
]
```

### Example 3: Specific Categories Only
```
[lma_products
    include="15,23,45"
    column="4"
    posts_per_page="12"
    filter="true"
]
```

### Example 4: Simple Product List
```
[lma_products
    column="full"
    style="3"
    show_rating="true"
    show_cart_button="false"
]
```

## Styling Customization

### Custom CSS

Add custom styles to your theme's `style.css` or use the Customizer:

```css
/* Change product item background */
.lma_product_item {
    background: #f9f9f9;
    border-radius: 8px;
}

/* Customize product title */
.lma_product_title a {
    color: #333;
    font-size: 18px;
}

/* Style the price */
.lma_product_price {
    color: #e74c3c;
    font-size: 22px;
}

/* Customize add to cart button */
.lma_product_cart .button {
    background: #27ae60;
    border-radius: 25px;
}

.lma_product_cart .button:hover {
    background: #229954;
}

/* Change sale badge color */
.lma_product_image .onsale {
    background: #ff6b6b;
}
```

### Using Filters

```php
// Customize animation duration
add_filter('lma_animation_duration', function() {
    return 500; // milliseconds
});

// Modify product data before sending
add_filter('lma_ajax_product_data', function($product_data, $product, $args) {
    // Add custom field
    $product_data['custom_field'] = get_post_meta($product->get_id(), '_custom_field', true);
    return $product_data;
}, 10, 3);

// Modify final response data
add_filter('lma_ajax_product_response_data', function($productdata, $args) {
    // Add custom meta
    $productdata['custom_meta'] = 'Custom value';
    return $productdata;
}, 10, 2);
```

## Hooks & Actions

### Available Actions

```php
// When products are loaded via AJAX
add_action('lma_products_loaded', function($products, $container) {
    // Your code here
}, 10, 2);
```

### Available Filters

| Filter | Parameters | Description |
|--------|------------|-------------|
| `lma_ajax_product_data` | `$product_data`, `$product`, `$args` | Modify individual product data |
| `lma_ajax_product_response_data` | `$productdata`, `$args` | Modify complete response data |
| `lma_animation_duration` | - | Change animation duration (ms) |
| `lma_scroll_threshold` | - | Change infinite scroll trigger point |

## Troubleshooting

### Products Not Loading

1. **Check WooCommerce Installation**: Ensure WooCommerce is installed and activated
2. **Clear Cache**: Clear your site cache and the LMA cache
3. **Check Console**: Open browser developer tools and check for JavaScript errors
4. **Verify Permissions**: Ensure proper user permissions

### Styling Issues

1. **Clear CSS Cache**: Hard refresh the page (Ctrl+F5 or Cmd+Shift+R)
2. **Check Theme Conflicts**: Try switching to a default theme temporarily
3. **Inspect Elements**: Use browser developer tools to inspect CSS

### AJAX Errors

1. **Check Error Log**: Review WordPress debug log
2. **Verify Nonce**: Ensure security nonces are working
3. **Rate Limiting**: You may be hitting rate limits (default 120 requests/minute)

## Performance Tips

1. **Use Caching**: The plugin automatically caches queries for better performance
2. **Limit Products Per Page**: Keep `posts_per_page` reasonable (6-12 recommended)
3. **Optimize Images**: Use proper image sizes for products
4. **Enable CDN**: Use a CDN for static assets
5. **Minimize Plugins**: Reduce the number of active plugins

## Browser Compatibility

- Chrome 60+
- Firefox 54+
- Safari 10+
- Edge 16+
- Modern mobile browsers

The plugin automatically detects older browsers and loads appropriate JavaScript.

## Requirements

- WordPress 5.2+
- PHP 7.4+
- WooCommerce 3.0+
- Modern browser with JavaScript enabled

## Support

For issues, feature requests, or questions:
1. Check the documentation
2. Review troubleshooting section
3. Contact plugin support

## Changelog

### Version 1.2.0
- Added WooCommerce product support
- New product shortcode `[lma_products]`
- Elementor WooCommerce Products widget
- Product category filtering
- Product sorting options
- Featured products filter
- Sale products filter
- Responsive product grid layouts
- WooCommerce-specific styling
- Add to cart integration
- Product ratings display
- Price display with sale pricing
- Stock status indicators
- Sale badges
