<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager\Rest_API_Routes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

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
class Whats_New {

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
	public function get_readme_file_data() {
		$readme_file_data = '';
		$readme_file      = DISQ_DIR_PATH . '/readme.txt';
		if ( is_readable( $readme_file ) ) {
			$readme_file_data = file_get_contents( DISQ_DIR_PATH . '/readme.txt' ); // phpcs:ignore
		}

		return rest_ensure_response(
			array(
				'code'   => 'success',
				'readme' => $readme_file_data,
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
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_readme_file_data' ),
				),
			),
		);
	}
}
