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
class ProActivation extends RouteCore {

	/**
	 * Count how many times the user closes the review notice.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function activation_notice_close() {
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
			'/pro/activation_notice_close' => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'activation_notice_close' ),
				),
			),
		);
	}
}
