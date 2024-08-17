<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Core class for Divi Squad.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Integrations;

use function add_action;
use function add_filter;
use function divi_squad;
use function is_admin;
use function wp_normalize_path;

/**
 * Divi Squad Core Class.
 *
 * @package DiviSquad
 * @since   1.0.0
 *
 * @property-read \DiviSquad\Base\Memory                  $memory     Squad memory.
 * @property-read \DiviSquad\Managers\Features\Modules    $modules    Squad module manager.
 * @property-read \DiviSquad\Managers\Features\Extensions $extensions Squad extension manger.
 */
abstract class Core extends \DiviSquad\Base\Core {

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options data.
	 *
	 * @return void
	 */
	protected function init( $options = array() ) {
		$this->container['modules']    = new \DiviSquad\Managers\Features\Modules();
		$this->container['extensions'] = new \DiviSquad\Managers\Features\Extensions();
	}

	/**
	 * Load all assets.
	 *
	 * @return void
	 */
	protected function load_assets() {
		// Load all plugin assets.
		\DiviSquad\Managers\Assets::load();
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
		// Register all hooks for divi integration.
		add_action( 'wp_loaded', array( $this, 'hook_initialize_builder_asset_definitions' ) );
		add_action( 'divi_extensions_init', array( $this, 'hook_migrate_builder_settings' ) );
		add_action( 'divi_extensions_init', array( $this, 'hook_initialize_builder_extension' ) );

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
		\DiviSquad\Managers\Ajax::load();
		\DiviSquad\Managers\RestRoutes::load();

		if ( is_admin() ) {
			\DiviSquad\Managers\Branding::load();
			\DiviSquad\Managers\Menus::load();
			\DiviSquad\Managers\Notices::load();

			// Load the site health integration.
			\DiviSquad\Managers\SiteHealth::get_instance()->load();
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

		// Set the plugin version.
		if ( $this->get_version_dot() !== $this->memory->get( 'version' ) ) {
			$this->memory->set( 'previous_version', $this->memory->get( 'version' ) );
		}
		$this->memory->set( 'version', $this->get_version_dot() );

		/*
		 * Clean the Divi Builder old cache
		 *
		 * @since 2.0.0
		 */
		if ( ! $this->memory->get( 'is_cache_deleted' ) ) {

			$filesystem = divi_squad()->get_wp_filesystem();

			// Clean the Divi Builder old cache from the current installation.
			$cache_path = wp_normalize_path( WP_CONTENT_DIR ) . 'et-cache';
			$can_write  = $filesystem->is_writable( $cache_path ) && ! $filesystem->is_file( $cache_path );

			if ( $can_write && $filesystem->exists( $cache_path ) ) {
				$filesystem->rmdir( $cache_path );

				// Store the status.
				$this->memory->set( 'is_cache_deleted', true );
			}
		}

		/**
		 * Fires after the plugin is activated.
		 *
		 * @param \DiviSquad\Integrations\Core $plugin The plugin instance.
		 */
		do_action( 'divi_squad_after_activation', $this );
	}

	/**
	 * Set the deactivation hook.
	 *
	 * @return void
	 */
	public function hook_deactivation() {
		$this->memory->set( 'version', $this->get_version_dot() );
		$this->memory->set( 'deactivation_time', time() );

		/**
		 * Fires after the plugin is deactivated.
		 *
		 * @param \DiviSquad\Integrations\Core $plugin The plugin instance.
		 */
		do_action( 'divi_squad_after_deactivation', $this );
	}

	/**
	 *  Load the settings migration.
	 *
	 * @return void
	 */
	public function hook_migrate_builder_settings() {
		if ( class_exists( \DiviSquad\Managers\Migrations::class ) ) {
			\DiviSquad\Managers\Migrations::init();
		}
	}

	/**
	 *  Load the extensions.
	 *
	 * @return void
	 */
	public function hook_initialize_builder_extension() {
		if ( class_exists( DiviBuilder::class ) ) {
			new DiviBuilder( $this->name, divi_squad()->get_path(), divi_squad()->get_url() );
		}
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @return void
	 */
	public function hook_initialize_builder_asset_definitions() {
		if ( function_exists( 'et_fb_process_shortcode' ) && class_exists( DiviBuilderBackend::class ) ) {
			$helpers = new DiviBuilderBackend();
			add_filter( 'et_fb_backend_helpers', array( $helpers, 'static_asset_definitions' ), 11 );
			add_filter( 'et_fb_get_asset_helpers', array( $helpers, 'asset_definitions' ), 11 );
		}
	}
}
