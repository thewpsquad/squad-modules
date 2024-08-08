<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules for Divi Builder
 *
 * @package     divi-squad
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 *
 * @wordpress-plugin
 * Plugin Name:         Squad Modules for Divi Builder
 * Plugin URI:          https://squadmodules.com/
 * Description:         Enhance your Divi-powered websites with an elegant collection of Divi modules.
 * Requires at least:   5.8
 * Requires PHP:        5.6
 * Version:             1.0.5
 * Author:              WP Squad
 * Author URI:          https://thewpsquad.com/
 * Text Domain:         squad-modules-for-divi
 * Domain Path:         /languages
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
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
			'DiviSquad\\Integration\\' => '/includes/integration/',
			'DiviSquad\\Manager\\'     => '/includes/manager/',
			'DiviSquad\\Modules\\'     => '/includes/modules/',
			'DiviSquad\\Utils\\'       => '/includes/utils/',
		);

		// Replace the namespace separator with the path prefix.
		$class_name = str_replace( array_keys( $regex ), array_values( $regex ), $class_name );

		// Replace the namespace separator with the directory separator.
		$class_name = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class_name );

		// Add the .php extension.
		$file_path = __DIR__ . $class_name . '.php';

		if ( file_exists( $file_path ) ) {
			require_once $file_path;
		}
	}
);

/**
 * Free Plugin Load class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */
final class SquadModules extends Integration\Core {

	/**
	 * The instance of current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name             = 'squad-modules-for-divi';
		$this->option_prefix    = 'disq';
		$this->version          = '1.0.5';
		$this->min_version_divi = '4.0.0';
		$this->min_version_php  = '5.6';
		$this->min_version_wp   = '5.8';

		// translations.
		$this->localize_handle = 'admin-free';
		$this->localize_path   = __DIR__;
	}

	/**
	 * Define the core constants.
	 *
	 * @return void
	 */
	private function define_core_constants() {
		define( 'DISQ__FILE__', __FILE__ );
		define( 'DISQ_PLUGIN_BASE', plugin_basename( DISQ__FILE__ ) );
		define( 'DISQ_DIR_PATH', dirname( DISQ__FILE__ ) );
		define( 'DISQ_DIR_URL', plugin_dir_url( DISQ__FILE__ ) );
		define( 'DISQ_ASSET_URL', trailingslashit( DISQ_DIR_URL . 'build' ) );
	}

	/**
	 *  The instance of current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
			self::$instance->define_general_constants();
			self::$instance->define_core_constants();
			self::$instance->load_memory();

			// Load the core.
			$wp = Integration\WP::get_instance( self::$instance->min_version_php );
			$wp->let_the_journey_start(
				static function () {
					self::$instance->load_global_assets();
					self::$instance->localize_scripts_data();
					self::$instance->load_admin_interface();
					self::$instance->register_ajax_rest_api_routes();
					self::$instance->init();
					self::$instance->load_text_domain();
					self::$instance->load_divi_modules_for_builder();
				}
			);
		}

		return self::$instance;
	}
}

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
