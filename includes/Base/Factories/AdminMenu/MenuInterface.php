<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories\AdminMenu;

/**
 * Interface for the Menu class.
 */
interface MenuInterface {

	/**
	 * The permission for menu management.
	 *
	 * @return string
	 */
	public function get_permission();

	/**
	 * Slug of the main menu.
	 *
	 * @return  string
	 */
	public function get_main_menu_slug();

	/**
	 * Get details about the Main Menu.
	 *
	 * @return array Details about the Main Menu.
	 */
	public function get_main_menu();

	/**
	 * Get details about the Sub Menu.
	 *
	 * @return array Details about the Sub Menu.
	 */
	public function get_sub_menus();

	/**
	 * Load template file for admin pages.
	 *
	 * @return void
	 */
	public function get_template();

	/**
	 * Add the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 */
	public function get_body_classes();
}

