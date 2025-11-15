<?php
/**
 * Security Helper Class
 * 
 * Handles security, validation, and sanitization for the plugin
 * 
 * @package Load_More_Ajax_Lite
 * @since 1.1.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class LMA_Security {
    
    /**
     * Verify nonce for Ajax requests
     * 
     * @param string $nonce_action
     * @param string $nonce_name
     * @return bool
     */
    public static function verify_ajax_nonce($nonce_action = 'load_more_ajax_nonce', $nonce_name = 'nonce') {
        if (!isset($_POST[$nonce_name])) {
            return false;
        }
        
        return wp_verify_nonce(sanitize_text_field($_POST[$nonce_name]), $nonce_action);
    }
    
    /**
     * Check if user has required capability
     * 
     * @param string $capability
     * @return bool
     */
    public static function check_capability($capability = 'read') {
        return current_user_can($capability);
    }
    
    /**
     * Sanitize and validate post type
     * 
     * @param string $post_type
     * @return string|false
     */
    public static function validate_post_type($post_type) {
        $post_type = sanitize_text_field($post_type);
        
        if (empty($post_type)) {
            return 'post';
        }
        
        // Check if post type exists
        if (!post_type_exists($post_type)) {
            return false;
        }
        
        // Check if post type is public or user has access
        $post_type_obj = get_post_type_object($post_type);
        if (!$post_type_obj || (!$post_type_obj->public && !current_user_can('read_private_posts'))) {
            return false;
        }
        
        return $post_type;
    }
    
    /**
     * Validate and sanitize numeric parameters
     * 
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @param int $default
     * @return int
     */
    public static function validate_numeric($value, $min = 1, $max = 100, $default = 1) {
        $value = intval($value);
        
        if ($value < $min || $value > $max) {
            return $default;
        }
        
        return $value;
    }
    
    /**
     * Validate category IDs
     * 
     * @param string $cat_ids
     * @param string $taxonomy
     * @return string
     */
    public static function validate_category_ids($cat_ids, $taxonomy = 'category') {
        if (empty($cat_ids)) {
            return '';
        }
        
        $cat_ids = sanitize_text_field($cat_ids);
        $ids = explode(',', $cat_ids);
        $valid_ids = [];
        
        foreach ($ids as $id) {
            $id = trim($id);
            if (is_numeric($id) && term_exists(intval($id), $taxonomy)) {
                $valid_ids[] = intval($id);
            }
        }
        
        return implode(',', $valid_ids);
    }
    
    /**
     * Sanitize HTML content with allowed tags
     * 
     * @param string $content
     * @return string
     */
    public static function sanitize_html_content($content) {
        $allowed_html = [
            'a' => [
                'href' => [],
                'class' => [],
                'title' => [],
                'target' => [],
            ],
            'div' => [
                'class' => [],
                'id' => [],
            ],
            'img' => [
                'class' => [],
                'src' => [],
                'srcset' => [],
                'alt' => [],
                'height' => [],
                'width' => [],
                'loading' => [],
            ],
            'span' => [
                'class' => [],
            ],
            'p' => [
                'class' => [],
            ],
            'h1' => ['class' => []],
            'h2' => ['class' => []],
            'h3' => ['class' => []],
            'h4' => ['class' => []],
            'h5' => ['class' => []],
            'h6' => ['class' => []],
            'br' => [],
            'strong' => [],
            'b' => [],
            'em' => [],
            'i' => [],
            'sup' => [],
            'sub' => [],
            'ul' => ['class' => []],
            'li' => ['class' => []],
        ];
        
        return wp_kses($content, $allowed_html);
    }
    
    /**
     * Rate limiting for Ajax requests
     * 
     * @param string $action
     * @param int $limit_per_minute
     * @return bool
     */
    public static function check_rate_limit($action = 'load_more_ajax', $limit_per_minute = 60) {
        $user_id = get_current_user_id();
        $ip_address = self::get_user_ip();
        $key = 'lma_rate_limit_' . $action . '_' . ($user_id ? $user_id : $ip_address);
        
        $current_requests = get_transient($key);
        
        if ($current_requests === false) {
            set_transient($key, 1, MINUTE_IN_SECONDS);
            return true;
        }
        
        if ($current_requests >= $limit_per_minute) {
            return false;
        }
        
        set_transient($key, $current_requests + 1, MINUTE_IN_SECONDS);
        return true;
    }
    
    /**
     * Get user IP address
     *
     * Safely retrieves the user's IP address with validation.
     * Only trusts proxy headers if explicitly enabled via filter.
     *
     * @return string
     */
    private static function get_user_ip() {
        $ip = '';

        // Always use REMOTE_ADDR as the base (most reliable)
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }

        // Only check proxy headers if explicitly enabled and behind a trusted proxy
        $trust_proxy_headers = apply_filters('lma_trust_proxy_headers', false);

        if ($trust_proxy_headers) {
            // Check for forwarded IP from proxy
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $forwarded_ips = explode(',', sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']));
                // Get the first IP (client's real IP)
                $forwarded_ip = trim($forwarded_ips[0]);

                // Validate IP format
                if (filter_var($forwarded_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $ip = $forwarded_ip;
                }
            } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $client_ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);

                // Validate IP format
                if (filter_var($client_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $ip = $client_ip;
                }
            }
        }

        return $ip ?: '0.0.0.0';
    }
    
    /**
     * Log security events
     * 
     * @param string $event_type
     * @param string $message
     * @param array $context
     */
    public static function log_security_event($event_type, $message, $context = []) {
        // Security events are logged only in debug mode
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $log_entry = [
            'timestamp' => current_time('mysql'),
            'event_type' => $event_type,
            'message' => $message,
            'user_ip' => self::get_user_ip(),
            'user_id' => get_current_user_id(),
            'context' => $context,
        ];
        
        // Only log in debug mode
    }
}