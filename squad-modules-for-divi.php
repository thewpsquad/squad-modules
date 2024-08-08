<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules Lite
 *
 * @package     divi-squad
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 *
 * @wordpress-plugin
 * Plugin Name:         Squad Modules Lite
 * Plugin URI:          https://squadmodules.com/
 * Description:         The Advanced Divi plugin you install after Divi or Extra Theme!
 * Version:             2.1.2
 * Requires at least:   5.0.0
 * Requires PHP:        5.6.40
 * Author:              WP Squad
 * Author URI:          https://squadmodules.com/
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:         squad-modules-for-divi
 * Domain Path:         /languages
 *
 *  Copyright 2023-2024 WP Squad (https://thewpsquad.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Autoload function.
 *
 * @param string $class_name Class name.
 *
 * @return void
 */
spl_autoload_register(
	static function ( $class_name ) {
		// Bail out if the class name doesn't start with our prefix.
		if ( strpos( $class_name, 'DiviSquad\\' ) !== 0 ) {
			return;
		}

		// Replace the namespace separator with the path prefix and the directory separator.
		$class_path_name = str_replace( 'DiviSquad\\', '', $class_name );
		$valid_path_name = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class_path_name );

		// Add the .php extension.
		$class_file_path = realpath( __DIR__ . "/includes/$valid_path_name.php" );

		if ( file_exists( $class_file_path ) ) {
			require_once $class_file_path;
		}
	}
);

if ( class_exists( DiviSquad\SquadModules::class ) ) {
	// Define the core constants.
	define( 'DIVI_SQUAD__FILE__', __FILE__ );
	define( 'DIVI_SQUAD_DIR_PATH', plugin_dir_path( DIVI_SQUAD__FILE__ ) );
	define( 'DIVI_SQUAD_PLUGIN_BASE', plugin_basename( DIVI_SQUAD__FILE__ ) );
	define( 'DIVI_SQUAD_DIR_URL', plugin_dir_url( DIVI_SQUAD__FILE__ ) );
	define( 'DIVI_SQUAD_ASSET_URL', trailingslashit( DIVI_SQUAD_DIR_URL . 'build' ) );
	define( 'DIVI_SQUAD_MODULES_ICON_DIR_PATH', DIVI_SQUAD_DIR_PATH . 'build/admin/icons' );
	define( 'DIVI_SQUAD_TEMPLATES_PATH', DIVI_SQUAD_DIR_PATH . 'templates' );

	/**
	 * Retrieve that the plugin basename of the premium version
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function divi_squad_get_pro_basename() {
		// Premium plugin path.
		return 'squad-modules-pro-for-divi/squad-modules-pro-for-divi.php';
	}

	/**
	 * Retrieve that the pro-version is installed or not.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
	function divi_squad_is_pro_activated() {
		static $pro_is_installed;

		if ( isset( $pro_is_installed ) ) {
			return $pro_is_installed;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Verify the pro-plugin activation status.
		$pro_is_installed = is_plugin_active( divi_squad_get_pro_basename() );

		return $pro_is_installed;
	}

	if ( is_admin() && DiviSquad\Integrations\Freemius::is_installed() ) {
		if ( function_exists( 'divi_squad_fs' ) ) {
			divi_squad_fs()->set_basename( false, __FILE__ );
		} else {
			/**
			 * Create a helper function for easy SDK access.
			 *
			 * @return \Freemius;
			 */
			function divi_squad_fs() {
				return DiviSquad\Integrations\Freemius::get_instance()->get_fs();
			}

			// Init Freemius.
			divi_squad_fs();

			// Signal that SDK was initiated.
			do_action( 'divi_squad_fs_loaded' );
		}
	}

	if ( ! function_exists( 'divi_squad' ) ) {
		/**
		 * The instance of Divi Squad Plugin.
		 *
		 * @return DiviSquad\SquadModules
		 */
		function divi_squad() {
			return DiviSquad\SquadModules::get_instance();
		}
	}

	// Load the plugin.
	divi_squad();
}
