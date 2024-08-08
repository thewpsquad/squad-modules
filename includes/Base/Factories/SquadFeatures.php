<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories;

use DiviSquad\Utils\Polyfills\Str;
use function gettype;
use function sort;

/**
 * Feature Management class
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
abstract class SquadFeatures {

	/**
	 * Get the type of Divi Builder, default is: D4. Available opinions are: D4, D5.
	 *
	 * @var string
	 */
	protected $builder_type = 'D4';

	/**
	 * Retrieve the list of registered.
	 *
	 * @return array[]
	 */
	abstract public function get_registered_list();

	/**
	 * Retrieve the list of inactive registered.
	 *
	 * @return array
	 */
	abstract public function get_inactive_registries();

	/**
	 * Retrieve the list of default active registered.
	 *
	 * @return array
	 */
	abstract public function get_default_registries();

	/**
	 * Retrieve details by the registered name.
	 *
	 * @param array  $registries The array list of available registries.
	 * @param string $name       The name of the current registry.
	 *
	 * @return array
	 */
	protected function get_details_by_name( $registries, $name ) {
		$details = array();
		foreach ( $registries as $registry ) {
			if ( isset( $registry['name'] ) && $registry['name'] === $name ) {
				$details[] = $registry;
				break;
			}
		}

		return $details;
	}

	/**
	 * Retrieve the filtered list of registered.
	 *
	 * @param array         $registered The list of registered.
	 * @param callable|null $callback   The callback function to filter the current registriy.
	 *
	 * @return array
	 */
	protected function get_filtered_registries( $registered, $callback = null ) {
		if ( is_callable( $callback ) ) {
			$filtered_registries = array();

			foreach ( $registered as $registry ) {
				if ( $callback( $registry ) ) {
					$filtered_registries[] = $registry;
				}
			}

			return $filtered_registries;
		}

		return $registered;
	}

	/**
	 * Verify third party plugin requirements for current registry.
	 *
	 * @param array $registry_info  Current registry information.
	 * @param array $active_plugins Active plugin lists from current installation.
	 *
	 * @return bool
	 */
	protected function verify_requirements( $registry_info, $active_plugins ) {
		if ( ! empty( $registry_info['required'] ) && ! empty( $registry_info['required']['plugin'] ) ) {
			// Collect required plugins by current module.
			$required_plugins = $registry_info['required']['plugin'];

			// Verify for the single requirements.
			if ( gettype( $required_plugins ) === 'string' ) {
				// Store required plugins that are string data types.
				$verified_plugins = array();

				// Verify optional plugins when available.
				if ( Str::contains( $required_plugins, '|' ) ) {
					$verified_plugins = explode( '|', $required_plugins );
				} else {
					$verified_plugins[] = $required_plugins;
				}

				// Collect all active plugins that are required by current plugin.
				$activated_plugins = array();
				foreach ( $active_plugins as $plugin ) {
					// Verify optional plugins are activated.
					if ( in_array( $plugin, $verified_plugins, true ) ) {
						$activated_plugins[] = $plugin;
					}
				}

				// Verify that any requireds plugin is/are available.
				return ! empty( $activated_plugins );
			}

			// Verify for the multiple requirements.
			if ( gettype( $required_plugins ) === 'array' ) {
				$dependencies_plugins = array();
				foreach ( $active_plugins as $plugin ) {
					if ( in_array( $plugin, $required_plugins, true ) ) {
						$dependencies_plugins[] = $plugin;
					}
				}

				// Short all plugin lists.
				sort( $required_plugins );
				sort( $dependencies_plugins );

				// Verify that all required are available.
				return $required_plugins === $dependencies_plugins;

			}
		}

		return false;
	}

	/**
	 * Load the module class.
	 *
	 * @param array $registered The available modules list.
	 * @param array $defaults   The default activated registries list.
	 * @param array $activated  The user defined an activated registries list.
	 *
	 * @return array
	 */
	protected function get_verified_registries( $registered, $defaults, $activated ) {
		$verified = array();

		if ( is_array( $activated ) && count( $activated ) !== 0 ) {
			// Get all registry names that activated by user.
			$activated_names = array();
			foreach ( $activated as $activate ) {
				if ( gettype( $activate ) === 'string' ) {
					$activated_names[] = $activate;
				}

				if ( gettype( $activate ) === 'array' ) {
					if ( ! empty( $activate['name'] ) ) {
						$activated_names[] = $activate['name'];
					}
				}
			}

			// Verify default active registries.
			foreach ( $defaults as $default ) {
				if ( ! in_array( $default['name'], $activated_names, true ) ) {
					$verified[] = $default;
				}
			}

			// Get registry details that user activates.
			foreach ( $registered as $module ) {
				if ( in_array( $module['name'], $activated_names, true ) ) {
					$verified[] = $module;
				}
			}
		}

		// Collect all activate registries.
		return array_unique( $verified, SORT_REGULAR );
	}
}
