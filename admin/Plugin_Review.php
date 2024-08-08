<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Plugin Review
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

use function DiviSquad\divi_squad;

/**
 * Plugin Review Class
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 */
class Plugin_Review {
	/**
	 * How Long timeout until first banner shown.
	 *
	 * @var int
	 */
	private $first_time_show = 3;

	/**
	 * How Long timeout after first banner shown.
	 *
	 * @var int
	 */
	private $another_time_show = 7;

	/**
	 * Init constructor.
	 */
	public function __construct() {
		add_filter( 'admin_body_class', array( $this, 'admin_classes' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'wp_ajax_divi_squad_notice_close', array( $this, 'close' ) );
		add_action( 'wp_ajax_divi_squad_notice_review', array( $this, 'review' ) );
		add_action( 'wp_ajax_divi_squad_notice_close_count', array( $this, 'notice_close_count' ) );
		add_action( 'wp_ajax_divi_squad_notice_ask_support', array( $this, 'ask_support_count' ) );

		// Set the initial options for the plugin review.
		$this->initial_option();
	}

	/**
	 * Initial Option.
	 */
	public function initial_option() {
		$memory     = divi_squad()->get_memory();
		$activation = $memory->get( 'activation_time' );
		$first_time = $this->get_second( $this->first_time_show );

		// Set review flag and time for the first time.
		if ( false === $memory->get( 'review_flag', false ) ) {
			$next_time = false === $activation ? time() : $activation;
			$memory->set( 'next_review_time', $next_time + $first_time );
			$memory->set( 'review_flag', false );
		}
	}

	/**
	 * Get Second by days.
	 *
	 * @param int $days Days Number.
	 *
	 * @return int
	 */
	public function get_second( $days ) {
		return $days * 24 * 60 * 60;
	}

	/**
	 * Close Button Clicked.
	 */
	public function close() {
		$next_time = time() + $this->get_second( $this->another_time_show );
		divi_squad()->get_memory()->set( 'next_review_time', $next_time );
	}

	/**
	 * Review Button Clicked.
	 */
	public function review() {
		divi_squad()->get_memory()->set( 'review_flag', true );
		divi_squad()->get_memory()->set( 'review_status', 'received' );
	}

	/**
	 * Count the close time for notice banner.
	 */
	public function notice_close_count() {
		$notice_close_count = divi_squad()->get_memory()->get( 'notice_close_count', 0 );
		divi_squad()->get_memory()->set( 'notice_close_count', (int) $notice_close_count + 1 );
	}

	/**
	 * Count the ask for support time for notice banner.
	 */
	public function ask_support_count() {
		$ask_support_count = divi_squad()->get_memory()->get( 'ask_support_count', 0 );
		divi_squad()->get_memory()->set( 'ask_support_count', (int) $ask_support_count + 1 );
	}

	/**
	 * Check if we can render notice.
	 */
	public function can_render_notice() {
		$flag = divi_squad()->get_memory()->get( 'review_flag' );

		if ( $flag ) {
			return false;
		} else {
			$next_review_time = divi_squad()->get_memory()->get( 'next_review_time' );

			return time() > $next_review_time;
		}
	}

	/**
	 * Filters the CSS classes for the body tag in the admin.
	 *
	 * @param string $classes Space-separated list of CSS classes.
	 *
	 * @return string
	 * @since 1.2.5
	 */
	public function admin_classes( $classes ) {
		// Add a specific class to detect the banner page.
		if ( $this->can_render_notice() ) {
			$classes .= ' divi-squad-notice';
		}

		return $classes;
	}

	/**
	 * Show Notice.
	 */
	public function notice() {
		if ( $this->can_render_notice() ) {
			if ( file_exists( sprintf( '%1$s/templates/plugin-review.php', DISQ_DIR_PATH ) ) ) {
				load_template( sprintf( '%1$s/templates/plugin-review.php', DISQ_DIR_PATH ) );
			}
		}
	}
}