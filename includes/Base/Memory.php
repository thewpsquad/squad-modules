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
use function wp_cache_get;
use function wp_cache_set;

/**
 * Memory class for managing Divi Squad plugin settings.
 *
 * This class provides a caching layer for WordPress options,
 * improving performance by reducing database queries and adding
 * advanced features for option management.
 *
 * @package DiviSquad\Base
 * @since 2.0.0
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
	 * Batch operation queue.
	 *
	 * @var array
	 */
	private $batch_queue = array();

	/**
	 * Memory constructor.
	 *
	 * @param string $prefix The prefix name for the plugin settings option.
	 */
	public function __construct( $prefix = 'divi-squad' ) {
		$this->option_group = $prefix;
		$this->option_name  = "{$prefix}_settings";
		$this->load_data();

		add_action( 'shutdown', array( $this, 'sync_data' ), 0 );
	}

	/**
	 * Migrate legacy options from 'name-settings' to 'name_settings' format.
	 *
	 * @return void
	 */
	public function migrate_legacy_options() {
		$legacy_option_name = str_replace( '_', '-', $this->option_name );
		$legacy_data        = get_option( $legacy_option_name );

		if ( false !== $legacy_data ) {
			// Legacy data exists, migrate it
			$this->data        = array_merge( $this->data, $legacy_data );
			$this->is_modified = true;

			// Save the migrated data
			$this->sync_data();

			// Delete the legacy option
			delete_option( $legacy_option_name );
		}
	}

	/**
	 * Load data from cache or database, including migration check.
	 *
	 * @return void
	 */
	private function load_data() {
		$this->data = wp_cache_get( $this->option_name, $this->option_group );

		if ( false === $this->data ) {
			$this->data = get_option( $this->option_name, array() );
			wp_cache_set( $this->option_name, $this->data, $this->option_group );

			// Check for legacy data and migrate if necessary
			$this->migrate_legacy_options();
		}
	}

	/**
	 * Get all stored options.
	 *
	 * @return array All stored options.
	 */
	public function get_all() {
		return $this->data;
	}

	/**
	 * Get the number of stored fields.
	 *
	 * @return int The number of stored fields.
	 */
	public function count() {
		return count( $this->data );
	}

	/**
	 * Check if a field exists.
	 *
	 * @param string $field The field key to check.
	 * @return bool True if the field exists, false otherwise.
	 */
	public function has( $field ) {
		return array_key_exists( $field, $this->data );
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
	 * Add a value to an array field.
	 *
	 * @param string $field The field key.
	 * @param mixed  $value The value to add.
	 * @throws \Exception If the field is not an array.
	 * @return void
	 */
	public function add_to_array( $field, $value ) {
		if ( ! isset( $this->data[ $field ] ) || ! is_array( $this->data[ $field ] ) ) {
			$this->data[ $field ] = array();
		}
		$this->data[ $field ][] = $value;
		$this->is_modified      = true;
	}

	/**
	 * Remove a value from an array field.
	 *
	 * @param string $field The field key.
	 * @param mixed  $value The value to remove.
	 * @throws \Exception If the field is not an array.
	 * @return bool True if the value was removed, false otherwise.
	 */
	public function remove_from_array( $field, $value ) {
		if ( ! isset( $this->data[ $field ] ) || ! is_array( $this->data[ $field ] ) ) {
			throw new \Exception( sprintf( 'Field %s is not an array.', esc_html( $field ) ) );
		}
		$key = array_search( $value, $this->data[ $field ], true );
		if ( false !== $key ) {
			unset( $this->data[ $field ][ $key ] );
			$this->is_modified = true;
			return true;
		}
		return false;
	}

	/**
	 * Queue a batch operation.
	 *
	 * @param string $operation The operation type ('set', 'update', 'delete').
	 * @param string $field     The field key.
	 * @param mixed  $value     The value (for 'set' and 'update' operations).
	 * @return void
	 */
	public function queue_batch_operation( $operation, $field, $value = null ) {
		$this->batch_queue[] = array(
			'operation' => $operation,
			'field'     => $field,
			'value'     => $value,
		);
	}

	/**
	 * Execute all queued batch operations.
	 *
	 * @return void
	 */
	public function execute_batch() {
		foreach ( $this->batch_queue as $operation ) {
			switch ( $operation['operation'] ) {
				case 'set':
					$this->set( $operation['field'], $operation['value'] );
					break;
				case 'update':
					$this->update( $operation['field'], $operation['value'] );
					break;
				case 'delete':
					$this->delete( $operation['field'] );
					break;
			}
		}
		$this->batch_queue = array();
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
			update_option( $this->option_name, $this->data );
			wp_cache_set( $this->option_name, $this->data, $this->option_group );
			$this->is_modified = false;
		}
	}

	/**
	 * Clear all stored data.
	 *
	 * @return void
	 */
	public function clear_all() {
		$this->data        = array();
		$this->is_modified = true;
	}
}


/**
 * Example:
 *
 * $memory = new DiviSquad\Base\Memory();
 *
 * // Get an option
 * $value = $memory->get('some_option', 'default_value');
 *
 * // Set an option
 * $memory->set('new_option', 'new_value');
 *
 * // Add to an array option
 * $memory->add_to_array('array_option', 'new_item');
 *
 * // Queue batch operations
 * $memory->queue_batch_operation('set', 'option1', 'value1');
 * $memory->queue_batch_operation('update', 'option2', 'value2');
 * $memory->queue_batch_operation('delete', 'option3');
 *
 * // Execute batch operations
 * $memory->execute_batch();
 */
