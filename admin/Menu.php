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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Utils\Polyfills\Str;
use function DiviSquad\divi_squad;

/**
 * Menu class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Menu {

	/**
	 * Enqueue scripts and styles files in the WordPress admin area.
	 */
	public function admin_menu_create() {
		global $submenu;

		// Check permission and create menu pages.
		if ( current_user_can( $this->admin_management_permission() ) ) {
			// main page.
			$main_menu = $this->get_admin_main_menu();
			add_menu_page(
				$main_menu['name'],
				$main_menu['name'],
				$main_menu['capability'],
				$main_menu['slug'],
				$main_menu['view'],
				$main_menu['icon'],
				$main_menu['position']
			);

			// Sub pages.
			$all_submenus   = $this->get_admin_sub_menu();
			$main_menu_slug = $this->get_admin_main_menu_slug();
			if ( empty( $submenu[ $main_menu_slug ] ) ) {
				$submenu[$main_menu_slug] = array(); // phpcs:ignore.
			}

			// Update all submenus to the global submenu list.
			array_push(
				$submenu[ $main_menu_slug ],
				...$all_submenus
			);
		}
	}

	/**
	 * Check permission for extension management.
	 *
	 * @return string
	 */
	public function admin_management_permission() {
		return 'manage_options';
	}

	/**
	 * Default slug for admin main menu.
	 *
	 * @return string
	 */
	public function get_admin_main_menu_slug() {
		return apply_filters( 'divi_squad_admin_main_menu_slug', 'divi_squad_dashboard' );
	}

	/**
	 * Details about the Main Menu.
	 *
	 * @return  array Details about the Main Menu.
	 */
	public function get_admin_main_menu() {
		$default_menu = array(
			'name'       => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
			'capability' => $this->admin_management_permission(),
			'slug'       => $this->get_admin_main_menu_slug(),
			'view'       => array( $this, 'get_template' ),
			'icon'       => 'dashicons-warning',
			'position'   => 101,
		);

		return apply_filters( 'divi_squad_admin_main_menu', $default_menu );
	}

	/**
	 * List of Sub Menu.
	 *
	 * @return  array Details about the submenus.
	 */
	public function get_admin_sub_menu() {
		$base          = admin_url( 'admin.php?page=' . $this->get_admin_main_menu_slug() );
		$default_menus = array(
			array(
				esc_html__( 'Dashboard', 'squad-modules-for-divi' ),
				$this->admin_management_permission(),
				$base . '#/',
			),
			array(
				esc_html__( 'Modules', 'squad-modules-for-divi' ),
				$this->admin_management_permission(),
				$base . '#/modules',
			),
			array(
				esc_html__( 'Extensions', 'squad-modules-for-divi' ),
				$this->admin_management_permission(),
				$base . '#/extensions',
			),
			array(
				esc_html__( "What's New", 'squad-modules-for-divi' ),
				$this->admin_management_permission(),
				$base . '#/whats-new',
			),
		);

		return apply_filters( 'divi_squad_admin_sub_menu', $default_menus );
	}

	/**
	 * Load template file for admin pages.
	 *
	 * @return  void
	 */
	public function get_template() {
		if ( file_exists( sprintf( '%1$s/templates/admin-view.php', DISQ_DIR_PATH ) ) ) {
			load_template( sprintf( '%1$s/templates/admin-view.php', DISQ_DIR_PATH ) );
		}
	}

	/**
	 * Filters the CSS classes for the body tag in the admin.
	 *
	 * @param string $classes Space-separated list of CSS classes.
	 *
	 * @return string
	 * @since 1.0.4
	 */
	public function admin_classes( $classes ) {
		global $current_screen;

		// Add divi squad version class in the body.
		$version  = divi_squad()->get_version();
		$version  = str_replace( '.', '_', $version );
		$classes .= sprintf( ' divi_squad_free_v%s', $version );

		// Add specific class to detect the current page.
		if ( isset( $current_screen ) && Str::contains( $current_screen->id, 'divi_squad' ) ) {
			$classes .= ' divi_squad_plugin_page';
		}

		return $classes;
	}
}
