<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers\RestRoutes;

use DiviSquad\Base\Factories\RestRoute\RouteCore;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function divi_squad;
use function esc_html__;

/**
 * Rest API Routes for Extension
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Extensions extends RouteCore {

	/**
	 * Get registered extensions list.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_available_extensions() {
		return rest_ensure_response( divi_squad()->extensions->get_registered_list() );
	}

	/**
	 * Get active extensions list from database
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_active_extensions() {
		$defaults = divi_squad()->extensions->get_default_registries();
		$current  = divi_squad()->memory->get( 'active_extensions' );

		if ( is_array( $current ) ) {
			return rest_ensure_response( $current );
		}

		return rest_ensure_response( array_column( $defaults, 'name' ) );
	}

	/**
	 * Get inactive extensions list from database.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_inactive_extensions() {
		$defaults = divi_squad()->extensions->get_inactive_registries();
		$current  = divi_squad()->memory->get( 'inactive_extensions' );

		if ( is_array( $current ) ) {
			return rest_ensure_response( $current );
		}

		return rest_ensure_response( array_column( $defaults, 'name' ) );
	}

	/**
	 * Get update extensions list from database
	 *
	 * @param WP_REST_Request $request The wp rest api request.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function update_active_extensions( $request ) {
		$extensions = divi_squad()->extensions->get_registered_list();
		$extensions = array_column( $extensions, 'name' );

		// Collect active extensions.
		$active_extensions = $request->get_json_params();
		$active_extensions = array_map( 'sanitize_text_field', $active_extensions );

		// Collect inactive extensions.
		$inactive_extensions = array();
		foreach ( $extensions as $extension ) {
			if ( ! in_array( $extension, $active_extensions, true ) ) {
				$inactive_extensions[] = $extension;
			}
		}

		// Update extensions including active and inactive.
		divi_squad()->memory->set( 'active_extensions', $active_extensions );
		divi_squad()->memory->set( 'inactive_extensions', $inactive_extensions );
		divi_squad()->memory->get( 'active_extension_version', divi_squad()->get_version() );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => esc_html__( 'The list of active extensions are updated.', 'squad-modules-for-divi' ),
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
			'/extensions'          => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_available_extensions' ),
				),
			),
			'/extensions/active'   => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_active_extensions' ),
				),
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => array( $this, 'update_active_extensions' ),
				),
			),
			'/extensions/inactive' => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_inactive_extensions' ),
				),
			),
		);
	}
}
