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
            wp_die('Are you cheating?');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Are you cheating?');
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

        
        $query = "SELECT * FROM $table_name";
        $results = $wpdb->get_results($query);

        if( !empty($block_id) && $results ){
            $where = array(
                'id' => $block_id,
            );
            // Execute the query
            $wpdb->update($table_name, $data, $where);
        }
        else{
            $wpdb->insert($table_name, $data);
        }
        wp_redirect('?page=load_more_ajax');
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
        echo 'sub menu page';
    }



}
new AdminMenu();