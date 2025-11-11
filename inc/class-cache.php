<?php
/**
 * Cache Helper Class
 * 
 * Handles caching operations for better performance
 * 
 * @package Load_More_Ajax_Lite
 * @since 1.1.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class LMA_Cache {
    
    /**
     * Cache expiration times
     */
    const CACHE_POSTS = 300; // 5 minutes
    const CACHE_TERMS = 900; // 15 minutes
    const CACHE_COUNTS = 600; // 10 minutes
    
    /**
     * Get cached posts
     * 
     * @param array $args Query arguments
     * @return WP_Query|false
     */
    public static function get_cached_posts($args) {
        $cache_key = self::generate_cache_key('posts', $args);
        return get_transient($cache_key);
    }
    
    /**
     * Set cached posts
     * 
     * @param array $args Query arguments
     * @param WP_Query $query
     * @param int $expiration
     */
    public static function set_cached_posts($args, $query, $expiration = null) {
        if ($expiration === null) {
            $expiration = self::CACHE_POSTS;
        }
        
        $cache_key = self::generate_cache_key('posts', $args);
        set_transient($cache_key, $query, $expiration);
    }
    
    /**
     * Get cached terms
     * 
     * @param string $taxonomy
     * @param array $args
     * @return array|false
     */
    public static function get_cached_terms($taxonomy, $args = []) {
        $cache_key = self::generate_cache_key('terms', array_merge(['taxonomy' => $taxonomy], $args));
        return get_transient($cache_key);
    }
    
    /**
     * Set cached terms
     * 
     * @param string $taxonomy
     * @param array $args
     * @param array $terms
     * @param int $expiration
     */
    public static function set_cached_terms($taxonomy, $args, $terms, $expiration = null) {
        if ($expiration === null) {
            $expiration = self::CACHE_TERMS;
        }
        
        $cache_key = self::generate_cache_key('terms', array_merge(['taxonomy' => $taxonomy], $args));
        set_transient($cache_key, $terms, $expiration);
    }
    
    /**
     * Get cached post count
     * 
     * @param array $args
     * @return int|false
     */
    public static function get_cached_post_count($args) {
        $cache_key = self::generate_cache_key('count', $args);
        return get_transient($cache_key);
    }
    
    /**
     * Set cached post count
     * 
     * @param array $args
     * @param int $count
     * @param int $expiration
     */
    public static function set_cached_post_count($args, $count, $expiration = null) {
        if ($expiration === null) {
            $expiration = self::CACHE_COUNTS;
        }
        
        $cache_key = self::generate_cache_key('count', $args);
        set_transient($cache_key, $count, $expiration);
    }
    
    /**
     * Generate cache key
     * 
     * @param string $type
     * @param array $args
     * @return string
     */
    private static function generate_cache_key($type, $args) {
        // Add site URL and user role to cache key for multi-site and role-based content
        $current_user = wp_get_current_user();
        $user_role = 'guest';

        if ($current_user && !empty($current_user->roles) && is_array($current_user->roles)) {
            $user_role = $current_user->roles[0];
        }

        $key_data = [
            'type' => $type,
            'args' => $args,
            'site' => get_site_url(),
            'role' => $user_role,
        ];

        return 'lma_' . $type . '_' . md5(wp_json_encode($key_data));
    }
    
    /**
     * Clear all plugin caches
     */
    public static function clear_all_cache() {
        global $wpdb;
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_lma_%'
            )
        );
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_timeout_lma_%'
            )
        );
    }
    
    /**
     * Clear cache by type
     * 
     * @param string $type
     */
    public static function clear_cache_by_type($type) {
        global $wpdb;
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                "_transient_lma_{$type}_%"
            )
        );
        
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                "_transient_timeout_lma_{$type}_%"
            )
        );
    }
    
    /**
     * Get cache statistics
     * 
     * @return array
     */
    public static function get_cache_stats() {
        global $wpdb;
        
        $total_cache = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_lma_%'
            )
        );
        
        $cache_size = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT SUM(LENGTH(option_value)) FROM {$wpdb->options} WHERE option_name LIKE %s",
                '_transient_lma_%'
            )
        );
        
        return [
            'total_entries' => intval($total_cache),
            'total_size' => intval($cache_size),
            'human_size' => size_format($cache_size),
        ];
    }
    
    /**
     * Warm up cache with popular queries
     */
    public static function warm_cache() {
        // Get popular post types
        $post_types = get_post_types(['public' => true], 'names');
        
        foreach ($post_types as $post_type) {
            // Cache first page of posts
            $args = [
                'post_type' => $post_type,
                'posts_per_page' => 6,
                'post_status' => 'publish',
                'paged' => 1,
            ];
            
            $query = new WP_Query($args);
            self::set_cached_posts($args, $query);
            
            // Cache categories/terms
            $taxonomy = get_load_more_ajax_lite_taxonomi($post_type);
            if ($taxonomy) {
                $terms = get_terms([
                    'taxonomy' => $taxonomy,
                    'hide_empty' => true,
                    'number' => 20,
                ]);
                self::set_cached_terms($taxonomy, ['hide_empty' => true, 'number' => 20], $terms);
            }
        }
    }
}