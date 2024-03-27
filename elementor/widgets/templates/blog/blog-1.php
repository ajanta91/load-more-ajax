<?php
$blog_layout   = isset($layout) ? $layout : '1';
$blog_columns  = isset($blog_column) ? 'column_' . $blog_column : 'column_4';
$per_page      = isset($per_page) ? $per_page : '3';
$title_limit   = !empty($title_length) ? $title_length : '20';
$excerpt_limit = !empty($excerpt_length) ? $excerpt_length : '40';
$selected_cat  = isset($selected_categories) ? $selected_categories : '0';
?>

<div class="apl_block_wraper lma_block_style_1 lma_blog_section">
    <div class="cat_filter">
        <?php
        $get_terms = get_terms([
            'taxonomy' => 'category',
            'hide_empty' => true
        ]);
        $all_slug = array();
        if (!empty($get_terms)) {
            foreach ($get_terms as $term) {
                $all_slug[] .= $term->slug;
            }
        }
        $cat_slugs = !empty($selected_cat) ? $selected_cat : $all_slug;
        $all_cat_id = '';
        if (is_array($cat_slugs)) {
            $cat_count = count($cat_slugs);
            $count     = $cat_count - 2;
            foreach ($cat_slugs as $key => $single_cat_id) {
                $cat_term = get_category_by_slug($single_cat_id);
                $all_cat_id .= $key <= $count ? $cat_term->term_id . ',' : $cat_term->term_id;
            }
        }
        ?>
        <div data-cateid="<?php echo esc_attr($all_cat_id) ?>" class="ajax_post_cat active">All</div>
        <?php
        if (is_array($cat_slugs)) {
            foreach ($cat_slugs as $single_cat) {
                $cat_term = get_category_by_slug($single_cat);
                echo '<div data-cateid="' . esc_attr($cat_term->term_id) . '" data-filter="' . esc_attr($single_cat) . '" class="ajax_post_cat">' . esc_html($cat_term->name) . '</div>';
            }
        }
        ?>

        <?php
        ?>
    </div>

    <div class="ajaxpost_loader <?php echo esc_attr($blog_columns) ?>" data-block_style="<?php echo esc_attr($blog_layout) ?>" data-column="<?php echo esc_attr($blog_columns) ?>" data-post_type="post" data-text_limit="<?php echo esc_attr($excerpt_limit) ?>" data-title_limit="<?php echo esc_attr($title_limit) ?>" data-order="6" data-limit="<?php echo esc_attr($per_page) ?>" data-cate="1">
    </div>
    <button class="loadmore_ajax" type="button">Load More</button>
</div>