<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * String Helper helper.
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils\Polyfills;

/**
 * String Helper class.
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 */
class Str {

	/**
	 * Polyfill for `str_starts_with()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if
	 * the haystack begins with a needle.
	 *
	 * @param String $haystack The string to search in.
	 * @param String $needle   The substring to search for in the `$haystack`.
	 *
	 * @return bool True if `$haystack` starts with `$needle`, otherwise false.
	 */
	public static function starts_with( $haystack, $needle ) {
		if ( function_exists( '\str_starts_with' ) ) {
			return \str_starts_with( $haystack, $needle );
		}

		if ( '' === $needle ) {
			return true;
		}

		return 0 === strpos( $haystack, $needle );
	}

	/**
	 * Polyfill for `str_ends_with()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if
	 * the haystack ends with a needle.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the `$haystack`.
	 *
	 * @return bool True if `$haystack` ends with `$needle`, otherwise false.
	 */
	public static function ends_with( $haystack, $needle ) {
		if ( function_exists( '\str_ends_with' ) ) {
			return \str_ends_with( $haystack, $needle );
		}

		if ( '' === $haystack ) {
			return '' === $needle;
		}

		$len = strlen( $needle );

		return substr( $haystack, - $len, $len ) === $needle;
	}

	/**
	 * Polyfill for `str_contains()` function added in PHP 8.0.
	 *
	 * Performs a case-sensitive check indicating if a needle is contained in a haystack.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the `$haystack`.
	 *
	 * @return bool True if `$needle` is in `$haystack`, otherwise false.
	 */
	public static function contains( $haystack, $needle ) {
		if ( function_exists( '\str_contains' ) ) {
			return \str_contains( $haystack, $needle );
		}

		if ( '' === $needle ) {
			return true;
		}

		return false !== strpos( $haystack, $needle );
	}

	/**
	 * Polyfill for `str_word_count()` function.
	 *
	 * Performs a case-sensitive check indicating if a needle is contained in a haystack.
	 *
	 * @param string  $string_content The string.
	 * @param int     $format         Specify the return value of this function, options are: 0, 1, 2.
	 * @param ?string $characters     The substring to search for in the `$haystack`.
	 *
	 * @return array|int True if `$needle` is in `$haystack`, otherwise false.
	 */
	public static function word_count( $string_content, $format = 0, $characters = null ) {
		/*
		 * The current supported values are:
		 * <ul>
		 *  <li>0: returns the number of words found</li>
		 *  <li>1: returns an array containing all the words found inside the string</li>
		 *  <li>2: returns an associative array, where the key is the numeric position of the word inside the string and the value is the actual word itself</li>
		 * </ul>
		 */

		if ( function_exists( '\str_word_count' ) ) {
			return \str_word_count( $string_content, $format, $characters );
		}

		// Split string into words.
		$break_words = preg_split( '~[^\p{L}\p{N}\']+~u', $string_content );

		return 0 === $format ? count( $break_words ) : $break_words;
	}
}
