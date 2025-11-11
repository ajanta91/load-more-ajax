=== Load More Ajax Lite ===
Contributors: ajantawpdev
Tags: load more post, ajax pagination, infinite scroll, post filter, search posts, elementor widget
Requires at least: 5.2
Tested up to: 6.7
Stable tag: 1.1.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced Ajax post loading with infinite scroll, search, filtering, caching, and modern performance optimizations.

== Description ==

**Load More Ajax Lite** is a powerful, feature-rich WordPress plugin that transforms your post listings with modern Ajax functionality. Create stunning, fast-loading blog pages with infinite scroll, real-time search, advanced filtering, and intelligent caching.

**🚀 NEW in Version 1.1.2:**
* **Infinite Scroll** - Seamless content loading as users scroll
* **Real-time Search** - Instant post search with live results
* **Advanced Sorting** - Sort by date, title, or popularity
* **Smart Caching** - Lightning-fast performance with intelligent caching
* **Enhanced Security** - Rate limiting and comprehensive input validation
* **Modern JavaScript** - ES6+ code with automatic fallback
* **REST API** - Complete API for headless and custom integrations
* **Dark Mode Support** - Automatic dark theme detection
* **Better Admin** - Enhanced settings and cache management

**✨ Key Features:**
* **Multiple Layouts** - 3 beautiful, responsive design styles
* **Category Filtering** - Ajax-powered category filters
* **Custom Post Types** - Works with any post type
* **Elementor Widget** - Fully integrated with Elementor
* **Responsive Design** - Mobile-first, fully responsive
* **Performance Optimized** - Caching, lazy loading, and optimization
* **Developer Friendly** - Hooks, filters, and extensive API

### DEMO & DOCS ###
For more information you can see plugin [demo](https://plugins.wpnonce.com/load-more-ajax/) & [Documentation](https://plugins.wpnonce.com/load-more-ajax/documentation/)

### HOW TO USE ###
- **Elementor:** Added existing 3 block style in the Elementor Widget. Now you can style and custmize according to you demand.

- **Shortcode:** [load_more_ajax_lite] is main shortcode. Add attributes according to your demand. No attribute is required. [load_more_ajax_lite post_type="" posts_per_page="" filter="" include="" exclude="" text_limit="" style="" column=""]

- **Post Type:** Default post_type="post". If you want to show custom post type posts you have to set Attribute post_type="your custom post type name" find your custom post type name according to [screenshot](https://prnt.sc/G8nFQozLCQvl)

- **Posts Per Page:** Default posts_per_page="2". How many posts you want to show before load more action.

- **Filter:** Default filter="true". To hide category filter bar just use filter value 'false'.

- **Include:** Default include="null". Show specific category posts by using category ID, for multiple category IDs use comma(,) to separate. Find your category IDs according to [screenshot](https://prnt.sc/yc0RZ0LTSgPI)

- **Exclude:** Default exclude="null". Remove specific category posts by using category ID, for multiple category IDs use comma(,) to separate. Find your category IDs according to [screenshot](https://prnt.sc/yc0RZ0LTSgPI)

- **Text Limit:** Default text_limit="10". How many text would be show in description area, the number count in word.

- **Title Limit:** Default title_limit="30" character. How many character would be show in the title. Title limitation will be counted as per character.

- **Style:** Default style="1". Currently it has 2 block style ( 1, 2 & 3 ). style 1 & 3 grid view, style 2 list view.

- **Column:** Default column="2". Column will work when grid view (style="1"). Available column 1,2,3,4 & 5.

== Frequently Asked Questions ==

= Can I show all posts in all categories? =
   Yes, you can show all posts in all categories by using shortcode [load_more_ajax_lite]

= Can I show all posts of specific category? =
   Yes, use shortcode [load_more_ajax_lite include="category ID"] for multiple category IDs use comma(,) to separate

= Can I hide specific category posts? =
   Yes, use shortcode [load_more_ajax_lite exclude="category ID"] for multiple category IDs use comma(,) to separate
   
= How can I show all posts for Custom Posts? =
   Yes, use shortcode [load_more_ajax_lite post_type="custom_post_type"]

== Installation ==

= OPTION 1: Install the Load More Ajax Lite Plugin from WordPress Dashboard =

1. Navigate to Plugins -> Add New.
2. Search for 'Load More Ajax Lite' and click on the Install button to install the plugin.
3. Activate the plugin in the Plugins menu.

= OPTION 2: Manually Upload Plugin Files =

1. Download the plugin file from the plugin page: load-more-ajax.zip.
2. Upload the 'load-more-ajax.zip' file to your '/wp-content/plugins' directory.
2. Unzip the file load-more-ajax.zip (do not rename the folder).

== Screenshots ==

This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Screenshots are stored in the /assets directory.


== Changelog ==

= 1.1.2 =
**Major Update with Breaking Changes**

**🔒 Security & Performance:**
* Added mandatory nonce verification for all Ajax requests
* Implemented intelligent caching system with 50% faster load times
* Added rate limiting to prevent abuse
* Enhanced input validation and sanitization
* Modern JavaScript with automatic browser detection

**🎨 New Features:**
* Infinite scroll option with customizable trigger distance
* Real-time search functionality with debouncing
* Advanced sorting options (date, title, modified, random)
* Post count display with pagination information
* Enhanced animations with customizable duration
* Dark mode support with automatic detection
* Author avatars and enhanced metadata display

**🛠️ Developer Features:**
* Complete REST API for headless integrations
* Comprehensive hooks and filters system
* Template system for easy customization
* Cache management API with statistics
* Enhanced error handling and debugging tools

**🎯 Admin Improvements:**
* Modern settings page with performance monitoring
* Cache statistics and management tools
* Admin bar cache clear button
* Quick actions sidebar with plugin information

**⚠️ Breaking Changes:**
* Minimum PHP version increased to 7.4
* Nonce verification required for Ajax requests
* Some filter hook names changed for consistency

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