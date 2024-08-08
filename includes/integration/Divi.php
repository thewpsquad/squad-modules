<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The Divi integration helper
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

/**
 * Define integration helper functionalities for Divi.
 *
 * @since      1.0.0
 * @package    squad-modules-for-divi
 */
class Divi {
	/**
	 * Returns boolean if the Divi Builder Plugin active in the current WordPress installation
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function is_any_divi_theme_installed() {
		$wp_installed_themes     = array_keys( wp_get_themes() );
		$is_divi_theme_installed = in_array( 'Divi', $wp_installed_themes, true );
		$is_divi_extra_installed = in_array( 'Extra', $wp_installed_themes, true );

		return ( $is_divi_theme_installed || $is_divi_extra_installed );
	}

	/**
	 * Returns boolean if the Divi Builder Plugin is installed in the current WordPress installation
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function is_divi_builder_plugin_installed() {
		return file_exists( WP_CONTENT_DIR . '/plugins/divi-builder/divi-builder.php' );
	}

	/**
	 * Returns boolean if the Dynamic CSS feature is turn on for Divi Builder in the current WordPress installation
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function is_dynamic_css_enable() {
		$divi_builder_dynamic_css = 'on';

		if ( wp_get_theme()->get( 'Name' ) === 'Divi' ) {
			$config_options = get_option( 'et_divi' );

			if ( isset( $config_options['divi_dynamic_css'] ) && 'false' === $config_options['divi_dynamic_css'] ) {
				$divi_builder_dynamic_css = 'off';
			}
		}

		if ( wp_get_theme()->get( 'Name' ) === 'Extra' ) {
			$config_options = get_option( 'et_extra' );

			if ( isset( $config_options['extra_dynamic_css'] ) && 'false' === $config_options['extra_dynamic_css'] ) {
				$divi_builder_dynamic_css = 'off';
			}
		}

		if ( self::is_divi_builder_plugin_active() ) {
			$config_options = get_option( 'et_pb_builder_options' );

			if ( isset( $config_options['performance_main_dynamic_css'] ) && 'false' === $config_options['performance_main_dynamic_css'] ) {
				$divi_builder_dynamic_css = 'off';
			}
		}

		return 'on' === $divi_builder_dynamic_css;
	}

	/**
	 * Returns boolean if the Divi Builder Plugin active in the current WordPress installation
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function is_divi_builder_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'divi-builder/divi-builder.php' );
	}

	/**
	 * Returns boolean if any Divi theme is installed active in the current WordPress installation
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public static function is_allowed_theme_activated() {
		return in_array( esc_html( get_template() ), self::modules_allowed_theme(), true );
	}

	/**
	 * Return the allowed theme list for Divi Module support
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function modules_allowed_theme() {
		return array( 'Divi', 'Extra' );
	}
}
