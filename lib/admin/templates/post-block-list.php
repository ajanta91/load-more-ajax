<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Post Block Shortcode', 'load-more-ajax-lite') ?></h1>
    <a href="<?php echo admin_url('admin.php?page=load_more_ajax&action=new') ?>" class="page-title-action">Add New</a>

    <?php
    global $wpdb;
    // Showing activity logs
    $table_name = $wpdb->prefix . 'load_more_post_shortcode_list';
    $all_blocks = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A); ?>

    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <caption class="screen-reader-text">Table ordered by Date. Descending.</caption>
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox">
                    <label for="cb-select-all-1"><span class="screen-reader-text">Select All</span></label>
                </td>
                <th scope="col" id="title" class="manage-column column-title column-primary sortable desc" abbr="Title"><a href="http://localhost/blocky/wp-admin/edit.php?orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span></a></th>
                <th scope="col" id="post_type" class="manage-column column-post-type">Post Type</th>
                <th scope="col" id="shortcode" class="manage-column column-shortcode">Shortcode</th>
                <th scope="col" id="author" class="manage-column column-author">Author</th>
                <th scope="col" id="date" class="manage-column column-date sorted desc" aria-sort="descending" abbr="Date"><a href="http://localhost/blocky/wp-admin/edit.php?orderby=date&amp;order=asc"><span>Date</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span></a></th>
            </tr>
        </thead>

        <tbody id="the-list">
            <?php
            if (!empty($all_blocks)) {
                foreach ($all_blocks as $data) {
                    $pt_value = !empty($data['post_type']) ? $data['post_type'] : 'post';
                    $post_type = 'post_type="' . esc_attr($pt_value) . '" ';
                    $tax_value = !empty($data['taxonomy']) ? $data['taxonomy'] : 'category';
                    $taxonomy_attr = 'taxonomy="' . esc_attr($tax_value) . '" ';
                    $block_style = 'style="' . $data['block_style'] . '" ';
                    $per_page   = !empty($data['per_page']) ? 'posts_per_page="' . $data['per_page'] . '" ' : '';
                    $filter     = !empty($data['is_filter']) ? 'filter="true" ' : '';
                    $include    = !empty($data['include_post']) ? 'include="' . $data['include_post'] . '" ' : '';
                    $exclude    = !empty($data['exclude_post']) ? 'exclude="' . $data['exclude_post'] . '" ' : '';
                    $text_limit = !empty($data['text_limit']) ? 'text_limit="' . $data['text_limit'] . '" ' : '';
                    // Convert legacy DB column values (bootstrap grid) to actual column counts
                    $col_map    = array( '6' => '2', '4' => '3', '3' => '4' );
                    $col_val    = isset( $col_map[ $data['post_column'] ] ) ? $col_map[ $data['post_column'] ] : $data['post_column'];
                    $cloumn     = !empty($data['post_column']) ? 'column="' . $col_val . '" ' : ''; ?>

                    <tr id="post-1" class="iedit author-self level-0 post-1 type-post status-publish format-standard hentry category-uncategorized">
                        <th scope="row" class="check-column"> <input id="cb-select-1" type="checkbox" name="post[]" value="1"></th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
                            <strong><a class="row-title" href="?page=load_more_ajax&action=edit&post_block=<?php echo esc_attr($data['id']) ?>" aria-label=""><?php echo esc_html($data['block_title']) ?></a></strong>
                        </td>
                        <td class="column-post-type" data-colname="Post Type">
                            <span class="lma-post-type-badge"><?php echo esc_html($pt_value); ?></span>
                        </td>
                        <td class="column-shortcode" data-colname="shortcode"><a href="javascript:void(0)" class="copy_block_shortcode">[load_more_ajax_lite <?php echo $post_type . $taxonomy_attr . $block_style . $per_page . $filter . $include . $exclude . $text_limit . $cloumn ?>]</a></td>
                        <td class="author column-author" data-colname="Author"><a href="edit.php?post_type=post&amp;author=1">ajanta</a></td>

                        <td class="date column-date" data-colname="Date">Published<br>2023/11/05 at 3:03 pm</td>
                    </tr>
                <?php
                }
            }
            ?>
        </tbody>

        <tfoot>
            <tr>
                <td class="manage-column column-cb check-column"><input id="cb-select-all-2" type="checkbox">
                    <label for="cb-select-all-2"><span class="screen-reader-text">Select All</span></label>
                </td>
                <th scope="col" class="manage-column column-title column-primary sortable desc" abbr="Title"><a href="http://localhost/blocky/wp-admin/edit.php?orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span></a></th>
                <th scope="col" class="manage-column column-post-type">Post Type</th>
                <th scope="col" class="manage-column column-shortcode">Shortcode</th>
                <th scope="col" class="manage-column column-author">Author</th>

                <th scope="col" class="manage-column column-date sorted desc" aria-sort="descending" abbr="Date"><a href="http://localhost/blocky/wp-admin/edit.php?orderby=date&amp;order=asc"><span>Date</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span></a></th>
            </tr>
        </tfoot>

    </table>
</div>