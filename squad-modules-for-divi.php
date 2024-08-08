<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules Lite
 *
 * @package     DiviSquad
 * @author      WP Squad <support@squadmodules.com>
 * @copyright   2023-2024 WP Squad (https://thewpsquad.com/)
 *
 * @wordpress-plugin
 * Plugin Name:         Squad Modules Lite
 * Plugin URI:          https://squadmodules.com/
 * Description:         The Advanced Divi plugin you install after Divi or Extra Theme!
 * Version:             3.0.0
 * Requires at least:   5.0.0
 * Requires PHP:        5.6.40
 * Author:              WP Squad
 * Author URI:          https://squadmodules.com/
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:         squad-modules-for-divi
 * Domain Path:         /languages
 *
 * Copyright 2023-2024 WP Squad (https://thewpsquad.com/)
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
		$class_path = str_replace( 'DiviSquad\\', '', $class_name );
		$valid_path = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class_path );

		// List of deprecated classes.
		$deprecated_classes = array(
			'Admin/Assets',
			'Admin/Plugin/AdminFooterText',
			'Admin/Plugin/ActionLinks',
			'Admin/Plugin/RowMeta',
			'Base/Factories/AdminMenu/MenuCore',
			'Integrations/Admin',
			'Managers/Assets',
		);

		// Load deprecated classes.
		if ( in_array( $valid_path, $deprecated_classes, true ) ) {
			$valid_path = 'deprecated/' . $valid_path;
		} else {
			$valid_path = 'includes/' . $valid_path;
		}

		// Add the .php extension.
		$class_file = realpath( __DIR__ . "/$valid_path.php" );
		if ( file_exists( $class_file ) ) {
			require_once $class_file;
		}
	}
);

if ( ! class_exists( DiviSquad\SquadModules::class ) ) {
	return;
}

/**
 * Helper function to get the Divi Squad Plugin instance.
 *
 * @return DiviSquad\SquadModules
 */
function divi_squad() {
	return DiviSquad\SquadModules::get_instance();
}

try {
	// Define the core constants.
	define( 'DIVI_SQUAD__FILE__', __FILE__ );

	// Load the plugin.
	divi_squad();
} catch ( Exception $e ) {
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
	error_log( 'SQUAD ERROR: ' . $e->getMessage() );
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
}
