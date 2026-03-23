=== Load More Ajax Lite ===
Contributors: ajantawpdev
Tags: load more, ajax pagination, infinite scroll, post filter, elementor widget
Requires at least: 5.2
Tested up to: 6.8
Stable tag: 2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced Ajax post loading with 5 layouts, custom post type support, Elementor widget, infinite scroll, search, filtering, and modern performance optimizations.

== Description ==

**Load More Ajax Lite** is a powerful, feature-rich WordPress plugin that transforms your post listings with modern Ajax functionality. Create stunning, fast-loading blog pages with infinite scroll, real-time search, advanced filtering, and intelligent caching.

### Key Features ###

* **5 Beautiful Layouts** — Classic Grid, List View, Modern Card, Masonry, and Carousel
* **Custom Post Type Support** — Works with any public post type (Posts, Pages, WooCommerce Products, Portfolios, etc.)
* **Dynamic Taxonomy Filtering** — Filter by any taxonomy, not just categories
* **Masonry Layout** — Pinterest-style staggered grid with smooth re-layout on filter/load-more
* **Carousel/Slider** — Swiper.js powered carousel with configurable slides, arrows, dots, and autoplay
* **Category Filtering** — Ajax-powered taxonomy term filters on the frontend
* **Elementor Widget** — Fully integrated widget with layout, query, and style controls
* **Carousel Styling Controls** — Customize arrow and dot appearance (size, colors, borders, hover states) in Elementor
* **Admin Block Builder** — Visual block creator with live preview, style thumbnails, and shortcode generator
* **Responsive Design** — Mobile-first, fully responsive across all layouts
* **Performance Optimized** — Caching, lazy loading, rate limiting, and modern JavaScript
* **Developer Friendly** — Hooks, filters, REST API, and extensive documentation

### DEMO & DOCS ###

