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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Utils\Helper;
use function add_action;
use function add_filter;
use function DiviSquad\divi_squad;
use function load_template;

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
	private $first_time_show = 7;

	/**
	 * Init constructor.
	 */
	public function __construct() {
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );

		// Set the initial options for the plugin review.
		$this->initial_option();
	}

	/**
	 * Initial Option.
	 */
	public function initial_option() {
		$memory = divi_squad()->get_memory();

		// Set review flag and time for the first time.
		if ( empty( $memory->get( 'review_flag' ) ) && empty( $memory->get( 'next_review_time' ) ) ) {
			// Calculate estimated next review time.
			$activation = $memory->get( 'activation_time' );
			$first_time = Helper::get_second( $this->first_time_show );
			$next_time  = ! empty( $activation ) ? $activation : time();

			// Update the database for next review.
			$memory->set( 'review_flag', false );
			$memory->set( 'next_review_time', $next_time + $first_time );
		}
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
	public function admin_body_class( $classes ) {
		// Add a specific class to detect the banner page.
		if ( $this->can_render_notice() ) {
			$classes .= ' divi-squad-notice';
		}

		return $classes;
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function get_template() {
		return sprintf( '%1$s/templates/plugin-review.php', DISQ_DIR_PATH );
	}

	/**
	 * Show Notice.
	 */
	public function admin_notice() {
		if ( $this->can_render_notice() && file_exists( $this->get_template() ) ) {
			load_template( $this->get_template() );
		}
	}
}
