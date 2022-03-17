=== Load More Ajax Lite ===
Contributors: ( ajantawpdev )
Tags: load more posts, load more, ajax, ajax post filter, ajax category filter, custom post type load more, ajax posts, ajax load more, masonry, grid, list, column
Requires at least: 4.7
Tested up to: 5.9
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The plugin for ajax load more posts and custom post type posts with ajax category filter.

== Description ==

'Load More Ajax Lite' is easiest and lite weight plugin that you can show posts and custom post type posts load more and category filter. Create your blog list/grid view page easily by using simple shortcode.

Main Shortcode: [load_more_ajax_lite]
Setup attributes according to your demand. No attribute is required.
[load_more_ajax_lite post_type="" posts_per_page="" filter="" include="" exclude="" text_limit="" style="" column=""]

Attributes & values:
Post Type:
Default post_type="post"
If you want to show custom post type posts you have to set Attribute post_type="your custom post type name"
find your custom post type name like that (https://prnt.sc/G8nFQozLCQvl)

Posts Per Page:
Default posts_per_page="2"
How many posts you want to show before load more action.

Filter:
Default filter="true"
To hide category filter bar just use filter value 'false'.

Include:
Default include="null"
Show specific category posts by using category ID, for multiple category IDs use comma(,) to separate.
Find your category IDs according to screenshot (https://prnt.sc/yc0RZ0LTSgPI)

Exclude:
Default exclude="null"
Remove specific category posts by using category ID, for multiple category IDs use comma(,) to separate.
Find your category IDs according to screenshot (https://prnt.sc/yc0RZ0LTSgPI)

Text Limit:
Default text_limit="10"
How many text would be show in description area, the number count in word.

Style:
Default style="1"
Currently it has 2 block style ( 1 & 2 ). style 1 grid view and style 2 list view.

Column: 
Default column="2"
Column will work when grid view (style="1"). Available column 1,2,3,4 & 5.

== Frequently Asked Questions ==

1. Can I show all posts in all categories?
   Yes, you can show all posts in all categories by using shortcode [load_more_ajax_lite]

2. Can I show all posts of specific category?
   Yes, use shortcode [load_more_ajax_lite include="category ID"] for multiple category IDs use comma(,) to separate

3. Can I hide specific category posts?
   Yes, use shortcode [load_more_ajax_lite exclude="category ID"] for multiple category IDs use comma(,) to separate
   
4. How can I show all posts for Custom Posts?
   Yes, use shortcode [load_more_ajax_lite post_type="custom_post_type"]

== Screenshots ==

This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Screenshots are stored in the /assets directory.


== Changelog ==

= 1.0.0 =
Initial Released