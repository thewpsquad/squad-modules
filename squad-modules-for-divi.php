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
 * Version:             1.4.9
 * Requires at least:   5.0.0
 * Requires PHP:        5.6.40
 * Author:              WP Squad
 * Author URI:          https://squadmodules.com/
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:         squad-modules-for-divi
 * Domain Path:         /languages
 */

namespace DiviSquad;

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

// Define the core constants.
define( 'DISQ__FILE__', __FILE__ );
define( 'DISQ_DIR_PATH', __DIR__ );
define( 'DISQ_PLUGIN_BASE', plugin_basename( DISQ__FILE__ ) );
define( 'DISQ_DIR_URL', plugin_dir_url( DISQ__FILE__ ) );
define( 'DISQ_ASSET_URL', trailingslashit( DISQ_DIR_URL . 'build' ) );
define( 'DISQ_MODULES_ICON_DIR_PATH', __DIR__ . '/build/admin/modules-icon' );

// Fixed the free plugin load issue in the live site.
if ( ! file_exists( __DIR__ . '/SquadModules.php' ) ) {
	return;
}

/**
 * Load the Plugin (free version).
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
	$options = array(
		'Name'         => 'squad-modules-for-divi',
		'Version'      => '1.4.9',
		'OptionPrefix' => 'disq',
		'Minimum_PHP'  => '5.6.40',
		'Minimum_WP'   => '5.0.0',
		'Minimum_DIVI' => '4.14.0',
	);

	return SquadModules::get_instance( $options );
}

// Load the plugin.
divi_squad();
