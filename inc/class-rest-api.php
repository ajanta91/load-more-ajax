<?php
/**
 * REST API Endpoints
 * 
 * Modern REST API endpoints for headless and API access
 * 
 * @package Load_More_Ajax_Lite
 * @since 1.1.2
 */

if (!defined('ABSPATH')) {
    exit;
}

class LMA_REST_API {
    
    private $namespace = 'load-more-ajax/v1';
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Get posts endpoint
        register_rest_route($this->namespace, '/posts', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_posts'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => $this->get_posts_args(),
        ));
        
        // Search posts endpoint
        register_rest_route($this->namespace, '/search', array(
            'methods' => 'GET',
            'callback' => array($this, 'search_posts'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => $this->get_search_args(),
        ));
        
        // Get categories endpoint
        register_rest_route($this->namespace, '/categories', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_categories'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => array(
                'post_type' => array(
                    'default' => 'post',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
        
        // Get post count endpoint
        register_rest_route($this->namespace, '/count', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_post_count'),
            'permission_callback' => array($this, 'check_permissions'),
            'args' => $this->get_posts_args(),
        ));
    }
    
    /**
     * Check permissions for API access
     */
    public function check_permissions($request) {
        // Allow public access for reading posts
        return true;
    }
    
    /**
     * Get posts endpoint
     */
    public function get_posts($request) {
        try {
            $params = $request->get_params();
            
            // Validate post type
            $post_type = LMA_Security::validate_post_type($params['post_type'] ?? 'post');
            if (!$post_type) {
                return new WP_Error('invalid_post_type', 'Invalid post type', array('status' => 400));
            }
            
            // Build query arguments
            $args = array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'posts_per_page' => LMA_Security::validate_numeric($params['per_page'] ?? 10, 1, 100, 10),
                'paged' => LMA_Security::validate_numeric($params['page'] ?? 1, 1, 999, 1),
                'orderby' => sanitize_text_field($params['orderby'] ?? 'date'),
                'order' => in_array(strtoupper($params['order'] ?? 'DESC'), ['ASC', 'DESC']) ? strtoupper($params['order']) : 'DESC',
            );
            
            // Add category filter
            if (!empty($params['category'])) {
                $category_ids = LMA_Security::validate_category_ids($params['category'], get_load_more_ajax_lite_taxonomi($post_type));
                if (!empty($category_ids)) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => get_load_more_ajax_lite_taxonomi($post_type),
                            'field' => 'term_id',
                            'terms' => explode(',', $category_ids),
                        ),
                    );
                }
            }
            
            // Try cache first
            $cached_query = LMA_Cache::get_cached_posts($args);
            if ($cached_query !== false) {
                $query = $cached_query;
            } else {
                $query = new WP_Query($args);
                LMA_Cache::set_cached_posts($args, $query);
            }
            
            $posts = array();
            $text_limit = LMA_Security::validate_numeric($params['text_limit'] ?? 20, 1, 200, 20);
            $title_limit = LMA_Security::validate_numeric($params['title_limit'] ?? 60, 1, 500, 60);
            
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    global $post;
                    
                    $taxonomy = get_load_more_ajax_lite_taxonomi($post_type);
                    $categories = array();
                    
                    if ($taxonomy) {
                        $terms = get_the_terms(get_the_ID(), $taxonomy);
                        if ($terms && !is_wp_error($terms)) {
                            foreach ($terms as $term) {
                                $categories[] = array(
                                    'id' => $term->term_id,
                                    'name' => $term->name,
                                    'slug' => $term->slug,
                                    'link' => get_term_link($term),
                                );
                            }
                        }
                    }
                    
                    $posts[] = array(
                        'id' => get_the_ID(),
                        'title' => array(
                            'full' => get_the_title(),
                            'excerpt' => load_more_ajax_title_excerpt(get_the_title(), $title_limit),
                        ),
                        'content' => array(
                            'full' => get_the_content(),
                            'excerpt' => wp_trim_words(get_the_content(), $text_limit, '...'),
                        ),
                        'excerpt' => get_the_excerpt(),
                        'link' => get_the_permalink(),
                        'date' => array(
                            'formatted' => get_the_time('d M, Y'),
                            'iso' => get_the_time('c'),
                            'timestamp' => get_the_time('U'),
                        ),
                        'author' => array(
                            'id' => get_the_author_meta('ID'),
                            'name' => get_the_author(),
                            'link' => get_author_posts_url(get_the_author_meta('ID')),
                            'avatar' => get_avatar_url(get_the_author_meta('ID')),
                        ),
                        'featured_media' => array(
                            'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                            'medium' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                            'large' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
                            'full' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                            'alt' => get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true),
                        ),
                        'categories' => $categories,
                        'comment_count' => get_comments_number(),
                        'read_time' => load_more_ajax_lite_estimated_reading_time(get_the_ID()),
                        'is_sticky' => is_sticky(),
                        'post_format' => get_post_format() ?: 'standard',
                    );
                }
                wp_reset_postdata();
            }
            
            $response = array(
                'posts' => $posts,
                'pagination' => array(
                    'current_page' => intval($args['paged']),
                    'total_pages' => intval($query->max_num_pages),
                    'total_posts' => intval($query->found_posts),
                    'per_page' => intval($args['posts_per_page']),
                    'has_next' => ($args['paged'] < $query->max_num_pages),
                    'has_previous' => ($args['paged'] > 1),
                ),
                'query' => array(
                    'post_type' => $post_type,
                    'orderby' => $args['orderby'],
                    'order' => $args['order'],
                ),
            );
            
            return rest_ensure_response($response);
            
        } catch (Exception $e) {
            return new WP_Error('query_failed', 'Query execution failed', array('status' => 500));
        }
    }
    
    /**
     * Search posts endpoint
     */
    public function search_posts($request) {
        $params = $request->get_params();
        $search_term = sanitize_text_field($params['search'] ?? '');
        
        if (strlen($search_term) < 3) {
            return new WP_Error('invalid_search', 'Search term must be at least 3 characters', array('status' => 400));
        }
        
        $post_type = LMA_Security::validate_post_type($params['post_type'] ?? 'post');
        if (!$post_type) {
            return new WP_Error('invalid_post_type', 'Invalid post type', array('status' => 400));
        }
        
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => LMA_Security::validate_numeric($params['per_page'] ?? 20, 1, 100, 20),
            's' => $search_term,
            'orderby' => 'relevance',
        );
        
        $query = new WP_Query($args);
        $results = array();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'excerpt' => wp_trim_words(get_the_content(), 30),
                    'link' => get_the_permalink(),
                    'date' => get_the_time('d M, Y'),
                    'author' => get_the_author(),
                );
            }
            wp_reset_postdata();
        }
        
        return rest_ensure_response(array(
            'results' => $results,
            'total' => $query->found_posts,
            'search_term' => $search_term,
        ));
    }
    
    /**
     * Get categories endpoint
     */
    public function get_categories($request) {
        $params = $request->get_params();
        $post_type = LMA_Security::validate_post_type($params['post_type'] ?? 'post');
        
        if (!$post_type) {
            return new WP_Error('invalid_post_type', 'Invalid post type', array('status' => 400));
        }
        
        $taxonomy = get_load_more_ajax_lite_taxonomi($post_type);
        if (!$taxonomy) {
            return rest_ensure_response(array('categories' => array()));
        }
        
        // Try cache first
        $cached_terms = LMA_Cache::get_cached_terms($taxonomy, array('hide_empty' => true));
        if ($cached_terms !== false) {
            $terms = $cached_terms;
        } else {
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
                'orderby' => 'name',
                'order' => 'ASC',
            ));
            LMA_Cache::set_cached_terms($taxonomy, array('hide_empty' => true), $terms);
        }
        
        $categories = array();
        
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $categories[] = array(
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug,
                    'description' => $term->description,
                    'count' => $term->count,
                    'link' => get_term_link($term),
                );
            }
        }
        
        return rest_ensure_response(array('categories' => $categories));
    }
    
    /**
     * Get post count endpoint
     */
    public function get_post_count($request) {
        $params = $request->get_params();
        $post_type = LMA_Security::validate_post_type($params['post_type'] ?? 'post');
        
        if (!$post_type) {
            return new WP_Error('invalid_post_type', 'Invalid post type', array('status' => 400));
        }
        
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );
        
        // Add category filter
        if (!empty($params['category'])) {
            $category_ids = LMA_Security::validate_category_ids($params['category'], get_load_more_ajax_lite_taxonomi($post_type));
            if (!empty($category_ids)) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => get_load_more_ajax_lite_taxonomi($post_type),
                        'field' => 'term_id',
                        'terms' => explode(',', $category_ids),
                    ),
                );
            }
        }
        
        // Try cache first
        $cached_count = LMA_Cache::get_cached_post_count($args);
        if ($cached_count !== false) {
            $count = $cached_count;
        } else {
            $query = new WP_Query($args);
            $count = $query->found_posts;
            LMA_Cache::set_cached_post_count($args, $count);
        }
        
        return rest_ensure_response(array('count' => $count));
    }
    
    /**
     * Get posts arguments schema
     */
    private function get_posts_args() {
        return array(
            'post_type' => array(
                'default' => 'post',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'per_page' => array(
                'default' => 10,
                'sanitize_callback' => 'absint',
            ),
            'page' => array(
                'default' => 1,
                'sanitize_callback' => 'absint',
            ),
            'orderby' => array(
                'default' => 'date',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'order' => array(
                'default' => 'DESC',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'category' => array(
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'text_limit' => array(
                'default' => 20,
                'sanitize_callback' => 'absint',
            ),
            'title_limit' => array(
                'default' => 60,
                'sanitize_callback' => 'absint',
            ),
        );
    }
    
    /**
     * Get search arguments schema
     */
    private function get_search_args() {
        return array(
            'search' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => function($param) {
                    return strlen($param) >= 3;
                },
            ),
            'post_type' => array(
                'default' => 'post',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'per_page' => array(
                'default' => 20,
                'sanitize_callback' => 'absint',
            ),
        );
    }
}

// Initialize REST API
new LMA_REST_API();