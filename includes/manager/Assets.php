<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Utils\Asset;
use function DiviSquad\divi_squad;
use function get_template_directory;
use function get_template_directory_uri;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_script_is;

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
	 * Enqueue scripts for frontend.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$core_asset_deps      = array( 'jquery' );
		$vendor_asset_options = array( 'path' => 'vendor' );
		$module_asset_options = array( 'path' => 'divi4/scripts/modules' );

		$lottie_js         = Asset::asset_path( 'lottie', $vendor_asset_options );
		$typed_js          = Asset::asset_path( 'typed.umd', $vendor_asset_options );
		$images_loaded_js  = Asset::asset_path( 'imagesloaded.pkgd', $vendor_asset_options );
		$isotope_js        = Asset::asset_path( 'isotope.pkgd', $vendor_asset_options );
		$light_gallery_js  = Asset::asset_path( 'lightgallery.umd', array_merge( $vendor_asset_options, array( 'prod_file' => 'lightgallery' ) ) );
		$scrolling_text_js = Asset::asset_path( 'jquery.marquee', $vendor_asset_options );

		// All vendor scripts.
		Asset::register_scripts( 'vendor-lottie', $lottie_js );
		Asset::register_scripts( 'vendor-typed', $typed_js );
		Asset::register_scripts( 'vendor-light-gallery', $light_gallery_js, $core_asset_deps );
		Asset::register_scripts( 'vendor-images-loaded', $images_loaded_js, $core_asset_deps );
		Asset::register_scripts( 'vendor-isotope', $isotope_js, $core_asset_deps );
		Asset::register_scripts( 'vendor-scrolling-text', $scrolling_text_js, $core_asset_deps );

		// Re-queue third party scripts.
		$magnific_popup_script_path = '/includes/builder/feature/dynamic-assets/assets/js/magnific-popup.js';
		if ( ! wp_script_is( 'magnific-popup', 'registered' ) && file_exists( get_template_directory() . $magnific_popup_script_path ) ) {
			wp_register_script( 'magnific-popup', get_template_directory_uri() . $magnific_popup_script_path, $core_asset_deps, divi_squad()->get_version(), true );
		}

		$lottie_asset_deps       = array_merge( $core_asset_deps, array( 'disq-vendor-lottie' ) );
		$typing_text_module_deps = array_merge( $core_asset_deps, array( 'disq-vendor-typed' ) );

		// All module js.
		Asset::register_scripts( 'module-divider', Asset::asset_path( 'divider-bundle', $module_asset_options ), $core_asset_deps );
		Asset::register_scripts( 'module-lottie', Asset::asset_path( 'lottie-bundle', $module_asset_options ), $lottie_asset_deps );
		Asset::register_scripts( 'module-typing-text', Asset::asset_path( 'typing-text-bundle', $module_asset_options ), $typing_text_module_deps );
		Asset::register_scripts( 'module-bais', Asset::asset_path( 'bai-slider-bundle', $module_asset_options ), $core_asset_deps );
		Asset::register_scripts( 'module-accordion', Asset::asset_path( 'accordion-bundle', $module_asset_options ), $core_asset_deps );
		Asset::register_scripts( 'module-gallery', Asset::asset_path( 'gallery-bundle', $module_asset_options ), $core_asset_deps );
		Asset::register_scripts( 'module-scrolling-text', Asset::asset_path( 'scrolling-text-bundle', $module_asset_options ), $core_asset_deps );
		Asset::register_scripts( 'module-video-popup', Asset::asset_path( 'video-popup-bundle', $module_asset_options ), array( 'magnific-popup' ) );
	}

	/**
	 * Enqueue scripts for builder.
	 *
	 * @return void
	 */
	public function enqueue_scripts_vb() {
		if ( function_exists( 'et_core_is_fb_enabled' ) && \et_core_is_fb_enabled() ) {
			wp_enqueue_script( 'disq-vendor-typed' );
			wp_enqueue_script( 'disq-vendor-isotope' );
			wp_enqueue_script( 'disq-vendor-imagesloaded' );
			wp_enqueue_script( 'disq-vendor-lightgallery' );
			wp_enqueue_script( 'disq-vendor-scrolling-text' );
			wp_enqueue_script( 'disq-module-video-popup' );

			// Contact form default style.
			if ( class_exists( 'WPCF7' ) ) {
				wp_enqueue_style( 'contact-form-7' );
			}

			// WP Form default style.
			if ( function_exists( '\wpforms' ) && function_exists( '\wpforms_get_render_engine' ) && function_exists( '\wpforms_setting' ) && function_exists( '\wpforms_get_min_suffix' ) ) {
				$min         = \wpforms_get_min_suffix();
				$wp_forms_re = \wpforms_get_render_engine();
				$disable_css = (int) \wpforms_setting( 'disable-css', '1' );

				// Required variables.
				$style_name     = 1 === $disable_css ? 'full' : 'base';
				$plugin_dir_url = defined( '\WPFORMS_PLUGIN_URL' ) ? \WPFORMS_PLUGIN_URL : '';
				$plugin_version = defined( '\WPFORMS_VERSION' ) ? \WPFORMS_VERSION : '';

				if ( ! empty( $plugin_dir_url ) && ! empty( $plugin_version ) ) {
					if ( ! wp_script_is( "wpforms-$wp_forms_re-$style_name", 'registered' ) ) {
						wp_register_style( "wpforms-$wp_forms_re-$style_name", $plugin_dir_url . "assets/css/frontend/$wp_forms_re/wpforms-$style_name$min.css", array(), $plugin_version );
					}

					if ( ! wp_script_is( "wpforms-$wp_forms_re-$style_name", 'enqueued' ) ) {
						wp_enqueue_style( "wpforms-$wp_forms_re-$style_name" );
					}
				}
			}

			// Gravity form default style.
			if ( function_exists( 'gravity_form' ) ) {
				wp_enqueue_style( 'gform_basic' );
				wp_enqueue_style( 'gform_theme' );
			}

			// Ninja Forms default style.
			if ( function_exists( '\Ninja_Forms' ) ) {
				$ver       = \Ninja_Forms::VERSION;
				$js_dir    = \Ninja_Forms::$url . 'assets/js/min/';
				$css_dir   = \Ninja_Forms::$url . 'assets/css/';
				$is_footer = array( 'in_footer' => true );

				switch ( \Ninja_Forms()->get_setting( 'opinionated_styles' ) ) {
					case 'light':
						wp_enqueue_style( 'nf-display', $css_dir . 'display-opinions-light.css', array( 'dashicons' ), $ver );
						wp_enqueue_style( 'nf-font-awesome', $css_dir . 'font-awesome.min.css', array(), $ver );
						break;
					case 'dark':
						wp_enqueue_style( 'nf-display', $css_dir . 'display-opinions-dark.css', array( 'dashicons' ), $ver );
						wp_enqueue_style( 'nf-font-awesome', $css_dir . 'font-awesome.min.css', array(), $ver );
						break;
					default:
						wp_enqueue_style( 'nf-display', $css_dir . 'display-structure.css', array( 'dashicons' ), $ver );
				}

				// Date Picker.
				wp_enqueue_style( 'jBox', $css_dir . 'jBox.css', array(), $ver );
				wp_enqueue_style( 'rating', $css_dir . 'rating.css', array(), $ver );
				wp_enqueue_style( 'nf-flatpickr', $css_dir . 'flatpickr.css', array(), $ver );
				wp_enqueue_script( 'nf-front-end-deps', $js_dir . 'front-end-deps.js', array( 'jquery', 'backbone' ), $ver, $is_footer );

				// Media.
				wp_enqueue_media();
				wp_enqueue_style( 'summernote', $css_dir . 'summernote.css', array(), $ver );
				wp_enqueue_style( 'codemirror', $css_dir . 'codemirror.css', array(), $ver );
				wp_enqueue_style( 'codemirror-monokai', $css_dir . 'monokai-theme.css', $ver, $is_footer );
			}

			// Fluent Forms default style.
			if ( function_exists( '\wpFluentForm' ) && function_exists( '\fluentFormMix' ) ) {
				$fluent_form_public_css         = \fluentFormMix( 'css/fluent-forms-public.css' );
				$fluent_form_public_default_css = \fluentFormMix( 'css/fluentform-public-default.css' );

				if ( \is_rtl() ) {
					$fluent_form_public_css         = \fluentFormMix( 'css/fluent-forms-public-rtl.css' );
					$fluent_form_public_default_css = \fluentFormMix( 'css/fluentform-public-default-rtl.css' );
				}
				wp_enqueue_style( 'fluent-form-styles', $fluent_form_public_css, array(), \FLUENTFORM_VERSION );
				wp_enqueue_style( 'fluentform-public-default', $fluent_form_public_default_css, array(), \FLUENTFORM_VERSION );
			}

			// Magnific Popup.
			$magnific_popup_script_path = '/includes/builder/feature/dynamic-assets/assets/js/magnific-popup.js';
			if ( ! wp_script_is( 'magnific-popup', 'registered' ) && file_exists( get_template_directory() . $magnific_popup_script_path ) ) {
				wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . $magnific_popup_script_path, array( 'jquery' ), divi_squad()->get_version(), true );
			}
		}
	}
}
