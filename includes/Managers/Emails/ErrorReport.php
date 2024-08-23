<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Error Report Manager
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.7
 */

namespace DiviSquad\Managers\Emails;

use DiviSquad\Utils\WP;
use WP_Error;
use Exception;

/**
 * Class ErrorReport
 *
 * Handles the creation and sending of error report emails for the Divi Squad Pro plugin.
 *
 * This class provides functionality to generate and send detailed error reports
 * via email. It includes information about the error, stack trace, and system
 * environment to aid in debugging and issue resolution.
 *
 * @package DiviSquad
 * @since   3.1.7
 */
class ErrorReport {
	/**
	 * Support email address
	 *
	 * @var string
	 */
	private $to = 'support@squadmodules.com';

	/**
	 * Data to be sent in the email.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Errors object.
	 *
	 * @var WP_Error
	 */
	private $errors;

	/**
	 * Result of the email sending.
	 *
	 * @var bool
	 */
	private $result = false;

	/**
	 * ErrorReport constructor
	 *
	 * Initializes a new instance of the ErrorReport class with the provided error data.
	 *
	 * @param array $data Error data to be sent in the email.
	 *
	 * @since 3.1.7
	 *
	 */
	public function __construct( array $data ) {
		$this->data   = $data;
		$this->errors = new WP_Error();
	}

