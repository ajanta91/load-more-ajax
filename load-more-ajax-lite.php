<?php
/**
 * Plugin Name:       Load More Ajax Lite
 * Description:       Load More Ajax Lite is WordPress posts and custom post type posts ajax load more and ajax category filter.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ajanta Das
 * Author URI:        mailto:ajanta.wpdev@gmail.com
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

        const  VERSION = '1.0.0';

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
        const  MINIMUM_PHP_VERSION = '7.2';

        /**
         * Constructor
         *
         * Initialize the Coro Core plugins.
         *
         * @since 1.7.0
         *
         * @access public
         */
        public function __construct() {

            $this->define_constants();

            register_activation_hook( __FILE__, [ $this, 'activate_info' ] );

            $this->init_hooks();

            $this->core_includes();
        }

        /**
         * Initialize a single Instance
         * 
         * @return \Load_More_Ajax_Lite
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
        }

        public function enqueue_scripts() {
        
            wp_register_style( 'load-more-ajax-lite', plugins_url('assets/css/load-more-ajax-lite.css', __FILE__ ) );
            wp_register_style( 'load-more-ajax-lite-s2', plugins_url('assets/css/load-more-ajax-lite-s2.css', __FILE__ ) );
            wp_register_script( 'load-more-ajax-lite', plugins_url('assets/js/load-more-ajax-lite.js', __FILE__ ), '1.0', true );
            wp_localize_script( 'load-more-ajax-lite', 'load_more_ajax_lite', array(
                'ajax_url' => admin_url('admin-ajax.php'),
            ) );
            
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
