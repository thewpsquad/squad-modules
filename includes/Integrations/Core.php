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

namespace DiviSquad\Integrations;

use DiviSquad\Base as Squad_WpOrg_Base;
use DiviSquad\Managers as Squad_WpOrg_Managers;
use DiviSquad\Settings\Migration;
use function add_action;
use function add_filter;
use function divi_squad;
use function is_admin;
use function register_activation_hook;
use function register_deactivation_hook;

/**
 * Divi Squad Core Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 *
 * @property Squad_WpOrg_Base\Memory         $memory     Squad memory.
 * @property Squad_WpOrg_Managers\Modules    $modules    Squad module manager.
 * @property Squad_WpOrg_Managers\Extensions $extensions Squad extension manger.
 */
abstract class Core extends Squad_WpOrg_Base\Core {

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options data.
	 *
	 * @return void
	 */
	protected function init( $options = array() ) {
		$this->container['memory']     = new Squad_WpOrg_Base\Memory( $this->opt_prefix );
		$this->container['modules']    = new Squad_WpOrg_Managers\Modules();
		$this->container['extensions'] = new Squad_WpOrg_Managers\Extensions();

		// Register all hooks for plugin.
		register_activation_hook( DIVI_SQUAD__FILE__, array( $this, 'hook_activation' ) );
		register_deactivation_hook( DIVI_SQUAD__FILE__, array( $this, 'hook_deactivation' ) );
	}

	/**
	 * Load all extensions.
	 *
	 * @return void
	 */
	protected function load_extensions() {
		// Load all extensions.
		$this->extensions->load_extensions( realpath( dirname( __DIR__ ) ) );
	}

	/**
	 * Load the divi custom modules for the divi builder.
	 *
	 * @return void
	 */
	protected function load_modules_for_builder() {
		// Register all assets.
		$asset_manager = new Squad_WpOrg_Managers\Assets();
		add_action( 'wp_enqueue_scripts', array( $asset_manager, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $asset_manager, 'enqueue_scripts_vb' ) );
		add_filter( 'divi_squad_assets_backend_extra', array( $asset_manager, 'wp_localize_script_data' ) );

		// Register all hooks for divi integration.
		add_action( 'divi_extensions_init', array( $this, 'hook_migrate_builder_settings' ) );
		add_action( 'divi_extensions_init', array( $this, 'hook_initialize_builder_extension' ) );
		add_action( 'wp_loaded', array( $this, 'hook_initialize_builder_asset_definitions' ) );

		// Force the legacy backend builder to reload its template cache.
		// This ensures that custom modules are available for use right away.
		if ( function_exists( 'et_pb_force_regenerate_templates' ) ) {
			\et_pb_force_regenerate_templates();
		}
	}

	/**
	 * The admin interface asset and others.
	 *
	 * @return void
	 */
	protected function load_admin() {
		Squad_WpOrg_Managers\Ajax::load();
		Squad_WpOrg_Managers\RestRoutes::load();

		if ( is_admin() ) {
			Admin::load();
		}
	}

	/**
	 * Set the activation hook.
	 *
	 * @return void
	 */
	public function hook_activation() {
		// Set plugin activation time.
		$this->memory->set( 'activation_time', time() );
		if ( divi_squad()->get_version() !== $this->memory->get( 'version' ) ) {
			$this->memory->set( 'previous_version', $this->memory->get( 'version' ) );
		}
		$this->memory->set( 'version', divi_squad()->get_version() );

		/*
		 * Clean the Divi Builder old cache
		 *
		 * @since 2.0.0
		 */
		if ( ! $this->memory->get( 'is_cache_deleted' ) ) {
			global $wp_filesystem;

			if ( ! isset( $wp_filesystem ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				\WP_Filesystem();
			}

			// Clean the Divi Builder old cache from the current installation.
			$cache_path = WP_CONTENT_DIR . 'et-cache';
			$can_write  = $wp_filesystem->is_writable( $cache_path ) && ! is_file( $cache_path );

			if ( $can_write && $wp_filesystem->exists( $cache_path ) ) {
				$wp_filesystem->rmdir( $cache_path );

				// Store the status.
				$this->memory->set( 'is_cache_deleted', true );
			}
		}
	}

	/**
	 * Set the deactivation hook.
	 *
	 * @return void
	 */
	public function hook_deactivation() {
		$this->memory->set( 'version', $this->get_version() );
		$this->memory->set( 'deactivation_time', time() );
	}

	/**
	 *  Load the settings migration.
	 *
	 * @return void
	 */
	public function hook_migrate_builder_settings() {
		if ( class_exists( Migration::class ) ) {
			Migration::init();
		}
	}

	/**
	 *  Load the extensions.
	 *
	 * @return void
	 */
	public function hook_initialize_builder_extension() {
		if ( class_exists( DiviBuilder::class ) ) {
			new DiviBuilder( $this->name, DIVI_SQUAD_DIR_PATH, DIVI_SQUAD_DIR_URL );
		}
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @return void
	 */
	public function hook_initialize_builder_asset_definitions() {
		if ( function_exists( 'et_fb_process_shortcode' ) && class_exists( DiviBackend::class ) ) {
			$helpers = new DiviBackend();
			add_filter( 'et_fb_backend_helpers', array( $helpers, 'static_asset_definitions' ), 11 );
			add_filter( 'et_fb_get_asset_helpers', array( $helpers, 'asset_definitions' ), 11 );
		}
	}
}
