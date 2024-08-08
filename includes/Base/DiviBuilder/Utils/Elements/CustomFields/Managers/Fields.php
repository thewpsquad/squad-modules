<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Custom Fields Query Manager
 *
 * This file contains the Fields class which handles the management
 * of custom fields in WordPress, including tracking, updating, and retrieving
 * custom field information across different post types.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\CustomFields\Managers;

use DiviSquad\Base\DiviBuilder\Utils\Elements\CustomFields\Manager;
use DiviSquad\Utils\Polyfills\Constant;

/**
 * Fields Class
 *
 * Manages custom fields across different post types in WordPress.
 * This class handles the creation and maintenance of a summary table
 * for custom fields, provides methods for updating and retrieving
 * custom field information, and integrates with a separate upgrader
 * for database structure management.
 *
 * @package DiviSquad
 * @since   3.1.0
 */
class Fields extends Manager {

	/**
	 * The name of the summary table in the database.
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Array of post types to track custom fields for.
	 *
	 * @var array
	 */
	private $tracked_post_types;

	/**
	 * Instance of the CustomFieldsUpgrader class.
	 *
	 * @var Upgraders
	 */
	private $upgrader;

	/**
	 * Version of the current table structure.
	 *
	 * @var string
	 */
	private $table_version = '1.0';

	/**
	 * Constructor.
	 *
	 * Initializes the Fields class with specified post types to track.
	 *
	 * @since 3.1.0
	 *
	 * @param array $post_types Array of post types to track custom fields for.
	 */
	public function __construct( $post_types = array( 'post' ) ) {
		$this->tracked_post_types = $post_types;
		$this->upgrader           = new Upgraders( $this->table_name );

		parent::__construct( 'divi-squad-custom_fields', 'custom_field_keys' );
	}

	/**
	 * Initialize the manager
	 *
	 * Sets up action hooks for various WordPress events related to custom fields.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public function init() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'divi_squad_custom_fields';

		add_action( 'wp_loaded', array( $this, 'check_table_version' ) );
		add_action( 'added_post_meta', array( $this, 'update_summary' ), Constant::PHP_INT_MAX, 3 );
		add_action( 'updated_post_meta', array( $this, 'update_summary' ), Constant::PHP_INT_MAX, 3 );
		add_action( 'deleted_post_meta', array( $this, 'delete_from_summary' ), Constant::PHP_INT_MAX, 3 );
		add_action( 'after_switch_theme', array( $this, 'create_summary_table' ) );
		add_action( 'wp_initialize_site', array( $this, 'create_summary_table' ) );
	}

	/**
	 * Get data from the manager.
	 *
	 * @since 3.1.0
	 *
	 * @param array $args Optional. Arguments to modify the query.
	 * @return array The retrieved data.
	 */
	public function get_data( $args = array() ) {
		$defaults = array(
			'post_type' => 'post',
			'limit'     => 30,
		);
		$args     = wp_parse_args( $args, $defaults );

		return $this->get_custom_field_keys( $args['post_type'], $args['limit'] );
	}

