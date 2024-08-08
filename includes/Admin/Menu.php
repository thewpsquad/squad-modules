<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The admin menu management class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

use DiviSquad\Base\Factories\AdminMenu\MenuCore;
use DiviSquad\Utils\Polyfills\Str;
use function admin_url;
use function esc_html__;
use function load_template;

/**
 * Menu class
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 */
class Menu extends MenuCore {
	/**
	 * Details about the Main Menu.
	 *
	 * @return  array Details about the Main Menu.
	 */
	public function get_main_menu() {
		return array(
			'name'       => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
			'title'      => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
			'capability' => $this->get_permission(),
			'slug'       => $this->get_main_menu_slug(),
			'view'       => array( $this, 'get_template' ),
			'icon'       => 'dashicons-warning',
			'position'   => 101,
		);
	}

	/**
	 * Details about the Sub Menu.
	 *
	 * @return  array Details about the Sub Menu.
	 */
	public function get_sub_menus() {
		$version    = divi_squad()->get_version_dot();
		$menu_slug  = $this->get_main_menu_slug();
		$menu_base  = admin_url( 'admin.php?page=' . $menu_slug );
		$permission = $this->get_permission();

		return array(
			array(
				esc_html__( 'Dashboard', 'squad-modules-for-divi' ),
				sprintf( '%s#/', $menu_base ),
				$permission,
			),
			array(
				esc_html__( 'Modules', 'squad-modules-for-divi' ),
				sprintf( '%s#/modules', $menu_base ),
				$permission,
			),
			array(
				esc_html__( 'Extensions', 'squad-modules-for-divi' ),
				sprintf( '%s#/extensions', $menu_base ),
				$permission,
			),
			array(
				esc_html__( "What's New", 'squad-modules-for-divi' ),
				sprintf( '%1$s#/whats-new/%2$s', $menu_base, $version ),
				$permission,
			),
		);
	}

	/**
	 * Load template file for admin pages.
	 *
	 * @return  void
	 */
	public function get_template() {
		$template_path = sprintf( '%1$s/admin/dashboard.php', DIVI_SQUAD_TEMPLATES_PATH );

		if ( file_exists( $template_path ) ) {
			load_template( $template_path );
		}
	}

	/**
	 * Add the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 */
	public function get_body_classes() {
		global $current_screen;

		// Set default classes.
		$classes = '';

		// Collect the version number.
		$version = divi_squad()->get_version();
		$version = str_replace( '.', '_', $version );

		// Add divi squad version class in the body.
		$classes .= sprintf( ' divi_squad_free_v%s', $version );

		// Add specific class to detect the current page.
		if ( isset( $current_screen ) && Str::contains( $current_screen->id, 'divi_squad' ) ) {
			$classes .= ' divi_squad_plugin_page';
		}

		return $classes;
	}
}