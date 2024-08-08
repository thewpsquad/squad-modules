<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The admin menu management class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

/**
 * Menu class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Menu {

	/** The instance.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the instance of self-class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			add_action( 'admin_menu', array( self::$instance, 'admin_menu_create' ) );
			add_filter( 'admin_body_class', array( self::$instance, 'admin_classes' ) );
		}

		return self::$instance;
	}

	/**
	 * Enqueue scripts and styles files in the WordPress admin area.
	 */
	public function admin_menu_create() {
		// Check permission and create menu pages.
		if ( current_user_can( $this->admin_management_permission() ) ) :
			// main page.
			$main_menus = $this->get_admin_main_menu();
			foreach ( $main_menus as $main_menu ) {
				add_menu_page(
					$main_menu['name'],
					$main_menu['name'],
					$main_menu['capability'],
					$main_menu['slug'],
					$main_menu['view'],
					$main_menu['icon'],
					$main_menu['position']
				);
			}

			// Sub pages.
			$sub_menus = $this->get_admin_sub_menu();
			foreach ( $sub_menus as $sub_menu ) {
				add_submenu_page(
					$sub_menu['parent'],
					$sub_menu['name'],
					$sub_menu['name'],
					$sub_menu['capability'],
					$sub_menu['slug'],
					$sub_menu['view']
				);
			}
		endif;
	}

	/**
	 * Check permission for extension management.
	 *
	 * @return string
	 */
	public function admin_management_permission() {
		return is_multisite() ? 'manage_network_options' : 'manage_options';
	}

	/**
	 * Details about the Main Menu.
	 *
	 * @return  array Details about the Main Menu.
	 */
	public function get_admin_main_menu() {
		$default_menus = array(
			array(
				'name'       => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
				'capability' => $this->admin_management_permission(),
				'slug'       => 'divi_squad_dashboard',
				'view'       => null,
				'icon'       => 'dashicons-warning',
				'position'   => 101,
			),
		);

		return apply_filters( 'divi_squad_admin_main_menu', $default_menus );
	}

	/**
	 * List of Sub Menu.
	 *
	 * @return  array Details about the sub menus.
	 */
	public function get_admin_sub_menu() {
		$default_menus = array(
			array(
				'name'       => esc_html__( 'Dashboard', 'squad-modules-for-divi' ),
				'capability' => $this->admin_management_permission(),
				'slug'       => 'divi_squad_dashboard',
				'parent'     => 'divi_squad_dashboard',
				'view'       => array( $this, 'get_template' ),
			),
			array(
				'name'       => esc_html__( 'Modules', 'squad-modules-for-divi' ),
				'capability' => $this->admin_management_permission(),
				'slug'       => 'divi_squad_modules',
				'parent'     => 'divi_squad_dashboard',
				'view'       => array( $this, 'get_template' ),
			),
		);

		return apply_filters( 'divi_squad_admin_sub_menu', $default_menus );
	}

	/**
	 * Load template file for admin pages.
	 *
	 * @return  void
	 */
	public static function get_template() {
		printf( '<section id="squad-modules-app"></section>' );
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

		if ( isset( $current_screen ) && str_contains( $current_screen->id, 'divi_squad' ) ) {
			$classes .= ' divi_squad_plugin_page';
		}

		return $classes;
	}

}
