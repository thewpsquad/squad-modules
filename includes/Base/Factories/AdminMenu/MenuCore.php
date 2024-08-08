<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories\AdminMenu;

/**
 * Abstract class representing the Menu.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class MenuCore implements MenuInterface {

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
