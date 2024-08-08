<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The WordPress integration helper
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

use function is_php_version_compatible;

/**
 * Define integration helper functionalities for this plugin.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class WP {

	/**
	 * The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The minimum version for PHP.
	 *
	 * @var string
	 */
	private static $php_min_version = '';

	/**
	 *  Get the instance of the current class.
	 *
	 * @param string|numeric|float $php The minimum version number of php.
	 *
	 * @return self
	 */
	public static function get_instance( $php ) {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			// version check.
			self::$php_min_version = $php;
		}

		return self::$instance;
	}

	/**
	 * The journey of a thousand miles starts here.
	 *
	 * @param callable $callback The callback function.
	 *
	 * @return bool Some voids are not really void, you have to explore to figure out why not!
	 */
	public function let_the_journey_start( callable $callback ) {
		// Check for the required PHP version.
		if ( ! is_php_version_compatible( self::$php_min_version ) ) {
			return add_action( 'admin_notices', array( self::$instance, 'required_php_version_missing_notice' ) );
		}

		// Load all features.
		$callback();

		return true;
	}

	/**
	 * Admin notice for the required php version.
	 *
	 * @return void
	 */
	public static function required_php_version_missing_notice() {
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
			sprintf(
				/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
				esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'squad-modules-for-divi' ),
				'<strong>' . esc_html__( 'Divi Squad', 'squad-modules-for-divi' ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', 'squad-modules-for-divi' ) . '</strong>',
				esc_html( self::$php_min_version )
			)
		);
	}

}
