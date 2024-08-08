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

use function DiviSquad\divi_squad;

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
	protected static $instance;

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
			array(
				'name'       => esc_html__( 'Extensions', 'squad-modules-for-divi' ),
				'capability' => $this->admin_management_permission(),
				'slug'       => 'divi_squad_extensions',
				'parent'     => 'divi_squad_dashboard',
				'view'       => array( $this, 'get_template' ),
			),
		);

		// phpcs:disable
		// if ( ! is_the_pro_plugin_active() ) {
		// $default_menus[] = array(
		// 'name'       => esc_html__( 'Go Premium', 'squad-modules-for-divi' ),
		// 'capability' => $this->admin_management_permission(),
		// 'slug'       => 'divi_squad_go_premium',
		// 'parent'     => 'divi_squad_dashboard',
		// 'view'       => array( $this, 'get_template' ),
		// );
		// }
		// phpcs:enable

		return apply_filters( 'divi_squad_admin_sub_menu', $default_menus );
	}

	/**
	 * Load template file for admin pages.
	 *
	 * @return  void
	 */
	public static function get_template() {
		print ( '<style>
		#squad-modules-app {
		  z-index: -1;
		}
		 /**===== Squad Modules App (Preloader) =====*/
		#squad-modules-app-loader.square {
		  display: block;
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  height: 50px;
		  width: 50px;
		  margin: -25px 0 0 -25px;
		}
		
		#squad-modules-app-loader.square span {
		  width: 16px;
		  height: 16px;
		  background-color: #5E2EFF;
		  display: inline-block;
		  -webkit-animation: app-loader-square 1.7s infinite ease-in-out both;
		          animation: app-loader-square 1.7s infinite ease-in-out both;
		}
		
		#squad-modules-app-loader.square span:nth-child(1) {
		  left: 0;
		  -webkit-animation-delay: 0.1s;
		          animation-delay: 0.1s;
		}
		
		#squad-modules-app-loader.square span:nth-child(2) {
		  left: 15px;
		  -webkit-animation-delay: 0.6s;
		          animation-delay: 0.6s;
		}
		
		#squad-modules-app-loader.square span:nth-child(3) {
		  left: 30px;
		  -webkit-animation-delay: 1.1s;
		          animation-delay: 1.1s;
		}
		
		#squad-modules-app-loader.square span:nth-child(4) {
		  left: 45px;
		  -webkit-animation-delay: 1.5s;
		          animation-delay: 1.5s;
		}
		
		@keyframes app-loader-square {
		  0% {
		    	-webkit-transform: scale(0);
		         transform: scale(0);
		    	opacity: 0;
		  }
		  50% {
		    	-webkit-transform: scale(1);
		        transform: scale(1);
		    	opacity: 1;
		  }
		  100% {
		    	-webkit-transform: rotate(60deg);
		        transform: rotate(60deg);
		    	opacity: .5;
		  }
		}
		@-webkit-keyframes app-loader-square {
		  0% {
		    -webkit-transform: scale(0);
		            transform: scale(0);
		    opacity: 0;
		  }
		  50% {
		    -webkit-transform: scale(1);
		            transform: scale(1);
		    opacity: 1;
		  }
		  100% {
		    -webkit-transform: rotate(60deg);
		            transform: rotate(60deg);
		    opacity: .5;
		  }
		}
		/** END of square */ 
		</style>' );
		print ( '<div id="squad-modules-app-loader" class="square"> <span></span> <span></span> <span></span> <span></span> </div>' );

		// show the admin page content.
		print ( '<section id="squad-modules-app"></section>' );
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
		if ( isset( $current_screen ) && str_contains( $current_screen->id, 'divi_squad' ) ) {
			$classes .= ' divi_squad_plugin_page';
		}

		return $classes;
	}
}
