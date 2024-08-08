<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base;

/**
 * The Base class for Core
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
abstract class Core {

	/** The instance of the modules class.
	 *
	 * @var \DiviSquad\Manager\Modules
	 */
	protected $modules;

	/** The instance of the extensions class.
	 *
	 * @var \DiviSquad\Manager\Extensions
	 */
	protected $extensions;

	/** The instance of the modules class for rest API Routes.
	 *
	 * @var \DiviSquad\Manager\Rest_API_Routes\Modules
	 */
	protected $modules_rest_api_routes;

	/** The instance of the extensions class for rest API Routes.
	 *
	 * @var \DiviSquad\Manager\Rest_API_Routes\Extensions
	 */
	protected $extensions_rest_api_routes;

	/**
	 * The Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The plugin option prefix
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $option_prefix;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Minimum version of Divi Theme
	 *
	 * @var string
	 */
	protected $min_version_divi;

	/**
	 * Minimum version of PHP
	 *
	 * @var string
	 */
	protected $min_version_php;

	/**
	 * Minimum version of WordPress
	 *
	 * @var string
	 */
	protected $min_version_wp;

	/**
	 * The Script handle the text domain will be attached to.
	 *
	 * @var string
	 */
	protected $localize_handle;

	/**
	 * The full file path to the directory containing translation files.
	 *
	 * @var string
	 */
	protected $localize_path;

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options.
	 *
	 * @return void
	 */
	abstract protected function init( $options = array() );

	/**
	 * Load all core components.
	 *
	 * @return void
	 */
	abstract protected function load_core_components();

	/**
	 * Register all rest api routes.
	 *
	 * @return void
	 */
	abstract protected function register_ajax_rest_api_routes();

	/**
	 * Load all extensions.
	 *
	 * @return void
	 */
	abstract protected function load_all_extensions();

	/**
	 * Load all divi modules.
	 *
	 * @return void
	 */
	abstract protected function load_divi_modules_for_builder();

	/**
	 * Get the instance of memory.
	 *
	 * @return \DiviSquad\Base\Memory
	 */
	abstract public function get_memory();

	/**
	 * Set the instance of memory.
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @return \DiviSquad\Base\Memory
	 */
	abstract public function set_memory( $prefix );

	/**
	 * Get the plugin name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * The full file path to the directory containing translation files.
	 *
	 * @return string
	 */
	public function get_localize_path() {
		return $this->localize_path;
	}

	/**
	 * Get the plugin option prefix.
	 *
	 * @return string
	 */
	public function get_option_prefix() {
		return $this->option_prefix;
	}

	/**
	 * Define the general constants for the plugin
	 *
	 * @return void
	 */
	protected function define_general_constants() {
		define( 'DISQ_VERSION', $this->version );
		define( 'DISQ_MINIMUM_DIVI_VERSION', $this->min_version_divi );
		define( 'DISQ_MINIMUM_DIVI_BUILDER_VERSION', $this->min_version_divi );
		define( 'DISQ_MINIMUM_PHP_VERSION', $this->min_version_php );
		define( 'DISQ_MINIMUM_WP_VERSION', $this->min_version_wp );
	}

	/**
	 * Get the instance of modules.
	 *
	 * @return \DiviSquad\Manager\Modules
	 */
	public function get_modules() {
		return $this->modules;
	}

	/**
	 * Get the instance of extensions.
	 *
	 * @return \DiviSquad\Manager\Extensions
	 */
	public function get_extensions() {
		return $this->extensions;
	}

	/**
	 * The instance of the modules class for rest API Routes.
	 *
	 * @return \DiviSquad\Manager\Rest_API_Routes\Modules
	 */
	public function get_modules_rest_api_routes() {
		return $this->modules_rest_api_routes;
	}

	/**
	 * The instance of the extensions class for rest API Routes.
	 *
	 * @return \DiviSquad\Manager\Rest_API_Routes\Extensions
	 */
	public function get_extensions_rest_api_routes() {
		return $this->extensions_rest_api_routes;
	}

	/**
	 * Load the local text domain.
	 *
	 * @return void
	 */
	public function load_text_domain() {
		load_plugin_textdomain( $this->name, false, "{$this->name}/languages" );
	}

	/**
	 * Set the deactivation hook.
	 *
	 * @return void
	 */
	public function hook_deactivation() {
		$this->get_memory()->set( 'deactivation_time', time() );
	}

	/**
	 * The admin interface asset and others.
	 *
	 * @return void
	 */
	protected function load_global_assets() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );

		if ( isset( $_GET['et_fb'] ) && '1' === $_GET['et_fb'] ) { // phpcs:ignore
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_admin_scripts' ) );
		}
	}

	/**
	 * Load css variables in the admin panel.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_admin_scripts() {
		// Set current required data into variables.
		$admin_page_id     = 'divi_squad_admin_assets_backend';
		$logo_fill_colord  = DISQ_DIR_URL . 'build/admin/images/divi-squad-default.png';
		$logo_fill_default = DISQ_DIR_URL . 'build/admin/images/divi-squad-menu-default.png';
		$logo_fill_active  = DISQ_DIR_URL . 'build/admin/images/divi-squad-menu-active.png';
		$logo_fill_focus   = DISQ_DIR_URL . 'build/admin/images/divi-squad-menu-focus.png';

		// Start style tag.
		printf( '<style id="%1$s">', esc_attr( $admin_page_id ) );
		// Start class selector.
		print '#toplevel_page_divi_squad_dashboard div.wp-menu-image:before,.et-fb-settings-options-tab.et-fb-modules-list ul li.et_fb_divi_squad_modules.et_pb_folder {';

		// Define all css variables for logos.
		printf( '--disq-brand-logo: url("%s");', esc_url_raw( $logo_fill_colord ) );
		printf( '--disq-brand-logo-menu-default: url("%s");', esc_url_raw( $logo_fill_default ) );
		printf( '--disq-brand-logo-active-default: url("%s");', esc_url_raw( $logo_fill_active ) );
		printf( '--disq-brand-logo-focus-default: url("%s");', esc_url_raw( $logo_fill_focus ) );

		// End class selector.
		print '}';
		// End style tag.
		print '</style>';
	}

	/**
	 * Load css variables in the frontend.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_scripts() {
	}

	/**
	 * Set the localize data.
	 *
	 * @return array
	 */
	public function localize_scripts_data() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_localize_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_localize_data' ) );

		return array(
			'frontend' => array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'assetsUrl' => DISQ_ASSET_URL . 'assets/',
			),
			'builder'  => array(),
		);
	}

	/**
	 * Load the localized data in the frontend and admin panel.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_localize_data() {
		// Set current required data into variables.
		$admin_page_id = 'divi_squad_assets_backend_extra';

		// Start script tag.
		printf( '<script id="%1$s" type="application/javascript">', esc_attr( $admin_page_id ) );

		printf(
			'var DiviSquadExtra = %1$s',
			wp_json_encode(
				array(
					'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
					'assetsUrl' => DISQ_ASSET_URL . 'assets/',
				)
			)
		);

		// End script tag.
		print '</script>';
	}
}
