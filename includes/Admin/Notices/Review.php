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

namespace DiviSquad\Admin\Notices;

use DiviSquad\Base\Factories\AdminNotice\NoticeCore;

/**
 * Plugin Review Class
 *
 * @since       1.2.3
 * @package     squad-modules-for-divi
 */
class Review extends NoticeCore {

	/**
	 * How Long timeout until first banner shown.
	 *
	 * @var int
	 */
	private $first_time_show = 7;

	/**
	 * Init constructor.
	 */
	public function __construct() {
		// Set the initial options for the plugin review.
		$this->initial_option();
	}

	/**
	 * Initial Option.
	 */
	public function initial_option() {
		// Set review flag and time for the first time.
		if ( empty( divi_squad()->memory->get( 'review_flag' ) ) && empty( divi_squad()->memory->get( 'next_review_time' ) ) ) {
			// Calculate estimated next review time.
			$activation = divi_squad()->memory->get( 'activation_time' );
			$first_time = $this->first_time_show * DAY_IN_SECONDS;
			$next_time  = ! empty( $activation ) ? $activation : time();

			// Update the database for next review.
			divi_squad()->memory->set( 'review_flag', false );
			divi_squad()->memory->set( 'next_review_time', $next_time + $first_time );
		}
	}

	/**
	 * Check if we can render notice.
	 */
	public function can_render_it() {
		$flag = divi_squad()->memory->get( 'review_flag' );

		if ( $flag ) {
			return false;
		} else {
			$next_review_time = divi_squad()->memory->get( 'next_review_time' );

			return time() > $next_review_time;
		}
	}

	/**
	 * Filters the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 * @since 1.2.5
	 */
	public function get_body_classes() {
		return ' divi-squad-notice';
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function get_template() {
		return sprintf( '%1$s/notices/review.php', DIVI_SQUAD_TEMPLATES_PATH );
	}
}
