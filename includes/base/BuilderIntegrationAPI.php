<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The main class for Divi Squad.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base;

/**
 * Divi Squad Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class BuilderIntegrationAPI {

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Absolute path to the plugin's directory.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_dir;

	/**
	 * The plugin's directory URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_dir_url;

	/**
	 * The plugin's version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin's version
	 */
	public $version = DISQ_VERSION;

	/**
	 * The asset build for the plugin
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin's version
	 */
	public $build_path;

	/**
	 * Dependencies for the plugin's JavaScript bundles.
	 *
	 * @since 1.0.0
	 *
	 * @var array {
	 *                          JavaScript Bundle Dependencies
	 *
	 * @type string[] $builder  Dependencies for the builder bundle
	 * @type string[] $frontend Dependencies for the frontend bundle
	 *                          }
	 */
	protected $bundle_dependencies = array();

	/**
	 * Divi Squad constructor.
	 *
	 * @param string $name           The plugin's WP Plugin name.
	 * @param string $plugin_dir     Absolute path to the plugin's directory.
	 * @param string $plugin_dir_url The plugin's directory URL.
	 */
	public function __construct( $name, $plugin_dir, $plugin_dir_url ) {
		// Set required variables as per definition.
		$this->build_path = 'build/shortcode/';

		$this->name           = $name;
		$this->plugin_dir     = $plugin_dir;
		$this->plugin_dir_url = $plugin_dir_url;

		$this->initialize();
	}

	/**
	 * Performs initialization tasks.
	 */
	protected function initialize() {
		$this->bundle_dependencies = array(
			'builder'  => array( 'react-dom', 'react' ),
			'frontend' => array( 'jquery', et_get_combined_script_handle() ),
		);

		add_action( 'et_builder_ready', array( $this, 'hook_et_builder_ready' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_hook_enqueue_scripts' ) );
	}

	/**
	 * Enqueues the plugin's scripts and styles for the admin area.
	 */
	public function admin_hook_enqueue_scripts() {
		if ( et_builder_bfb_enabled() || et_builder_is_tb_admin_screen() ) {
			$this->enqueue_backend_styles();
		}
	}

	/**
	 * Enqueues minified (production) or non-minified (hot reloaded) backend styles.
	 */
	public function enqueue_backend_styles() {
		$file_handle = "{$this->name}-backend-styles";
		$file_path   = "{$this->build_path}styles/backend-style.css";

		// Ensure backend style CSS file exists.
		if ( file_exists( "{$this->plugin_dir}/{$file_path}" ) ) {
			wp_enqueue_style( $file_handle, "{$this->plugin_dir_url}{$file_path}", array(), $this->version );
		}
	}

	/**
	 * Enqueues the plugin's scripts and styles.
	 */
	public function wp_hook_enqueue_scripts() {
		// Enqueues non-minified, hot reloaded javascript bundles. (Builder).
		if ( et_core_is_fb_enabled() ) {
			$builder_styles_url = "{$this->plugin_dir_url}{$this->build_path}styles/style.css";
			wp_enqueue_style( "{$this->name}-builder-styles", $builder_styles_url, array(), $this->version );

			$bundle_url = "{$this->plugin_dir_url}{$this->build_path}scripts/builder-bundle.js";
			wp_enqueue_script( "{$this->name}-builder-bundle", $bundle_url, $this->bundle_dependencies['builder'], $this->version, true );
		} else {
			// Enqueues minified, production javascript bundles. (Frontend).
			$styles             = et_is_builder_plugin_active() ? 'style-dbp' : 'style';
			$builder_styles_url = "{$this->plugin_dir_url}{$this->build_path}styles/{$styles}.css";
			wp_enqueue_style( "{$this->name}-styles", $builder_styles_url, array(), $this->version );
		}

		if ( et_core_is_fb_enabled() && ! et_builder_bfb_enabled() ) {
			$this->enqueue_backend_styles();
		}
	}

	/**
	 * Loads custom modules when the builder is ready.
	 */
	abstract public function hook_et_builder_ready();
}
