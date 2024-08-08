<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use DiviSquad\Utils\Asset;
use function DiviSquad\divi_squad;
use function wp_parse_args;

/**
 * Assets Class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Assets {

	/**
	 * Register scripts for frontend and builder.
	 *
	 * @param string $handle The handle name.
	 * @param array  $path   The script path url with options.
	 * @param array  $deps   The script dependencies.
	 *
	 * @return void
	 */
	protected function register_scripts( $handle, $path, $deps = array() ) {
		$handle       = sprintf( 'disq-%1$s', $handle );
		$asset_data   = Asset::process_asset_path_data( $path );
		$dependencies = array_merge( $asset_data['dependencies'], $deps );

		wp_register_script( $handle, $asset_data['path'], $dependencies, divi_squad()->get_version(), true );
	}

	/**
	 * Enqueue scripts for frontend.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$core_asset_deps      = array( 'jquery' );
		$vendor_asset_options = array( 'path' => 'vendor' );
		$module_asset_options = array( 'path' => 'divi4/scripts/modules' );

		$lottie_js        = Asset::asset_path( 'lottie', $vendor_asset_options );
		$typed_js         = Asset::asset_path( 'typed.umd', $vendor_asset_options );
		$images_loaded_js = Asset::asset_path( 'imagesloaded.pkgd', $vendor_asset_options );
		$isotope_js       = Asset::asset_path( 'isotope.pkgd', $vendor_asset_options );
		$light_gallery_js = Asset::asset_path( 'lightgallery.umd', array_merge( $vendor_asset_options, array( 'prod_file' => 'lightgallery' )) ); // phpcs:ignore

		// All vendor scripts.
		$this->register_scripts( 'vendor-lottie', $lottie_js );
		$this->register_scripts( 'vendor-typed', $typed_js );
		$this->register_scripts( 'vendor-lightgallery', $light_gallery_js, $core_asset_deps );
		$this->register_scripts( 'vendor-imagesloaded', $images_loaded_js, $core_asset_deps );
		$this->register_scripts( 'vendor-isotope', $isotope_js, $core_asset_deps );

		$lottie_asset_deps       = array_merge( $core_asset_deps, array( 'disq-vendor-lottie' ) );
		$typing_text_module_deps = array_merge( $core_asset_deps, array( 'disq-vendor-typed' ) );

		// All module js.
		$this->register_scripts( 'module-divider', Asset::asset_path( 'divider-bundle', $module_asset_options ), $core_asset_deps );
		$this->register_scripts( 'module-lottie', Asset::asset_path( 'lottie-bundle', $module_asset_options ), $lottie_asset_deps );
		$this->register_scripts( 'module-typing-text', Asset::asset_path( 'typing-text-bundle', $module_asset_options ), $typing_text_module_deps );
		$this->register_scripts( 'module-bais', Asset::asset_path( 'bai-slider-bundle', $module_asset_options ), $core_asset_deps );
		$this->register_scripts( 'module-accordion', Asset::asset_path( 'accordion-bundle', $module_asset_options ), $core_asset_deps );
		$this->register_scripts( 'module-gallery', Asset::asset_path( 'gallery-bundle', $module_asset_options ), $core_asset_deps );
	}

	/**
	 * Enqueue scripts for builder.
	 *
	 * @return void
	 */
	public function enqueue_scripts_vb() {
		wp_enqueue_script( 'disq-vendor-typed' );
		wp_enqueue_script( 'disq-vendor-imagesloaded' );
		wp_enqueue_script( 'disq-vendor-isotope' );
		wp_enqueue_script( 'disq-vendor-lightgallery' );

		// Load third party resources.
		if ( class_exists( 'WPCF7' ) ) {
			wp_enqueue_style( 'contact-form-7' );
		}

		if ( function_exists( '\wpforms' ) && function_exists( '\wpforms_get_render_engine' ) && function_exists( '\wpforms_setting' ) && function_exists( '\wpforms_get_min_suffix' ) ) {
			$min         = \wpforms_get_min_suffix();
			$wp_forms_re = \wpforms_get_render_engine();
			$disable_css = (int) \wpforms_setting( 'disable-css', '1' );

			// Required variables.
			$style_name     = 1 === $disable_css ? 'full' : 'base';
			$plugin_dir_url = defined( '\WPFORMS_PLUGIN_URL' ) ? \WPFORMS_PLUGIN_URL : '';
			$plugin_version = defined( '\WPFORMS_VERSION' ) ? \WPFORMS_VERSION : '';

			if ( ! empty( $plugin_dir_url ) && $plugin_version ) {
				if ( ! wp_script_is( "wpforms-$wp_forms_re-$style_name", 'registered' ) ) {
					wp_enqueue_style( "wpforms-$wp_forms_re-$style_name", $plugin_dir_url . "assets/css/frontend/$wp_forms_re/wpforms-$style_name$min.css", array(), $plugin_version );
				}

				if ( ! wp_script_is( "wpforms-$wp_forms_re-$style_name" ) ) {
					wp_enqueue_style( "wpforms-$wp_forms_re-$style_name" );
				}
			}
		}

		if ( function_exists( 'gravity_form' ) ) {
			wp_enqueue_style( 'gform_basic' );
			wp_enqueue_style( 'gform_theme' );
		}
	}
}
