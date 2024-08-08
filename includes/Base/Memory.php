<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Memory class
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Base;

use function add_action;
use function get_option;
use function update_option;
use function wp_cache_delete;
use function wp_cache_get;
use function wp_cache_set;

/**
 * Memory class for managing plugin settings.
 *
 * This class provides a caching layer for WordPress options,
 * improving performance by reducing database queries.
 *
 * @package DiviSquad\Base
 * @since 1.0.0
 */
class Memory {
	/**
	 * The store of data (Option data).
	 *
	 * @var array
	 */
	private $data = array();

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
	 * Flag to track if data has been modified.
	 *
	 * @var bool
	 */
	private $is_modified = false;

	/**
	 * Memory constructor.
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 */
	public function __construct( $prefix = 'squad-core' ) {
		$this->option_group = $prefix;
		$this->option_name  = "{$prefix}-settings";
		$this->load_data();

		add_action( 'shutdown', array( $this, 'sync_data' ), 0 );
	}

	/**
	 * Load data from cache or database.
	 *
	 * @return void
	 */
	private function load_data() {
		// Get cache from memory.
		if ( wp_cache_get( $this->option_name, $this->option_group ) ) {
			$this->data = wp_cache_get( $this->option_name, $this->option_group );
		}

		// Get data from database if cache is empty.
		if ( empty( $this->data ) ) {
			$this->data = get_option( $this->option_name, array() );
			wp_cache_set( $this->option_name, $this->data, $this->option_group );
		}
	}

	/**
	 * Get the value of a field.
	 *
	 * @param string $field The field key.
	 * @param mixed  $default_value The default value if the field doesn't exist.
	 * @return mixed The field value or default if not found.
	 */
	public function get( $field, $default_value = null ) {
		return isset( $this->data[ $field ] ) ? $this->data[ $field ] : $default_value;
	}

	/**
	 * Set the value of a field.
	 *
	 * @param string $field The field key.
	 * @param mixed  $value The value to set.
	 * @return void
	 */
	public function set( $field, $value ) {
		if ( ! isset( $this->data[ $field ] ) || $this->data[ $field ] !== $value ) {
			$this->data[ $field ] = $value;
			$this->is_modified    = true;
		}
	}

	/**
	 * Update an existing field's value.
	 *
	 * @param string $field The field key.
	 * @param mixed  $value The new value.
	 * @return bool True if the field was updated, false if it doesn't exist.
	 */
	public function update( $field, $value ) {
		if ( array_key_exists( $field, $this->data ) ) {
			$this->set( $field, $value );
			return true;
		}
		return false;
	}

	/**
	 * Delete a field.
	 *
	 * @param string $field The field key to delete.
	 * @return bool True if the field was deleted, false if it doesn't exist.
	 */
	public function delete( $field ) {
		if ( array_key_exists( $field, $this->data ) ) {
			unset( $this->data[ $field ] );
			$this->is_modified = true;
			return true;
		}
		return false;
	}

	/**
	 * Sync modified data to the database and update cache.
	 *
	 * This method is hooked to the 'shutdown' action.
	 *
	 * @return void
	 */
	public function sync_data() {
		if ( $this->is_modified ) {
			update_option( $this->option_name, $this->data, false );
			wp_cache_set( $this->option_name, $this->data, $this->option_group );
			$this->is_modified = false;
		}
	}
}
