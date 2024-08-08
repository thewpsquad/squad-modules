<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The WordPress integration helper
 *
 * @package DiviSquad\Integrations
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Integrations;

use DiviSquad\Utils\Divi;
use DiviSquad\Utils\Singleton;
use function version_compare;

/**
 * Define integration helper functionalities for this plugin.
 *
 * @package DiviSquad\Integrations
 * @since   1.0.0
 */
class WP {

	use Singleton;

	/**
	 * The plugin options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Checks compatibility with the current version.
	 *
	 * @param string $required       Minimum required version.
	 * @param string $target_version The current version.
	 *
	 * @return bool True if a required version is compatible or empty, false if not.
	 * @since 1.2.0
	 * @deprecated 1.2.3
	 */
	public static function is_version_compatible( $required, $target_version ) {
		return self::version_compare( $required, $target_version );
	}

	/**
	 * Checks compatibility with the current version.
	 *
	 * @param string $required       Minimum required version.
	 * @param string $target_version The current version.
	 *
	 * @return bool True if a required version is compatible or empty, false if not.
	 * @since 1.2.3
	 */
	public static function version_compare( $required, $target_version ) {
		return empty( $required ) || empty( $target_version ) || version_compare( $target_version, $required, '>=' );
	}

	/**
	 * Set the plugin options.
	 *
	 * @param array $options The plugin options.
	 */
	public function set_options( $options ) {
		$this->options = $options;
	}

	/**
	 * The journey of a thousand miles starts here.
	 *
	 * @return bool Some voids are not really void, you have to explore to figure out why not!
	 */
	public function let_the_journey_start() {
		// Check for the required PHP version.
		if ( isset( $this->options['RequiresPHP'] ) && ! self::version_compare( $this->options['RequiresPHP'], PHP_VERSION ) ) {
			add_action( 'admin_notices', array( $this, 'required_php_version_missing_notice' ) );

			return false;
		}

		/**
		 * Check for the required WordPress version.
		 * If the current WordPress version is less than the required version, then disable the plugin.
		 * The plugin will not be activated if the current WordPress version is less than the required version.
		 *
		 * @since 1.2.0
		 */
		if ( isset( $this->options['RequiresWP'] ) && ! self::version_compare( $this->options['RequiresWP'], get_bloginfo( 'version' ) ) ) {
			add_action( 'admin_notices', array( $this, 'required_wordpress_version_missing_notice' ) );

			return false;
		}

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
