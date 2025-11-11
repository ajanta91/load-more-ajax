# Load More Ajax Lite - Developer Documentation

## Overview

Load More Ajax Lite is a comprehensive WordPress plugin for creating dynamic, Ajax-powered post listings with advanced features including infinite scroll, search, filtering, and modern performance optimizations.

## Version 1.1.2 Improvements

### 🔒 Security Enhancements
- **Nonce Verification**: All Ajax requests now require valid nonces
- **Rate Limiting**: Prevents abuse with configurable request limits
- **Input Validation**: Comprehensive sanitization and validation
- **Capability Checks**: Proper permission verification
- **Security Logging**: Detailed logging of security events

### ⚡ Performance Optimizations
- **Smart Caching**: Transient-based caching system
- **Query Optimization**: Efficient database queries
- **Cache Management**: Automatic cache invalidation
- **Modern JavaScript**: ES6+ features with fallback support
- **Lazy Loading**: Built-in image lazy loading

### 🎨 New Features
- **Infinite Scroll**: Alternative to load more button
- **Search Functionality**: Real-time post search
- **Advanced Sorting**: Multiple sorting options
- **Post Count Display**: Shows pagination info
- **Enhanced UI**: Modern, responsive design
- **Dark Mode Support**: Automatic dark theme detection

### 🛠️ Developer Features
- **REST API**: Complete REST endpoints
- **Hooks & Filters**: Extensive customization options
- **Modern JavaScript**: Class-based architecture
- **Error Handling**: Comprehensive error management
- **Documentation**: Detailed API documentation

## Usage

### Basic Shortcode
```
[load_more_ajax_lite]
```

### Advanced Shortcode
```
[load_more_ajax_lite 
    post_type="post" 
    posts_per_page="6" 
    style="1" 
    column="3" 
    filter="true" 
    infinite_scroll="true" 
    enable_search="true" 
    enable_sort="true"
    animation="true"
    show_count="true"
]
```

### Shortcode Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `post_type` | `post` | Post type to display |
| `posts_per_page` | `6` | Number of posts per load |
| `style` | `1` | Layout style (1, 2, or 3) |
| `column` | `3` | Grid columns (2, 3, 4, 5, or full) |
| `filter` | `true` | Show category filter |
| `include` | `""` | Include specific category IDs |
| `exclude` | `""` | Exclude specific category IDs |
| `text_limit` | `10` | Excerpt word limit |
| `title_limit` | `30` | Title character limit |
| `infinite_scroll` | `false` | Enable infinite scroll |
| `enable_search` | `false` | Enable search box |
| `enable_sort` | `false` | Enable sort options |
| `animation` | `true` | Enable animations |
| `show_count` | `true` | Show post count |

## Elementor Widget

The plugin includes a comprehensive Elementor widget with all features and styling options.

### Widget Settings
- **Layout Selection**: 3 different layouts
- **Query Filters**: Post type, categories, sorting
- **Content Limits**: Title and excerpt length
- **Style Controls**: Typography, colors, spacing
- **Advanced Options**: All shortcode features

## REST API

### Endpoints

#### Get Posts
```
GET /wp-json/load-more-ajax/v1/posts
```

**Parameters:**
- `post_type` (string): Post type
- `per_page` (int): Posts per page (1-100)
- `page` (int): Page number
- `orderby` (string): Sort field
- `order` (string): Sort direction (ASC/DESC)
- `category` (string): Category IDs (comma-separated)
- `text_limit` (int): Excerpt word limit
- `title_limit` (int): Title character limit

**Response:**
```json
{
  "posts": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_posts": 50,
    "per_page": 10,
    "has_next": true,
    "has_previous": false
  },
  "query": {...}
}
```

#### Search Posts
```
GET /wp-json/load-more-ajax/v1/search?search=keyword
```

#### Get Categories
```
GET /wp-json/load-more-ajax/v1/categories?post_type=post
```

#### Get Post Count
```
GET /wp-json/load-more-ajax/v1/count?post_type=post&category=1,2,3
```

## JavaScript API

### Class: LoadMoreAjax

The modern JavaScript implementation provides a comprehensive API for programmatic control.

#### Methods

```javascript
// Get instance
const lma = window.loadMoreAjaxInstance;

// Reload posts for a specific instance
lma.reload('lma_0');

// Set category filter
lma.setCategory('lma_0', '1,2,3');

// Destroy instance
lma.destroy('lma_0');
```

#### Events

