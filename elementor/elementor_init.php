<?php

namespace LOAD_MORE_AJAX_ELEMENTOR;
use Elementor\{Plugin,
	Controls_Manager,
	Group_Control_Typography,
	Repeater,
	Utils
};


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Load_More_Ajax_Addons {
	/**
	 * Holds the class object.
	 *
	 * @since 1.0
	 *
	 */
	public static $_instance;

	/**
	 * Localize data array
	 *
	 * @var array
	 */
	public $localize_data = array();

	/**
	 * Directory Path
	 *
	 * @var string
	 */
	public $dir_path = '';

	/**
	 * Load Construct
	 *
	 * @since 1.0
	 */

	public function __construct() {

		$this->dir_path = plugin_dir_path( __FILE__ );
		add_action( 'plugins_loaded', [ $this, 'elementor_setup' ] );

		add_action( 'elementor/init', [ $this, 'load_more_ajax_elementor_init' ] );
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'before_enqueue_scripts' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'after_register_scripts' ] );

		// Elementor Preview
		add_action('elementor/editor/before_enqueue_scripts', [ $this, 'elementor_preview_mode' ] );

	}

	public function elementor_preview_mode() {
		
	}

	public function after_register_scripts() {

	}

	/**
	 * Enqueue Scripts
	 *
	 * @return void
	 */

	public function before_enqueue_scripts() {
		wp_enqueue_script( 'load_more_ajax-elementor', LOAD_MORE_AJAX_LITE_ASSETS . '/js/elementor.js', array(
			'jquery',
			'elementor-frontend'
		), LOAD_MORE_AJAX_LITE_VERSION, true );

		
	}

	/**
	 * Elementor Initialization
	 *
	 * @since 1.0
	 *
	 */

	public function load_more_ajax_elementor_init() {
		\Elementor\Plugin::$instance->elements_manager->add_category(
		'load_more_ajax-elements', [
				'title' => esc_html__( 'Load More Ajax Elements', 'load-more-ajax-lite' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	/**
	 * Installs default variables and checks if Elementor is installed
	 *
	 * @return void
	 */
	public function elementor_setup() {
		/**
		 * Check if Elementor installed and activated
		 * @see https://developers.elementor.com/creating-an-extension-for-elementor/
		 */
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		// Include Modules files
		$this->includes();

		$this->init_addons();
	}

	/**
	 * Load required core files.
	 */
	public function includes() {
		$this->init_helper_files();
	}

	/**
	 * Require initial necessary files
	 */
	public function init_helper_files() {
		
	}

	/**
	 * Load required file for addons integration
	 */
	public function init_addons() {
		add_action( 'elementor/widgets/register', [ $this, 'widgets_area' ] );

	}


	/**
	 * Register widgets by file name
	 */
	public function register_widgets_addon( $file_name ) {
		$widget_manager = Plugin::instance()->widgets_manager;

		$base  = basename( str_replace( '.php', '', $file_name ) );
		$class = ucwords( str_replace( '-', ' ', $base ) );
		$class = str_replace( ' ', '_', $class );
		$class = sprintf( 'LMA\Widgets\%s', $class ); // Ultraland\Widgets\abc;


		// Class File
		require_once $file_name;

		if ( class_exists( $class ) ) {
			$widget_manager->register( new $class );
		}
	}

	/**
	 * Load widgets require function
	 */
	public function widgets_area() {
		$this->widgets_register();
	}

	/**
	 * Requires widgets files
	 */
	private function widgets_register() {
		foreach ( glob( $this->dir_path . 'widgets/' . '*.php' ) as $file ) {
			$this->register_widgets_addon( $file );
		}
	}




	public static function load_more_ajax_get_instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new Load_More_Ajax_Addons();
		}

		return self::$_instance;
	}
}

$load_more_ajax_shortcode = Load_More_Ajax_Addons::load_more_ajax_get_instance();