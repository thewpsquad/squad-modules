<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The main class for Divi Squad.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Base\DiviBuilder;

use DiviSquad\Utils\Asset;
use DiviSquad\Utils\Divi;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\WP;
use function add_action;
use function et_builder_enabled_for_post;
use function get_the_ID;

/**
 * Divi Squad Class.
 *
 * @package DiviSquad
 * @since   1.0.0
 */
abstract class IntegrationAPI extends IntegrationAPIBase {

	/**
	 * Loads custom modules when the builder is ready.
	 */
	abstract public function hook_et_builder_ready();

	/**
	 * Performs initialization tasks.
	 */
	public function initialize() {
		// Loads custom modules when the builder is ready.
		add_action( 'et_builder_ready', array( $this, 'hook_et_builder_ready' ), 9 );

		// Load all assets in the admin and frontend area.
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ), 0 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_hook_enqueue_scripts' ), 0 );
	}

	/**
	 * Enqueues the plugin's scripts and styles for the admin area.
	 */
	public function admin_hook_enqueue_scripts() {
		if ( Divi::is_bfb_enabled() || Divi::is_tb_admin_screen() ) {
			$this->enqueue_backend_styles();
		}
	}

	/**
	 * Enqueues minified (production) or non-minified (hot reloaded) backend styles.
	 */
	public function enqueue_backend_styles() {
		$file_path = "{$this->build_path}/styles/backend-style.css";

		// Ensure backend style CSS file exists.
		if ( file_exists( "$this->plugin_dir/$file_path" ) ) {
			$style_asset_path = Asset::module_asset_path( 'backend-style', array( 'ext' => 'css' ) );
			Asset::style_enqueue( "$this->name-backend", $style_asset_path, array(), 'all', true );
		}
	}

	/**
	 * Enqueues the plugin's scripts and styles.
	 */
	public function wp_hook_enqueue_scripts() {
		// Do not load assets if current page is plugin admin page.
		if ( empty( $this->name ) || ( is_admin() && ! function_exists( 'get_current_screen' ) ) || Helper::is_squad_page() ) {
			return;
		}

		// Enqueues non-minified, hot reloaded javascript bundles. (Builder).
		if ( Divi::is_fb_enabled() ) {
			$script_asset_deps = array( 'jquery', 'react', 'react-dom', 'react-jsx-runtime', 'wp-i18n' );
			$script_asset_path = Asset::module_asset_path( 'builder-bundle' );
			Asset::asset_enqueue( "$this->name-builder", $script_asset_path, $script_asset_deps, true );

			// Load script translations.
			WP::set_script_translations( "$this->name-builder", divi_squad()->get_name() );
		}

		// Enqueues styles for divi builder including theme and plugin.
		if ( ( Divi::is_tb_admin_screen() || Divi::is_theme_builder_used() ) || ( is_singular() && et_builder_enabled_for_post( get_the_ID() ) ) ) {
			$style_handle_name = Divi::is_fb_enabled() ? "$this->name-builder" : $this->name;
			$style_asset_name  = defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) && ! Divi::is_fb_enabled() ? 'builder-style-dbp' : 'builder-style';
			$style_asset_path  = Asset::module_asset_path( $style_asset_name, array( 'ext' => 'css' ) );
			Asset::style_enqueue( $style_handle_name, $style_asset_path, array(), 'all', true );
		}

		if ( Divi::is_fb_enabled() && ! Divi::is_bfb_enabled() ) {
			$this->enqueue_backend_styles();
		}
	}
}
