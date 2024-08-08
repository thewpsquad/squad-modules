<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Module helper.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Module class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Module {

	/**
	 * Decode json data from properties in module.
	 *
	 * @param string $html_content json data raw content from module.
	 *
	 * @return array
	 */
	public static function decode_json_data( $html_content ) {
		// Collect data as unmanaged json string.
		$data = stripslashes( html_entity_decode( $html_content ) );

		// Return json data as array for better management.
		return json_decode( $data, true );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	public static function collect_raw_props( $content ) {
		return wp_strip_all_tags( $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	public static function json_format_raw_props( $content ) {
		return sprintf( '[%s]', $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return array
	 */
	public static function collect_child_json_props( $content ) {
		$raw_props   = static::json_format_raw_props( $content );
		$clean_props = str_replace( '},]', '}]', $raw_props );
		$child_props = json_decode( $clean_props, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				sprintf(
				/* translators: 1: Error message. */
					esc_html__( __( 'Error when decoding child props: %1$s', 'squad-modules-for-divi' ), 'squad-modules-for-divi' ), // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					json_last_error_msg() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			);

			return array();
		}

		return $child_props;
	}
}
