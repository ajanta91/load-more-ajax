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

    /**
     * Estimated Reading Time
     */
    function load_more_ajax_lite_estimated_reading_time( $post_id ) {

        $the_content = get_the_content( '', '', $post_id );
        $words = str_word_count(strip_tags( $the_content ) );
        
        $minute = floor( $words / 200 );
        $min    = 1 <= $minute ? $minute . ' min' : '';
        
        $second = floor( $words % 200 / (200 / 60 ) );
        $sec = $second . ' sec';
        
        $estimate = 1 > $minute ? $sec : $min;
        $output = $estimate . ' read';
        
        return $output;
    }

    /**
     * Title Excerpt
     */
    function load_more_ajax_title_excerpt( $title ) {
        $max = 50;
        if ( strlen($title) > $max ) {
            $title = substr($title, 0, $max) . ' &hellip;';
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

        $post = wp_slash( filter_var_array( $_POST ) );

        if ( ! isset( $post['order'] ) ) {
            wp_send_json_error( ['error' => true, 'message' => esc_html__( 'Couldn\'t found any data', 'load-more-ajax-lite' ) ] );
        }

        $posttype   = $post['post_type'] ? sanitize_text_field( $post['post_type'] ) : 'post';
        $order      = $post['order'] ? sanitize_text_field( $post['order'] ) : '1';
        $limit      = $post['limit'] ? sanitize_text_field( $post['limit'] ) : '1';
        $cat        = $post['cate'] ? sanitize_text_field( $post['cate'] ) : '0';
        $image_size = $post['column'] ? sanitize_text_field( $post['column'] ) : 'column_3';
        $block_style= $post['block_style'] ? sanitize_text_field( $post['block_style'] ) : '1';
        $text_limit = $post['text_limit'] ? sanitize_text_field( $post['text_limit'] ) : '10';
        
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
                    'title'         => esc_html( get_the_title() ),
                    'title_excerpt' => esc_html( load_more_ajax_title_excerpt( get_the_title() ) ),
                    'permalink'     => esc_url( get_the_permalink( get_the_ID() ) ),
                    'thumbnail'     => esc_url( get_the_post_thumbnail_url( get_the_ID(), $image_size ) ),
                    'cats'          => load_more_ajax_lite_kses_post( $cat_item ),
                    'author'        => get_the_author_link(),
                    'date'          => get_the_time( 'd M, Y' ),
                    'read_time'     => esc_html( load_more_ajax_lite_estimated_reading_time( get_the_ID() ) ),
                    'content'       => esc_html( wp_trim_words( get_the_content(), $text_limit, ' ...' ) ),
                    'block_style'   => esc_html( $block_style ),
                ];
                
            endwhile;
        endif;

        $postdata['paged'] = $order + 1;
        $postdata['limit'] = $limit;

        wp_send_json_success( $postdata );
    }