	/**
	 * Clear the custom fields cache.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public function clear_cache() {
		wp_cache_delete( 'custom_field_keys_' . md5( '' . 30 ), 'divi-squad-custom_fields' );
		foreach ( $this->tracked_post_types as $post_type ) {
			wp_cache_delete( 'custom_field_keys_' . md5( $post_type . 30 ), 'divi-squad-custom_fields' );
		}
	}

	/**
	 * Run database upgrades using the Upgrader.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public function run_upgrades() {
		$this->upgrader->run_upgrades();
	}

	/**
	 * Check if the table needs to be created or updated.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public function check_table_version() {
		$installed_version = divi_squad()->memory->get( 'custom_fields_table_version' );

		if ( $installed_version !== $this->table_version ) {
			$this->create_summary_table();
			divi_squad()->memory->set( 'custom_fields_table_version', $this->table_version );
		}

		add_action( 'shutdown', array( $this, 'populate_summary_table' ) );
	}

	/**
	 * Create the summary table in the database.
	 *
	 * This method creates the custom fields summary table if it doesn't exist.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public function create_summary_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $this->table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            meta_key varchar(255) NOT NULL,
            post_type varchar(20) NOT NULL,
            last_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY meta_key_post_type (meta_key, post_type)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Populate the summary table with initial data.
	 *
	 * This method populates the summary table with existing custom field data.
	 * It uses caching to prevent unnecessary database queries on each page load.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public function populate_summary_table() {
		global $wpdb;

		$cache_key = 'divi_squad_populate_summary_table';
		$populated = wp_cache_get( $cache_key, 'divi-squad-custom_fields' );

		if ( false === $populated ) {
			$placeholders           = array_fill( 0, count( $this->tracked_post_types ), '%s' );
			$post_types_placeholder = implode( ', ', $placeholders );

            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$query = $wpdb->prepare(
				"INSERT INTO {$this->table_name} (meta_key, post_type)
                SELECT DISTINCT pm.meta_key, p.post_type
                FROM {$wpdb->postmeta} pm
                INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                WHERE p.post_type IN ($post_types_placeholder)
                AND pm.meta_key NOT LIKE %s
                AND NOT EXISTS (
                    SELECT 1
                    FROM {$wpdb->postmeta} pm2
                    INNER JOIN {$wpdb->posts} p2 ON p2.ID = pm2.post_id
                    WHERE pm2.meta_key = CONCAT('_', pm.meta_key)
                    AND p2.post_type = p.post_type
                )
                ON DUPLICATE KEY UPDATE last_updated = CURRENT_TIMESTAMP",
				array_merge(
					$this->tracked_post_types,
					array( $wpdb->esc_like( '_' ) . '%' )
				)
			);
            // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
			wp_cache_set( $cache_key, true, 'divi-squad-custom_fields', 3600 );
		}
	}


	/**
	 * Update the summary table when postmeta is added or updated.
	 *
	 * This method checks for the existence of an underscore version of the meta key
	 * and updates the summary table accordingly. It uses caching to reduce database queries.
	 *
	 * @since 3.1.0
	 *
	 * @param int    $meta_id    ID of the metadata field.
	 * @param int    $object_id  ID of the object metadata is for.
	 * @param string $meta_key   Metadata key.
	 * @return void
	 */
	public function update_summary( $meta_id, $object_id, $meta_key ) {
		global $wpdb;

		if ( 0 === strpos( $meta_key, '_' ) ) {
			return;
		}

		$post_type = get_post_type( $object_id );
		if ( ! in_array( $post_type, $this->tracked_post_types, true ) ) {
			return;
		}

		$cache_key              = 'divi_squad_has_underscore_version_' . md5( $meta_key );
		$has_underscore_version = wp_cache_get( $cache_key, 'divi-squad-custom_fields' );

		if ( false === $has_underscore_version ) {
			$has_underscore_version = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->prepare(
					"SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s",
					'_' . $meta_key
				)
			);
			wp_cache_set( $cache_key, $has_underscore_version, 'divi-squad-custom_fields', 3600 );
		}

		if ( $has_underscore_version ) {
			$wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$this->table_name,
				array(
					'meta_key'  => $meta_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'post_type' => $post_type,
				)
			);
		} else {
			$wpdb->replace( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$this->table_name,
				array(
					'meta_key'  => $meta_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'post_type' => $post_type,
				),
				array( '%s', '%s' )
			);
		}

		$this->clear_cache();
	}


	/**
	 * Update the summary table when postmeta is deleted.
	 *
	 * This method removes the corresponding entry from the summary table
	 * when a post meta is deleted.
	 *
	 * @since 3.1.0
	 *
	 * @param string[] $meta_ids  An array of metadata entry IDs to delete.
	 * @param int      $object_id ID of the object metadata is for.
	 * @param string   $meta_key  Metadata key.
	 * @return void
	 */
	public function delete_from_summary( $meta_ids, $object_id, $meta_key ) {
		global $wpdb;

		if ( 0 === strpos( $meta_key, '_' ) ) {
			return;
		}

		$post_type = get_post_type( $object_id );

		$wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$this->table_name,
			array(
				'meta_key'  => $meta_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'post_type' => $post_type,
			),
			array( '%s', '%s' )
		);

		$this->clear_cache();
	}


	/**
	 * Get custom field keys, optionally filtered by post type.
	 *
	 * This method retrieves custom field keys from the database, filtered by post type
	 * and limited to a specified number of results. It uses caching to improve performance.
	 *
	 * @since 3.1.0
	 *
	 * @param string $post_type Optional. Post type to filter by. Default 'post'.
	 * @param int    $limit     Optional. Number of results to return. Default 30.
	 * @return array            Array of custom field keys.
	 */
	private function get_custom_field_keys( $post_type = 'post', $limit = 30 ) {
		$cache_key = 'divi_squad_custom_field_keys_' . md5( $post_type . $limit );

		return $this->get_cached_data(
			$cache_key,
			function () use ( $post_type, $limit ) {
				global $wpdb;

				return $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->prepare(
						"SELECT DISTINCT meta_key FROM {$this->table_name} WHERE post_type = %s ORDER BY meta_key LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
						$post_type,
						$limit
					)
				);
			},
			3600
		);
	}
}
