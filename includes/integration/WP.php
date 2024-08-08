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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use function version_compare;

/**
 * Define integration helper functionalities for this plugin.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class WP {

	/**
	 * The plugin options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param array $options The plugin options.
	 */
	public function __construct( $options ) {
		$this->options = $options;
	}

	/**
	 * Checks compatibility with the current version.
	 *
	 * @param string $required       Minimum required version.
	 * @param string $target_version The current version.
	 *
	 * @return bool True if a required version is compatible or empty, false if not.
	 * @since 1.2.0
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
		if ( ! $this->is_version_compatible( $this->options['Minimum_PHP'], PHP_VERSION ) ) {
			return add_action( 'admin_notices', array( $this, 'required_php_version_missing_notice' ) );
		}

		// Check for the required WordPress version.
		if ( ! $this->is_version_compatible( $this->options['Minimum_WP'], get_bloginfo( 'version' ) ) ) {
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
	public function required_php_version_missing_notice() {
		printf(
			'<div class="notice notice-error"><p>%1$s</p><p>%2$s</p></div>',
			sprintf(
			/* translators: 1: PHP version symbolic text */
				esc_html__( 'Your site is running an %1$s of PHP that is no longer supported. Please contact your web hosting provider to update your PHP version.', 'squad-modules-for-divi' ),
				'<strong>' . esc_html__( 'insecure version', 'squad-modules-for-divi' ) . '</strong>'
			),
			sprintf(
			/* translators: 1: Plugin name 2: Required WordPress version */
				esc_html__( '%1$s The %2$s plugin is disabled on your site until you fix the issue.', 'squad-modules-for-divi' ),
				'<strong>' . esc_html__( 'Note', 'squad-modules-for-divi' ) . ':</strong>',
				'<strong>' . esc_html__( 'Squad Modules Lite', 'squad-modules-for-divi' ) . ':</strong>'
			)
		);
	}

	/**
	 * Admin notice for the required WordPress version.
	 *
	 * @return void
	 */
	public function required_wordpress_version_missing_notice() {
		printf(
			'<div class="notice notice-error is-dismissible"><p>%1$s</p></div>',
			sprintf(
			/* translators: 1: Plugin name 2: Required WordPress version */
				esc_html__( 'The %1$s plugin is disabled because it requires WordPress "%2$s" or later.', 'squad-modules-for-divi' ),
				'<strong>' . esc_html__( 'Squad Modules Lite', 'squad-modules-for-divi' ) . '</strong>',
				esc_html( $this->options['Minimum_WP'] )
			)
		);
	}
}
