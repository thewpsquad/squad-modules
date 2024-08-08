<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base;

/**
 * Memory class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Memory {

	/**
	 * The store of data (Option data).
	 *
	 * @var array
	 */
	private $data;

	/**
	 * The database option prefix.
	 *
	 * @var string
	 */
	private $option_prefix;

	/**
	 * The constructor
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @since 1.2.0
	 */
	public function __construct( $prefix = 'disq' ) {
		$this->option_prefix = $prefix;

		// Load current data.
		$data_option = sprintf( '%1$s-settings', $prefix );
		$this->data  = get_option( $data_option, array() );
	}

	/**
	 * Get all data from option.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set the option prefix.
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @return void
	 */
	public function set_prefix( $prefix ) {
		$this->option_prefix = $prefix;
	}

	/**
	 * Get the option prefix.
	 *
	 * @return string
	 */
	public function get_option_prefix() {
		return $this->option_prefix;
	}

	/**
	 * Get the field value.
	 *
	 * @param string                          $field   The field key.
	 * @param array|string|numeric|null|false $defaults The default value for field.
	 *
	 * @return array|string|numeric|null|false
	 */
	public function get( $field, $defaults = null ) {
		return isset( $this->data[ $field ] ) ? $this->data[ $field ] : $defaults;
	}

	/**
	 * Set the field value.
	 *
	 * @param string                          $field The field key.
	 * @param array|string|numeric|null|false $value The value for field.
	 *
	 * @return array|string|numeric|null|false
	 */
	public function set( $field, $value ) {
		$this->data[ $field ] = $value;
		$this->update_database();

		return $value;
	}

	/**
	 * Update the field value.
	 *
	 * @param string                          $field The field key.
	 * @param array|string|numeric|null|false $value The value for field.
	 *
	 * @return array|string|numeric|null|false
	 */
	public function update( $field, $value ) {
		if ( isset( $this->data[ $field ] ) ) {
			$this->data[ $field ] = $value;
			$this->update_database();

			return $value;
		}

		return null;
	}

	/**
	 * Delete the field.
	 *
	 * @param string $field The field key.
	 *
	 * @return void
	 */
	public function delete( $field ) {
		if ( isset( $this->data[ $field ] ) ) {
			unset( $this->data[ $field ] );

			$this->update_database();
		}
	}

	/**
	 * Update the database option with stored data.
	 *
	 * @return void
	 */
	private function update_database() {
		$prefix          = $this->get_option_prefix();
		$new_option_name = sprintf( '%1$s-settings', $prefix );

		// Store all data into the database.
		$this->save_options( $new_option_name );
	}

	/**
	 * Save the database option with stored data.
	 *
	 * @param string $option_name The database option name.
	 *
	 * @return void
	 */
	private function save_options( $option_name ) {
		if ( function_exists( 'update_option' ) ) {
			update_option( $option_name, $this->data );
		}
	}
}