```javascript
// Listen for post load events
document.addEventListener('lma:posts:loaded', function(e) {
    console.log('Posts loaded:', e.detail);
});

// Listen for errors
document.addEventListener('lma:error', function(e) {
    console.log('Error:', e.detail);
});
```

## Hooks & Filters

### Action Hooks

```php
// Before posts query
do_action('lma_before_query', $args);

// After posts loaded
do_action('lma_after_posts_loaded', $posts, $query);

// Cache cleared
do_action('lma_cache_cleared', $cache_type);
```

### Filter Hooks

```php
// Modify query arguments
$args = apply_filters('lma_query_args', $args, $request_data);

// Modify post data
$post_data = apply_filters('lma_ajax_post_data', $post_data, $post, $args);

// Modify response data
$response = apply_filters('lma_ajax_response_data', $response, $args);

// Modify cache duration
$duration = apply_filters('lma_cache_duration', 300, $cache_type);

// Use modern JavaScript
$use_modern = apply_filters('lma_use_modern_javascript', true);
```

## Caching System

### Cache Types
- **Posts Cache**: Query results (5 minutes)
- **Terms Cache**: Category/taxonomy data (15 minutes)
- **Count Cache**: Post counts (10 minutes)

### Cache Management

```php
// Clear all cache
LMA_Cache::clear_all_cache();

// Clear specific cache type
LMA_Cache::clear_cache_by_type('posts');

// Get cache statistics
$stats = LMA_Cache::get_cache_stats();

// Warm cache
LMA_Cache::warm_cache();
```

## Security Features

### Rate Limiting
```php
// Check rate limit
$allowed = LMA_Security::check_rate_limit('action_name', 60);

// Custom rate limit
add_filter('lma_rate_limit_ajaxpostsload', function($limit) {
    return 120; // 120 requests per minute
});
```

### Input Validation
```php
// Validate post type
$post_type = LMA_Security::validate_post_type($input);

// Validate numeric input
$number = LMA_Security::validate_numeric($input, $min, $max, $default);

// Validate category IDs
$categories = LMA_Security::validate_category_ids($input, $taxonomy);
```

## Styling & Customization

### CSS Classes

```css
/* Main wrapper */
.apl_block_wraper { }

/* Search box */
.lma-search-container { }
.lma-search-box { }
.lma-search-input { }

/* Sort controls */
.lma-sort-container { }
.lma-sort-select { }

/* Post count */
.lma-post-count { }
.lma-count-text { }

/* Category filters */
.cat_filter { }
.ajax_post_cat { }
.ajax_post_cat.active { }

/* Posts */
.apl_post_wraper { }
.apl_content_wraper { }
.apl_post_title { }

/* Load more button */
.loadmore_ajax { }
.loadmore_ajax:disabled { }

/* Loading states */
.loading_overlay { }
.loading-spinner { }
```

### Dark Mode Support
The plugin automatically detects and supports dark mode through CSS media queries.

### Responsive Design
All components are fully responsive with mobile-first design principles.

## Performance Tips

1. **Enable Caching**: Use the built-in caching system
2. **Optimize Images**: Use appropriate image sizes
3. **Limit Posts**: Don't load too many posts at once
4. **Use Modern JS**: Enable modern JavaScript for better performance
5. **Configure Rate Limits**: Prevent abuse with proper rate limiting

## Troubleshooting

### Common Issues

**Posts not loading:**
- Check nonce verification
- Verify post type exists
- Check category IDs are valid
- Review rate limiting settings

**JavaScript errors:**
- Enable fallback to legacy JavaScript
- Check browser console for errors
- Verify nonce is properly set

**Performance issues:**
- Enable caching
- Reduce posts per page
- Optimize database queries
- Check server resources

### Debug Mode

Enable debug mode in settings to get detailed logging:

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Migration Guide

### From Version 1.1.1 to 1.1.2

1. **Backup your site**
2. **Update the plugin**
3. **Clear all caches**
4. **Test functionality**
5. **Update shortcodes** (optional new parameters)
6. **Review security settings**

### Breaking Changes
- Nonce verification is now required for all Ajax requests
- Some filter names have changed (check documentation)
- Minimum PHP version increased to 7.4

## Support

For support and feature requests:
- **Plugin URI**: https://plugins.wpnonce.com/load-more-ajax/
- **Documentation**: https://plugins.wpnonce.com/load-more-ajax/documentation/
- **Author**: Ajanta Das
- **Author URI**: https://wpnonce.com

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html