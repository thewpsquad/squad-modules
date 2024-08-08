<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Rest API Routes for What's New
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Managers\RestRoutes\V1\WhatsNew;

use DiviSquad\Base\Factories\RestRoute\Route;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Response;
use function current_user_can;
use function divi_squad;
use function esc_html__;
use function rest_ensure_response;

/**
 * Rest API Routes for What's New
 *
 * @package DiviSquad
 * @since   1.0.0
 */
class Changelog extends Route {

	/**
	 * Remind the user for review the plugin.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_readme_file_data() {
		global $wp_filesystem;

		// Default response for rest api.
		$response = array(
			'success' => false,
			'code'    => 'error',
			'message' => esc_html__( 'Something went wrong', 'squad-modules-for-divi' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$response['code']    = 'rest_forbidden';
			$response['message'] = esc_html__( 'You do not have permission to access this endpoint..', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 403 );
		}

		if ( ! isset( $wp_filesystem ) ) {
			require_once divi_squad()->get_wp_path() . '/wp-admin/includes/file.php';
			\WP_Filesystem();
		}

		// Check if the file exists.
		$local_file = divi_squad()->get_path( '/changelog.txt' );
		if ( ! $wp_filesystem->exists( $local_file ) ) {
			$response['code']    = 'rest_file_read_error';
			$response['message'] = esc_html__( 'File not found.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 404 );
		}

		// Get the file content.
		$content = $wp_filesystem->get_contents( $local_file );
		if ( empty( $content ) ) {
			$response['code']    = 'rest_file_empty';
			$response['message'] = esc_html__( 'File is empty.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 202 );
		}

		return rest_ensure_response(
			array(
				'code'   => 'success',
				'readme' => $content,
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
			'/whats-new' => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'get_readme_file_data' ),
				),
			),
		);
	}
}
