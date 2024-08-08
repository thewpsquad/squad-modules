<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Core class for Divi Squad.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

use DiviSquad\Manager;
use function et_pb_force_regenerate_templates;

/**
 * Divi Squad Core Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class Core extends \DiviSquad\Base\Core {

	/**
	 * Initialize the plugin with required components.
	 *
	 * @return void
	 */
	protected function init() {
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
		$this->memory->set( 'activation_time', time() );
		$this->memory->set( 'version', DISQ_VERSION );
		$this->memory->set( 'activate_version', DISQ_VERSION );

		// add previous plugin version.
		$previous_activate_version = $this->memory->get( 'previous_activate_version', DISQ_VERSION );
		if ( DISQ_VERSION !== $previous_activate_version ) {
			$this->memory->set( 'previous_activate_version', $previous_activate_version );
		}
	}

	/**
	 * The admin interface asset and others.
	 *
	 * @return void
	 */
	protected function load_admin_interface() {
		if ( is_admin() ) {
			Admin::get_instance();
		}
	}

	/**
	 * Register all rest api routes.
	 *
	 * @return void
	 */
	protected function register_ajax_rest_api_routes() {
		// Register all rest api.
		Manager\Rest_API_Routes::get_instance()->register_all();
	}

	/**
	 * Load the divi custom modules for the divi builder.
	 *
	 * @return void
	 */
	protected function load_divi_modules_for_builder() {
		// Register all assets.
		Manager\Assets::get_instance();

		// Register all hooks for divi integration.
		add_action( 'divi_extensions_init', array( $this, 'initialize_extension' ) );
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
	public function initialize_extension() {
		if ( class_exists( DiviSquad::class ) ) {
			new DiviSquad( $this->name, DISQ_DIR_PATH, DISQ_DIR_URL );
		}
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @return void
	 */
	public function initialize_divi_asset_definitions() {
		if ( function_exists( 'et_fb_process_shortcode' ) && class_exists( Divi\Backend::class ) ) {
			$helpers = new Divi\Backend();
			add_filter( 'et_fb_backend_helpers', array( $helpers, 'static_asset_definitions' ), 11 ); // load on functions when et-dynamic-asset-helpers are registered.
			add_filter( 'et_fb_get_asset_helpers', array( $helpers, 'asset_definitions' ), 11 ); // this data load after written the required file.

			$load_backend_data = static function () {
				$helpers      = new Divi\Backend();
				$helpers_data = $helpers->static_asset_definitions();

				// Pass helpers data via localization.
				wp_localize_script( 'et-frontend-builder', 'DISQBuilderBackend', $helpers_data );
			};

			add_action( 'wp_enqueue_scripts', $load_backend_data );
			add_action( 'admin_enqueue_scripts', $load_backend_data );
		}
	}
}
