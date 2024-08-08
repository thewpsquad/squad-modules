<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Plugin Review
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.2.3
 */

namespace DiviSquad\Managers\Notices;

use DiviSquad\Base\Factories\AdminNotice\Notice;
use DiviSquad\Managers\Links;
use function divi_squad;
use function esc_html__;

/**
 * Plugin Review Class
 *
 * @package DiviSquad
 * @since   1.2.3
 *
 * @ref essential-addons-for-elementor-lite/includes/Traits/Helper.php:551.
 */
class Review extends Notice {

	/**
	 * The notice id for the notice.
	 *
	 * @var string
	 */
	protected $notice_id = 'review';

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
		// Check if the review flag is set.
		if ( divi_squad()->memory->get( 'review_flag' ) ) {
			return false;
		}

		// Check if the review time is passed.
		return time() > absint( divi_squad()->memory->get( 'next_review_time' ) );
	}

	/**
	 * Filters the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 * @since 1.2.5
	 */
	public function get_body_classes() {
		return 'divi-squad-notice';
	}

	/**
	 * Get the template arguments
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function get_template_args() {
		// phpcs:disable
		/**
		 * The arguments to the template.
		 *
		 * title: 'Enjoying Divi Squad?',
		 * content: 'Please consider leaving a review to help us spread the word and boost our motivation.',
		 *
		 * action-buttons: {
		 *    left: [
		 *   {
		 *     link: 'https://wordpress.org/support/plugin/squad-modules-for-divi/reviews/?rate=5#new-post',
		 *     classes: 'button button-primary',
		 *     text: 'Leave a Review',
		 *   },
		 *   {
		 *     link: '#',
		 *     classes: 'divi-squad-notice-close',
		 *     text: 'Maybe Later',
		 *   },
		 *   {
		 *     link: '#',
		 *     classes: 'divi-squad-notice-already',
		 *     text: 'Never show again',
		 *   },
		 *   ],
		 *  right: [
		 *   {
		 *    link: 'https://squadmodules.com/contact/',
		 *    classes: 'button button-secondary',
		 *    text: 'Contact Support',
		 *   },
		 *   ],
		 * },
		 */
		// phpcs:enable
		return array(
			'wrapper_classes' => 'divi-squad-review-banner',
			'logo'            => 'logos/divi-squad-d-default.svg',
			'title'           => esc_html__( 'Loving Squad Modules Lite?', 'squad-modules-for-divi' ),
			'content'         => esc_html__( 'Please consider leaving a 5-star review to help us spread the word and boost our motivation.', 'squad-modules-for-divi' ),
			'action-buttons'  => array(
				'left'  => array(
					array(
						'link'    => Links::RATTING_URL,
						'classes' => 'button-primary divi-squad-notice-action-button',
						'text'    => esc_html__( 'Ok, you deserve it!', 'squad-modules-for-divi' ),
						'icon'    => 'dashicons-external',
					),
					array(
						'link'    => '#',
						'classes' => 'divi-squad-notice-close',
						'style'   => 'text-decoration: none;',
						'text'    => esc_html__( 'Maybe Later', 'squad-modules-for-divi' ),
						'icon'    => 'dashicons-calendar-alt',
					),
					array(
						'link'    => '#',
						'classes' => 'divi-squad-notice-already',
						'style'   => 'text-decoration: none;',
						'text'    => esc_html__( 'Already did it', 'squad-modules-for-divi' ),
						'icon'    => 'dashicons-dismiss',
					),
				),
				'right' => array(
					array(
						'link'     => Links::ISSUES_URL,
						'classes'  => 'support',
						'text'     => esc_html__( 'Help Needed? Create a Issue', 'squad-modules-for-divi' ),
						'icon_svg' => 'icons/question.svg',
					),
				),
			),
			'is_dismissible'  => true,
		);
	}
}
