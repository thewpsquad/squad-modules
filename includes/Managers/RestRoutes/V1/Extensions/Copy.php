<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Extension (Copy) Rest API
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   SQUAD_MODULES_VERSION
 */

namespace DiviSquad\Managers\RestRoutes\V1\Extensions;

use DiviSquad\Base\Factories\RestRoute\Route;
use DiviSquad\Extensions\Copy as CopyExtension;
use DiviSquad\Utils\Sanitization;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use function current_user_can;
use function esc_html__;
use function wp_unslash;

/**
 * Extension (Copy) Rest API class
 *
 * @package DiviSquad
 * @since   SQUAD_MODULES_VERSION
 */
class Copy extends Route {

	/**
	 * Get available posts
	 *
	 * @param WP_REST_Request $request The wp rest api request.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_available_posts( $request ) {
		// Default response for rest api.
		$response = array(
			'success' => false,
			'code'    => 'error',
			'message' => esc_html__( 'Something went wrong', 'squad-modules-for-divi' ),
		);

		if ( ! current_user_can( 'edit_posts' ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to access this endpoint.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		// Check if the request has a body.
		if ( empty( $request->get_json_params() ) ) {
			$response['code']    = 'rest_no_body_params';
			$response['message'] = esc_html__( 'No data provided.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 400 );
		}

		// Retrieve the request body parameters.
		$params = $request->get_json_params();
		if ( 0 === count( array_keys( $params ) ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to access this endpoint.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		try {
			// Sanitize the query options.
			$options = array_map( array( Sanitization::class, 'sanitize_array' ), wp_unslash( $params ) );

			// Duplicate the posts and return the response.
			CopyExtension::duplicate_the_post( $options );

			return rest_ensure_response(
				array(
					'success' => true,
					'message' => esc_html__( 'Post(s) duplicated successfully.', 'squad-modules-for-divi' ),
				)
			);
		} catch ( \Exception $e ) {
			$response['code']    = 'rest_error';
			$response['message'] = $e->getMessage();
			return new WP_REST_Response( $response, 400 );
		}
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @return array
	 */
	public function get_routes() {
		return array(
			'/extension/copy/duplicate-post' => array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'get_available_posts' ),
					'permission_callback' => '__return_true',
				),
			),
		);
	}
}
