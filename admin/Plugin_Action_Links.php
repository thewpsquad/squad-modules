<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The plugin action links management class for the plugin dashboard at admin area.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

/**
 * Plugin Action Links class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Plugin_Action_Links {

	/** The instance
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the instance of self-class
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			add_filter( 'network_admin_plugin_action_links_' . DISQ_PLUGIN_BASE, array( self::$instance, 'add_plugin_action_links' ) );
			add_filter( 'plugin_action_links_' . DISQ_PLUGIN_BASE, array( self::$instance, 'add_plugin_action_links' ) );
		}

		return self::$instance;
	}

	/**
	 * Add some link to plugin action links.
	 *
	 * @param array $links Exists action links.
	 *
	 * @return array All action links for plugin.
	 */
	public function add_plugin_action_links( $links ) {
		$dashboard_url   = admin_url( 'admin.php?page=divi_squad_dashboard' );

		$action_links = array(
			sprintf( '<a href="%1$s" aria-label="%2$s">%2$s</a>', $dashboard_url, esc_html__( 'Settings', 'squad-modules-for-divi' ) ),
		);

		return array_merge( $action_links, $links );
	}
}
