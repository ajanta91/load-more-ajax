<?php

/**
 * Plugin Name:       Load More Ajax Lite
 * * Plugin URI:      https://plugins.wpnonce.com/load-more-ajax/
 * Description:       Load More Ajax Lite is WordPress posts and custom post type posts ajax load more and ajax category filter.
 * Version:           1.1.2
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Ajanta Das
 * Author URI:        https://wpnonce.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       load-more-ajax-lite
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) )
    die( '-1' );

if ( ! class_exists( 'Load_More_Ajax_Lite' ) ) {
    /**
     * Main class of this plugin
     */
    final class Load_More_Ajax_Lite {

        const  VERSION = '1.1.1';

        /**
         * Minimum PHP Version
         *
         * Holds the minimum PHP version required to run the plugin.
         *
         * @since 1.7.0
         * @since 1.7.1 Moved from property with that name to a constant.
         *
         * @var string Minimum PHP version required to run the plugin.
         */
        const  MINIMUM_PHP_VERSION = '7.4';

        /**
         * Minimum Elementor Version
         *
         * Holds the minimum Elementor version required to run the plugin.
         *
         * @since 1.0.0
         *
         * @var string Minimum Elementor version required to run the plugin.
         */
        const MINIMUM_ELEMENTOR_VERSION = '3.3.0';


        
        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      Hostim_Core    $loader    Maintains and registers all hooks for the plugin.
         */
        protected $loader;

                

        /**
         * Clone
         *
         * Disable class cloning.
         *
         * @return void
         * @since 1.0.0
         *
         * @access protected
         *
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&#8217; huh?', 'hostim-core'), '2.0.0');
        }

        /**
         * Wakeup
         *
         * Disable unserializing the class.
         *
         * @return void
         * @since 1.0.0
         *
         * @access protected
         *
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&#8217; huh?', 'hostim-core'), '2.0.0');
        }
  

        /**
         * Constructor
         *
         * Initialize the Coro Core plugins.
         *
         * @since 1.7.0
         *
         * @access public
         */
        private function __construct() {

            $this->define_constants();

            register_activation_hook( __FILE__, [ $this, 'activate_info' ] );

            $this->init_hooks();

            $this->core_includes();

            
        }

        /**
         * Instance
         *
         * Ensures only one instance of the class is loaded or can be loaded.
         *
         * @return \Load_More_Ajax_Lite An instance of the class.
         * @since 1.0.0
         *
         * @access public
         * @static
         *
         */
        public static function instance() {

            static $instance = false;

            if ( ! $instance ) {
                $instance = new self();
            }

            return $instance;
        }

        /**
         * Define Some Constants
         */
        public function define_constants() {
            define( 'LOAD_MORE_AJAX_LITE_VERSION', self::VERSION );
            define( 'LOAD_MORE_AJAX_LITE_FILE', __FILE__ );
            define( 'LOAD_MORE_AJAX_LITE_PATH', __DIR__ );
            define( 'LOAD_MORE_AJAX_LITE_URL', plugins_url( '', LOAD_MORE_AJAX_LITE_FILE ) );
            define( 'LOAD_MORE_AJAX_LITE_ASSETS', LOAD_MORE_AJAX_LITE_URL . '/assets' );
        }

        /**
         * activate_info
         */
        public function activate_info() {
            $installed = get_option('load_more_ajax_lite_installed');

            if ( ! $installed ) {
                update_option( 'load_more_ajax_lite_installed', time() );
            }

            update_option( 'load_more_ajax_lite_version', LOAD_MORE_AJAX_LITE_VERSION );



            global $wpdb;

            $table_name = $wpdb->prefix . 'load_more_post_shortcode_list';

            
            // SQL query to create table
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                id INT NOT NULL AUTO_INCREMENT,
                block_title VARCHAR(255),
                block_style INT,
                per_page INT,
                title_limit INT,
                text_limit INT,
                is_filter VARCHAR(5),
                include_post VARCHAR(255),
                exclude_post VARCHAR(255),
                post_column INT,
                created_time TIMESTAMP,
                user_id INT,
                PRIMARY KEY (id)
    	    )";

            // Execute SQL query
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); // Include WordPress upgrade script
            dbDelta($sql);
        }

        /**
         * Include Files
         *
         * Load core files required to run the plugin.
         *
         * @access public
         */
        public function core_includes() {
            // Extra functions
            require_once __DIR__ . '/inc/functions.php';
            require_once __DIR__ . '/inc/shortcodes.php';
            require_once __DIR__ . '/lib/admin/AdminMenu.php';
            require_once __DIR__ . '/lib/admin/PostBlock.php';

            require_once __DIR__ . '/elementor/elementor_init.php';
        }

        /**
         * Init Hooks
         *
         * Hook into actions and filters.
         *
         * @access private
         */
        private function init_hooks() {
            add_action( 'init', [ $this, 'i18n' ] );
            add_action( 'plugins_loaded', [ $this, 'init' ] );
        }

        /**
         * Load Textdomain
         *
         * Load plugin localization files.
         *
         * @access public
         */
        public function i18n() {
            load_plugin_textdomain( 'load-more-ajax-lite', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
        }


        /**
         * Init Coro Core
         *
         * Load the plugin after Elementor (and other plugins) are loaded.
         *
         * @access public
         */
        public function init() {
            // Check for required PHP version
            if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
                return;
            }

            // enqueue scripts
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
            add_action('admin_enqueue_scripts', [ $this, 'lmal_admin_enqueue_scripts'] );

            
        }


        public function loadmoreajax_get_font_url() {
            $fonts_url = '';
            /* Translators: If there are characters in your language that are not
            * supported by Libre Franklin, translate this to 'off'. Do not translate
            * into your own language.
            */
            $Poppins = _x('on', 'Poppins font: on or off', 'load-more-ajax-lite');

            if ('off' !== $Poppins) {
                $font_families = array();

                if ('off' !== $Poppins) {
                    $font_families[] = 'Poppins:400,500,600,700';
                }

                $query_args = array(
                    'family' => urlencode(implode('|', $font_families)),
                    'subset' => urlencode('latin,latin-ext'),
                );
                $fonts_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');
            }
            return esc_url_raw($fonts_url);
        }

        public function enqueue_scripts() {
            $font_url = $this->loadmoreajax_get_font_url();
            if ( !empty( $font_url ) ){
                wp_enqueue_style('loadmoreajax-fonts', esc_url_raw( $font_url ), array(), null);
            }

            wp_register_style( 'load-more-ajax-lite', plugins_url('assets/css/load-more-ajax-lite.css', __FILE__ ) );
            wp_register_style( 'load-more-ajax-lite-s2', plugins_url('assets/css/load-more-ajax-lite-s2.css', __FILE__ ) );
            wp_register_style( 'load-more-ajax-lite-s3', plugins_url('assets/css/load-more-ajax-lite-s3.css', __FILE__ ) );
            wp_enqueue_style( 'fontawesome', plugins_url( 'assets/css/all.min.css', __FILE__ ) );

            wp_register_script( 'load-more-ajax-lite', plugins_url('assets/js/load-more-ajax-lite.js', __FILE__ ), '1.0', true );
            wp_localize_script( 'load-more-ajax-lite', 'load_more_ajax_lite', array(
                'ajax_url' => admin_url('admin-ajax.php'),
            ) );
            
        }

        public function lmal_admin_enqueue_scripts(){
            wp_enqueue_style('lmal-admin', plugins_url('/lib/admin/assets/css/admin.css', __FILE__) );

            wp_enqueue_script('lmal-admin', plugins_url('/lib/admin/assets/js/admin-script.js', __FILE__), '1.0', true);
        }
    }
}

if ( ! function_exists( 'load_more_ajax_lite_load' ) ) {

    function load_more_ajax_lite_load() {
        return Load_More_Ajax_Lite::instance();
    }

    // Run ajax post lite
    load_more_ajax_lite_load();
}
