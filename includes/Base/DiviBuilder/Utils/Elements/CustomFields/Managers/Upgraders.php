<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Custom Fields Upgrader
 *
 * This file contains the CustomFieldsUpgrader class which manages
 * database upgrades for the custom fields summary table.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\CustomFields\Managers;

use function divi_squad;

/**
 * Upgrader Class
 *
 * Manages database upgrades for the custom fields summary table.
 * This class handles version tracking and applies necessary database
 * structure changes when upgrading from older versions.
 *
 * @package DiviSquad
 * @since   3.1.1
 */
class Upgraders {

	/**
	 * The name of the summary table in the database.
	 *
	 * @since 3.1.1
	 * @var string
	 */
	private $summary_table_name;

	/**
	 * The option name used to store the current version in the database.
	 *
	 * @since 3.1.1
	 * @var string
	 */
	private $version_option_name = 'custom_fields_summary_version';

	/**
	 * The current version of the database structure.
	 *
	 * @since 3.1.1
	 * @var string
	 */
	private $current_version = '1.0';

	/**
	 * Constructor.
	 *
	 * @since 3.1.1
	 *
	 * @param string $summary_table_name The name of the summary table in the database.
	 */
	public function __construct( $summary_table_name ) {
		$this->summary_table_name = $summary_table_name;
	}

	/**
	 * Run necessary database upgrades.
	 *
	 * Checks the installed version against the current version and
	 * performs any necessary upgrade procedures.
	 *
	 * @since 3.1.1
	 *
	 * @return void
	 */
	public function run_upgrades() {
		$installed_version = divi_squad()->memory->get( $this->version_option_name, '0' );

		if ( version_compare( $installed_version, $this->current_version, '<' ) ) {
			$this->perform_upgrades( $installed_version );
			divi_squad()->memory->set( $this->version_option_name, $this->current_version );
		}
	}

	/**
	 * Perform specific upgrade procedures.
	 *
	 * This method contains the logic for upgrading the database
	 * structure from one version to another.
	 *
	 * @since 3.1.1
	 *
	 * @param string $from_version The version to upgrade from.
	 * @return void
	 */
	private function perform_upgrades( $from_version ) {
		// Perform upgrade procedures here.
	}

	/**
	 * @return string
	 */
	public function get_summary_table_name() {
		return $this->summary_table_name;
	}

	/**
	 * Get the current version of the database structure.
	 *
	 * @since 3.1.1
	 *
	 * @return string The current version.
	 */
	public function get_current_version() {
		return $this->current_version;
	}

	/**
	 * Set the current version of the database structure.
	 *
	 * This method is primarily used for testing purposes or manual version management.
	 *
	 * @since 3.1.1
	 *
	 * @param string $version The version to set.
	 * @return void
	 */
	public function set_current_version( $version ) {
		$this->current_version = $version;
	}

	/**
	 * Check if an upgrade is needed.
	 *
	 * Compares the installed version with the current version to determine
	 * if an upgrade is necessary.
	 *
	 * @since 3.1.1
	 *
	 * @return bool True if an upgrade is needed, false otherwise.
	 */
	public function is_upgrade_needed() {
		$installed_version = divi_squad()->memory->get( $this->version_option_name, '0' );
		return version_compare( $installed_version, $this->current_version, '<' );
	}

	/**
	 * Get the installed version of the database structure.
	 *
	 * @since 3.1.1
	 *
	 * @return string The installed version.
	 */
	public function get_installed_version() {
		return divi_squad()->memory->get( $this->version_option_name, '0' );
	}
}
