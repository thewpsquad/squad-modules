<?php
/**
 * Error Report Email Template
 *
 * This template generates the HTML content for error report emails sent by
 * the Divi Squad (lite & Pro) plugins. It displays detailed information about the error,
 * including the error message, stack trace, and system environment.
 *
 * @package DiviSquad\Managers\Emails
 * @since 3.1.7
 *
 * @var array $args {
 *     An array of arguments passed to the template.
 *
 *     @type int    $error_code      The error code.
 *     @type string $error_message   The error message.
 *     @type string $error_file      The file where the error occurred.
 *     @type int    $error_line      The line number where the error occurred.
 *     @type string $stack_trace     The full stack trace of the error.
 *     @type string $debug_log       The last 50 lines of the debug log.
 *     @type string $additional_info Any additional information provided.
 * }
 */

// Ensure that this file is not directly accessed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset='UTF-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1.0'>
	<title>Squad Error Report</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			line-height: 1.6;
			color: #333;
			max-width: 800px;
			margin: 0 auto;
			padding: 20px;
			background-color: #f5f5f5;
		}
		.container {
			background-color: #ffffff;
			border: 1px solid #ddd;
			border-radius: 5px;
			padding: 20px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		h1 {
			color: #e74c3c;
			border-bottom: 2px solid #e74c3c;
			padding-bottom: 10px;
		}
		.info-section {
			margin-bottom: 20px;
			background-color: #f9f9f9;
			border: 1px solid #eee;
			border-radius: 4px;
			padding: 15px;
		}
		.info-title {
			font-weight: bold;
			font-size: 18px;
			margin-bottom: 10px;
			color: #2c3e50;
		}
		.info-item {
			margin-bottom: 5px;
		}
		.info-label {
			font-weight: bold;
			color: #34495e;
		}
		pre {
			background-color: #f4f4f4;
			border: 1px solid #ddd;
			border-radius: 3px;
			padding: 10px;
			overflow-x: auto;
			font-size: 12px;
			line-height: 1.4;
		}
		.footer {
			margin-top: 20px;
			text-align: center;
			font-size: 12px;
			color: #777;
		}
	</style>
</head>
<body>
<div class='container'>
	<h1>Squad Error Report</h1>

	<div class='info-section'>
		<div class='info-title'>Error Details</div>
		<div class='info-item'>
			<span class='info-label'>Error Code:</span> <?php echo esc_html( $args['error_code'] ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">Error Message:</span> <?php echo esc_html( $args['error_message'] ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">File:</span> <?php echo esc_html( $args['error_file'] ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">Line:</span> <?php echo esc_html( $args['error_line'] ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">Timestamp:</span> <?php echo esc_html( current_time( 'mysql' ) ); ?>
		</div>
	</div>

	<div class="info-section">
		<div class="info-title">Stack Trace</div>
		<pre><?php echo esc_html( $args['stack_trace'] ); ?></pre>
	</div>

	<div class="info-section">
		<div class="info-title">Debug Information</div>
		<div class="info-item">
			<span class="info-label">PHP Version:</span> <?php echo esc_html( phpversion() ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">WordPress Version:</span> <?php echo esc_html( get_bloginfo( 'version' ) ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">Divi Squad Lite Version:</span> <?php echo esc_html( divi_squad()->get_version_dot() ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">Active Theme:</span> <?php echo esc_html( wp_get_theme()->get( 'Name' ) ); ?>
		</div>
		<div class="info-item">
			<span class="info-label">Active Plugins:</span>
			<span><?php echo esc_html( $args['active_plugins'] ); ?></span>
		</div>
	</div>

	<?php if ( ! empty( $args['debug_log'] ) ) : ?>
		<div class="info-section">
			<div class="info-title">Debug Log (Last 50 lines)</div>
			<pre><?php echo esc_html( $args['debug_log'] ); ?></pre>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $args['additional_info'] ) ) : ?>
		<div class="info-section">
			<div class="info-title">Additional Information</div>
			<pre><?php echo esc_html( $args['additional_info'] ); ?></pre>
		</div>
	<?php endif; ?>

	<p>This is an automated error report. Please investigate and take appropriate action.</p>

	<div class="footer">
		<p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> Divi Squad. All rights reserved.</p>
		<p>If you have any questions, please contact our support team at support@divisquad.com</p>
	</div>
</div>
</body>
</html>
