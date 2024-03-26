<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('New Shortcode', 'load-more-ajax-lite') ?></h1>'
    <?php
    $block_id = $_GET['post_block'] ? $_GET['post_block'] : '';
    $block_data = '';
    if (!empty($block_id)) {
        $block_block = new PostBlock();
        $block_data  =  $block_block->block_update_data($block_id);
    }
    ?>

    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="block_title"><?php _e('Name', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="block_title" id="block_title" value="<?php echo esc_attr($block_data['block_title']) ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="block_style"><?php _e('Block Style', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <select class="regular-text" name="block_style" id="block_style">
                            <option value="1" <?php echo $block_data['block_style'] == '1' ? 'selected' : '' ?>>Style 01</option>
                            <option value="2" <?php echo $block_data['block_style'] == '2' ? 'selected' : '' ?>>Style 02</option>
                            <option value="3" <?php echo $block_data['block_style'] == '3' ? 'selected' : '' ?>>Style 03</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="post_per_page"><?php _e('Post Per Page', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="number" class="regular-text" name="posts_number" id="post_per_page" value="<?php echo esc_attr($block_data['per_page']) ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="title_limit"><?php _e('Title Limit', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="number" class="regular-text" name="title_limit" id="title_limit" value="<?php echo esc_attr($block_data['title_limit']) ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="text_limit"><?php _e('Text Limit', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="number" class="regular-text" name="text_limit" id="text_limit" value="<?php echo esc_attr($block_data['text_limit']) ?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="is_cat_filter"><?php _e('Show Category Filter', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="is_cat_filter" name="category_filter" value="1" <?php echo $block_data['is_filter'] == '1' ? 'checked' : '' ?> />
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="include"><?php _e('Include', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="include" id="include" value="<?php echo esc_attr($block_data['include_post']) ?>">
                        <p class="description">Add Category term ID saparate with ',' </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="exclude"><?php _e('Exclude', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="exclude" id="exclude" value="<?php echo esc_attr($block_data['exclude_post']) ?>">
                        <p class="description">Add Category term ID saparate with ',' </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="column"><?php _e('Column', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <select class="regular-text" name="column" id="post_column">
                            <option value=""><?php echo __('Select Column', 'load-more-ajax-lite') ?></option>
                            <option value="6" <?php echo $block_data['post_column'] == '6' ? 'selected' : '' ?>><?php echo __('Two Column', 'load-more-ajax-lite') ?></option>
                            <option value="4" <?php echo $block_data['post_column'] == '4' ? 'selected' : '' ?>><?php echo __('Three Column', 'load-more-ajax-lite') ?></option>
                            <option value="3" <?php echo $block_data['post_column'] == '3' ? 'selected' : '' ?>><?php echo __('Four Column', 'load-more-ajax-lite') ?></option>
                        </select>
                    </td>
                </tr>

            </tbody>
        </table>
        <input type="hidden" name="created_by" value="<?php echo get_current_user_id() ?>">
        <input type="hidden" name="block_id" value="<?php echo $block_data['id'] ?>">
        <?php
        wp_nonce_field('add_new_block');
        submit_button(__('Update', 'load-more-ajax-lite'), 'primary', 'submit_block'); ?>
    </form>
</div>