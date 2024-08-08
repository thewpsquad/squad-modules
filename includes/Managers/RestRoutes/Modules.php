<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers\RestRoutes;

use DiviSquad\Base\Factories\RestRoute\RouteCore;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function esc_html__;

/**
 * Rest API Routes for Modules
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Modules extends RouteCore {

	/**
	 * Get registered modules list.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_available_modules() {
		return rest_ensure_response( divi_squad()->modules->get_modules_with_extra() );
	}

	/**
	 * Get active modules list from database.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_active_modules() {
		$defaults = divi_squad()->modules->get_default_registries();
		$current  = divi_squad()->memory->get( 'active_modules' );

		if ( is_array( $current ) ) {
			return rest_ensure_response( $current );
		}

		return rest_ensure_response( array_column( $defaults, 'name' ) );
	}

	/**
	 * Get inactive modules list from database.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_inactive_modules() {
		$defaults = divi_squad()->modules->get_inactive_registries();
		$current  = divi_squad()->memory->get( 'inactive_modules' );

		if ( is_array( $current ) ) {
			return rest_ensure_response( $current );
		}

		return rest_ensure_response( array_column( $defaults, 'name' ) );
	}

	/**
	 * Get update active modules list from database.
	 *
	 * @param WP_REST_Request $request The wp rest api request.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function update_active_modules( $request ) {
		$modules = divi_squad()->modules->get_registered_list();
		$modules = array_column( $modules, 'name' );

		// Collect active modules.
		$active_modules = $request->get_json_params();
		$active_modules = array_map( 'sanitize_text_field', $active_modules );

		// Collect inactive modules.
		$inactive_modules = array();
		foreach ( $modules as $module ) {
			if ( ! in_array( $module, $active_modules, true ) ) {
				$inactive_modules[] = $module;
			}
		}

		// Update modules including active and inactive.
		divi_squad()->memory->set( 'active_modules', $active_modules );
		divi_squad()->memory->set( 'inactive_modules', $inactive_modules );
		divi_squad()->memory->get( 'active_module_version', divi_squad()->get_version() );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => esc_html__( 'The list of active modules are updated.', 'squad-modules-for-divi' ),
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
			'/modules'          => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_available_modules' ),
				),
			),
			'/modules/active'   => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_active_modules' ),
				),
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => array( $this, 'update_active_modules' ),
				),
			),
			'/modules/inactive' => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_inactive_modules' ),
				),
			),
		);
	}
}
