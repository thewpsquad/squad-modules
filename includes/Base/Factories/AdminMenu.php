<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories;

use DiviSquad\Utils\Singleton;

final class AdminMenu {

	use Singleton;

	/**
	 * Save an indicator for save state.
	 *
	 * @var bool
	 */
	private static $is_menu_registered = false;

	/**
	 * Save an indicator for save state.
	 *
	 * @var bool
	 */
	private static $is_body_classes_added = false;

	/**
	 * Store all menus
	 *
	 * @var AdminMenu\MenuInterface[]
	 */
	private static $menus = array();

	private function __construct() {
		// Load all main menus and submenus for admin.
		add_action( 'admin_menu', array( $this, 'create_admin_menus' ) );
		add_filter( 'admin_body_class', array( $this, 'add_body_classes' ) );
	}

	public function add( $menu_class ) {
		$menu = new $menu_class();

		if ( ! $menu instanceof AdminMenu\MenuInterface ) {
			return false;
		}

		self::$menus[] = $menu;

		return true;
	}

	/**
	 * Enqueue scripts and styles files in the WordPress admin area.
	 *
	 * @return void
	 */
	public function create_admin_menus() {
		global $submenu;

		if ( ! empty( self::$menus ) ) {
			/**
			 * Store of all Menus
			 *
			 * @var AdminMenu\MenuInterface[] $menus
			 */
			foreach ( self::$menus as $menu ) {
				// Collect all options for the main menu.
				$main_menu = $menu->get_main_menu();
				if ( count( $main_menu ) > 0 ) {
					// Register the main menu.
					add_menu_page(
						$main_menu['name'],
						$main_menu['title'],
						$main_menu['capability'],
						$main_menu['slug'],
						$main_menu['view'],
						$main_menu['icon'],
						$main_menu['position']
					);
				}

				$all_submenus = $menu->get_sub_menus();
				if ( count( $all_submenus ) > 0 ) {
					$main_menu_slug = $menu->get_main_menu_slug();
					if ( empty( $submenu[ $main_menu_slug ] ) ) {
						$submenu[ $main_menu_slug ] = array(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					}

					// Update all submenus to the global submenu list.
					foreach ( $all_submenus as $current_submenu ) {
						list( $name, $url, $permission ) = $current_submenu;
						$submenu[ $main_menu_slug ][]    = array( $name, $permission, $url ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					}
				}
			}
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
	public function add_body_classes( $classes ) {
		if ( ! empty( self::$menus ) ) {
			/**
			 * Store of all Menus
			 *
			 * @var AdminMenu\MenuInterface[] $menus
			 */
			foreach ( self::$menus as $menu ) {
				$classes .= ' ' . $menu->get_body_classes();
			}
		}

		return $classes;
	}

	/**
	 * Registered all menus.
	 *
	 * @return array
	 */
	public function get_registered_submenus() {
		global $submenu;

		// Set initial value.
		$submenus = array();

		if ( ! empty( self::$menus ) ) {
			foreach ( self::$menus as $menu ) {
				$main_menu_slug = $menu->get_main_menu_slug();
				if ( ! empty( $submenu[ $main_menu_slug ] ) ) {
					foreach ( $submenu[ $main_menu_slug ] as $current_submenu ) {
						if ( ! in_array( $current_submenu, $submenus, true ) ) {
							$submenus[] = $current_submenu;
						}
					}
				}
			}
		}

		return $submenus;
	}
}
