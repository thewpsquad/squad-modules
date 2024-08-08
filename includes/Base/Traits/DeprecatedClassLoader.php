<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Deprecated Classes Trait
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.1
 */

namespace DiviSquad\Base\Traits;

use DiviSquad\Utils\WP;
use function add_action;
use function apply_filters;
use function version_compare;

/**
 * Deprecated Classes Trait
 *
 * @package DiviSquad
 * @since   3.1.1
 */
trait DeprecatedClassLoader {
	/**
	 * Load deprecated classes after Divi Squad has initialized.
	 *
	 * @since 3.1.1
	 *
	 * @return void
	 */
	public function load_deprecated_class_compatibility() {
		if ( ! defined( '\DIVI_SQUAD__FILE__' ) ) {
			return;
		}

		$active_plugins = WP::get_active_plugins();
		foreach ( $active_plugins as $plugin ) {
			if ( ! isset( $plugin['slug'], $plugin['version'] ) ) {
				continue;
			}

			if ( $plugin['slug'] !== $this->get_pro_basename() ) {
				continue;
			}

			if ( version_compare( $plugin['version'], '1.1.0', '>=' ) ) {
				$this->load_deprecated_classes();
				break;
			}
		}
	}
	/**
	 * Load deprecated classes after Divi Squad has initialized.
	 *
	 * @since 3.1.1
	 *
	 * @return void
	 */
	public function load_deprecated_classes() {
		if ( ! defined( '\DIVI_SQUAD__FILE__' ) ) {
			return;
		}

		$deprecated_classes = $this->get_deprecated_classes_list();

		/**
		 * Filters the list of deprecated classes to be loaded.
		 *
		 * @since 3.1.1
		 *
		 * @param array $deprecated_classes Array of deprecated class names and their configurations.
		 */
		$deprecated_classes = apply_filters( 'divi_squad_deprecated_classes', $deprecated_classes );

		foreach ( $deprecated_classes as $class => $config ) {
			$this->load_deprecated_class( $class, $config );
		}
	}

	/**
	 * Load a deprecated class file.
	 *
	 * @param string $class_name The full class name.
	 * @param array  $config     The configuration array.
	 * @return void
	 */
	private function load_deprecated_class( $class_name, $config = array() ) {
		if ( class_exists( $class_name ) ) {
			return;
		}

		$valid_path = $this->get_deprecated_class_path( $class_name );

		if ( ! file_exists( $valid_path ) ) {
			$this->log_deprecated_class_error( $valid_path );
			return;
		}

		if ( empty( $config ) ) {
			if ( ! class_exists( $class_name ) ) {
				require_once $valid_path;
			}
			return;
		}

		$this->execute_before_load_callback( $config, $class_name );

		if ( isset( $config['action']['name'] ) ) {
			$this->add_deprecated_class_action( $config, $class_name, $valid_path );
		} elseif ( ! class_exists( $class_name ) ) {
				require_once $valid_path;
		}

		$this->execute_after_load_callback( $config, $class_name );
	}

	/**
	 * Get the list of deprecated classes and their configurations.
	 *
	 * @return array
	 */
	private function get_deprecated_classes_list() {
		return array(
			'DiviSquad\Admin\Assets'                      => array(),
			'DiviSquad\Admin\Plugin\AdminFooterText'      => array(),
			'DiviSquad\Admin\Plugin\ActionLinks'          => array(),
			'DiviSquad\Admin\Plugin\RowMeta'              => array(),
			'DiviSquad\Base\DiviBuilder\DiviSquad_Module' => array(
				'action' => array(
					'name'     => 'divi_extensions_init',
					'priority' => 9,
				),
			),
			'DiviSquad\Base\DiviBuilder\IntegrationAPIBase' => array(),
			'DiviSquad\Base\DiviBuilder\IntegrationAPI'   => array(),
			'DiviSquad\Base\DiviBuilder\Utils\UtilsInterface' => array(),
			'DiviSquad\Base\Factories\AdminMenu\MenuCore' => array(),
			'DiviSquad\Integrations\Admin'                => array(),
			'DiviSquad\Managers\Assets'                   => array(),
			'DiviSquad\Managers\Extensions'               => array(),
			'DiviSquad\Managers\Modules'                  => array(),
			'DiviSquad\Modules\PostGridChild\PostGridChild' => array(
				'action' => array(
					'name'     => 'divi_extensions_init',
					'priority' => 9,
				),
			),
		);
	}

	/**
	 * Get the file path for a deprecated class.
	 *
	 * @param string $class_name The full class name.
	 * @return string
	 */
	private function get_deprecated_class_path( $class_name ) {
		$class_path = str_replace( 'DiviSquad\\', '', $class_name );
		$valid_path = str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, $class_path );
		return $this->get_path( '/deprecated/' . $valid_path . '.php' );
	}

	/**
	 * Log an error when a deprecated class file is not found.
	 *
	 * @param string $valid_path The expected file path.
	 * @return void
	 */
	private function log_deprecated_class_error( $valid_path ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( "SQUAD ERROR: Deprecated class file not found: $valid_path" );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Execute the before load callback if it exists in the configuration.
	 *
	 * @param array  $config     The configuration array.
	 * @param string $class_name The full class name.
	 * @return void
	 */
	private function execute_before_load_callback( $config, $class_name ) {
		if ( isset( $config['condition']['callback_before'] ) && is_callable( $config['condition']['callback_before'] ) ) {
			$callback_args = isset( $config['condition']['callback_before_args'] ) ? $config['condition']['callback_before_args'] : array( $class_name );
			call_user_func_array( $config['condition']['callback_before'], $callback_args );
		}
	}

	/**
	 * Add an action to load the deprecated class.
	 *
	 * @param array  $config     The configuration array.
	 * @param string $class_name The full class name.
	 * @param string $valid_path The file path of the deprecated class.
	 * @return void
	 */
	private function add_deprecated_class_action( $config, $class_name, $valid_path ) {
		$priority = isset( $config['action']['priority'] ) ? $config['action']['priority'] : 10;
		$priority = apply_filters( 'divi_squad_deprecated_class_priority', $priority, $class_name );

		$custom_callback = function () use ( $config, $class_name, $valid_path ) {
			if ( isset( $config['condition']['callback_inside'] ) && is_callable( $config['condition']['callback_inside'] ) ) {
				$callback_args     = isset( $config['condition']['callback_inside_args'] ) ? $config['condition']['callback_inside_args'] : array( $class_name );
				$should_load_class = call_user_func_array( $config['condition']['callback_inside'], $callback_args );

				if ( $should_load_class && ! class_exists( $class_name ) ) {
					require_once $valid_path;
				}
			} elseif ( ! class_exists( $class_name ) ) {
					require_once $valid_path;
			}
		};

		add_action( $config['action']['name'], $custom_callback, $priority );
	}

	/**
	 * Execute the after load callback if it exists in the configuration.
	 *
	 * @param array  $config     The configuration array.
	 * @param string $class_name The full class name.
	 * @return void
	 */
	private function execute_after_load_callback( $config, $class_name ) {
		if ( isset( $config['condition']['callback_after'] ) && is_callable( $config['condition']['callback_after'] ) ) {
			$callback_args = isset( $config['condition']['callback_after_args'] ) ? $config['condition']['callback_after_args'] : array( $class_name );
			call_user_func_array( $config['condition']['callback_after'], $callback_args );
		}
	}
}
