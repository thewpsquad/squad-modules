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
	 * Collect json data from html content in module.
	 *
	 * @param string $html_content Html raw content from module.
	 *
	 * @return array
	 */
	public static function collect_json_data( $html_content ) {
		// Collect data as unmanaged json string.
		$data = '[' . substr( trim( wp_strip_all_tags( $html_content ) ), 0, - 1 ) . ']';

		// Return json data as array for better management.
		return json_decode( $data, true );
	}
}
