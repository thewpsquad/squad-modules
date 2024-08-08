<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules Lite
 *
 * @package     divi-squad
 * @author      WP Squad <wp@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad;

use DiviSquad\Utils\Singleton;

/**
 * Squad Modules class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */
final class SquadModules extends Integrations\Core {

	use Singleton;

	protected $admin_menu_slug = 'divi_squad_dashboard';

	/**
	 * Constructor.
	 */
	private function __construct() {
		$options         = $this->get_plugin_data( DIVI_SQUAD__FILE__ );
		$default_options = array( 'RequiresDIVI' => '4.14.0' );

		// Set plugin options and others.
		$this->version    = $options['Version'];
		$this->options    = array_merge( $default_options, $options );
		$this->name       = 'squad-modules-for-divi';
		$this->opt_prefix = 'disq';

		// Translations path.
		$this->localize_path = DIVI_SQUAD_DIR_PATH;
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
	 * Get the plugin version number (doted)
	 *
	 * @return string
	 */
	public function get_version_dot() {
		return $this->options['Version'];
	}

	/**
	 * Run the plugin
	 */
	public function run() {
		// Init the plugin.
		$this->init();

		// Load the core.
		$wp = Integrations\WP::get_instance();
		if ( $wp->let_the_journey_start() ) {
			$this->load_text_domain();
			$this->load_extensions();
			$this->load_modules_for_builder();
			$this->load_admin();
			$this->load_global_assets();
			$this->localize_scripts_data();
		}

		// Signal that Core was initiated.
		do_action( 'divi_squad_loaded', $this );
	}
}
