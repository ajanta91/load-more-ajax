=== Load More Ajax Lite ===
Contributors: ajantawpdev
Tags: load more post, load more elementor, ajax post, ajax category filter
Requires at least: 4.7
Tested up to: 6.7
Stable tag: 1.1.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The plugin for ajax load more posts and custom post type posts with ajax category filter.

== Description ==

'Load More Ajax Lite' is easiest and lite weight plugin that you can show posts and custom post type posts load more and category filter. Create your blog list/grid view page easily by using simple shortcode.

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
=1.1.2=
- Compatible with WordPress 6.7
- Changed Thumbnail

= 1.1.1 =
- Added ....

= 1.1.0 =
- Added Elementor Widget for 3 block Style

= 1.0.4 =
Fixed Style 02 grid issue while using together with the layout 03

= 1.0.3 =
Fixed Load More button hidden issue

= 1.0.2 =
Added - New Block style
Added - Title character limit

= 1.0.1 =
Supported multiple post block in single page

= 1.0.0 =
Initial Released