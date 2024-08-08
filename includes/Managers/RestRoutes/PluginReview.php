<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers\RestRoutes;

use DiviSquad\Base\Factories\RestRoute\RouteCore;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Rest API Routes for Plugin Review
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class PluginReview extends RouteCore {

	/**
	 * How Long timeout after first banner shown.
	 *
	 * @var int
	 */
	private $another_time_show = 7;

	/**
	 * Remind the user for review the plugin.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function remind_next_week() {
		$next_time = time() + $this->another_time_show * DAY_IN_SECONDS;
		divi_squad()->memory->set( 'next_review_time', $next_time );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'ok',
			)
		);
	}

	/**
	 * Update the database when the user submits a review for the plugin.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function review_done() {
		divi_squad()->memory->set( 'review_flag', true );
		divi_squad()->memory->set( 'review_status', 'received' );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'received',
			)
		);
	}

	/**
	 * Count how many times the user closes the review notice.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function review_notice_close_count() {
		$notice_close_count = divi_squad()->memory->get( 'notice_close_count', 0 );
		divi_squad()->memory->set( 'notice_close_count', (int) $notice_close_count + 1 );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'closed',
			)
		);
	}

	/**
	 * Count how many times the user asks for support from the review notice.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function ask_support() {
		$ask_support_count = divi_squad()->memory->get( 'ask_support_count', 0 );
		divi_squad()->memory->set( 'ask_support_count', (int) $ask_support_count + 1 );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'redirect',
			)
		);
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @return array
	 */
	public function get_routes() {
		return array(
			'/review/next_week'          => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'remind_next_week' ),
				),
			),
			'/review/done'               => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'review_done' ),
				),
			),
			'/review/notice_close_count' => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'review_notice_close_count' ),
				),
			),
			'/review/ask_support'        => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'ask_support' ),
				),
			),
		);
	}
}
