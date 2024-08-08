<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Squad Modules for Divi Builder
 *
 * @package     divi-squad
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad;

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
	 * The instance of Memory class.
	 *
	 * @var \DiviSquad\Base\Memory
	 */
	protected $plugin_memory;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->name          = 'squad-modules-for-divi';
		$this->option_prefix = 'disq';

		// translations.
		$this->localize_path = __DIR__;
	}

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	public function get_version() {
		return DISQ_VERSION;
	}

	/**
	 * Get the plugin version number
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @return \DiviSquad\Base\Memory
	 */
	public function set_memory( $prefix ) {
		$this->plugin_memory = new \DiviSquad\Base\Memory( $prefix );

		return $this->get_memory();
	}

	/**
	 * Get the plugin version number
	 *
	 * @return \DiviSquad\Base\Memory
	 */
	public function get_memory() {
		return $this->plugin_memory;
	}

	/**
	 *  The instance of current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			try {
				self::$instance->set_memory( self::$instance->option_prefix );
				self::$instance->load_core_components();

				// Load the core.
				$wp = new Integration\WP();
				$wp->let_the_journey_start(
					static function () {
						self::$instance->load_global_assets();
						self::$instance->localize_scripts_data();
						self::$instance->load_admin_interface();
						self::$instance->register_ajax_rest_api_routes();
						self::$instance->init();
						self::$instance->load_text_domain();
						self::$instance->load_all_extensions();
						self::$instance->load_divi_modules_for_builder();
					}
				);
			} catch ( \Exception $exception ) {
				error_log( 'DiviSquad: ' . $exception->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}

		return self::$instance;
	}

	/**
	 * Get the plugin name for the pro-version
	 *
	 * @return bool
	 */
	public static function is_the_pro_plugin_active() {
		return defined( '\DISQ_PRO_PLUGIN_BASE' ) && Utils\WP::is_plugin_active( DISQ_PRO_PLUGIN_BASE );
	}
}
