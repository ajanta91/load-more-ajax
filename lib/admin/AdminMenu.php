<?php

/**
 * Class for Admin Menu.
 */
class AdminMenu {

    /**
     * Initializes the Admin Menu
     */
    function __construct()
    {
        $this->dispace_action();
        add_action('admin_menu', [$this, 'admin_menu_page']);
    }

    function dispace_action(){
        
        add_action('admin_init', [ $this, 'form_handler'] );
    }

    public function form_handler()
    {
        if (!isset($_POST['submit_block']) ) {
            return;
        }
        if (!wp_verify_nonce($_POST['_wpnonce'], 'add_new_block') ) {
            wp_die( esc_html__('Are you cheating?', 'load-more-ajax-lite'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Are you cheating?', 'load-more-ajax-lite'));
        }

        $block_id     = isset( $_POST['block_id'] ) ? intval( $_POST['block_id'] ) : '';
        $block_title  = isset( $_POST['block_title'] ) ? sanitize_text_field( $_POST['block_title'] ) : '1';
        $block_style  = isset( $_POST['block_style'] ) ? intval( $_POST['block_style'] ) : '1';
        $post_munber  = isset( $_POST['posts_number'] ) ? intval( $_POST['posts_number'] ) : '3';
        $cat_filter   = isset( $_POST['category_filter'] ) ? sanitize_text_field( $_POST['category_filter'] ) : '';
        $title_limit  = isset( $_POST['title_limit'] ) ? intval( $_POST['title_limit'] ) : '';
        $text_limit   = isset( $_POST['text_limit'] ) ? intval( $_POST['text_limit'] ) : '';
        $include      = isset( $_POST['include'] ) ? sanitize_text_field( $_POST['include'] ) : '';
        $exclude      = isset( $_POST['exclude'] ) ? sanitize_text_field( $_POST['exclude'] ) : '';
        $column       = isset( $_POST['column'] ) ? intval( $_POST['column'] ) : '';
        $created_by   = isset( $_POST['created_by'] ) ? intval( $_POST['created_by'] ) : '';
        $currentTimes = time();
        $created_time = date("Y-m-d H:i:s", $currentTimes);

        global $wpdb;
        $table_name = $wpdb->prefix . 'load_more_post_shortcode_list';
        // Insert data into the table
        $data = array(
            "block_title"  => esc_html($block_title),
            "block_style"  => esc_html($block_style),
            "per_page"     => $post_munber,
            "title_limit"  => $title_limit,
            "text_limit"   => $text_limit,
            "is_filter"    => $cat_filter,
            "include_post" => $include,
            "exclude_post" => $exclude,
            "post_column"  => $column,
            'created_time' => $created_time,
            "user_id"      => $created_by,
        );


        // Check if updating existing block
        $results = null;
        if (!empty($block_id)) {
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $block_id));
        }

        if (!empty($block_id) && $results) {
            $where = array(
                'id' => $block_id,
            );
            // Execute the query
            $wpdb->update($table_name, $data, $where);
        }
        else{
            $wpdb->insert($table_name, $data);
        }
        wp_redirect(admin_url('admin.php?page=load_more_ajax'));
        exit;
    }
    /**
     * Register a custom menu page.
     */
    function admin_menu_page()
    {
        add_menu_page( __('Load More Ajax', 'textdomain'), __('Load More Ajax', 'textdomain'), 'manage_options', 'load_more_ajax', [ $this, 'admin_menu_page_callback'], 'dashicons-hourglass', 6 );
        add_submenu_page( 'load_more_ajax', __('All Blocks', 'textdomain'),__('All Blocks', 'textdomain'), 'manage_options', 'load_more_ajax', [ $this, 'admin_menu_page_callback' ] );
        add_submenu_page( 'load_more_ajax', __('Settings', 'textdomain'),__('Settings', 'textdomain'), 'manage_options', 'settings', [ $this, 'load_more_ajax_settings' ] );
    }

    /**
     * Display a custom menu page
     */
    function admin_menu_page_callback() {
        $PostBlock = new PostBlock();
        $PostBlock->post_block();
    }

