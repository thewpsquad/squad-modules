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
		$file_handle = "{$this->name}-backend";
		$file_path   = "{$this->build_path}styles/backend-style.css";

		// Ensure backend style CSS file exists.
		if ( file_exists( "{$this->plugin_dir}/{$file_path}" ) ) {
			wp_enqueue_style( $file_handle, "{$this->plugin_dir_url}{$file_path}", array(), $this->get_version() );
		}
	}

	/**
	 * Enqueues the plugin's scripts and styles.
	 */
	public function wp_hook_enqueue_scripts() {
		// Enqueues non-minified, hot reloaded javascript bundles. (Builder).
		if ( et_core_is_fb_enabled() ) {
			$builder_styles_url = "{$this->plugin_dir_url}{$this->build_path}styles/builder-style.css";
			wp_enqueue_style( "{$this->name}-builder", $builder_styles_url, array(), $this->get_version() );

			$bundle_url = "{$this->plugin_dir_url}{$this->build_path}scripts/builder-bundle.js";
			wp_enqueue_script( "{$this->name}-builder", $bundle_url, $this->bundle_dependencies['builder'], $this->get_version(), true );
		} else {
			// Enqueues minified, production javascript bundles. (Frontend).
			$styles             = et_is_builder_plugin_active() ? 'builder-style-dbp' : 'builder-style';
			$builder_styles_url = "{$this->plugin_dir_url}{$this->build_path}styles/{$styles}.css";
			wp_enqueue_style( $this->name, $builder_styles_url, array(), $this->get_version() );
		}

		if ( et_core_is_fb_enabled() && ! et_builder_bfb_enabled() ) {
			$this->enqueue_backend_styles();
		}
	}
}
