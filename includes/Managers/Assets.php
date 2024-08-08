<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers;

use DiviSquad\Utils\Asset;
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

		$lottie_asset_deps = array_merge( $core_asset_deps, array( 'squad-vendor-lottie' ) );
		$typing_text_deps  = array_merge( $core_asset_deps, array( 'squad-vendor-typed' ) );
		$video_popup_deps  = array_merge( $core_asset_deps, array( 'magnific-popup' ) );

		// All module js.
		Asset::register_script( 'module-divider', Asset::module_asset_path( 'modules/divider-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-lottie', Asset::module_asset_path( 'modules/lottie-bundle' ), $lottie_asset_deps );
		Asset::register_script( 'module-typing-text', Asset::module_asset_path( 'modules/typing-text-bundle' ), $typing_text_deps );
		Asset::register_script( 'module-bais', Asset::module_asset_path( 'modules/bai-slider-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-accordion', Asset::module_asset_path( 'modules/accordion-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-gallery', Asset::module_asset_path( 'modules/gallery-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-scrolling-text', Asset::module_asset_path( 'modules/scrolling-text-bundle' ), $core_asset_deps );
		Asset::register_script( 'module-video-popup', Asset::module_asset_path( 'modules/video-popup-bundle' ), $video_popup_deps );
		Asset::register_script( 'module-post-grid', Asset::module_asset_path( 'modules/post-grid-bundle' ), $core_asset_deps );
	}

	/**
	 * Load requires asset extra in the visual builder by default.
	 *
	 * @param string $output Exist output.
	 *
	 * @return string
	 */
	public function wp_localize_script_data( $output ) {
		if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
			$numbers = array(
				'num_1' => esc_html__( '1', 'squad-modules-for-divi' ),
				'num_2' => esc_html__( '2', 'squad-modules-for-divi' ),
				'num_3' => esc_html__( '3', 'squad-modules-for-divi' ),
				'num_4' => esc_html__( '4', 'squad-modules-for-divi' ),
				'num_5' => esc_html__( '5', 'squad-modules-for-divi' ),
				'num_6' => esc_html__( '6', 'squad-modules-for-divi' ),
				'num_7' => esc_html__( '7', 'squad-modules-for-divi' ),
				'num_8' => esc_html__( '8', 'squad-modules-for-divi' ),
				'num_9' => esc_html__( '9', 'squad-modules-for-divi' ),
				'dot_3' => esc_html__( '...', 'squad-modules-for-divi' ),
			);

			// Set all localized data here.
			$localize = array(
				'l10n' => array_merge(
					$numbers,
					array(
						'home'              => esc_html__( 'Home', 'squad-modules-for-divi' ),
						'no_posts'          => esc_html__( 'Posts not available according to your criteria.', 'squad-modules-for-divi' ),
						'add_post_elements' => esc_html__( 'Add one or more post element(s).', 'squad-modules-for-divi' ),
						'add_ba_images'     => esc_html__( 'Add <strong>Before</strong> and <strong>After</strong> images from <strong>Image</strong> Toggle under the Content tab. You are see a preview.', 'squad-modules-for-divi' ),
						'add_business_days' => esc_html__( 'Add one or more business day(s).', 'squad-modules-for-divi' ),
						'field_is_required' => esc_html__( 'The field is required.', 'squad-modules-for-divi' ),
						'add_form'          => esc_html__( 'Please select a form.', 'squad-modules-for-divi' ),
						'form_not_found'    => esc_html__( 'Forms are not available.', 'squad-modules-for-divi' ),
						'field_error'       => esc_html__( 'One or more fields have an error. Please check and try again.', 'squad-modules-for-divi' ),
						'message_sent'      => esc_html__( 'Thank you for your message. It has been sent.', 'squad-modules-for-divi' ),
						'thanks_contact'    => esc_html__( 'Thanks for contacting us! We will be in touch with you shortly.', 'squad-modules-for-divi' ),
						'empty_map_place'   => esc_html__( 'Enter a place on google map.', 'squad-modules-for-divi' ),
						'scrolling_text'    => esc_html__( 'Scrolling Placeholder Text Here', 'squad-modules-for-divi' ),
					)
				),
			);

			$output .= sprintf( 'window.DISQBuilderLocalize = %1$s;', wp_json_encode( $localize ) );
		}

		return $output;
	}

	/**
	 * Enqueue scripts for builder.
	 *
	 * @return void
	 */
	public function enqueue_scripts_vb() {
		if ( function_exists( 'et_core_is_fb_enabled' ) && \et_core_is_fb_enabled() ) {
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
