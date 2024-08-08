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

/**
 * WP Helper class.
 *
 * @since       1.2.2
 * @package     squad-modules-for-divi
 */
class WP {

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
	 * @return array | \WP_Plugins_List_Table
	 */
	public static function get_active_plugins_info() {
		$all_plugins        = \get_plugins();
		$active_plugins     = \get_option( 'active_plugins', array() );
		$all_active_plugins = array(); //prepare array for all Active plugins
		foreach ( $all_plugins as $plugin => $plugin_info ) {
			if ( function_exists( '\is_plugin_active' ) && \is_plugin_active( $plugin ) || in_array( $plugin, $active_plugins, true ) ) {
				$plugin_info['Slug']  = $plugin;
				$all_active_plugins[] = $plugin_info;
			}
		}

		return $all_active_plugins;
	}
}
