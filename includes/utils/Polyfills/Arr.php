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
	 * @param array $a An array.
	 *
	 * @return string|int|null The first key of array if the array is not empty; `null` otherwise.
	 */
	public static function key_first( array $a ) {
		if ( function_exists( '\array_key_first' ) ) {
			return \array_key_first( $a );
		}

		foreach ( $a as $key => $value ) {
			return $key;
		}

		return null;
	}

	/**
	 * Polyfill for `array_key_last()` function added in PHP 7.3.
	 *
	 * Get the last key of the given array without affecting the internal array pointer.
	 *
	 * @param array $a An array.
	 *
	 * @return string|int|null The last key of array if the array is not empty; `null` otherwise.
	 */
	public static function key_last( array $a ) {
		if ( function_exists( '\array_key_last' ) ) {
			return array_key_last( $a );
		}

		if ( empty( $a ) ) {
			return null;
		}

		end( $a );

		return key( $a );
	}


	/**
	 * Check current array data is a list
	 *
	 * @param array $a The array data
	 *
	 * @return bool
	 */
	public static function is_list( array $a ) {
		if ( function_exists( '\array_is_list' ) ) {
			return array_key_last( $a );
		}

		if ( array() === $a || array_values( $a ) === $a ) {
			return true;
		}

		$next_key = - 1;

		foreach ( $a as $k => $v ) {
			if ( ++$next_key !== $k ) {
				return false;
			}
		}

		return true;
	}
}
