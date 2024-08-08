<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Generic helper.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils;

use function get_shortcode_regex;
use function wp_doing_ajax;

/**
 * Helper class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Helper {

	/**
	 * Fix slash issue for Windows os
	 *
	 * @param string $path Full path for fixing.
	 *
	 * @return string
	 */
	public static function fix_slash( $path ) {
		// define slash into individual variables.
		$backslash = '\\';
		$slash     = '/';

		// return fixed string.
		if ( PHP_OS === 'WINNT' ) {
			return str_replace( $slash, $backslash, $path );
		}

		return $path;
	}

	/**
	 * Implode array like html attributes.
	 *
	 * @param array $array_data The associate array data.
	 *
	 * @return string
	 */
	public static function implode_assoc_array( $array_data ) {
		array_walk(
			$array_data,
			static function ( &$item, $key ) {
				$item = sprintf( '%s="%s"', $key, $item );
			}
		);

		return implode( '  ', $array_data );
	}
}
