<?php
function load_more_ajax_lite_shortcode( $atts ) {
    $attributes = shortcode_atts( array(
        'post_type'     => 'post',
        'posts_per_page'=> 6,
        'include'       => '',
        'exclude'       => '',
        'filter'        => 'true',
        'text_limit'    => '10',
        'title_limit'   => '30',
        'style'         => '1',
        'column'        => '3',
        'infinite_scroll' => 'false',
        'enable_search' => 'false',
        'enable_sort'   => 'false',
        'animation'     => 'true',
        'show_count'    => 'true',
    ), $atts );

    ob_start();

    // Use the merged attributes instead of checking $atts directly
    $posttype       = $attributes['post_type'];
    $include        = $attributes['include'];
    $exclude        = $attributes['exclude'];
    $filter         = $attributes['filter'];
    $text_limit     = $attributes['text_limit'];
    $title_limit    = $attributes['title_limit'];
    $style          = $attributes['style'];
    $column         = $attributes['column'];
    $infinite_scroll = $attributes['infinite_scroll'];
    $enable_search  = $attributes['enable_search'];
    $enable_sort    = $attributes['enable_sort'];
    $animation      = $attributes['animation'];
    $show_count     = $attributes['show_count'];

    // Enqueue scripts and styles
    if ( $style == '1' ) {
        wp_enqueue_style( 'load-more-ajax-lite' );
    } elseif ( $style == '2' ) {
        wp_enqueue_style( 'load-more-ajax-lite-s2' );
    } elseif ( $style == '3') {
        wp_enqueue_style('load-more-ajax-lite-s3');
    }
    wp_enqueue_script( 'load-more-ajax-lite' );

    switch ( $column ) {
        case 'full':
            $wraper_class = 'full';
            $limit        = '3';
            break;
        case '6': // Legacy DB value for 2 columns
        case '2':
            $wraper_class = 'column_2';
            $limit        = '2';
            break;
        case '3':
            $wraper_class = 'column_3';
            $limit        = '3';
            break;
        case '4':
            $wraper_class = 'column_4';
            $limit        = '4';
            break;
        case '5':
            $wraper_class = 'column_5';
            $limit        = '5';
            break;
        default:
            $wraper_class = 'column_3';
            break;
    }
    $limit      = ! empty( $attributes['posts_per_page'] ) ? $attributes['posts_per_page'] : '2';
    
    // Create wrapper with enhanced data attributes
    $block_classes = 'apl_block_wraper lma_block_style_' . esc_attr( $style );
    $data_attributes = array(
        'data-infinite-scroll' => esc_attr( $infinite_scroll ),
        'data-enable-search' => esc_attr( $enable_search ),
        'data-enable-sort' => esc_attr( $enable_sort ),
        'data-animation' => esc_attr( $animation ),
        'data-show-count' => esc_attr( $show_count ),
    );
    
    echo '<div class="' . esc_attr( $block_classes ) . '" ' . implode(' ', array_map(function($key, $value) {
        return $key . '="' . $value . '"';
    }, array_keys($data_attributes), $data_attributes)) . '>';
    
    $cat_item = ! empty( get_load_more_ajax_lite_taxonomi( $posttype ) ) ? get_load_more_ajax_lite_taxonomi( $posttype ) : '';
        if( in_array( $filter, array( 'true', '1', 'yes' ), true ) && ! empty( $cat_item ) ) { ?>
            <div class="cat_filter">
                <?php
                $args['taxonomy']   = $cat_item;
                $args['hide_empty'] = true;
                $args['orderby']    = 'name';
                $args['order']      = 'ASC';
                if( ! empty( $include ) ){
                    $args['include']   = $include;
                }
                if( ! empty( $exclude ) ){
                    $args['exclude']   = $exclude;
                }
                $categories = get_terms( $args );


                $all_cat_id = '';
                if( is_array( $categories ) ){
                    $cat_count = count( $categories );
                    $count = $cat_count - 2;
                    foreach ($categories as $key => $value) {
                        $all_cat_id .= $key <= $count ? $value->term_id . ',' : $value->term_id;
                    }
                }
                echo '<div data-cateid="' . esc_attr( $all_cat_id ) . '" class="ajax_post_cat active">'. esc_html__( 'All', 'load-more-ajax-lite' ) .'</div>';
                foreach ($categories as $cat) {
                    echo '<div data-cateid="'. esc_attr( $cat->term_id ) .'" data-filter="'. esc_attr( $cat->slug ) .'" class="ajax_post_cat">'. esc_html( $cat->name ) .'</div>';
                } ?>
            </div>
            <?php
        }
        echo '<div class="ajaxpost_loader '. esc_attr( $wraper_class ) .'" data-block_style="'. esc_attr( $style ) .'" data-column="'. esc_attr( $wraper_class ) .'" data-post_type="'. esc_attr( $posttype ) . '" data-text_limit="'. esc_attr( $text_limit ) . '" data-title_limit="' . esc_attr($title_limit) . '" data-order="1" data-limit="'. esc_attr( $limit ) .'" data-cate=""></div>';
        echo '<div class="load_more_wrapper"><button class="loadmore_ajax" type="button" >'. esc_html__( 'Load More', 'load-more-ajax-lite' ) .'</button></div>';
    echo '</div>';
    
    return ob_get_clean();
}
add_shortcode('load_more_ajax_lite', 'load_more_ajax_lite_shortcode');
