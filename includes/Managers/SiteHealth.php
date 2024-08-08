<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Site Health Info.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.1
 */

namespace DiviSquad\Managers;

use DiviSquad\Utils\DateTime;
use DiviSquad\Utils\Singleton;

/**
 * Site Health Info Manager.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.1
 */
class SiteHealth {

	use Singleton;

	/**
	 * Init Site Health.
	 *
	 * @since 3.0.1
	 */
	public function load() {
		if ( $this->is_compatible() ) {
			$this->hooks();
		}
	}

	/**
	 * Check if the current WordPress version is compatible.
	 *
	 * @return bool
	 */
	private function is_compatible() {
		global $wp_version;

		return version_compare( $wp_version, '5.2', '>=' );
	}

	/**
	 * Integration hooks.
	 *
	 * @since 3.0.1
	 */
	protected function hooks() {
		add_filter( 'debug_information', array( $this, 'add_info_section' ) );
	}

	/**
	 * Add section to Info tab.
	 *
	 * @param array $debug_info Array of all information.
	 *
	 * @return array Array with added info section.
	 * @since 3.0.1
	 */
	public function add_info_section( array $debug_info ) {
		$squad_core = array(
			'label'       => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
			'description' => esc_html__( 'The Divi Squad plugin stores some data in the database.', 'squad-modules-for-divi' ),
			'fields'      => $this->get_info_fields(),
		);

		/**
		 * Filter the Divi Squad debug information.
		 *
		 * @param array $squad_core The Divi Squad debug information.
		 * @since 3.0.1
		 */
		$debug_info['divi-squad'] = apply_filters( 'divi_squad_debug_information', $squad_core );

		return $debug_info;
	}

	/**
	 * Get info fields for the Site Health section.
	 *
	 * @return array
	 */
	private function get_info_fields() {
		$fields = array(
			'version-core' => array(
				'label' => esc_html__( 'Lite Version', 'squad-modules-for-divi' ),
				'value' => $this->get_plugin_version(),
			),
		);

		$install_date = $this->get_install_date();
		if ( $install_date ) {
			$fields['install-date-core'] = array(
				'label' => esc_html__( 'Lite install date', 'squad-modules-for-divi' ),
				'value' => $install_date,
			);
		}

		// Add more fields here as needed.
		return $this->add_additional_fields( $fields );
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	private function get_plugin_version() {
		return \divi_squad()->get_version();
	}

	/**
	 * Get the plugin install date.
	 *
	 * @return string|null
	 */
	private function get_install_date() {
		$activated = \divi_squad()->memory->get( 'activation_time' );

		return $activated ? DateTime::datetime_format( $activated, '', true ) : null;
	}

	/**
	 * Add additional fields to the info section.
	 *
	 * @param array $fields Existing fields.
	 *
	 * @return array
	 */
	private function add_additional_fields( array $fields ) {
		// Example of adding a new field.
		$fields['php-version'] = array(
			'label' => esc_html__( 'PHP Version', 'squad-modules-for-divi' ),
			'value' => phpversion(),
		);

		// Add more fields as needed.
		return $fields;
	}
}
