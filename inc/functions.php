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
        foreach ($categories as $category) {
            $cat_item .= '<a href="'. esc_url( get_term_link( $category->term_id, $taxonomy ) ) .'" class="apl_post_category">'. esc_html( $category->name ) .'</a>';
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

        $posttype   = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';
        $order      = isset( $_POST['order'] ) ? sanitize_text_field( $_POST['order'] ) : '1';
        $limit      = isset( $_POST['limit'] ) ? sanitize_text_field( $_POST['limit'] ) : '1';
        $cat        = isset( $_POST['cate'] ) ? sanitize_text_field( $_POST['cate'] ) : '0';
        $image_size = isset( $_POST['column'] ) ? sanitize_text_field( $_POST['column'] ) : 'column_3';
        $block_style= isset( $_POST['block_style'] ) ? sanitize_text_field( $_POST['block_style'] ) : '1';
        $text_limit = isset( $_POST['text_limit'] ) ? sanitize_text_field( $_POST['text_limit'] ) : '10';
        $titleLimit = isset( $_POST['title_limit'] ) ? sanitize_text_field( $_POST['title_limit'] ) : 30;

        if ( !isset( $order ) ) {
            wp_send_json_error(['error' => true, 'message' => esc_html__('Couldn\'t found any data', 'load-more-ajax-lite')]);
        }

        $args['suppress_filters'] = true;
        $args['post_type'] = $posttype;
        $args['posts_per_page'] = $limit;
        $args['order'] = 'ASC';
        $args['paged'] = $order;
        if ( $cat > 0 ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => get_load_more_ajax_lite_taxonomi( $posttype ), //double check your taxonomy name in you dd
                    'field'    => 'term_id',
                    'terms'    =>  explode( ',', $cat )
                ),
            );
        }

        $query = new \WP_Query( $args );

        $postdata = [];

        if ( $query->have_posts() ) :
            while ( $query->have_posts() ) : $query->the_post();
                global $post;

                $cat_item = ! empty( get_load_more_ajax_lite_taxonomi( $posttype ) ) ? load_more_ajax_lite_cat_id( get_the_ID(), get_load_more_ajax_lite_taxonomi( $posttype ) ) : '';
                $postdata['posts'][] = [
                    'id'            => get_the_ID(),
                    'class'         => implode( ' ', get_post_class() ),
                    'title'         => get_the_title(),
                    'title_excerpt' => load_more_ajax_title_excerpt( get_the_title(), $titleLimit ),
                    'permalink'     => esc_url( get_the_permalink( get_the_ID() ) ),
                    'thumbnail'     => esc_url( get_the_post_thumbnail_url( get_the_ID(), $image_size ) ),
                    'cats'          => load_more_ajax_lite_kses_post( $cat_item ),
                    'author'        => get_the_author_link(),
                    'date'          => get_the_time( 'd M, Y' ),
                    'read_time'     => esc_html( load_more_ajax_lite_estimated_reading_time( get_the_ID() ) ),
                    'content'       => esc_html( wp_trim_words( get_the_content(), $text_limit, ' ...' ) ),
                    'comment_count' => get_comments_number_text( '0 Comments', '1 Comment', '% Comments' ),
                ];
                
            endwhile;
        endif;

        $postdata['paged'] = $order + 1;
        $postdata['limit'] = $limit;
        $postdata['block_style'] = esc_html( $block_style );

        wp_send_json_success( $postdata );
    }
