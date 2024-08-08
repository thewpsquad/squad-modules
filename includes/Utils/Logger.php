<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Logger class for DiviSquad plugin.
 *
 * @package DiviSquad\Utils
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.0
 */

namespace DiviSquad\Utils;

use DiviSquad\Utils\Media\Filesystem;
use WP_Filesystem_Base;

/**
 * Logger class for DiviSquad plugin.
 *
 * Provides comprehensive logging capabilities with different log levels,
 * file and database logging options, and log rotation.
 *
 * @package DiviSquad
 * @since   3.1.0
 */
class Logger extends Filesystem {

	/**
	 * Log levels.
	 *
	 * @var array
	 */
	private $log_levels = array(
		'DEBUG'     => 100,
		'INFO'      => 200,
		'NOTICE'    => 250,
		'WARNING'   => 300,
		'ERROR'     => 400,
		'CRITICAL'  => 500,
		'ALERT'     => 550,
		'EMERGENCY' => 600,
	);

	/**
	 * Minimum log level to record.
	 *
	 * @var int
	 */
	private $min_log_level;

	/**
	 * Log file path.
	 *
	 * @var string
	 */
	private $log_file_path;

	/**
	 * Maximum log file size in bytes before rotation.
	 *
	 * @var int
	 */
	private $max_file_size;

	/**
	 * Number of log files to keep during rotation.
	 *
	 * @var int
	 */
	private $max_files;

	/**
	 * Whether to log to database.
	 *
	 * @var bool
	 */
	private $log_to_database;

	/**
	 * Database table name for logs.
	 *
	 * @var string
	 */
	private $db_table;

	/**
	 * The WP_Filesystem_Base instance.
	 *
	 * @var WP_Filesystem_Base
	 */
	private $wp_filesystem;

	/**
	 * Logger constructor.
	 *
	 * @param array $config Configuration options.
	 * @throws \Exception If configuration is invalid.
	 */
	public function __construct( $config = array() ) {
		$this->min_log_level   = isset( $config['min_log_level'] ) ? $config['min_log_level'] : $this->log_levels['DEBUG'];
		$this->log_file_path   = isset( $config['log_file_path'] ) ? $config['log_file_path'] : WP_CONTENT_DIR . '/divi-squad-logs/debug.log';
		$this->max_file_size   = isset( $config['max_file_size'] ) ? $config['max_file_size'] : 5 * 1024 * 1024; // 5 MB
		$this->max_files       = isset( $config['max_files'] ) ? $config['max_files'] : 5;
		$this->log_to_database = isset( $config['log_to_database'] ) ? $config['log_to_database'] : false;
		$this->db_table        = isset( $config['db_table'] ) ? $config['db_table'] : 'divi_squad_logs';

		$this->wp_filesystem = $this->get_wp_filesystem();
		$this->validate_config();
		$this->ensure_log_directory_exists();
	}

	/**
	 * Validate logger configuration.
	 *
	 * @throws \Exception If configuration is invalid.
	 */
	private function validate_config() {
		$log_dir = dirname( $this->log_file_path );
		if ( ! $this->wp_filesystem->is_writable( $log_dir ) ) {
			throw new \Exception( sprintf( 'Log directory %s is not writable', esc_html( $log_dir ) ) );
		}

		if ( $this->log_to_database && ! $this->is_database_table_ready() ) {
			throw new \Exception( sprintf( 'Database table %s is not ready for logging', esc_html( $this->db_table ) ) );
		}
	}

	/**
	 * Ensure log directory exists and is writable.
	 *
	 * @throws \Exception If unable to create or write to log directory.
	 */
	private function ensure_log_directory_exists() {
		$log_dir = dirname( $this->log_file_path );
		if ( ! $this->wp_filesystem->exists( $log_dir ) ) {
			if ( ! $this->wp_filesystem->mkdir( $log_dir ) ) {
				throw new \Exception( sprintf( 'Unable to create log directory: %s', esc_html( $log_dir ) ) );
			}
		}
	}

	/**
	 * Log a message.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function log( $level, $message, $context = array() ) {
		if ( ! isset( $this->log_levels[ $level ] ) || $this->log_levels[ $level ] < $this->min_log_level ) {
			return;
		}

		$log_entry = $this->format_log_entry( $level, $message, $context );

		$this->write_to_file( $log_entry );

		if ( $this->log_to_database ) {
			$this->write_to_database( $level, $message, $context );
		}
	}

	/**
	 * Format a log entry.
	 *
	 * @param string $level   Log level.
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 * @return string Formatted log entry.
	 */
	private function format_log_entry( $level, $message, $context ) {
		$timestamp      = current_time( 'Y-m-d H:i:s' );
		$context_string = ! empty( $context ) ? wp_json_encode( $context ) : '';
		return sprintf( "[%s] [%s] %s %s\n", $timestamp, $level, $message, $context_string );
	}

