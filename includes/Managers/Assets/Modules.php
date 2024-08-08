<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Modules class.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Managers\Assets;

use DiviSquad\Base\Factories\PluginAsset\PluginAsset;
use DiviSquad\Utils\Asset;
use DiviSquad\Utils\Divi;
use function apply_filters;
use function divi_squad;
use function esc_js;
use function get_template_directory;
use function get_template_directory_uri;
use function is_rtl;
use function wp_enqueue_media;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_json_encode;
use function wp_register_script;
use function wp_register_style;
use function wp_script_is;

/**
 * Modules class.
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Modules extends PluginAsset {

	/**
	 * Enqueue scripts, styles, and other assets in the WordPress frontend and admin area.
	 *
	 * @param string $type The type of the script. Default is 'frontend'.
	 * @param string $hook_suffix The hook suffix for the current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $type = 'frontend', $hook_suffix = '' ) {
		// Check if the type is not frontend.
		if ( 'frontend' !== $type ) {
			return;
		}

		// Enqueue scripts for frontend and builder.
		$this->enqueue_frontend_scripts();
		$this->enqueue_builder_scripts();
	}

	/**
	 * Localize script data.
	 *
	 * @param string       $type The type of the localize data. Default is 'raw'. Accepts 'raw' or 'output'.
	 * @param string|array $data The data to localize.
	 *
	 * @return string|array
	 */
	public function get_localize_data( $type = 'raw', $data = array() ) {
		if ( 'output' === $type && Divi::is_fb_enabled() ) {
			$localize = apply_filters( 'divi_squad_assets_builder_backend_extra_data', array() );
			$data    .= sprintf( 'window.DISQBuilderLocalize = %1$s;', esc_js( wp_json_encode( $localize ) ) );
		}

		return $data;
	}

	/**
	 * Enqueue scripts for frontend.
	 *
	 * @return void
	 */
	public function enqueue_frontend_scripts() {
		$version         = divi_squad()->get_version();
		$footer_args     = Asset::footer_arguments( true );
		$core_asset_deps = array( 'jquery' );

		// All vendor scripts.
		Asset::register_script( 'vendor-lottie', Asset::vendor_asset_path( 'lottie' ) );
		Asset::register_script( 'vendor-typed', Asset::vendor_asset_path( 'typed.umd' ) );
		Asset::register_script( 'vendor-light-gallery', Asset::vendor_asset_path( 'lightgallery.umd', array( 'prod_file' => 'lightgallery' ) ), $core_asset_deps );
		Asset::register_script( 'vendor-images-loaded', Asset::vendor_asset_path( 'imagesloaded.pkgd' ), $core_asset_deps );
		Asset::register_script( 'vendor-scrolling-text', Asset::vendor_asset_path( 'jquery.marquee' ), $core_asset_deps );

		// Re-queue third party scripts.
		$magnific_popup_script_path = '/includes/builder/feature/dynamic-assets/assets/js/magnific-popup.js';
		if ( ! wp_script_is( 'magnific-popup', 'registered' ) && file_exists( get_template_directory() . $magnific_popup_script_path ) ) {
			wp_register_script( 'magnific-popup', get_template_directory_uri() . $magnific_popup_script_path, $core_asset_deps, $version, $footer_args );
		}

		// All module js dependencies.
		$post_grid_deps    = array_merge( $core_asset_deps, array( 'wp-api-fetch' ) );
		$lottie_asset_deps = array_merge( $core_asset_deps, array( 'squad-vendor-lottie' ) );
		$typing_text_deps  = array_merge( $core_asset_deps, array( 'squad-vendor-typed' ) );
		$video_popup_deps  = array_merge( $core_asset_deps, array( 'magnific-popup' ) );

		// Register all module scripts.
		Asset::register_script( 'module-divider', Asset::module_asset_path( 'modules/divider-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-lottie', Asset::module_asset_path( 'modules/lottie-bundle' ), $lottie_asset_deps );
		Asset::register_script( 'module-typing-text', Asset::module_asset_path( 'modules/typing-text-bundle' ), $typing_text_deps );
		Asset::register_script( 'module-bais', Asset::module_asset_path( 'modules/bai-slider-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-accordion', Asset::module_asset_path( 'modules/accordion-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-gallery', Asset::module_asset_path( 'modules/gallery-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-scrolling-text', Asset::module_asset_path( 'modules/scrolling-text-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-video-popup', Asset::module_asset_path( 'modules/video-popup-bundle' ), $video_popup_deps );
		Asset::register_script( 'module-post-grid', Asset::module_asset_path( 'modules/post-grid-bundle' ), $post_grid_deps );
	}

	/**
	 * Enqueue scripts for builder.
	 *
	 * @return void
	 */
	public function enqueue_builder_scripts() {
		if ( Divi::is_fb_enabled() ) {
			wp_enqueue_script( 'squad-vendor-typed' );
			wp_enqueue_script( 'squad-vendor-imagesloaded' );
			wp_enqueue_script( 'squad-vendor-lightgallery' );
			wp_enqueue_script( 'squad-vendor-scrolling-text' );
			wp_enqueue_script( 'squad-module-video-popup' );

			// Contact form default style.
			if ( class_exists( 'WPCF7' ) ) {
				wp_enqueue_style( 'contact-form-7' );
			}

			// WP Form default style.
			if ( function_exists( '\wpforms' ) && function_exists( '\wpforms_get_render_engine' ) && function_exists( '\wpforms_setting' ) && function_exists( '\wpforms_get_min_suffix' ) ) {
				$min         = \wpforms_get_min_suffix();
				$wp_forms_re = \wpforms_get_render_engine();
				$disable_css = absint( \wpforms_setting( 'disable-css', '1' ) );

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
				wp_enqueue_style( 'codemirror-monokai', $css_dir . 'monokai-theme.css', array(), $ver );
			}

			// Fluent Forms default style.
			if ( function_exists( '\wpFluentForm' ) && function_exists( '\fluentFormMix' ) ) {
				$fluent_form_public_css         = \fluentFormMix( 'css/fluent-forms-public.css' );
				$fluent_form_public_default_css = \fluentFormMix( 'css/fluentform-public-default.css' );

				if ( is_rtl() ) {
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