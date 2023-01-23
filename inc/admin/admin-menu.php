<?php

/**
 * Register a custom menu page.
 */
function wpdocs_register_my_custom_menu_page()
{
    add_menu_page(
        __('Custom Menu Title', 'textdomain'),
        'custom menu',
        'manage_options',
        'custompage',
        'my_custom_menu_page',
        'dashicons-hourglass',
        6
    );
}
add_action('admin_menu', 'wpdocs_register_my_custom_menu_page');

/**
 * Display a custom menu page
 */
function my_custom_menu_page()
{
    esc_html_e('Admin Page Test', 'textdomain');
}