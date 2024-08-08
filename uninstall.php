<?php
/**
 * Clean up the DB when the plugin is uninstalled.
 * This is a magic file that is only loaded when the plugin is uninstalled.
 *
 * @since       1.0.0
 * @package     squad-modules-pro-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

// Stop if uninstall.php is directly accessed or loaded incorrectly.
defined( 'ABSPATH' ) || die();
defined( 'WP_UNINSTALL_PLUGIN' ) || die();

if ( function_exists( 'delete_option' ) ) {
	// Clean up options.
	$disq_options = array(
		'squad-modules-for-divi-settings',
		'disq-settings',
	);

	foreach ( $disq_options as $disq_option ) {
		delete_option( $disq_option );
	}
	// Clean up cron events.
}
