<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules Lite
 *
 * @package     divi-squad
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

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
	 * The plugin options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param array $options The plugin options.
	 */
	public function __construct( $options ) {
		// translations.
		$this->localize_path = DISQ_DIR_PATH;

		$this->options    = $options;
		$this->opt_prefix = $this->options['OptionPrefix'];
		$this->name       = $this->options['Name'];
		$this->version    = $this->options['Version'];
	}

	/**
	 * The plugin options.
	 *
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
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
	 * Cloning is forbidden.
	 *
	 * @access public
	 */
	public function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @access public
	 */
	public function __wakeup() {}

	/**
	 *  The instance of current class.
	 *
	 * @param array $options The plugin options.
	 *
	 * @return self
	 */
	public static function get_instance( $options ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self( $options );

			self::$instance->set_memory( self::$instance->opt_prefix );
			self::$instance->load_core_components();

			// Load the core.
			$wp = new Integration\WP( $options );
			$wp->let_the_journey_start(
				static function () {
					self::$instance->load_global_assets();
					self::$instance->localize_scripts_data();
					self::$instance->load_admin_interface( self::$instance->get_options() );
					self::$instance->register_ajax_rest_api_routes();
					self::$instance->init();
					self::$instance->load_text_domain();
					self::$instance->load_all_extensions();
					self::$instance->load_divi_modules_for_builder();
				}
			);
		}

		return self::$instance;
	}
}
