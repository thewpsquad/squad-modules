<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Squad Modules Lite
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad;

use DiviSquad\Utils\Polyfills\Str;
use DiviSquad\Utils\Singleton;
use DiviSquad\Utils\WP;
use function add_action;
use function plugin_basename;
use function plugin_dir_url;
use function trailingslashit;
use function wp_parse_args;
use const ABSPATH;
use const DIVI_SQUAD__FILE__;

/**
 * Squad Modules class.
 *
 * @package DiviSquad
 * @since   1.0.0
 */
final class SquadModules extends Integrations\Core {

	use Singleton;

	/**
	 * Admin menu slug.
	 *
	 * @var string
	 */
	protected $admin_menu_slug = 'divi_squad_dashboard';

	/**
	 * Plugin Constructor.
	 *
	 * @since 1.0.0
	 * @since 3.0.0 Added the plugin initialization on `plugin_loaded` hook.
	 * @since 3.0.0 Added the plugin publisher initialization on `plugin_loaded` hook.
	 */
	private function __construct() {
		// Initialize the plugin.
		add_action( 'plugin_loaded', array( $this, 'init_plugin' ) );
		add_action( 'plugin_loaded', array( $this, 'init_publisher' ) );
		add_action( 'plugins_loaded', array( $this, 'run' ), 0 );

		// Hook the deprecated class loader to run after Divi Squad has initialized.
		add_action( 'divi_squad_loaded', array( $this, 'load_deprecated_classes' ), 0 );
	}

	/**
	 * Get the plugin directory path.
	 *
	 * @param string $path The path to append.
	 *
	 * @return string
	 */
	public function get_path( $path = '' ) {
		return realpath( dirname( DIVI_SQUAD__FILE__ ) ) . $path;
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
		return ! empty( $this->options['Version'] ) ? $this->options['Version'] : $this->version;
	}

	/**
	 * Get the plugin name.
	 *
	 * @return string
	 */
	public function get_base() {
		return plugin_basename( DIVI_SQUAD__FILE__ );
	}

	/**
	 * Get the plugin template path.
	 *
	 * @return string
	 */
	public function get_template_path() {
		return $this->get_path( '/templates' );
	}

	/**
	 * Get the plugin asset URL.
	 *
	 * @return string
	 */
	public function get_asset_url() {
		return trailingslashit( $this->get_url() . 'build' );
	}

	/**
	 * Get the plugin directory URL.
	 *
	 * @return string
	 */
	public function get_url() {
		return plugin_dir_url( DIVI_SQUAD__FILE__ );
	}

	/**
	 * Get the plugin icon path.
	 *
	 * @return string
	 */
	public function get_icon_path() {
		// Get the root path.
		$root_path = $this->get_path();

		// Add backslash when it not found.
		if ( ! Str::ends_with( $root_path, '/' ) ) {
			$root_path .= '/';
		}

		return $root_path . 'build/admin/icons';
	}

	/**
	 * Retrieve the WordPress root path.
	 *
	 * @return string
	 */
	public function get_wp_path() {
		$wp_root_path = ABSPATH;

		if ( strlen( $wp_root_path ) > 0 && ! Str::ends_with( $wp_root_path, DIRECTORY_SEPARATOR ) ) {
			$wp_root_path .= DIRECTORY_SEPARATOR;
		}

		return $wp_root_path;
	}

	/**
	 * Retrieve the plugin basename of the premium version.
	 *
	 * @return string
	 */
	public function get_pro_basename() {
		// Premium plugin path.
		return 'squad-modules-pro-for-divi/squad-modules-pro-for-divi.php';
	}

	/**
	 * Retrieve whether the pro-version is installed or not.
	 *
	 * @return bool
	 */
	public function is_pro_activated() {
		static $pro_is_installed;

		if ( isset( $pro_is_installed ) ) {
			return $pro_is_installed;
		}

		// Verify the pro-plugin activation status.
		$pro_is_installed = WP::is_plugin_active( $this->get_pro_basename() );

		return $pro_is_installed;
	}

	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return \Freemius
	 */
	public static function publisher() {
		return \DiviSquad\Integrations\Publisher::get_instance()->get_fs();
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init_plugin() {
		$options  = $this->get_plugin_data( DIVI_SQUAD__FILE__ );
		$defaults = array( 'RequiresDIVI' => '4.14.0' );

		// Set plugin options and others.
		$this->opt_prefix = 'disq';
		$this->textdomain = ! empty( $options['TextDomain'] ) ? $options['TextDomain'] : 'squad-modules-for-divi';

		// Set the plugin name.
		$this->name = $this->textdomain;

		// Set the plugin version and options.
		$this->version = ! empty( $options['Version'] ) ? $options['Version'] : '3.0.1';
		$this->options = wp_parse_args( $options, $defaults );

		// Translations path.
		$this->localize_path = $this->get_path( '/languages' );
	}

	/**
	 * Initialize the publisher.
	 *
	 * @return void
	 */
	public function init_publisher() {
		if ( is_admin() && \DiviSquad\Integrations\Publisher::is_installed() ) {
			if ( function_exists( '\divi_squad_fs' ) ) {
				\divi_squad_fs()->set_basename( false, DIVI_SQUAD__FILE__ );
			} else {
				// Init Freemius.
				self::publisher();

				// Signal that SDK was initiated.
				do_action( 'divi_squad_fs_loaded' );
			}
		}
	}

	/**
	 * Load the plugin.
	 *
	 * @return void
	 */
	public function run() {
		/**
		 * Fires before the plugin is loaded.
		 *
		 * @param SquadModules $plugin The plugin instance.
		 */
		do_action( 'divi_squad_before_loaded', $this );

		// Init the plugin.
		$this->init();

		// Load the core.
		$wp = Integrations\WP::get_instance();
		$wp->set_options( $this->options );

		// Check if the journey can start.
		if ( $wp->let_the_journey_start() ) {
			$this->load_text_domain();
			$this->load_assets();
			$this->load_global_assets();
			$this->load_extensions();
			$this->load_modules_for_builder();
			$this->load_admin();
			$this->localize_scripts_data();
		}

		/**
		 * Fires after the plugin is loaded.
		 *
		 * @param SquadModules $plugin The plugin instance.
		 */
		do_action( 'divi_squad_loaded', $this );
	}

	/**
	 * Load deprecated classes after Divi Squad has initialized.
	 *
	 * @since 3.0.1
	 *
	 * @return void
	 */
	public function load_deprecated_classes() {
		$deprecated_classes = array(
			'DiviSquad\Admin\Assets',
			'DiviSquad\Admin\Plugin\AdminFooterText',
			'DiviSquad\Admin\Plugin\ActionLinks',
			'DiviSquad\Admin\Plugin\RowMeta',
			'DiviSquad\Base\Factories\AdminMenu\MenuCore',
			'DiviSquad\Integrations\Admin',
			'DiviSquad\Managers\Assets',
			'DiviSquad\Managers\Extensions',
			'DiviSquad\Managers\Modules',
			'DiviSquad\Modules\PostGridChild\PostGridChild',
		);

		foreach ( $deprecated_classes as $class ) {
			// Replace the namespace separator with the path prefix and the directory separator.
			$class_path = str_replace( 'DiviSquad\\', '', $class );
			$valid_path = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class );

			// Add the includes directory and the .php extension.
			$valid_path = 'deprecated/' . $valid_path;
			$class_file = realpath( __DIR__ . "/$valid_path.php" );

			if ( file_exists( $class_file ) ) {
				require_once $class_file;
			}
		}
	}
}
