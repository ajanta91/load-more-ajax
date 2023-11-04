<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('New Shortcode', 'load-more-ajax-lite') ?></h1>

    <form action="" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="block_style"><?php _e('Block Style', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <select class="regular-text" name="block_style" id="block_style">
                            <option value="1">Style 01</option>
                            <option value="2">Style 02</option>
                            <option value="3">Style 03</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="post_per_page"><?php _e('Post Per Page', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="number" class="regular-text" name="posts_number" id="post_per_page" value="3">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="is_cat_filter"><?php _e('Show Category Filter', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="is_cat_filter" name="category_filter" value="1" checked />
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="include"><?php _e('Include', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="include" id="include" value="">
                        <p class="description">Add Category term ID saparate with ',' </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="name"><?php _e('Name', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <input type="text" class="regular-text" name="name" id="name" value="">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="column"><?php _e('Column', 'load-more-ajax-lite') ?></label>
                    </th>
                    <td>
                        <select class="regular-text" name="column" id="post_column">
                            <option value=""><?php echo __('Select Column', 'load-more-ajax-lite') ?></option>
                            <option value="6"><?php echo __('Two Column', 'load-more-ajax-lite') ?></option>
                            <option value="4"><?php echo __('Three Column', 'load-more-ajax-lite') ?></option>
                            <option value="3"><?php echo __('Four Column', 'load-more-ajax-lite') ?></option>
                        </select>
                    </td>
                </tr>

            </tbody>
        </table>
        <?php
        wp_nonce_field('new_shortcode');
        submit_button(__('Save', 'load-more-ajax-lite'), 'primary', 'submit_block'); ?>
    </form>
</div>