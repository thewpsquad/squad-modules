<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Rest API Routes for Plugin Activation Notice
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
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
 * Plugin Activation Notice class.
 *
 * @package DiviSquad
 * @since   2.0.0
 */
class ProActivation extends Route {

	/**
	 * Count how many times the user closes the review notice.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function activation_notice_close() {
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

		if ( divi_squad()->memory->get( 'pro_activation_notice_close', false ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'The notice is not available.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		// Set the user meta to close the notice.
		divi_squad()->memory->set( 'pro_activation_notice_close', true );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'closed',
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
			'/notice/pro-activation-close' => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'activation_notice_close' ),
				),
			),
		);
	}
}
