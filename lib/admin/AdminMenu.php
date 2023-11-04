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
        if (!isset($_POST['submit_block'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'new_shortcode')) {
            wp_die('Are you cheating?');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Are you cheating?');
        }

        $block_style = isset( $_POST['block_style'] ) ? sanitize_text_field( $_POST['block_style'] ) : '1';
        $post_munber = isset( $_POST['posts_number'] ) ? sanitize_text_field( $_POST['posts_number'] ) : '3';
        $cat_filter  = isset( $_POST['category_filter'] ) ? sanitize_text_field( $_POST['category_filter'] ) : '';


        echo '<pre>';
       print_r(
        load_more_ajax_lite_load()->wp_lma_block_list_insert([
            'block_style'   => $block_style,
            'posts_number'  => $post_munber,
            'cat_filter'    => $cat_filter

        ])
        );
        echo '</pre>';

        load_more_ajax_lite_load()->wp_lma_block_list_insert([
            'block_style'   => $block_style,
            'posts_number'  => $post_munber,
            'cat_filter'    => $cat_filter

        ]);
        
    //     echo '<pre>';
    //     var_dump($_POST);
    //     echo '</pre>';
        exit;
    }
    /**
     * Register a custom menu page.
     */
    function admin_menu_page()
    {
        add_menu_page( __('Load More Ajax', 'textdomain'), __('Load More Ajax', 'textdomain'), 'manage_options', 'load_more_ajax', [ $this, 'admin_menu_page_callback'], 'dashicons-hourglass', 6 );
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