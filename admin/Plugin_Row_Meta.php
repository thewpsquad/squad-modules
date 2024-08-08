<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The plugin row meta management class for the plugin dashboard at admin area.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

/**
 * Plugin Row Meta class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Plugin_Row_Meta {

	/**
	 * Get the instance of self-class
	 *
	 * @return string
	 */
	public static function get_plugin_base() {
		return DISQ_PLUGIN_BASE;
	}

	/**
	 * Filters the array of row meta for each/specific plugin in the Plugins list table.
	 * Appends additional links below each/specific plugin on the plugin page.
	 *
	 * @access  public
	 *
	 * @param array  $links            An array of the plugin's metadata.
	 * @param string $plugin_file_name Path to the plugin file.
	 *
	 * @return  array
	 */
	public function add_plugin_row_meta( $links, $plugin_file_name ) {
		if ( static::get_plugin_base() === $plugin_file_name ) {
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/squad-modules-for-divi/reviews/#new-post' ), esc_html__( 'Rate The Plugin', 'squad-modules-for-divi' ) );
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/squad-modules-for-divi/#new-post' ), esc_html__( 'Support', 'squad-modules-for-divi' ) );
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://squadmodules.com/?utm_campaign=wporg&utm_source=wp_plugin_dashboard&utm_medium=rowmeta' ), esc_html__( 'Documentation', 'squad-modules-for-divi' ) );
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://wordpress.org/plugins/squad-modules-for-divi/?utm_campaign=wporg&utm_source=wp_plugin_dashboard&utm_medium=rowmeta' ), esc_html__( 'FAQ', 'squad-modules-for-divi' ) );

			return $links;
		}

		return $links;
	}
}
