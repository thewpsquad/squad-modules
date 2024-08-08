<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use DiviSquad\Admin\Assets\Utils;
use function DiviSquad\divi_squad;

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
	 * Set the asset path.
	 *
	 * @param string $file        The file name.
	 * @param string $ext         The file extension.
	 * @param string $path_prefix The asset file path prefix.
	 *
	 * @return string
	 */
	protected function asset_path( $file, $ext = 'js', $path_prefix = 'shortcode/scripts/modules' ) {
		return sprintf( 'build/%1$s/%2$s.%3$s', $path_prefix, $file, $ext );
	}

	/**
	 * Register scripts for frontend and builder.
	 *
	 * @param string $handle The handle name.
	 * @param string $path   The script path url.
	 * @param array  $deps   The script dependencies.
	 *
	 * @return void
	 */
	protected function register_scripts( $handle, $path, $deps = array() ) {
		$handle       = sprintf( 'disq-%1$s', $handle );
		$asset_data   = Utils::process_asset_path_data( $path );
		$dependencies = array_merge( $asset_data['dependencies'], $deps );

		wp_register_script( $handle, $asset_data['path'], $dependencies, divi_squad()->get_version(), true );
	}

	/**
	 * Enqueue scripts for frontend.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$core_asset_deps  = array( 'jquery' );
		$lottie_js        = $this->asset_path( 'lottie', 'js', 'vendor' );
		$typed_js         = $this->asset_path( 'typed.umd', 'js', 'vendor' );
		$light_gallery_js = $this->asset_path( 'lightgallery.umd', 'js', 'vendor' );
		$imagesloaded_js  = $this->asset_path( 'imagesloaded.pkgd', 'js', 'vendor' );
		$isotope_js       = $this->asset_path( 'isotope.pkgd', 'js', 'vendor' );

		// All vendor scripts.
		$this->register_scripts( 'vendor-lottie', $lottie_js );
		$this->register_scripts( 'vendor-typed', $typed_js );
		$this->register_scripts( 'vendor-lightgallery', $light_gallery_js, $core_asset_deps );
		$this->register_scripts( 'vendor-imagesloaded', $imagesloaded_js, $core_asset_deps );
		$this->register_scripts( 'vendor-isotope', $isotope_js, $core_asset_deps );

		$lottie_asset_deps       = array_merge( $core_asset_deps, array( 'disq-vendor-lottie' ) );
		$typing_text_module_deps = array_merge( $core_asset_deps, array( 'disq-vendor-typed' ) );

		// All module js.
		$this->register_scripts( 'module-divider', $this->asset_path( 'divider-bundle' ), $core_asset_deps );
		$this->register_scripts( 'module-lottie', $this->asset_path( 'lottie-bundle' ), $lottie_asset_deps );
		$this->register_scripts( 'module-typing-text', $this->asset_path( 'typing-text-bundle' ), $typing_text_module_deps );
		$this->register_scripts( 'module-bais', $this->asset_path( 'bai-slider-bundle' ), $core_asset_deps );
		$this->register_scripts( 'module-accordion', $this->asset_path( 'accordion-bundle' ), $core_asset_deps );
		$this->register_scripts( 'module-gallery', $this->asset_path( 'gallery-bundle' ), $core_asset_deps );
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

		// Load third party resources
		if ( class_exists( 'WPCF7' ) ) {
			wp_enqueue_style( 'contact-form-7' );
		}

		if ( function_exists( '\wpforms' ) && function_exists( '\wpforms_get_render_engine' ) && function_exists( '\wpforms_setting' ) && function_exists( '\wpforms_get_min_suffix' ) ) {
			$min         = \wpforms_get_min_suffix();
			$wp_forms_re = \wpforms_get_render_engine();
			$disable_css = (int) \wpforms_setting( 'disable-css', '1' );
			$style_name  = 1 === $disable_css ? 'full' : 'base';

			if ( ! wp_script_is( "wpforms-$wp_forms_re-$style_name", 'registered' ) ) {
				wp_enqueue_style( "wpforms-$wp_forms_re-$style_name", \WPFORMS_PLUGIN_URL . "assets/css/frontend/$wp_forms_re/wpforms-$style_name$min.css", array(), \WPFORMS_VERSION );
			}

			if ( ! wp_script_is( "wpforms-$wp_forms_re-$style_name" ) ) {
				wp_enqueue_style( "wpforms-$wp_forms_re-$style_name" );
			}
		}

		if ( function_exists( 'gravity_form' ) ) {
			wp_enqueue_style( 'gform_basic' );
			wp_enqueue_style( 'gform_theme' );
		}
	}
}
