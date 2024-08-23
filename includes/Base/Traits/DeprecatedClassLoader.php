<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Deprecated Classes Loader Trait
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.1
 */

namespace DiviSquad\Base\Traits;

use DiviSquad\Managers\Emails\ErrorReport;
use DiviSquad\Utils\WP;

/**
 * DeprecatedClassLoader Trait
 *
 * Handles the loading of deprecated classes for backwards compatibility.
 *
 * @package DiviSquad
 * @since   3.1.1
 */
trait DeprecatedClassLoader {

	/**
	 * Deprecated classes configuration.
	 *
	 * @var array
	 */
	private $deprecated_classes;

	/**
	 * Initialize the deprecated class loader.
	 *
	 * @since 3.1.1
	 */
	public function init_deprecated_class_loader() {
		if ( ! defined( 'DIVI_SQUAD__FILE__' ) ) {
			return;
		}

		$this->deprecated_classes = $this->get_deprecated_classes_list();
		$this->maybe_load_deprecated_classes();
	}

	/**
	 * Load all deprecated classes.
	 *
	 * @since 3.1.1
	 */
	public function load_deprecated_classes() {
		if ( ! defined( 'DIVI_SQUAD__FILE__' ) ) {
			return;
		}

		/**
		 * Filters the list of deprecated classes.
		 *
		 * @since 3.1.1
		 *
		 * @param array $deprecated_classes The list of deprecated classes.
		 */
		$this->deprecated_classes = apply_filters( 'divi_squad_deprecated_classes', $this->deprecated_classes );

		foreach ( $this->deprecated_classes as $class_name => $config ) {
			$this->load_deprecated_class( $class_name, $config );
		}
	}

	/**
	 * Load deprecated classes if conditions are met.
	 *
	 * @since 3.1.1
	 */
	private function maybe_load_deprecated_classes() {
		if ( $this->should_load_deprecated_classes() ) {
			$this->load_deprecated_classes();
		}
	}

	/**
	 * Check if deprecated classes should be loaded.
	 *
	 * @since 3.1.1
	 *
	 * @return bool
	 */
	private function should_load_deprecated_classes() {
		$active_plugins = WP::get_active_plugins();
		$pro_basename   = $this->get_pro_basename();

		foreach ( $active_plugins as $plugin ) {
			if ( isset( $plugin['slug'], $plugin['version'] ) &&
				$plugin['slug'] === $pro_basename &&
				version_compare( $plugin['version'], '1.1.0', '<=' )
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load a specific deprecated class.
	 *
	 * @since 3.1.1
	 *
	 * @param string $class_name Full class name.
	 * @param array  $config     Class configuration.
	 */
	private function load_deprecated_class( $class_name, $config ) {
		try {
			if ( class_exists( $class_name ) ) {
				return;
			}

			if ( interface_exists( $class_name ) ) {
				return;
			}

			$file_path = $this->get_deprecated_class_path( $class_name );

			if ( ! file_exists( $file_path ) ) {
				$this->log_deprecated_class_error( $file_path );
				return;
			}

			$this->execute_callback( $config, 'before', $class_name );

			if ( isset( $config['action'] ) ) {
				$this->schedule_class_loading( $config, $class_name, $file_path );
			} else {
				require_once $file_path;
			}

			$this->execute_callback( $config, 'after', $class_name );
		} catch ( \Exception $e ) {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( 'SQUAD ERROR: %s', $e->getMessage() ) );
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log

			// Send an error report.
			ErrorReport::quick_send(
				$e,
				array(
					'additional_info' => 'An error message from deprecated class loader.',
				)
			);
		}
	}

	/**
	 * Schedule class loading on a specific action.
	 *
	 * @since 3.1.1
	 *
	 * @param array  $config     Class configuration.
	 * @param string $class_name Full class name.
	 * @param string $file_path  Path to the class file.
	 */
	private function schedule_class_loading( $config, $class_name, $file_path ) {
		$priority = isset( $config['action']['priority'] ) ? $config['action']['priority'] : 10;

		/**
		 * Filters the priority for loading a deprecated class.
		 *
		 * @since 3.1.1
		 *
		 * @param int    $priority   The default priority.
		 * @param string $class_name The full class name.
		 */
		$priority = apply_filters( 'divi_squad_deprecated_class_loading_priority', $priority, $class_name );

		add_action(
			$config['action']['name'],
			function () use ( $config, $class_name, $file_path ) {
				$this->load_class_if_needed( $config, $class_name, $file_path );
			},
			$priority
		);
	}

	/**
	 * Load class if needed based on conditions.
	 *
	 * @since 3.1.1
	 *
	 * @param array  $config     Class configuration.
	 * @param string $class_name Full class name.
	 * @param string $file_path  Path to the class file.
	 */
	private function load_class_if_needed( $config, $class_name, $file_path ) {
		$should_load = true;

		if ( isset( $config['condition']['callback'] ) ) {
			$callback    = $config['condition']['callback'];
			$args        = isset( $config['condition']['args'] ) ? $config['condition']['args'] : array( $class_name );
			$should_load = call_user_func_array( $callback, $args );
		}

		if ( $should_load && ! class_exists( $class_name ) ) {
			require_once $file_path;
		}
	}

	/**
	 * Execute a callback if it exists in the configuration.
	 *
	 * @since 3.1.1
	 *
	 * @param array  $config     Class configuration.
	 * @param string $type       Callback type ('before' or 'after').
	 * @param string $class_name Full class name.
	 */
	private function execute_callback( $config, $type, $class_name ) {
		$callback_key = "callback_{$type}";
		if ( isset( $config['condition'][ $callback_key ] ) && is_callable( $config['condition'][ $callback_key ] ) ) {
			$args = isset( $config['condition'][ "{$callback_key}_args" ] ) ? $config['condition'][ "{$callback_key}_args" ] : array( $class_name );
			call_user_func_array( $config['condition'][ $callback_key ], $args );
		}
	}

	/**
	 * Get the file path for a deprecated class.
	 *
	 * @since 3.1.1
	 *
	 * @param string $class_name The full class name.
	 * @return string
	 */
	private function get_deprecated_class_path( $class_name ) {
		$relative_path = str_replace(
			array( 'DiviSquad\\', '\\' ),
			array( '', DIRECTORY_SEPARATOR ),
			$class_name
		);
		return $this->get_path( "/deprecated/{$relative_path}.php" );
	}

	/**
	 * Log an error when a deprecated class file is not found.
	 *
	 * @since 3.1.1
	 *
	 * @param string $file_path The expected file path.
	 */
	private function log_deprecated_class_error( $file_path ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( sprintf( 'SQUAD ERROR: Deprecated class file not found: %s', $file_path ) );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
