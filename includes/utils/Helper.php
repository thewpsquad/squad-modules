<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Utils;

/**
 * Helper class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Helper {

	/**
	 * Remove quotes from string.
	 *
	 * @param string $value The array value in string format.
	 *
	 * @return array The clean array.
	 */
	public static function remove_quotes( $value ) {
		return str_replace( '"', '', $value );
	}

	/**
	 * Collect shortcode tags from html content.
	 *
	 * @param string $content The HTML content.
	 *
	 * @return array The shortcode tags list.
	 */
	public static function collect_all_shortcode_tags( $content ) {
		if ( preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches ) ) {
			return array_unique( $matches );
		}

		return array();
	}

	/**
	 * Collect the shortcode list with tag and attributes from content.
	 *
	 * @param string $content The HTML content.
	 *
	 * @return array The shortcode list with tag and attributes.
	 */
	public static function collect_all_shortcodes( $content ) {
		$reg = get_shortcode_regex();
		if ( preg_match_all( '/' . $reg . '/', $content, $matches, PREG_SET_ORDER ) ) {
			return array_map(
				static function ( $v ) {
					return array_values( array_filter( $v ) );
				},
				$matches
			);
		}

		return array();
	}

	/**
	 * Show data in debug mode
	 *
	 * @param string|array|false|null $content     content for debugging and showing frontend.
	 * @param bool                    $is_any_time Show content in output anyway, by default is off.
	 *
	 * @return void
	 */
	public static function debug( $content, $is_any_time = false ) {
		if ( $is_any_time ) {
			self::debug_output( $content );
		} elseif ( ! wp_doing_ajax() ) {
			self::debug_output( $content );
		}
	}

	/**
	 * Show data in debug mode
	 *
	 * @param string|array|int|bool|object $content Content for debugging and showing frontend.
	 *
	 * @return void
	 */
	public static function debug_output( $content ) {
		echo '<pre>';
		print_r( $content, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		echo '</pre>';
	}

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
	 * Clean all array values.
	 *
	 * @param string $array_values The array values in string format.
	 *
	 * @return array The clean array
	 */
	public static function clean_array_values( $array_values ) {
		$result = array();

		if ( ! empty( $array_values ) ) {
			$key_value_pairs = explode( ' ', $array_values );
			if ( ! empty( $key_value_pairs ) ) {
				foreach ( $key_value_pairs as $key_value_pair ) {
					list( $key, $value ) = explode( '=', $key_value_pair );
					$result[ $key ]      = $value;
				}

				return array_filter( $result );
			}
		}

		return $result;
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
	 * @param string $on    The column number.
	 * @param int    $order The optional second parameter flags may be used to modify the sorting behavior using these values.
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
	 * Determines whether the plugin is active for the entire network.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins' directory.
	 *
	 * @return bool True if active for the network, otherwise false.
	 * @since 3.0.0
	 */
	public static function is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Determines whether a plugin is active.
	 *
	 * Only plugins installed in the plugins/ folder can be active.
	 *
	 * Plugins in the mu-plugins/ folder can't be "activated," so this function will
	 * return false for those plugins.
	 *
	 * For more information on this and similar theme functions, check out
	 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
	 * Conditional Tags} article in the Theme Developer Handbook.
	 *
	 * @param string $plugin Path to the plugin file relative to the plugins' directory.
	 *
	 * @return bool True, if in the active plugins list. False, not in the list.
	 * @since 2.5.0
	 */
	public static function is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || self::is_plugin_active_for_network( $plugin );
	}
}
