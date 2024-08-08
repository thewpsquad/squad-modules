<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base;

/**
 * The Base class for Core
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */
abstract class Core {
	/**
	 * The instance of Memory class.
	 *
	 * @var Memory
	 */
	protected $memory;

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
	 * Get the plugin name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
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
	 * Get the instance of memory.
	 *
	 * @return Memory
	 */
	public function get_memory() {
		return $this->memory;
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
	 * Load the memory instance.
	 *
	 * @return void
	 */
	protected function load_memory() {
		$this->memory = Memory::get_instance( $this->option_prefix );
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
		$this->memory->set( 'deactivation_time', time() );
	}

	/**
	 * The admin interface asset and others.
	 *
	 * @return void
	 */
	protected function load_global_assets() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
	}

	/**
	 * Load css variables in the frontend and admin panel.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_scripts() {
		// Set current required data into variables.
		$admin_page_id     = 'divi_squad_assets_backend';
		$logo_fill_colord  = DISQ_DIR_URL . 'build/assets/logos/defaults/divi-squad-fill-colord.png';
		$logo_fill_default = DISQ_DIR_URL . 'build/assets/logos/menu-icons/default.png';
		$logo_fill_active  = DISQ_DIR_URL . 'build/assets/logos/menu-icons/active.png';
		$logo_fill_focus   = DISQ_DIR_URL . 'build/assets/logos/menu-icons/focus.png';

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
	 * Load the localize data in the frontend and admin panel.
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
