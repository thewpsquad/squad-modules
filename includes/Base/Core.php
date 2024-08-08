<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base;

use function add_action;
use function apply_filters;
use function esc_attr;
use function is_admin;
use function is_multisite;
use function load_plugin_textdomain;
use function wp_json_encode;
use function wp_kses_data;

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

	protected $admin_menu_slug = '';

	/**
	 * The plugin options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * The Plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The Plugin Version.
	 *
	 * @since 1.4.5
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * The plugin option prefix
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $opt_prefix;

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
	 * List of containers
	 *
	 * @var array
	 */
	protected $container = array();

	/**
	 * Initialize the plugin with required components.
	 *
	 * @param array $options Options.
	 *
	 * @return void
	 */
	abstract protected function init( $options = array() );

	/**
	 * Load all extensions.
	 *
	 * @return void
	 */
	abstract protected function load_extensions();

	/**
	 * Load all divi modules.
	 *
	 * @return void
	 */
	abstract protected function load_modules_for_builder();

	/**
	 * Get the plugin options.
	 *
	 * @return array
	 */
	abstract public function get_options();

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	abstract public function get_version();

	/**
	 * Get the plugin version (doted).
	 *
	 * @return string
	 */
	abstract public function get_version_dot();

	/**
	 * Set the activation hook.
	 *
	 * @return void
	 */
	abstract public function hook_activation();

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
	 * Get the plugin admin menu slug.
	 *
	 * @return string
	 */
	public function get_admin_menu_slug() {
		return apply_filters( 'divi_squad_admin_main_menu_slug', $this->admin_menu_slug );
	}

	/**
	 * Get the plugin option prefix.
	 *
	 * @return string
	 */
	public function get_option_prefix() {
		return $this->opt_prefix;
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
	 * The admin interface asset and others.
	 *
	 * @return void
	 */
	protected function load_global_assets() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
	}

	/**
	 * Load css variables in the admin panel.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_admin_scripts() {
		$logo_css_selector = '';
		$is_maybe_admin    = is_admin();
		$is_visual_builder = function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled();

		if ( $is_maybe_admin || $is_visual_builder ) {
			if ( $is_maybe_admin ) {
				$logo_css_selector .= '#toplevel_page_divi_squad_dashboard div.wp-menu-image:before';
			}

			if ( '' !== $logo_css_selector && $is_visual_builder ) {
				$logo_css_selector .= ', ';
			}

			if ( $is_visual_builder ) {
				$logo_css_selector .= '.et-fb-settings-options-tab.et-fb-modules-list ul li.et_fb_divi_squad_modules.et_pb_folder';
			}

			// Start style tag and class selector.
			printf( '<style id="divi_squad_admin_assets_backend"> %1$s {', esc_attr( $logo_css_selector ) );

			// Set current required data into variables.
			$logo_fill_colored = DIVI_SQUAD_DIR_URL . 'build/admin/images/divi-squad-default.png';
			$logo_fill_default = DIVI_SQUAD_DIR_URL . 'build/admin/images/divi-squad-menu-default.png';
			$logo_fill_active  = DIVI_SQUAD_DIR_URL . 'build/admin/images/divi-squad-menu-active.png';
			$logo_fill_focus   = DIVI_SQUAD_DIR_URL . 'build/admin/images/divi-squad-menu-focus.png';

			// Define all css variables for logos.
			printf( '--squad-brand-logo: url("%s");', esc_url( $logo_fill_colored ) );
			printf( '--squad-brand-logo-menu-default: url("%s");', esc_url( $logo_fill_default ) );
			printf( '--squad-brand-logo-active-default: url("%s");', esc_url( $logo_fill_active ) );
			printf( '--squad-brand-logo-focus-default: url("%s");', esc_url( $logo_fill_focus ) );

			// End class selector and style tag.
			print '} </style>';
		}
	}

	/**
	 * Load css variables in the frontend.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_scripts() {}

	/**
	 * Set the localize data.
	 *
	 * @return void
	 */
	public function localize_scripts_data() {
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_localize_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_localize_data' ) );
	}

	/**
	 * Load the localized data in the frontend and admin panel.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_localize_data() {
		global $wp_version;

		// Default data for asset backend.
		$assets_backend_data_defaults = array(
			'site_type'  => is_multisite() ? 'multi' : 'default',
			'wp_version' => $wp_version,
		);

		// Add public api to modify or delete asset backend extra data.
		$assets_backend_data  = apply_filters( 'divi_squad_assets_backend_extra_data', $assets_backend_data_defaults );
		$assets_backend_extra = sprintf( 'window.DiviSquadExtra = %1$s;', wp_json_encode( $assets_backend_data ) );
		$assets_backend_extra = apply_filters( 'divi_squad_assets_backend_extra', $assets_backend_extra );

		// Start script tag.
		print '<script id="divi_squad_assets_extra" type="text/javascript">';
		print wp_kses_data( $assets_backend_extra );
		print '</script>';
		// End script tag.
	}

	/**
	 * Localizes a script.
	 *
	 * Works only if the script has already been registered.
	 *
	 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
	 * @param array  $l10n        The data itself. The data can be either a single or multidimensional array.
	 *
	 * @return string Localizes a script.
	 */
	public function localize_script( $object_name, $l10n ) {
		return sprintf( 'window.%1$s = %2$s;', $object_name, wp_json_encode( $l10n ) );
	}

	/**
	 * Resolve the plugin data.
	 *
	 * @param string $plugin_file Absolute path to the main plugin file.
	 *
	 * @return array
	 */
	protected function get_plugin_data( $plugin_file ) {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Retrieve plugin's metadata.
		return get_plugin_data( $plugin_file );
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->container[ $key ] );
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( array_key_exists( $key, $this->container ) ) {
			return $this->container[ $key ];
		}

		return new \stdClass();
	}
}
