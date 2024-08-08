<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Post Grid Load More Rest API
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   SQUAD_MODULES_VERSION
 */

namespace DiviSquad\Managers\RestRoutes\V1\Modules;

use DiviSquad\Base\Factories\RestRoute\Route;
use DiviSquad\Modules;
use DiviSquad\Utils\Polyfills\Str;
use DiviSquad\Utils\Sanitization;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use function esc_html__;
use function rest_ensure_response;
use function wp_unslash;

/**
 * Post Grid Load More Rest API class
 *
 * @package DiviSquad
 * @since   SQUAD_MODULES_VERSION
 */
class PostGrid extends Route {

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

		// Check if the request has a body.
		if ( empty( $request->get_json_params() ) ) {
			$response['code']    = 'rest_no_body_params';
			$response['message'] = esc_html__( 'No data provided.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 400 );
		}

		// Retrieve the request body parameters.
		$params = $request->get_json_params();
		if ( ! isset( $params['query_args'], $params['content'] ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to access this endpoint.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		// Sanitize the content and query parameters.
		$query_params = array_map( array( Sanitization::class, 'sanitize_array' ), wp_unslash( $params['query_args'] ) );

		// Get the posts per page and offset.
		$posts_per_page    = isset( $query_params['posts_per_page'] ) ? absint( $query_params['posts_per_page'] ) : 10;
		$post_query_offset = isset( $query_params['offset'] ) ? absint( $query_params['offset'] ) : $posts_per_page;

		// Update post offset for the next query.
		$query_params['list_post_offset'] = $post_query_offset;

		// Update the query parameters.
		$query_params['is_rest_query'] = 'on';

		// Assign the CPT Grid module to the variable.
		$post_grid_module = new Modules\PostGrid\PostGrid();
		$post_grid_module->squad_init_custom_hooks();

		// Get all posts.
		$posts = Modules\PostGrid\PostGrid::squad_get_posts_html( $query_params, wp_kses_post( $params['content'] ) );
		if ( empty( $posts ) ) {
			$response['code']    = 'rest_no_data';
			$response['message'] = esc_html__( 'No data found.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 404 );
		}

		return rest_ensure_response(
			array(
				'type'   => 'success',
				'offset' => $post_query_offset,
				'html'   => Str::remove_new_lines_and_tabs( $posts ),
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
			'/module/post-grid/load-more' => array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'get_available_posts' ),
					'permission_callback' => '__return_true',
				),
			),
		);
	}
}
