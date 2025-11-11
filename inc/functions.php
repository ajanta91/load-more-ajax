<?php
    /**
     * Add Image Size
     */
    add_image_size( 'column_2', 600, 400, true );
    add_image_size( 'column_3', 400, 270, true );
    add_image_size( 'column_4', 300, 200, true );
    add_image_size( 'column_5', 240, 160, true );

    /**
     * Post type to Taxonomi
     */
    function get_load_more_ajax_lite_taxonomi( $type = 'post' ) {
        $taxonomies = get_object_taxonomies( array( 'post_type' => $type ) );
        $taxonomy = !empty( $taxonomies ) ? $taxonomies[0] : '';
        return $taxonomy;
    }

    /**
     * Post Category item
     */
    function load_more_ajax_lite_cat_id( $post_id, $taxonomy = 'category' ) {
        $categories = get_the_terms( $post_id, $taxonomy );
        $cat_item = '';
        
        if ($categories && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $cat_item .= '<a href="'. esc_url( get_term_link( $category->term_id, $taxonomy ) ) .'" class="apl_post_category">'. esc_html( $category->name ) .'</a>';
            }
        }
        
        return $cat_item;
    }

    // Categories Suggestion ================
    function categories_suggester()
    {
        $content = [];

        foreach (get_categories() as $cat) {
            $content[(string) $cat->slug] = $cat->cat_name;
        }

        return $content;
    }

    /**
     * Estimated Reading Time
     */
    function load_more_ajax_lite_estimated_reading_time( $post_id ) {

        $the_content = get_the_content( '', '', $post_id );
        $words = str_word_count(strip_tags( $the_content ) );
        
        $minute = floor( $words / 200 );
        $min    = 1 <= $minute ? $minute . esc_html__( ' min', 'load-more-ajax-lite' ) : '';
        
        $second = floor( $words % 200 / (200 / 60 ) );
        $sec = $second .  esc_html__( ' sec', 'load-more-ajax-lite');
        
        $estimate = 1 > $minute ? $sec : $min;
        $output = $estimate .  esc_html__( ' read', 'load-more-ajax-lite');
        
        return $output;
    }

    /**
     * Title Excerpt
     */
    function load_more_ajax_title_excerpt( $title, $title_limit = 50 ) {
        
        if ( strlen( $title ) > $title_limit ) {
            $title = substr( $title, 0, $title_limit ) . ' &hellip;';
        }
        return $title;
    }

    /**
     * Load More Ajax Lite Kses Post
     */
    function load_more_ajax_lite_kses_post( $content ) {
        $allowed_html = array(
            'a'     => [
                'href'  => [],
                'class' => [],
                'style' => [],
            ],
            'div'   => [
                'class' => [],
                'style' => [],
            ],
            'img'   => [
                'class' => [],
                'src'   => [],
                'srcset' => [],
                'alt'   => [],
                'height' => [],
                'width' => [],
            ],
            'span'  => [
                'class' => [],
                'style' => [],
            ],
            'br'    => [],
            'strong' => [],
            'p'     => [
                'class' => [],
                'text-align' => []
            ],
            'b'    => [],
            'em'    => [],
            'sup'    => [],

        );
        return wp_kses( $content, $allowed_html );
    }

    /**
     * WP Ajax Post Query
     */
    add_action('wp_ajax_nopriv_ajaxpostsload', 'load_more_ajax_lite_with_cat_filter');
    add_action('wp_ajax_ajaxpostsload', 'load_more_ajax_lite_with_cat_filter');

    function load_more_ajax_lite_with_cat_filter() {
        try {
            // Security checks - only if class exists
            if (class_exists('LMA_Security')) {
                if (!LMA_Security::check_rate_limit('ajaxpostsload', 120)) {
                    LMA_Security::log_security_event('rate_limit', 'Rate limit exceeded for ajaxpostsload');
                    wp_send_json_error(['error' => true, 'message' => esc_html__('Too many requests. Please wait.', 'load-more-ajax-lite')]);
                }

                // Verify nonce - make it optional for backward compatibility
                $nonce_provided = isset($_POST['nonce']) && !empty($_POST['nonce']);
                if ($nonce_provided && !LMA_Security::verify_ajax_nonce('load_more_ajax_nonce', 'nonce')) {
                    LMA_Security::log_security_event('invalid_nonce', 'Invalid nonce for ajaxpostsload');
                    wp_send_json_error(['error' => true, 'message' => esc_html__('Security check failed.', 'load-more-ajax-lite')]);
                }
            }

            // Validate and sanitize inputs
            if (class_exists('LMA_Security')) {
                $posttype = LMA_Security::validate_post_type($_POST['post_type'] ?? 'post');
                if (!$posttype) {
                    wp_send_json_error(['error' => true, 'message' => esc_html__('Invalid post type.', 'load-more-ajax-lite')]);
                }

                $order = LMA_Security::validate_numeric($_POST['order'] ?? 1, 1, 999, 1);
                $limit = LMA_Security::validate_numeric($_POST['limit'] ?? 6, 1, 50, 6);
                $cat = LMA_Security::validate_category_ids($_POST['cate'] ?? '', get_load_more_ajax_lite_taxonomi($posttype));
                $block_style = LMA_Security::validate_numeric($_POST['block_style'] ?? 1, 1, 3, 1);
                $text_limit = LMA_Security::validate_numeric($_POST['text_limit'] ?? 10, 1, 200, 10);
                $title_limit = LMA_Security::validate_numeric($_POST['title_limit'] ?? 30, 1, 500, 30);
            } else {
                // Fallback validation
                $posttype = sanitize_text_field($_POST['post_type'] ?? 'post');
                $order = intval($_POST['order'] ?? 1);
                $limit = intval($_POST['limit'] ?? 6);
                $cat = sanitize_text_field($_POST['cate'] ?? '');
                $block_style = intval($_POST['block_style'] ?? 1);
                $text_limit = intval($_POST['text_limit'] ?? 10);
                $title_limit = intval($_POST['title_limit'] ?? 30);
            }
            
            $image_size = sanitize_text_field($_POST['column'] ?? 'column_3');
            $sort_by = sanitize_text_field($_POST['sort_by'] ?? 'date');
            $sort_order = sanitize_text_field($_POST['sort_order'] ?? 'DESC');

            if (!$order) {
                wp_send_json_error(['error' => true, 'message' => esc_html__('Invalid request parameters.', 'load-more-ajax-lite')]);
            }

            // Build query arguments with improved structure
            $args = [
                'suppress_filters' => false, // Allow filters for better extensibility
                'post_type' => $posttype,
                'posts_per_page' => $limit,
                'post_status' => 'publish',
                'paged' => $order,
                'no_found_rows' => false, // We need pagination info
                'update_post_meta_cache' => false, // Skip meta cache if not needed
                'update_post_term_cache' => true, // We need term cache
            ];

            // Add sorting
            switch ($sort_by) {
                case 'title':
                    $args['orderby'] = 'title';
                    break;
                case 'modified':
                    $args['orderby'] = 'modified';
                    break;
                case 'menu_order':
                    $args['orderby'] = 'menu_order';
                    break;
                case 'rand':
                    $args['orderby'] = 'rand';
                    break;
                default:
                    $args['orderby'] = 'date';
            }
            $args['order'] = in_array($sort_order, ['ASC', 'DESC']) ? $sort_order : 'DESC';

            // Add category filter
            if (!empty($cat)) {
                $taxonomy = get_load_more_ajax_lite_taxonomi($posttype);
                if ($taxonomy) {
                    $args['tax_query'] = [
                        [
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => explode(',', $cat),
                            'operator' => 'IN',
                        ],
                    ];
                }
            }

            // Try to get cached results first (if cache class exists)
            $query = null;
            if (class_exists('LMA_Cache')) {
                $cached_query = LMA_Cache::get_cached_posts($args);
                if ($cached_query !== false) {
                    $query = $cached_query;
                }
            }
            
            if (!$query) {
                $query = new \WP_Query($args);
                // Cache the results if cache class exists
                if (class_exists('LMA_Cache')) {
                    LMA_Cache::set_cached_posts($args, $query);
                }
            }

            $postdata = [
                'posts' => [],
                'pagination' => [
                    'current_page' => $order,
                    'next_page' => $order + 1,
                    'total_pages' => $query->max_num_pages,
                    'total_posts' => $query->found_posts,
                    'has_more' => ($order < $query->max_num_pages),
                ],
                'meta' => [
                    'block_style' => $block_style,
                    'limit' => $limit,
                    'post_type' => $posttype,
                    'showing' => sprintf(
                        esc_html__('Showing %d-%d of %d posts', 'load-more-ajax-lite'),
                        (($order - 1) * $limit) + 1,
                        min($order * $limit, $query->found_posts),
                        $query->found_posts
                    ),
                ],
            ];

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    global $post;

                    $taxonomy = get_load_more_ajax_lite_taxonomi($posttype);
                    $cat_item = $taxonomy ? load_more_ajax_lite_cat_id(get_the_ID(), $taxonomy) : '';
                    
                    // Post data structure with both old and new format support
                    $author_id = get_the_author_meta('ID');
                    $author_name = get_the_author();
                    $author_link = get_author_posts_url($author_id);
                    $author_avatar = get_avatar_url($author_id, ['size' => 32]);

                    $post_data = [
                        'id' => get_the_ID(),
                        'class' => implode(' ', get_post_class()),
                        'title' => get_the_title(),
                        'title_excerpt' => load_more_ajax_title_excerpt(get_the_title(), $title_limit),
                        'permalink' => esc_url(get_the_permalink()),
                        'thumbnail' => esc_url(get_the_post_thumbnail_url(get_the_ID(), $image_size)),
                        'thumbnail_alt' => get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true),
                        'cats' => class_exists('LMA_Security') ? LMA_Security::sanitize_html_content($cat_item) : load_more_ajax_lite_kses_post($cat_item),
                        'read_time' => load_more_ajax_lite_estimated_reading_time(get_the_ID()),
                        'content' => esc_html(wp_trim_words(get_the_content(), $text_limit, ' ...')),
                        'comment_count' => get_comments_number_text('0 Comments', '1 Comment', '% Comments'),
                        'comment_text' => get_comments_number_text('0 Comments', '1 Comment', '% Comments'),
                        'block_style' => $block_style,
                        'featured' => is_sticky(),
                        'formats' => get_post_format() ?: 'standard',
                        // Modern JavaScript expects 'author' and 'date' as objects
                        'author' => [
                            'name' => $author_name,
                            'link' => $author_link,
                            'avatar' => $author_avatar,
                            'id' => $author_id,
                        ],
                        'date' => [
                            'formatted' => get_the_time('d M, Y'),
                            'iso' => get_the_time('c'),
                            'timestamp' => get_the_time('U'),
                        ],
                    ];

                    // Apply filters for extensibility
                    $post_data = apply_filters('lma_ajax_post_data', $post_data, $post, $args);
                    
                    $postdata['posts'][] = $post_data;
                }
                wp_reset_postdata();
            }

            // Add backward compatibility fields
            $postdata['paged'] = $order + 1; // Backward compatibility
            $postdata['limit'] = $limit; // Backward compatibility
            $postdata['block_style'] = esc_html($block_style); // Backward compatibility

            // Apply filters to final data
            $postdata = apply_filters('lma_ajax_response_data', $postdata, $args);

            wp_send_json_success($postdata);

        } catch (Exception $e) {
            // Log error if class exists
            if (class_exists('LMA_Security')) {
                LMA_Security::log_security_event('ajax_error', 'Ajax request failed', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            } else {
                error_log('Load More Ajax Error: ' . $e->getMessage());
            }
            
            wp_send_json_error([
                'error' => true,
                'message' => esc_html__('Something went wrong. Please try again.', 'load-more-ajax-lite'),
            ]);
        }
    }

    /**
     * Ajax handler for getting post count
     */
    add_action('wp_ajax_nopriv_lma_get_post_count', 'lma_get_post_count');
    add_action('wp_ajax_lma_get_post_count', 'lma_get_post_count');

    function lma_get_post_count() {
        try {
            // Security checks
            if (class_exists('LMA_Security')) {
                if (!LMA_Security::verify_ajax_nonce('load_more_ajax_nonce', 'nonce')) {
                    wp_send_json_error(['message' => esc_html__('Security check failed.', 'load-more-ajax-lite')]);
                }

                $posttype = LMA_Security::validate_post_type($_POST['post_type'] ?? 'post');
                $cat = LMA_Security::validate_category_ids($_POST['cate'] ?? '', get_load_more_ajax_lite_taxonomi($posttype));
            } else {
                // Fallback validation if security class doesn't exist
                $posttype = sanitize_text_field($_POST['post_type'] ?? 'post');
                $cat = sanitize_text_field($_POST['cate'] ?? '');
            }

            $count_args = [
                'post_type' => $posttype,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
            ];

            if (!empty($cat)) {
                $taxonomy = get_load_more_ajax_lite_taxonomi($posttype);
                if ($taxonomy) {
                    $count_args['tax_query'] = [
                        [
                            'taxonomy' => $taxonomy,
                            'field' => 'term_id',
                            'terms' => explode(',', $cat),
                        ],
                    ];
                }
            }

            // Try cache first if available
            if (class_exists('LMA_Cache')) {
                $cached_count = LMA_Cache::get_cached_post_count($count_args);
                if ($cached_count !== false) {
                    $total_posts = $cached_count;
                } else {
                    $count_query = new WP_Query($count_args);
                    $total_posts = $count_query->found_posts;
                    LMA_Cache::set_cached_post_count($count_args, $total_posts);
                }
            } else {
                $count_query = new WP_Query($count_args);
                $total_posts = $count_query->found_posts;
            }

            wp_send_json_success(['total_posts' => $total_posts]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => esc_html__('Could not get post count.', 'load-more-ajax-lite')]);
        }
    }

    /**
     * Ajax handler for search posts
     */
    add_action('wp_ajax_nopriv_lma_search_posts', 'lma_search_posts');
    add_action('wp_ajax_lma_search_posts', 'lma_search_posts');

    function lma_search_posts() {
        try {
            // Security checks
            if (class_exists('LMA_Security')) {
                if (!LMA_Security::verify_ajax_nonce('load_more_ajax_nonce', 'nonce')) {
                    wp_send_json_error(['message' => esc_html__('Security check failed.', 'load-more-ajax-lite')]);
                }

                if (!LMA_Security::check_rate_limit('search_posts', 30)) {
                    wp_send_json_error(['message' => esc_html__('Too many search requests.', 'load-more-ajax-lite')]);
                }

                $posttype = LMA_Security::validate_post_type($_POST['post_type'] ?? 'post');
                $limit = LMA_Security::validate_numeric($_POST['limit'] ?? 10, 1, 50, 10);
            } else {
                // Fallback validation if security class doesn't exist
                $posttype = sanitize_text_field($_POST['post_type'] ?? 'post');
                $limit = intval($_POST['limit'] ?? 10);
                $limit = min(max($limit, 1), 50); // Clamp between 1 and 50
            }

            $search_term = sanitize_text_field($_POST['search'] ?? '');

            if (strlen($search_term) < 3) {
                wp_send_json_error(['message' => esc_html__('Search term must be at least 3 characters.', 'load-more-ajax-lite')]);
            }

            $search_args = [
                'post_type' => $posttype,
                'post_status' => 'publish',
                'posts_per_page' => $limit,
                's' => $search_term,
                'orderby' => 'relevance',
                'order' => 'DESC',
            ];

            $search_query = new WP_Query($search_args);
            $results = [];

            if ($search_query->have_posts()) {
                while ($search_query->have_posts()) {
                    $search_query->the_post();
                    $results[] = [
                        'id' => get_the_ID(),
                        'title' => get_the_title(),
                        'permalink' => get_the_permalink(),
                        'excerpt' => wp_trim_words(get_the_content(), 20),
                        'date' => get_the_time('d M, Y'),
                    ];
                }
                wp_reset_postdata();
            }

            wp_send_json_success([
                'results' => $results,
                'total' => $search_query->found_posts,
                'search_term' => $search_term,
            ]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => esc_html__('Search failed.', 'load-more-ajax-lite')]);
        }
    }