	/**
	 * Write log entry to file.
	 *
	 * @param string $log_entry Formatted log entry.
	 */
	private function write_to_file( $log_entry ) {
		if ( $this->wp_filesystem->exists( $this->log_file_path ) && $this->wp_filesystem->size( $this->log_file_path ) > $this->max_file_size ) {
			$this->rotate_log_files();
		}

		$this->wp_filesystem->put_contents( $this->log_file_path, $log_entry, FILE_APPEND );
	}

	/**
	 * Rotate log files.
	 */
	private function rotate_log_files() {
		for ( $i = $this->max_files - 1; $i > 0; $i-- ) {
			$old_file = $this->log_file_path . '.' . $i;
			$new_file = $this->log_file_path . '.' . ( $i + 1 );
			if ( $this->wp_filesystem->exists( $old_file ) ) {
				$this->wp_filesystem->move( $old_file, $new_file, true );
			}
		}
		$this->wp_filesystem->move( $this->log_file_path, $this->log_file_path . '.1', true );
	}

    /**
     * Check if database table is ready for logging.
     *
     * @return bool
     */
    private function is_database_table_ready() {
        global $wpdb;
        $table = $wpdb->prefix . $this->db_table;
        $cache_key = 'divi_squad_log_table_exists';
        $table_exists = wp_cache_get($cache_key);

        if (false === $table_exists) {
            $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table)) === $table; // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            wp_cache_set($cache_key, $table_exists ? 1 : 0, '', 3600); // Cache for 1 hour
        }

        return (bool) $table_exists;
    }

    /**
     * Write log entry to database.
     *
     * @param string $level   Log level.
     * @param string $message Log message.
     * @param array  $context Additional context data.
     */
    private function write_to_database($level, $message, $context) {
        global $wpdb;
        $table = $wpdb->prefix . $this->db_table;
        $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            $table,
            array(
                'level'      => $level,
                'message'    => $message,
                'context'    => wp_json_encode($context),
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%s', '%s')
        );

        // Clear the cache for get_logs_from_database
        wp_cache_delete('divi_squad_logs_' . md5(serialize(func_get_args())));
    }

	/**
     * Get all logs from the database.
     *
     * @param int $limit  Number of logs to retrieve.
     * @param int $offset Offset for pagination.
     * @return array
     */
    public function get_logs_from_database($limit = 100, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . $this->db_table;
        $cache_key = 'divi_squad_logs_' . md5(serialize(func_get_args()));
        $results = wp_cache_get($cache_key);

        if (false === $results) {
            $results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare(
                    "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $limit,
                    $offset
                ),
                ARRAY_A
            );
            wp_cache_set($cache_key, $results, '', 300); // Cache for 5 minutes
        }

        return $results;
    }

	/**
	 * Clear all logs from the database.
	 *
	 * @return int Number of rows affected.
	 */
	public function clear_database_logs() {
		global $wpdb;
		$table = $wpdb->prefix . $this->db_table;
		return $wpdb->query( "TRUNCATE TABLE $table" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Log a debug message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function debug( $message, $context = array() ) {
		$this->log( 'DEBUG', $message, $context );
	}

	/**
	 * Log an info message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function info( $message, $context = array() ) {
		$this->log( 'INFO', $message, $context );
	}

	/**
	 * Log a notice message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function notice( $message, $context = array() ) {
		$this->log( 'NOTICE', $message, $context );
	}

	/**
	 * Log a warning message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function warning( $message, $context = array() ) {
		$this->log( 'WARNING', $message, $context );
	}

	/**
	 * Log an error message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function error( $message, $context = array() ) {
		$this->log( 'ERROR', $message, $context );
	}

	/**
	 * Log a critical message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function critical( $message, $context = array() ) {
		$this->log( 'CRITICAL', $message, $context );
	}

	/**
	 * Log an alert message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function alert( $message, $context = array() ) {
		$this->log( 'ALERT', $message, $context );
	}

	/**
	 * Log an emergency message.
	 *
	 * @param string $message Log message.
	 * @param array  $context Additional context data.
	 */
	public function emergency( $message, $context = array() ) {
		$this->log( 'EMERGENCY', $message, $context );
	}

	/**
	 * Get current log file content.
	 *
	 * @return string
	 */
	public function get_log_file_content() {
		return $this->wp_filesystem->exists( $this->log_file_path ) ? $this->wp_filesystem->get_contents( $this->log_file_path ) : '';
	}

	/**
	 * Clear the current log file.
	 *
	 * @return bool True if the file was successfully cleared, false otherwise.
	 */
	public function clear_log_file() {
		return $this->wp_filesystem->put_contents( $this->log_file_path, '' );
	}
}
