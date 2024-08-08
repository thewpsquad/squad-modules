<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Core class for Divi Squad.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Manager;
use function DiviSquad\divi_squad;
use function et_pb_force_regenerate_templates;

/**
 * Divi Squad Core Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class Core extends \DiviSquad\Base\Core {

	/**
	 * Load all core components.
	 *
	 * @return void
	 */
	protected function load_core_components() {
		$this->modules    = new Manager\Modules();
		$this->extensions = new Manager\Extensions();

		// Activate all modules and extensions.
		$this->modules->active_modules();
		$this->extensions->activate_extensions();

		// Load all rest api routes.
		$this->modules_rest_api_routes    = new Manager\Rest_API_Routes\Modules();
		$this->extensions_rest_api_routes = new Manager\Rest_API_Routes\Extensions();
	}

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options data.
	 *
	 * @return void
	 */
	protected function init( $options = array() ) {
		// Register all hooks for plugin.
		register_activation_hook( DISQ__FILE__, array( $this, 'hook_activation' ) );
		register_deactivation_hook( DISQ__FILE__, array( $this, 'hook_deactivation' ) );
	}

	/**
	 * Set the activation hook.
	 *
	 * @return void
	 */
	public function hook_activation() {
		$this->get_memory()->set( 'activation_time', time() );
		if ( divi_squad()->get_version() !== $this->get_memory()->get( 'version' ) ) {
			$this->get_memory()->set( 'previous_version', $this->get_memory()->get( 'version' ) );
		}
		$this->get_memory()->set( 'version', divi_squad()->get_version() );
	}

	/**
	 * The admin interface asset and others.
	 *
	 * @param array $options The plugin options.
	 *
	 * @return void
	 */
	protected function load_admin_interface( $options ) {
		if ( is_admin() ) {
			Admin::load( $options );

			// Load plugin review.
			new \DiviSquad\Admin\Plugin_Review();
		}
	}

	/**
	 * Register all rest api routes.
	 *
	 * @return void
	 */
	protected function register_ajax_rest_api_routes() {
		// Register all rest api.
		add_action( 'rest_api_init', array( new Manager\Rest_API_Routes(), 'register_all' ) );
	}

	/**
	 * Load all extensions.
	 *
	 * @return void
	 */
	public function load_all_extensions() {
		// Load all extensions.
		$this->extensions->load_extensions( realpath( dirname( __DIR__ ) ) );
	}

	/**
	 * Load the divi custom modules for the divi builder.
	 *
	 * @return void
	 */
	protected function load_divi_modules_for_builder() {
		// Register all assets.
		$asset_manager = new Manager\Assets();
		add_action( 'wp_enqueue_scripts', array( $asset_manager, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $asset_manager, 'enqueue_scripts_vb' ) );
		add_filter( 'divi_squad_assets_backend_extra', array( $asset_manager, 'wp_localize_script_data' ) );

		// Register all hooks for divi integration.
		add_action( 'divi_extensions_init', array( $this, 'initialize_divi_extension' ) );
		add_action( 'wp_loaded', array( $this, 'initialize_divi_asset_definitions' ) );

		// Force the legacy backend builder to reload its template cache.
		// This ensures that custom modules are available for use right away.
		if ( function_exists( 'et_pb_force_regenerate_templates' ) ) {
			et_pb_force_regenerate_templates();
		}
	}

	/**
	 *  Load the extensions.
	 *
	 * @return void
	 */
	public function initialize_divi_extension() {
		if ( class_exists( DiviBuilder::class ) ) {
			new DiviBuilder( $this->name, DISQ_DIR_PATH, DISQ_DIR_URL );
		}
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @return void
	 */
	public function initialize_divi_asset_definitions() {
		if ( function_exists( 'et_fb_process_shortcode' ) && class_exists( DiviBuilderBackend::class ) ) {
			$helpers = new DiviBuilderBackend();
			add_filter( 'et_fb_backend_helpers', array( $helpers, 'static_asset_definitions' ), 11 );
			add_filter( 'et_fb_get_asset_helpers', array( $helpers, 'asset_definitions' ), 11 );
		}
	}
}
