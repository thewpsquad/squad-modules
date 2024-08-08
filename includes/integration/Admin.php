<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The WordPress connection class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

/**
 * Admin Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Admin {

	/**
	 * Get the instance of the current class.
	 *
	 * @return void
	 */
	public static function load() {
		$admin_menu   = new \DiviSquad\Admin\Menu();
		$admin_asset  = new \DiviSquad\Admin\Assets();
		$action_links = new \DiviSquad\Admin\Plugin_Action_Links();
		$row_meta     = new \DiviSquad\Admin\Plugin_Row_Meta();

		// Load all main menus and submenus for admin interface.
		add_action( 'admin_menu', array( $admin_menu, 'admin_menu_create' ) );
		add_filter( 'admin_body_class', array( $admin_menu, 'admin_classes' ) );

		// Load all assets for admin interface and another asset.
		self::register_admin_scripts( $admin_asset );

		// Include all actions links for the plugin.
		self::register_plugin_action_links( $action_links );

		// Include all row metas for the plugin.
		self::register_plugin_row_meta( $row_meta );
	}

	/**
	 * Get the dynamic portion of the hook name, `$hook_suffix`, refers to the hook suffix
	 * for the admin page.
	 *
	 * @return string
	 * @since 1.2.0
	 */
	protected static function get_plugin_hook_suffix() {
		return 'toplevel_page_divi_squad_dashboard';
	}

	/**
	 * Fires when enqueuing scripts for all admin pages.
	 *
	 * @param \DiviSquad\Admin\Assets $admin_asset The instance of Admin asset class.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	protected static function register_admin_scripts( $admin_asset ) {
		add_action( 'admin_enqueue_scripts', array( $admin_asset, 'wp_hook_enqueue_plugin_admin_asset' ) );
		add_action( 'admin_enqueue_scripts', array( $admin_asset, 'wp_hook_enqueue_extra_admin_asset' ) );
	}

	/**
	 * Include all actions links for the plugin.
	 *
	 * @param \DiviSquad\Admin\Plugin_Action_Links $action_links The instance of Plugin action links class.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	protected static function register_plugin_action_links( $action_links ) {
		$plugin_base    = $action_links->get_plugin_base();
		$callback_array = array( $action_links, 'add_plugin_action_links' );
		add_filter( 'network_admin_plugin_action_links_' . $plugin_base, $callback_array );
		add_filter( 'plugin_action_links_' . $plugin_base, $callback_array );
	}

	/**
	 * Include all row metas for the plugin.
	 *
	 * @param \DiviSquad\Admin\Plugin_Row_Meta $row_meta The instance of the Plugin row meta.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	protected static function register_plugin_row_meta( $row_meta ) {
		add_filter( 'plugin_row_meta', array( $row_meta, 'add_plugin_row_meta' ), 10, 2 );
	}
}
