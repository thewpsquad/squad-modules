<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The main class for Divi Squad.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Base\DiviBuilder;

/**
 * Divi Squad Class.
 *
 * @package DiviSquad
 * @since   1.0.0
 */
abstract class IntegrationAPIBase {

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Absolute path to the plugin's directory.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_dir = '';

	/**
	 * The plugin's directory URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_dir_url = '';

	/**
	 * The plugin's version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin's version
	 */
	protected $version = '';

	/**
	 * The asset build for the plugin
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin's version
	 */
	protected $build_path = 'build/divi-builder-4/';

	/**
	 * Divi Squad constructor.
	 *
	 * @param string $name           The plugin's WP Plugin name.
	 * @param string $plugin_dir     Absolute path to the plugin's directory.
	 * @param string $plugin_dir_url The plugin's directory URL.
	 */
	public function __construct( $name, $plugin_dir, $plugin_dir_url ) {
		// Set required variables as per definition.
		$this->name           = $name;
		$this->plugin_dir     = $plugin_dir;
		$this->plugin_dir_url = $plugin_dir_url;

		$this->initialize();
	}

	/**
	 * Performs initialization tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	abstract public function initialize();

	/**
	 * Get the plugin version number
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	abstract public function get_version();
}
