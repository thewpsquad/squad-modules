<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Generic helper class for utility.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Utils;

use function divi_squad;
use function get_shortcode_regex;

/**
 * Helper class.
 *
 * @package DiviSquad
 * @since   1.0.0
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
	 * Verify the current screen is a squad page or not.
	 *
	 * @param string $page_id The page id.
	 *
	 * @return bool
	 */
	public static function is_squad_page( $page_id = '' ) {
		$plugin_slug = divi_squad()->get_admin_menu_slug();

		// Get the current screen id if not provided.
		if ( empty( $page_id ) ) {
			if ( is_admin() || ! function_exists( '\get_current_screen' ) ) {
				return false;
			}

			$screen = \get_current_screen();

			return $screen instanceof \WP_Screen && strpos( $screen->id, $plugin_slug ) !== false;
		}

		return strpos( $page_id, $plugin_slug ) !== false;
	}
}
