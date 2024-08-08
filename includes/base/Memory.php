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
	 * The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The store of data (Option data).
	 *
	 * @var array
	 */
	private static $data = array();

	/**
	 * The database option prefix.
	 *
	 * @var string
	 */
	private $option_prefix = '';

	/**
	 * Get the instance of the current class.
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @return self
	 */
	public static function get_instance( $prefix ) {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->set_prefix( $prefix );

			// Load current data.
			$new_option_name = sprintf( '%1$s-settings', $prefix );
			if ( array() === self::$data && ! empty( get_option( $new_option_name ) ) ) {
				self::$data = get_option( $new_option_name );
			}
		}

		return self::$instance;
	}

	/**
	 * Get all data from option.
	 *
	 * @return array
	 */
	public static function get_data() {
		return self::$data;
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
	 * @param array|string|numeric|null|false $default The default value for field.
	 *
	 * @return array|string|numeric|null|false
	 */
	public function get( $field, $default = null ) {
		return isset( self::$data[ $field ] ) ? self::$data[ $field ] : $default;
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
		self::$data[ $field ] = $value;
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
		if ( isset( self::$data[ $field ] ) ) {
			self::$data[ $field ] = $value;
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
		if ( isset( self::$data[ $field ] ) ) {
			unset( self::$data[ $field ] );
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
			update_option( $option_name, self::$data );
		}
	}
}
