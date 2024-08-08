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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

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

	/**
	 * Simple function to sort an array by a specific key. Maintains index association.
	 *
	 * <code>
	 * print_r(array_sort($people, 'age', SORT_DESC)); // Sort by oldest first
	 * print_r(array_sort($people, 'surname', SORT_ASC)); // Sort by surname
	 * </code>
	 *
	 * @param array  $array_data The input array.
	 * @param string $on         The column number.
	 * @param int    $order      The optional second parameter flags may be used to modify the sorting behavior using these values.
	 *
	 * @return array
	 */
	public static function array_sort( $array_data, $on, $order = SORT_ASC ) {
		$new_array      = array();
		$sortable_array = array();

		/*
		 *
		 * Sorting type flags:
		 * <ol>
		 * <li> <strong> SORT_ASC </strong> - sort in ascending order. </li>
		 * <li> <strong> SORT_DESC </strong> - sort in descending order.. </li>
		 * <li> <strong> SORT_REGULAR </strong> - compare items normally; the details are described in the comparison operators section. </li>
		 * <li> <strong> SORT_NUMERIC </strong> - compare items numerically. </li>
		 * <li> <strong> SORT_STRING </strong> - compare items as strings. </li>
		 * <li> <strong> SORT_LOCALE_STRING </strong> - compare items as strings, based on the current locale. It uses the locale, which can be changed using setlocale(). </li>
		 * <li> <strong> SORT_NATURAL </strong> - compare items as strings using "natural ordering" like natsort(). </li>
		 * <li> <strong> SORT_FLAG_CASE </strong> - can be combined (bitwise OR) with SORT_STRING or SORT_NATURAL to sort strings case-insensitively. </li>
		 * </ol>
		 */

		if ( count( $array_data ) > 0 ) {
			foreach ( $array_data as $k => $v ) {
				if ( is_array( $v ) ) {
					foreach ( $v as $k2 => $v2 ) {
						if ( $k2 === $on ) {
							$sortable_array[ $k ] = $v2;
						}
					}
				} else {
					$sortable_array[ $k ] = $v;
				}
			}

			switch ( $order ) {
				case SORT_ASC:
					asort( $sortable_array );
					break;
				case SORT_DESC:
					arsort( $sortable_array );
					break;
			}

			foreach ( $sortable_array as $k => $v ) {
				$new_array[ $k ] = $array_data[ $k ];
			}
		}

		return $new_array;
	}

	/**
	 * Get Second by days.
	 *
	 * @param int $days Days Number.
	 *
	 * @return int
	 */
	public static function get_second( $days ) {
		return $days * 24 * 60 * 60;
	}
}
