<?php
/**
 * Debug Helper - Add this to functions.php temporarily for debugging
 * Remove after testing
 */

// Add debug info to admin footer
add_action('admin_footer', 'lma_debug_info');
add_action('wp_footer', 'lma_debug_info');

function lma_debug_info() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    echo '<div style="position: fixed; bottom: 0; right: 0; background: #fff; border: 1px solid #ccc; padding: 10px; z-index: 9999; max-width: 300px; font-size: 12px;">';
    echo '<strong>LMA Debug Info:</strong><br>';
    echo 'Plugin Version: ' . (defined('LOAD_MORE_AJAX_LITE_VERSION') ? LOAD_MORE_AJAX_LITE_VERSION : 'Not defined') . '<br>';
    echo 'LMA_Security: ' . (class_exists('LMA_Security') ? 'Loaded' : 'Not loaded') . '<br>';
    echo 'LMA_Cache: ' . (class_exists('LMA_Cache') ? 'Loaded' : 'Not loaded') . '<br>';
    echo 'LMA_REST_API: ' . (class_exists('LMA_REST_API') ? 'Loaded' : 'Not loaded') . '<br>';
    echo 'Ajax URL: ' . admin_url('admin-ajax.php') . '<br>';
    echo 'Nonce: ' . wp_create_nonce('load_more_ajax_nonce') . '<br>';
    
    // Check if Ajax handler is registered
    $ajax_actions = array();
    if (has_action('wp_ajax_ajaxpostsload')) {
        $ajax_actions[] = 'ajaxpostsload (logged in)';
    }
    if (has_action('wp_ajax_nopriv_ajaxpostsload')) {
        $ajax_actions[] = 'ajaxpostsload (public)';
    }
    
    echo 'Ajax Actions: ' . (empty($ajax_actions) ? 'None registered' : implode(', ', $ajax_actions)) . '<br>';
    echo '</div>';
}

// Test Ajax endpoint directly
add_action('wp_ajax_lma_test', 'lma_test_ajax');
add_action('wp_ajax_nopriv_lma_test', 'lma_test_ajax');

function lma_test_ajax() {
    wp_send_json_success(['message' => 'Ajax is working!', 'time' => current_time('mysql')]);
}