	/**
	 * Send error report email.
	 *
	 * Validates the error data, adds necessary email filters, sends the email,
	 * and then removes the filters. It also handles any errors that occur during
	 * the process.
	 *
	 * @return bool True if the email was sent successfully, false otherwise.
	 * @since 3.1.7
	 *
	 */
	public function send() {
		if ( ! $this->validate_data() ) {
			$this->log_error( 'Data validation failed' );
			return false;
		}

		$this->add_email_filters();

		$subject = $this->get_email_subject();
		$message = $this->get_email_message_html();
		$headers = $this->get_email_headers();

		$this->result = wp_mail( $this->to, $subject, $message, $headers );

		$this->remove_email_filters();

		if ( ! $this->result ) {
			$this->errors->add( 'send_failed', esc_html__( 'Failed to send error report email.', 'squad-modules-for-divi' ) );
			$this->log_error( 'Failed to send email: ' . print_r( $this->errors, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		} else {
			$this->log_success( 'Error report email sent successfully' );
		}

		return $this->result;
	}

	/**
	 * Validate the error report data.
	 *
	 * Checks if all required fields are present in the error data.
	 *
	 * @return bool True if all required fields are present, false otherwise.
	 * @since 3.1.7
	 *
	 */
	private function validate_data() {
		$required_fields = array( 'error_message', 'error_code', 'error_file', 'error_line' );

		foreach ( $required_fields as $field ) {
			if ( empty( $this->data[ $field ] ) ) {
				// translators: %s Error field name.
				$this->errors->add( $field, sprintf( esc_html__( '%s is required for error reporting.', 'squad-modules-for-divi' ), ucfirst( str_replace( '_', ' ', $field ) ) ) );
			}
		}

		return $this->errors->has_errors();
	}

	/**
	 * Add email-related filters.
	 *
	 * Adds filters for handling email failures and setting the content type.
	 *
	 * @since 3.1.7
	 */
	private function add_email_filters() {
		add_action( 'wp_mail_failed', array( $this, 'set_failure_errors' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
	}

	/**
	 * Remove email-related filters.
	 *
	 * Removes the filters that were added for handling email failures and setting the content type.
	 *
	 * @since 3.1.7
	 */
	private function remove_email_filters() {
		remove_action( 'wp_mail_failed', array( $this, 'set_failure_errors' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );
	}

	/**
	 * Get the email subject.
	 *
	 * Generates a subject line for the error report email based on the error code and message.
	 *
	 * @return string The generated email subject.
	 * @since 3.1.7
	 *
	 */
	private function get_email_subject() {
		return sprintf( '[Error Report] %s: %s', $this->data['error_code'], substr( $this->data['error_message'], 0, 50 ) );
	}

	/**
	 * Get the email headers.
	 *
	 * Generates the headers for the error report email.
	 *
	 * @return array An array of email headers.
	 * @since 3.1.7
	 *
	 */
	private function get_email_headers() {
		return array(
			'X-Mailer-Type: SquadModules/Lite/ErrorReport',
			'Content-Type: text/html',
		);
	}

	/**
	 * Get the HTML-prepared message for email.
	 *
	 * Loads the error report email template and populates it with the error data.
	 *
	 * @return string The HTML content of the email message.
	 * @since 3.1.7
	 *
	 */
	private function get_email_message_html() {
		ob_start();
		$template_path = divi_squad()->get_template_path() . '/emails/error-report.php';
		if ( file_exists( $template_path ) ) {
			load_template( $template_path, true, $this->data );
		} else {
			esc_html_e( 'Error report email template not found.', 'squad-modules-for-divi' );
		}

		return ob_get_clean();
	}

	/**
	 * Set the HTML content type for the email.
	 *
	 * @return string The HTML content type.
	 * @since 3.1.7
	 *
	 */
	public function set_html_content_type() {
		return 'text/html';
	}

	/**
	 * Collect the PHPMailer\PHPMailer\Exception on sending the email.
	 *
	 * Merges any errors that occurred during the email sending process into the errors object.
	 *
	 * @param WP_Error $error A WP_Error object with the PHPMailer\PHPMailer\Exception message.
	 *
	 * @since 3.1.7
	 *
	 */
	public function set_failure_errors( $error ) {
		$this->errors->merge_from( $error );
	}

	/**
	 * Get the errors object.
	 *
	 * @return WP_Error The errors object containing any errors that occurred during the process.
	 * @since 3.1.7
	 *
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Get the result of the email sending.
	 *
	 * @return bool True if the email was sent successfully, false otherwise.
	 * @since 3.1.7
	 *
	 */
	public function get_result() {
		return $this->result;
	}

	/**
	 * Log an error message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message The error message to log.
	 */
	private function log_error( $message ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'Squad Error Report: ' . $message );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Log a success message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message The success message to log.
	 */
	private function log_success( $message ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'Squad Error Report: ' . $message );
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Static helper method to quickly send an error report.
	 *
	 * This method simplifies the process of sending an error report by
	 * automatically creating an ErrorReport instance and sending the email.
	 *
	 * @param Exception $exception       The caught exception.
	 * @param array     $additional_data Additional data to include in the report.
	 *
	 * @return bool Whether the email was sent successfully.
	 * @since 3.1.7
	 *
	 */
	public static function quick_send( Exception $exception, array $additional_data = array() ) {
		$error_data = array_merge(
			array(
				'error_message'  => $exception->getMessage(),
				'error_code'     => $exception->getCode(),
				'error_file'     => $exception->getFile(),
				'error_line'     => $exception->getLine(),
				'stack_trace'    => $exception->getTraceAsString(),
				'debug_log'      => self::get_debug_log(),
				'active_plugins' => self::get_active_plugins(),
			),
			$additional_data
		);

		$error_report = new self( $error_data );
		$result       = $error_report->send();

		if ( ! $result ) {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
			error_log( 'Divi Squad Error Report: Failed to send error report via quick_send method. Errors: ' . print_r( $error_report->get_errors(), true ) );
			// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
		}

		return $result;
	}

	/**
	 * Get active plugins
	 *
	 * @return string
	 */
	private static function get_active_plugins() {
		$active_plugins = WP::get_active_plugins();
		return implode( ', ', array_column( $active_plugins, 'name' ) );
	}

	/**
	 * Get the debug log.
	 *
	 * Retrieves the last 50 lines of the WordPress debug log file.
	 *
	 * @return string The last 50 lines of the debug log or an empty string if the log is not accessible.
	 * @since 3.1.7
	 *
	 */
	private static function get_debug_log() {
		$debug_log = '';
		$log_file  = WP_CONTENT_DIR . '/debug.log';

		if ( file_exists( $log_file ) && is_readable( $log_file ) ) {
			$debug_log = file_get_contents( $log_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			// Get only the last 100 lines of the debug log
			$debug_log = implode( "\n", array_slice( explode( "\n", $debug_log ), - 100 ) );
		}

		return $debug_log;
	}
}
