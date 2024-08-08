<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base;

use function add_action;
use function get_option;
use function update_option;
use function wp_cache_delete;
use function wp_cache_get;
use function wp_cache_set;

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
	 * The database option name.
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * The cache option group.
	 *
	 * @var string
	 */
	private $option_group;

	/**
	 * The constructor
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 *
	 * @since 1.2.0
	 */
	public function __construct( $prefix = 'squad' ) {
		$this->option_group = $prefix;
		$this->option_name  = sprintf( '%1$s-settings', $prefix );
		$this->data         = wp_cache_get( $this->option_name, $this->option_group );

		// Load current data.
		if ( empty( $this->data ) ) {
			$this->data = get_option( $this->option_name, array() );

			// Store to the wp cache also.
			wp_cache_set( $this->option_name, $this->data, $this->option_group );
		}

		// Store the memory data to the database.
		add_action( 'shutdown', array( $this, 'sync_all_data' ) );
	}

	/**
	 * Get the field value.
	 *
	 * @param string                          $field    The field key.
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

			return $value;
		}

		return null;
	}

	/**
	 * Delete the field.
	 *
	 * @param string $field The field key.
	 *
	 * @return bool
	 */
	public function delete( $field ) {
		if ( isset( $this->data[ $field ] ) ) {
			unset( $this->data[ $field ] );

			return true;
		}

		return false;
	}

	/**
	 * Update the database option with stored data.
	 *
	 * @return void
	 */
	public function sync_all_data() {
		// Store all data into the database.
		update_option( $this->option_name, $this->data, false );
		wp_cache_delete( $this->option_name, $this->option_group );
	}
}
