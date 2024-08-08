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

use function version_compare;

/**
 * Define integration helper functionalities for this plugin.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class WP {

	/**
	 * Checks compatibility with the current version.
	 *
	 * @param string $required       Minimum required version.
	 * @param string $target_version The current version.
	 *
	 * @return bool True if a required version is compatible or empty, false if not.
	 * @since 1.2.0
	 *
	 */
	public static function is_version_compatible( $required, $target_version ) {
		return empty( $required ) || version_compare( $target_version, $required, '>=' );
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
		if ( ! $this->is_version_compatible( DISQ_MINIMUM_PHP_VERSION, PHP_VERSION ) ) {
			return add_action( 'admin_notices', array( $this, 'required_php_version_missing_notice' ) );
		}

		// Check for the required WordPress version.
		if ( ! $this->is_version_compatible( DISQ_MINIMUM_WP_VERSION, get_bloginfo( 'version' ) ) ) {
			return add_action( 'admin_notices', array( $this, 'required_wordpress_version_missing_notice' ) );
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
				esc_html__( '%1$s requires "%2$s" version %3$s or greater.', 'squad-modules-for-divi' ),
				'<strong>' . esc_html__( 'Squad Modules for Divi Builder', 'squad-modules-for-divi' ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', 'squad-modules-for-divi' ) . '</strong>',
				esc_html( DISQ_MINIMUM_PHP_VERSION )
			)
		);
	}

	/**
	 * Admin notice for the required WordPress version.
	 *
	 * @return void
	 */
	public static function required_wordpress_version_missing_notice() {
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>',
			sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required WordPress version */
				esc_html__( '%1$s requires "%2$s" version %3$s or greater.', 'squad-modules-for-divi' ),
				'<strong>' . esc_html__( 'Squad Modules', 'squad-modules-for-divi' ) . '</strong>',
				'<strong>' . esc_html__( 'WordPress', 'squad-modules-for-divi' ) . '</strong>',
				esc_html( DISQ_MINIMUM_WP_VERSION )
			)
		);
	}
}
