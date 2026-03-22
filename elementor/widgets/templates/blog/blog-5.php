<?php
$blog_layout    = isset($layout) ? $layout : '5';
$per_page       = isset($per_page) ? $per_page : '6';
$template_taxonomy  = isset($lma_taxonomy_for_template) ? $lma_taxonomy_for_template : 'category';
$template_post_type = isset($lma_post_type_for_template) ? $lma_post_type_for_template : 'post';
$title_limit    = !empty($title_length) ? $title_length : '20';
$excerpt_limit  = !empty($excerpt_length) ? $excerpt_length : '40';
$slides_per_view = isset($slides_per_view) ? $slides_per_view : '3';
$show_arrows    = isset($show_arrows) ? $show_arrows : 'yes';
$show_dots      = isset($show_dots) ? $show_dots : 'yes';
$show_autoplay  = isset($show_autoplay) ? $show_autoplay : 'yes';

$wrapper_classes = 'apl_block_wraper lma_block_style_5 lma_blog_section';
if ($show_arrows !== 'yes') $wrapper_classes .= ' no-arrows';
if ($show_dots !== 'yes') $wrapper_classes .= ' no-dots';
?>
<div class="<?php echo esc_attr($wrapper_classes); ?>">
    <div class="ajaxpost_loader" data-block_style="<?php echo esc_attr($blog_layout) ?>" data-post_type="<?php echo esc_attr($template_post_type); ?>" data-taxonomy="<?php echo esc_attr($template_taxonomy); ?>" data-text_limit="<?php echo esc_attr($excerpt_limit) ?>" data-title_limit="<?php echo esc_attr($title_limit) ?>" data-order="1" data-limit="<?php echo esc_attr($per_page) ?>" data-cate="" data-slides_per_view="<?php echo esc_attr($slides_per_view) ?>" data-show_arrows="<?php echo $show_arrows === 'yes' ? 'true' : 'false'; ?>" data-show_dots="<?php echo $show_dots === 'yes' ? 'true' : 'false'; ?>" data-autoplay="<?php echo $show_autoplay === 'yes' ? 'true' : 'false'; ?>">
    </div>
</div>
