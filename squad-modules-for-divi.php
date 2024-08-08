<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules for Divi Builder
 *
 * @package     divi-squad
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 *
 * @wordpress-plugin
 * Plugin Name:         Squad Modules for Divi Builder
 * Plugin URI:          https://squadmodules.com/
 * Description:         Enhance your Divi-powered websites with an elegant collection of Divi modules.
 * Version:             1.2.0
 * Requires at least:   5.8
 * Requires PHP:        5.6
 * Author:              WP Squad
 * Author URI:          https://squadmodules.com/
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:         squad-modules-for-divi
 * Domain Path:         /languages
 */

namespace DiviSquad;

defined( 'ABSPATH' ) || die();

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
		// Generate paths by namespace.
		$regex = array(
			'DiviSquad\\Admin\\'       => '/admin/',
			'DiviSquad\\Base\\'        => '/includes/base/',
			'DiviSquad\\Extensions\\'  => '/includes/extensions',
			'DiviSquad\\Integration\\' => '/includes/integration/',
			'DiviSquad\\Manager\\'     => '/includes/manager/',
			'DiviSquad\\Modules\\'     => '/includes/modules/',
			'DiviSquad\\Utils\\'       => '/includes/utils/',
		);

		// Replace the namespace separator with the path prefix.
		$class_path_name = str_replace( array_keys( $regex ), array_values( $regex ), $class_name );

		// Replace the namespace separator with the directory separator.
		$valid_path_name = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class_path_name );

		// Add the .php extension.
		$file_path = __DIR__ . $valid_path_name . '.php';

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);

/** Load the Plugin (free version).
 *
 * @since 1.2.0
 */
require_once __DIR__ . '/SquadModules.php';

/**
 * The instance of Divi Squad Plugin (Free).
 *
 * @return SquadModules
 */
function divi_squad() {
	return SquadModules::get_instance();
}

// Load the plugin.
divi_squad();
