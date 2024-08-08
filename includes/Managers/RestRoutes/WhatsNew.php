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
class WhatsNew extends RouteCore {

	/**
	 * Remind the user for review the plugin.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_readme_file_data() {
		global $wp_filesystem;

		if ( ! isset( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$local_file = DIVI_SQUAD_DIR_PATH . 'changelog.txt';
		if ( $wp_filesystem->exists( $local_file ) ) {
			$content = $wp_filesystem->get_contents( $local_file );

			return rest_ensure_response(
				array(
					'code'   => 'success',
					'readme' => $content,
				)
			);
		}

		return new WP_REST_Response( array( 'code' => 'error' ), '404' );
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
