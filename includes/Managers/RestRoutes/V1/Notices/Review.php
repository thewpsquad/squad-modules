<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Rest API Routes for Plugin Review
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Managers\RestRoutes\V1\Notices;

use DiviSquad\Base\Factories\RestRoute\Route;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Response;
use function current_user_can;
use function divi_squad;
use function esc_html__;
use function rest_ensure_response;

/**
 * Plugin Review Notice class
 *
 * @package DiviSquad
 * @since   1.0.0
 */
class Review extends Route {

	/**
	 * How Long timeout after first banner shown.
	 *
	 * @var int
	 */
	private $another_time_show = 7;

	/**
	 * Update the database when the user submits a review for the plugin.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function make_it_done() {
		// Default response for rest api.
		$response = array(
			'success' => false,
			'code'    => 'error',
			'message' => esc_html__( 'Something went wrong', 'squad-modules-for-divi' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to process current action.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		if ( divi_squad()->memory->get( 'review_flag', false ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'The review is not available.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

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
	 * Remind the user for review the plugin.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function remind_me_at_next_week() {
		// Default response for rest api.
		$response = array(
			'success' => false,
			'code'    => 'error',
			'message' => esc_html__( 'Something went wrong', 'squad-modules-for-divi' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to process current action.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		if ( ! empty( divi_squad()->memory->get( 'next_review_time' ) ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'The review is not available.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

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
	 * Count how many times the user closes the review notice.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function count_review_closing() {
		// Default response for rest api.
		$response = array(
			'success' => false,
			'code'    => 'error',
			'message' => esc_html__( 'Something went wrong', 'squad-modules-for-divi' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to process current action.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		$notice_close_count = divi_squad()->memory->get( 'notice_close_count', 0 );
		divi_squad()->memory->set( 'notice_close_count', absint( $notice_close_count ) + 1 );

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
	public function ask_for_support() {
		// Default response for rest api.
		$response = array(
			'success' => false,
			'code'    => 'error',
			'message' => esc_html__( 'Something went wrong', 'squad-modules-for-divi' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to process current action.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		$ask_support_count = divi_squad()->memory->get( 'ask_support_count', 0 );
		divi_squad()->memory->set( 'ask_support_count', absint( $ask_support_count ) + 1 );

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
			'/notice/review/done'        => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'make_it_done' ),
				),
			),
			'/notice/review/next-week'   => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'remind_me_at_next_week' ),
				),
			),
			'/notice/review/close-count' => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'count_review_closing' ),
				),
			),
			'/notice/review/ask-support' => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'ask_for_support' ),
				),
			),
		);
	}
}
