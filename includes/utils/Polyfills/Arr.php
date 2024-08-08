<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Array Helper helper.
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils\Polyfills;

/**
 * Array Helper class.
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 */
class Arr {

	/**
	 * Polyfill for array_key_first() function added in PHP 7.3.
	 *
	 * Get the first key of the given array without affecting the internal array pointer.
	 *
	 * @param array $array An array.
	 * @return string|int|null The first key of array if the array is not empty; `null` otherwise.
	 */
	public static function key_first( array $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		if ( function_exists( '\array_key_first' ) ) {
			return \array_key_first( $array );
		}

		foreach ( $array as $key => $value ) {
			return $key;
		}

		return null;
	}

	/**
	 * Polyfill for `array_key_last()` function added in PHP 7.3.
	 *
	 * Get the last key of the given array without affecting the internal array pointer.
	 *
	 * @param array $array An array.
	 * @return string|int|null The last key of array if the array is not empty; `null` otherwise.
	 */
	public static function key_last( array $array ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.arrayFound
		if ( function_exists( '\array_key_last' ) ) {
			return array_key_last( $array );
		}

		if ( empty( $array ) ) {
			return null;
		}

		end( $array );

		return key( $array );
	}
}