    /**
     * load_more_ajax_settings
     */
    function load_more_ajax_settings(){
        if (isset($_POST['save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'lma_settings')) {
            $this->save_settings();
        }
        
        $this->render_settings_page();
    }

    /**
     * Save settings
     */
    private function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings = array(
            'cache_duration' => intval($_POST['cache_duration'] ?? 300),
            'enable_modern_js' => isset($_POST['enable_modern_js']),
            'animation_duration' => intval($_POST['animation_duration'] ?? 300),
            'scroll_threshold' => intval($_POST['scroll_threshold'] ?? 200),
            'rate_limit' => intval($_POST['rate_limit'] ?? 60),
            'enable_debug' => isset($_POST['enable_debug']),
        );

        update_option('lma_settings', $settings);
        echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved successfully!', 'load-more-ajax-lite') . '</p></div>';
    }

    /**
     * Render settings page
     */
    private function render_settings_page() {
        $settings = get_option('lma_settings', array());
        $cache_stats = LMA_Cache::get_cache_stats();
        
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Load More Ajax Settings', 'load-more-ajax-lite'); ?></h1>
            
            <div class="lma-admin-container">
                <div class="lma-admin-main">
                    <form method="post" action="">
                        <?php wp_nonce_field('lma_settings'); ?>
                        
                        <div class="lma-settings-section">
                            <h2><?php esc_html_e('Performance Settings', 'load-more-ajax-lite'); ?></h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Cache Duration (seconds)', 'load-more-ajax-lite'); ?></th>
                                    <td>
                                        <input type="number" name="cache_duration" value="<?php echo esc_attr($settings['cache_duration'] ?? 300); ?>" min="60" max="3600" />
                                        <p class="description"><?php esc_html_e('How long to cache query results. Recommended: 300 seconds (5 minutes)', 'load-more-ajax-lite'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Enable Modern JavaScript', 'load-more-ajax-lite'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="enable_modern_js" <?php checked(!empty($settings['enable_modern_js'])); ?> />
                                            <?php esc_html_e('Use modern ES6+ JavaScript for better performance', 'load-more-ajax-lite'); ?>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Rate Limit (requests/minute)', 'load-more-ajax-lite'); ?></th>
                                    <td>
                                        <input type="number" name="rate_limit" value="<?php echo esc_attr($settings['rate_limit'] ?? 60); ?>" min="10" max="300" />
                                        <p class="description"><?php esc_html_e('Maximum AJAX requests per minute per user', 'load-more-ajax-lite'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="lma-settings-section">
                            <h2><?php esc_html_e('UI/UX Settings', 'load-more-ajax-lite'); ?></h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Animation Duration (ms)', 'load-more-ajax-lite'); ?></th>
                                    <td>
                                        <input type="number" name="animation_duration" value="<?php echo esc_attr($settings['animation_duration'] ?? 300); ?>" min="100" max="1000" />
                                        <p class="description"><?php esc_html_e('Duration for post loading animations', 'load-more-ajax-lite'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Scroll Threshold (px)', 'load-more-ajax-lite'); ?></th>
                                    <td>
                                        <input type="number" name="scroll_threshold" value="<?php echo esc_attr($settings['scroll_threshold'] ?? 200); ?>" min="50" max="1000" />
                                        <p class="description"><?php esc_html_e('Distance from bottom to trigger infinite scroll', 'load-more-ajax-lite'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="lma-settings-section">
                            <h2><?php esc_html_e('Developer Settings', 'load-more-ajax-lite'); ?></h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Enable Debug Mode', 'load-more-ajax-lite'); ?></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="enable_debug" <?php checked(!empty($settings['enable_debug'])); ?> />
                                            <?php esc_html_e('Enable detailed logging for debugging', 'load-more-ajax-lite'); ?>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <?php submit_button(esc_html__('Save Settings', 'load-more-ajax-lite'), 'primary', 'save_settings'); ?>
                    </form>
                </div>

                <div class="lma-admin-sidebar">
                    <div class="lma-widget">
                        <h3><?php esc_html_e('Cache Statistics', 'load-more-ajax-lite'); ?></h3>
                        <ul>
                            <li><?php printf(esc_html__('Total Entries: %d', 'load-more-ajax-lite'), $cache_stats['total_entries']); ?></li>
                            <li><?php printf(esc_html__('Cache Size: %s', 'load-more-ajax-lite'), $cache_stats['human_size']); ?></li>
                        </ul>
                        <p>
                            <a href="<?php echo wp_nonce_url(admin_url('admin-ajax.php?action=lma_clear_cache'), 'lma_clear_cache'); ?>" class="button">
                                <?php esc_html_e('Clear Cache', 'load-more-ajax-lite'); ?>
                            </a>
                        </p>
                    </div>

                    <div class="lma-widget">
                        <h3><?php esc_html_e('Quick Actions', 'load-more-ajax-lite'); ?></h3>
                        <p>
                            <a href="<?php echo admin_url('admin.php?page=load_more_ajax&action=new'); ?>" class="button button-primary">
                                <?php esc_html_e('Create New Block', 'load-more-ajax-lite'); ?>
                            </a>
                        </p>
                        <p>
                            <a href="<?php echo home_url('/wp-json/load-more-ajax/v1/posts'); ?>" target="_blank" class="button">
                                <?php esc_html_e('View API Docs', 'load-more-ajax-lite'); ?>
                            </a>
                        </p>
                    </div>

                    <div class="lma-widget">
                        <h3><?php esc_html_e('Plugin Info', 'load-more-ajax-lite'); ?></h3>
                        <ul>
                            <li><?php printf(esc_html__('Version: %s', 'load-more-ajax-lite'), LOAD_MORE_AJAX_LITE_VERSION); ?></li>
                            <li><?php printf(esc_html__('PHP Version: %s', 'load-more-ajax-lite'), PHP_VERSION); ?></li>
                            <li><?php printf(esc_html__('WordPress Version: %s', 'load-more-ajax-lite'), get_bloginfo('version')); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .lma-admin-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .lma-admin-main {
            flex: 1;
        }
        .lma-admin-sidebar {
            width: 300px;
        }
        .lma-widget {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .lma-widget h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 600;
        }
        .lma-widget ul {
            margin: 0;
            padding-left: 20px;
        }
        .lma-widget ul li {
            margin-bottom: 5px;
        }
        .lma-settings-section {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .lma-settings-section h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 18px;
            color: #23282d;
        }
        </style>
        <?php
    }



}
new AdminMenu();