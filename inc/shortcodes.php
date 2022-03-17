<?php
function load_more_ajax_lite_shortcode( $atts ) {
    $attributes = shortcode_atts( array(
        'post_type'     => 'post',
        'posts_per_page'=> 2,
        'include'       => '',
        'exclude'       => '',
        'filter'        => 'true',
        'text_limit'    => '10',
        'style'         => '1',
        'column'        => '2'
    ), $atts );

    ob_start();
    
    $posttype   = ! empty( $atts['post_type'] ) ? $atts['post_type'] : 'post';
    $limit      = ! empty( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : '2';
    $include    = ! empty( $atts['include'] ) ? $atts['include'] : '';
    $exclude    = ! empty( $atts['exclude'] ) ? $atts['exclude'] : '';
    $filter     = ! empty( $atts['filter'] ) ? $atts['filter'] : 'true';
    $text_limit = ! empty( $atts['text_limit'] ) ? $atts['text_limit'] : '10';
    $style      = ! empty( $atts['style'] ) ? $atts['style'] : '1';
    $column     = ! empty( $atts['column'] ) ? $atts['column'] : '2';

    if ( $style == '1' ) {
        wp_enqueue_style( 'load-more-ajax-lite' );
    } elseif ( $style == '2' ) {
        wp_enqueue_style( 'load-more-ajax-lite-s2' );
    }
    wp_enqueue_script( 'load-more-ajax-lite' );

    switch ( $column ) {
        case 'full':
            $wraper_class = 'full';
            break;
        case '2':
            $wraper_class = 'column_2';
            break;
        case '3':
            $wraper_class = 'column_3';
            break;
        case '4':
            $wraper_class = 'column_4';
            break;
        case '5':
            $wraper_class = 'column_5';
            break;
        default:
            $wraper_class = 'column_2';
            break;
    }

    echo '<div class="apl_block_wraper lma_block_style_'.esc_attr( $style ).'">';
    $cat_item = ! empty( get_load_more_ajax_lite_taxonomi( $posttype ) ) ? get_load_more_ajax_lite_taxonomi( $posttype ) : '';
        if( $filter == 'true' && ! empty( $cat_item ) ) { ?>
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
        echo '<div class="ajaxpost_loader '. esc_attr( $wraper_class ) .'" data-block_style="'. esc_attr( $style ) .'" data-column="'. esc_attr( $wraper_class ) .'" data-post_type="'. esc_attr( $posttype ) . '" data-text_limit="'. esc_attr( $text_limit ) .'" data-order="1" data-limit="'. esc_attr( $limit ) .'" data-cate="1"></div>';
        echo '<button class="loadmore_ajax" type="button" >'. esc_html__('Load More', 'load-more-ajax-lite' ) .'</button>';
    echo '</div>';
    
    return ob_get_clean();
}
add_shortcode('load_more_ajax_lite', 'load_more_ajax_lite_shortcode');
