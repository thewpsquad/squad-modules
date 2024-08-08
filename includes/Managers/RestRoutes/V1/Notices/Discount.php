<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Rest API Routes for Welcome 60% Discount Notice
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
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
 * Welcome 60% Discount Notice class
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Discount extends Route {

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

		if ( divi_squad()->memory->get( 'beta_campaign_notice_close', false ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'The discount is not available.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		divi_squad()->memory->set( 'beta_campaign_notice_close', true );
		divi_squad()->memory->set( 'beta_campaign_status', 'received' );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'received',
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
			'/notice/discount/done' => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'make_it_done' ),
				),
			),
		);
	}
}
