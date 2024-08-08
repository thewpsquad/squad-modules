<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The main class for Divi Squad.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base;

use DiviSquad\Utils\Asset;
use function add_action;
use function et_builder_bfb_enabled;
use function et_builder_is_tb_admin_screen;
use function et_core_is_fb_enabled;
use function et_get_combined_script_handle;
use function et_is_builder_plugin_active;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Divi Squad Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class BuilderIntegrationAPI extends BuilderIntegrationAPIBase {

	/**
	 * Loads custom modules when the builder is ready.
	 */
	abstract public function hook_et_builder_ready();

	/**
	 * Performs initialization tasks.
	 */
	protected function initialize() {
		$this->bundle_dependencies = array(
			'builder'  => array( 'react-dom', 'react' ),
			'frontend' => array( 'jquery', et_get_combined_script_handle() ),
		);

		// Loads custom modules when the builder is ready.
		add_action( 'et_builder_ready', array( $this, 'hook_et_builder_ready' ), 9 );

		// Load all assets in the admin and frontend area.
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
		$file_path = "{$this->build_path}styles/backend-style.css";

		// Ensure backend style CSS file exists.
		if ( file_exists( "{$this->plugin_dir}/{$file_path}" ) ) {
			$style_asset_path = Asset::module_asset_path( 'backend-style', array( 'ext' => 'css' ) );
			Asset::style_enqueue( "{$this->name}-backend", $style_asset_path, array(), 'all', true );
		}
	}

	/**
	 * Enqueues the plugin's scripts and styles.
	 */
	public function wp_hook_enqueue_scripts() {
		// Enqueues non-minified, hot reloaded javascript bundles. (Builder).
		if ( et_core_is_fb_enabled() ) {
			$script_asset_path = Asset::module_asset_path( 'builder-bundle' );
			$style_asset_path  = Asset::module_asset_path( 'builder-style', array( 'ext' => 'css' ) );
			Asset::asset_enqueue( "{$this->name}-builder", $script_asset_path, $this->bundle_dependencies['builder'], true );
			Asset::style_enqueue( "{$this->name}-builder", $style_asset_path, array(), 'all', true );
		} else {
			// Enqueues minified, production javascript bundles. (Frontend).
			$styles = et_is_builder_plugin_active() ? 'builder-style-dbp' : 'builder-style';
			Asset::style_enqueue( $this->name, Asset::module_asset_path( $styles, array( 'ext' => 'css' ) ), array(), 'all', true );
		}

		if ( et_core_is_fb_enabled() && ! et_builder_bfb_enabled() ) {
			$this->enqueue_backend_styles();
		}
	}
}