For more information see the plugin [demo](https://plugins.wpnonce.com/load-more-ajax/) & [Documentation](https://plugins.wpnonce.com/load-more-ajax/documentation/)

== How To Use ==

= Admin Block Builder =

1. Go to **Load More Ajax → Add New** in your WordPress admin
2. Give your block a title
3. Select a **Post Type** (Post, Page, Product, or any custom post type)
4. Choose a **Taxonomy** (Category, Tag, Product Category, or any registered taxonomy)
5. Pick a **Layout** — Classic Grid, List, Modern Card, Masonry, or Carousel
6. Configure columns, posts per page, text/title limits
7. For Carousel: set slides per view, arrows, dots, and autoplay
8. Select terms to include or exclude
9. Save and copy the generated shortcode

= Shortcode Usage =

Basic: `[load_more_ajax_lite]`

Full attributes:

`[load_more_ajax_lite post_type="post" taxonomy="category" posts_per_page="6" filter="true" include="" exclude="" text_limit="10" title_limit="30" style="1" column="3"]`

**Shortcode Attributes:**

* **post_type** — Any registered public post type. Default: `post`
* **taxonomy** — Taxonomy to use for filtering. Default: auto-detected from post type
* **posts_per_page** — Number of posts before load more. Default: `6`
* **filter** — Show/hide category filter bar. Default: `true`
* **include** — Show only specific term IDs (comma-separated)
* **exclude** — Hide specific term IDs (comma-separated)
* **text_limit** — Excerpt word count. Default: `10`
* **title_limit** — Title character limit. Default: `30`
* **style** — Layout style: `1` (Grid), `2` (List), `3` (Modern Card), `4` (Masonry), `5` (Carousel). Default: `1`
* **column** — Grid columns: `2`, `3`, or `4`. Default: `3`

**Carousel-only attributes:**

* **slides_per_view** — Slides visible at once: `1` to `4`. Default: `3`
* **show_arrows** — Show navigation arrows. Default: `true`
* **show_dots** — Show pagination dots. Default: `true`
* **autoplay** — Enable auto-slide. Default: `true`

= Elementor Widget =

1. In the Elementor editor, search for **LMA Blog Post**
2. Drag the widget to your page
3. **Content Tab:**
   * Select layout (5 options)
   * Choose post type and taxonomy
   * Select specific terms to display
   * Set columns, posts per page, and order
   * Configure carousel options (for carousel layout)
4. **Style Tab:**
   * Customize card background, title colors, meta colors
   * **Arrow Style** (carousel) — Size, icon size, border radius, colors (normal/hover), border
   * **Dot Style** (carousel) — Size, active width, spacing, border radius, colors, border

== Frequently Asked Questions ==

= Can I use this with WooCommerce products? =
Yes! Select "Product" as the post type in the admin block builder or use `post_type="product"` in the shortcode. The taxonomy dropdown will automatically show Product Categories and Product Tags.

= Can I use custom taxonomies? =
Yes. When you select a post type, all its registered public taxonomies appear in the taxonomy dropdown. You can filter by any taxonomy, not just the default category.

= Can I show all posts from all categories? =
Yes, use the shortcode `[load_more_ajax_lite]` without any include/exclude attributes.

= Can I show posts from a specific category only? =
Yes, use `[load_more_ajax_lite include="category_id"]`. For multiple categories, separate with commas: `include="1,3,5"`.

= Can I hide specific category posts? =
Yes, use `[load_more_ajax_lite exclude="category_id"]`.

= What layouts are available? =
Five layouts: Classic Grid (style 1), List View (style 2), Modern Card (style 3), Masonry (style 4), and Carousel (style 5).

= Can I customize the carousel arrows and dots? =
Yes, in the Elementor widget you get full styling controls for arrows (size, colors, border, hover states) and dots (size, active width, colors, border, spacing).

= Does it work with the block editor (Gutenberg)? =
Currently the plugin uses shortcodes and an Elementor widget. Gutenberg block support is planned for a future release.

== Installation ==

= Option 1: Install from WordPress Dashboard =

1. Navigate to **Plugins → Add New**
2. Search for **Load More Ajax Lite**
3. Click **Install Now** and then **Activate**

= Option 2: Manual Upload =

1. Download the plugin zip file
2. Navigate to **Plugins → Add New → Upload Plugin**
3. Upload the zip file and click **Install Now**
4. Activate the plugin

== Screenshots ==

1. Admin Block Builder — Visual block creator with post type and taxonomy selection
2. Classic Grid Layout — Responsive grid with category filtering
3. List Layout — Horizontal list view
4. Modern Card Layout — Card-style grid with hover effects
5. Masonry Layout — Pinterest-style staggered grid
6. Carousel Layout — Swiper-powered slider with arrows and dots
7. Elementor Widget — Full layout and style controls

== Changelog ==

= 2.0 =
* **New: Masonry Layout (Style 4)** — Pinterest-style staggered grid using Masonry.js
  * True masonry stagger with smooth re-layout on filter and load more
  * Category filter with automatic reflow
  * Responsive: single column on mobile
* **New: Carousel Layout (Style 5)** — Swiper.js powered post slider
  * Configurable slides per view (1–4), navigation arrows, pagination dots, autoplay
  * Responsive breakpoints with pause-on-hover
  * Vendor libraries (Swiper, Masonry, imagesLoaded) conditionally loaded per layout
* **New: Carousel Arrow & Dot Styling** — Full Elementor style controls
  * Arrow: size, icon size, border radius, icon color, background color, border (normal + hover)
  * Dots: size, active width, spacing, border radius, color, active color, border
* **New: Custom Post Type Support** — Full support across admin, shortcode, and Elementor
  * Post type dropdown with all registered public post types
  * Dynamic taxonomy selection based on post type
  * AJAX-driven term checkboxes that reload when post type or taxonomy changes
  * Works with WooCommerce Products, Portfolios, Events, and any custom post type
* **New: Dynamic Taxonomy Filtering** — Filter by any taxonomy, not just categories
  * New `taxonomy` shortcode attribute
  * Taxonomy passed through to frontend AJAX for correct load-more filtering
  * `data-taxonomy` attribute on frontend containers
* **New: Admin Block Builder UX Overhaul**
  * Visual style selector with CSS-drawn layout thumbnails
  * Visual column selector with grid icons
  * Category checkboxes with post counts (replaces manual comma-separated IDs)
  * Live preview panel with real-time updates
  * Collapsible sections, toggle switches, shortcode copy button
  * Post type column with badge in block list
* **Improved: Elementor Widget** — Post type, taxonomy, and terms controls in Query Filter section
* **Improved: categories_suggester()** now supports any post type's taxonomy
* **Fix: Grid CSS overflow** — Changed grid-template-columns from percentages to repeat(N, 1fr)
* **Fix: Column value mismatch** — Block list converts DB values to actual column counts
* **Fix: Filter value bug** — Shortcode accepts "1", "true", or "yes" for filter attribute
* **Fix: Elementor preview** — Scripts and styles now load correctly in Elementor preview mode
* **Fix: Elementor init timing** — Added elementorFrontend hooks with double-init guard
* **Fix: JS null error** — Added null check for load_more_wrapper in addPostCountDisplay
* Database migration: added post_type and taxonomy columns with backward-compatible defaults

= 1.2 =
* Security & Performance:
* Added mandatory nonce verification for all Ajax requests
* Implemented intelligent caching system with 50% faster load times
* Added rate limiting to prevent abuse
* Enhanced input validation and sanitization
* Modern JavaScript with automatic browser detection
* Advanced sorting options (date, title, modified, random)
* Post count display with pagination information
* Cache statistics and management tools
* Admin bar cache clear button
* Nonce verification required for Ajax requests

= 1.1.2 =
- Compatible with WordPress 6.7
- Changed Thumbnail

= 1.1.1 =
- WordPress 6.7 compatibility
- Thumbnail improvements
- Bug fixes and performance enhancements

= 1.1.0 =
- Added Elementor Widget for 3 block styles
- Enhanced customization options
- Improved responsive design

= 1.0.4 =
- Fixed Style 02 grid issue with layout 03
- Performance improvements

= 1.0.3 =
- Fixed Load More button visibility issue
- CSS improvements

= 1.0.2 =
- Added new block style
- Added title character limit option
- Enhanced styling options

= 1.0.1 =
- Added support for multiple post blocks on single page
- JavaScript improvements

= 1.0.0 =
- Initial release
- Basic Ajax load more functionality
- Category filtering
- Multiple layout options

== Upgrade Notice ==

= 2.0 =
Major update: 2 new layouts (Masonry & Carousel), custom post type & taxonomy support, carousel styling controls, admin UX overhaul. Database migration runs automatically — existing blocks are preserved with no changes needed.
