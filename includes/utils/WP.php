<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * WP helper.
 *
 * @since       1.2.2
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils;

use WP_Plugins_List_Table;

/**
 * WP Helper class.
 *
 * @since       1.2.2
 * @package     squad-modules-for-divi
 */
class WP {
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
		if ( ! \is_multisite() ) {
			return false;
		}

		$plugins = \get_site_option( 'active_sitewide_plugins' );
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
		return in_array( $plugin, (array) \get_option( 'active_plugins', array() ), true ) || self::is_plugin_active_for_network( $plugin );
	}

	/**
	 * Get the active plugins name and versions.
	 *
	 * @return array
	 */
	public static function get_active_plugins() {
		$active_plugins = self::get_active_plugins_info();
		$plugins_list   = array();

		foreach ( $active_plugins as $active_plugin ) {
			$plugins_list[] = array(
				'name'    => $active_plugin['Name'],
				'slug'    => $active_plugin['Slug'],
				'version' => $active_plugin['Version'],
			);
		}

		return $plugins_list;
	}

	/**
	 * Get the active plugins' information.
	 *
	 * @return array|WP_Plugins_List_Table
	 */
	public static function get_active_plugins_info() {
		$all_plugins        = \get_plugins();
		$active_plugins     = \get_option( 'active_plugins', array() );
		$all_active_plugins = array(); // prepare array for all Active plugins.
		foreach ( $all_plugins as $plugin => $plugin_info ) {
			if ( function_exists( '\is_plugin_active' ) && \is_plugin_active( $plugin ) || in_array( $plugin, $active_plugins, true ) ) {
				$plugin_info['Slug']  = $plugin;
				$all_active_plugins[] = $plugin_info;
			}
		}

		return $all_active_plugins;
	}

	/**
	 * Sets translated strings for a script.
	 *
	 * Works only if the script has already been registered.
	 *
	 * @param string $handle Script handle the textdomain will be attached to.
	 * @param string $domain Optional. Text domain. Default 'default'.
	 * @param string $path   Optional. The full file path to the directory containing translation files.
	 *
	 * @return bool True if the text domain was successfully localized, false otherwise.
	 */
	public static function set_script_translations( $handle, $domain = 'default', $path = '' ) {
		if ( function_exists( '\wp_set_script_translations' ) ) {
			return \wp_set_script_translations( $handle, $domain, $path );
		}

		return false;
	}

	/**
	 * Localizes a script.
	 *
	 * Works only if the script has already been registered.
	 *
	 * @param string $handle      Script handle the data will be attached to.
	 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
	 * @param array  $l10n        The data itself. The data can be either a single or multi-dimensional array.
	 *
	 * @return bool True if the script was successfully localized, false otherwise.
	 */
	public static function localize_script( $handle, $object_name, $l10n ) {
		return \wp_localize_script( $handle, $object_name, $l10n );
	}
}