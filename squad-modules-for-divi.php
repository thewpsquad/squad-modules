<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Squad Modules for Divi
 *
 * @package     divi-squad
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 *
 * @wordpress-plugin
 * Plugin Name:         Squad Modules for Divi
 * Plugin URI:          https://squadmodules.com/
 * Description:         Enhance your Divi-powered websites with an elegant collection of Divi modules.
 * Requires at least:   5.8
 * Requires PHP:        5.6
 * Version:             1.0.1
 * Author:              WP Squad
 * Author URI:          https://thewpsquad.com/
 * Text Domain:         squad-modules-for-divi
 * Domain Path:         /languages
 * License:             GPL-3.0-only
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace DiviSquad;

defined( 'ABSPATH' ) || die();

// Verify the composer autoload file is existed or not.
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	return;
}

// Load the composer autoload file.
require __DIR__ . '/vendor/autoload.php';

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
		$this->version          = '1.0.0';
		$this->min_version_divi = '4.0.0';
		$this->min_version_php  = '5.6';
		$this->min_version_wp   = '5.8';
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
	 * Get the plugin name for the pro-version
	 *
	 * @return bool
	 */
	public static function is_the_pro_plugin_active() {
		return defined( '\DISQ_PRO_PLUGIN_BASE' ) && Utils\Helper::is_plugin_active( DISQ_PRO_PLUGIN_BASE );
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
 * Get the plugin name for the pro-version
 *
 * @return bool
 */
function is_the_pro_plugin_active() {
	return defined( '\DISQ_PRO_PLUGIN_BASE' ) && Utils\Helper::is_plugin_active( DISQ_PRO_PLUGIN_BASE );
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
