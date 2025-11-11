<?php
/**
 * Simplified Ajax Handler - for testing
 * This is a minimal version to test if Ajax is working
 */

add_action('wp_ajax_nopriv_ajaxpostsload_simple', 'load_more_ajax_lite_simple');
add_action('wp_ajax_ajaxpostsload_simple', 'load_more_ajax_lite_simple');

function load_more_ajax_lite_simple() {
    // Security check - verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'load_more_ajax_nonce')) {
        wp_send_json_error(['message' => esc_html__('Security check failed.', 'load-more-ajax-lite')]);
        return;
    }

    // Basic validation
    $posttype = sanitize_text_field($_POST['post_type'] ?? 'post');
    $order = intval($_POST['order'] ?? 1);
    $limit = intval($_POST['limit'] ?? 6);
    $cat = sanitize_text_field($_POST['cate'] ?? '');
    $image_size = sanitize_text_field($_POST['column'] ?? 'column_3');
    $block_style = intval($_POST['block_style'] ?? 1);
    $text_limit = intval($_POST['text_limit'] ?? 10);
    $title_limit = intval($_POST['title_limit'] ?? 30);

    // Simple query
    $args = [
        'post_type' => $posttype,
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'paged' => $order,
    ];

    // Add category filter if specified
    if (!empty($cat) && $cat != '0' && $cat != '-1') {
        $taxonomy = get_load_more_ajax_lite_taxonomi($posttype);
        if ($taxonomy) {
            $args['tax_query'] = [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => explode(',', $cat),
                ],
            ];
        }
    }

    $query = new WP_Query($args);
    $posts = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            $taxonomy = get_load_more_ajax_lite_taxonomi($posttype);
            $cat_item = $taxonomy ? load_more_ajax_lite_cat_id(get_the_ID(), $taxonomy) : '';

            $author_id = get_the_author_meta('ID');

            $posts[] = [
                'id' => get_the_ID(),
                'class' => implode(' ', get_post_class()),
                'title' => get_the_title(),
                'title_excerpt' => load_more_ajax_title_excerpt(get_the_title(), $title_limit),
                'permalink' => esc_url(get_the_permalink()),
                'thumbnail' => esc_url(get_the_post_thumbnail_url(get_the_ID(), $image_size)),
                'thumbnail_alt' => get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true),
                'cats' => load_more_ajax_lite_kses_post($cat_item),
                'read_time' => load_more_ajax_lite_estimated_reading_time(get_the_ID()),
                'content' => esc_html(wp_trim_words(get_the_content(), $text_limit, ' ...')),
                'comment_count' => get_comments_number_text('0 Comments', '1 Comment', '% Comments'),
                'comment_text' => get_comments_number_text('0 Comments', '1 Comment', '% Comments'),
                'block_style' => $block_style,
                // Use object format for author and date (modern format)
                'author' => [
                    'name' => get_the_author(),
                    'link' => get_author_posts_url($author_id),
                    'avatar' => get_avatar_url($author_id, ['size' => 32]),
                    'id' => $author_id,
                ],
                'date' => [
                    'formatted' => get_the_time('d M, Y'),
                    'iso' => get_the_time('c'),
                    'timestamp' => get_the_time('U'),
                ],
            ];
        }
        wp_reset_postdata();
    }

    // Response
    $response = [
        'posts' => $posts,
        'paged' => $order + 1,
        'limit' => $limit,
        'block_style' => $block_style,
        'total_pages' => $query->max_num_pages,
        'found_posts' => $query->found_posts,
    ];

    wp_send_json_success($response);
}