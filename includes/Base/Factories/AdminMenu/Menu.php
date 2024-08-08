<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Abstract class representing the Menu.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */

namespace DiviSquad\Base\Factories\AdminMenu;

/**
 * Abstract class representing the Menu.
 *
 * @package DiviSquad
 * @since   2.0.0
 */
abstract class Menu implements MenuInterface {

	/**
	 * The permission for menu management.
	 *
	 * @return string
	 */
	public function get_permission() {
		return 'manage_options';
	}

	/**
	 * Slug of the main menu.
	 *
	 * @return  string
	 */
	public function get_main_menu_slug() {
		// Get admin menu slug.
		return divi_squad()->get_admin_menu_slug();
	}

	/**
	 * Details about the Main Menu.
	 *
	 * @return  array Details about the Main Menu.
	 */
	public function get_main_menu() {
		return array();
	}

	/**
	 * Details about the Sub Menu.
	 *
	 * @return  array Details about the Sub Menu.
	 */
	public function get_sub_menus() {
		return array();
	}

	/**
	 * Load template file for admin pages.
	 *
	 * @return  void
	 */
	public function get_template() {}

	/**
	 * Add the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 */
	public function get_body_classes() {
		return '';
	}
}
