<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager\Rest_API_Routes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function DiviSquad\divi_squad;

/**
 * Rest API Routes for Extensions
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Extensions {

	/**
	 * Get active extensions list from database
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_active_extensions() {
		$memory            = divi_squad()->get_memory();
		$default_activates = $memory->get( 'default_active_extensions', array() );
		$current_activates = $memory->get( 'active_extensions' );

		if ( is_array( $current_activates ) ) {
			return rest_ensure_response( $current_activates );
		}

		return rest_ensure_response( $default_activates );
	}

	/**
	 * Get update extensions list from database
	 *
	 * @param WP_REST_Request $request The wp rest api request.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function update_active_extensions( $request ) {
		$memory = divi_squad()->get_memory();
		$memory->set( 'active_extensions', $request->get_json_params() );
		$memory->get( 'active_extension_version', divi_squad()->get_version() );

		return rest_ensure_response(
			array(
				'code'      => 'success',
				'message'   => 'The list of active extensions are updated.',
				'activates' => $memory->get( 'active_extensions', array() ),
			)
		);
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @param \DiviSquad\Manager\Extensions $extensions The extensions manager object.
	 *
	 * @return array
	 */
	public function get_routes( $extensions ) {
		return array(
			'/extensions'        => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $extensions, 'get_available_extensions' ),
				),
			),
			'/active_extensions' => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_active_extensions' ),
				),
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => array( $this, 'update_active_extensions' ),
				),
			),
		);
	}
}
