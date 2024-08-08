<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The plugin action links management class for the plugin dashboard at admin area.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

use function admin_url;
use function esc_html__;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Plugin Action Links class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Plugin_Action_Links {

	/**
	 * Get the instance of self-class
	 *
	 * @return string
	 */
	public function get_plugin_base() {
		return DISQ_PLUGIN_BASE;
	}

	/**
	 * Add some link to plugin action links.
	 *
	 * @param array $links Exists action links.
	 *
	 * @return array All action links for plugin.
	 */
	public function add_plugin_action_links( $links ) {
		$manage_modules_url = admin_url( 'admin.php?page=divi_squad_dashboard#/modules' );

		$action_links = array(
			sprintf( '<a href="%1$s" aria-label="%2$s">%2$s</a>', $manage_modules_url, esc_html__( 'Manage', 'squad-modules-for-divi' ) ),
		);

		return array_merge( $action_links, $links );
	}
}
