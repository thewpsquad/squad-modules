<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Rest API Routes for Modules
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Managers\RestRoutes\V1;

use DiviSquad\Base\Factories\RestRoute\Route;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use function current_user_can;
use function divi_squad;
use function esc_html__;
use function rest_ensure_response;

/**
 * Rest API Routes for Modules
 *
 * @package DiviSquad
 * @since   1.0.0
 */
class Modules extends Route {

	/**
	 * Get registered modules list.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_available_modules() {
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

		return rest_ensure_response( divi_squad()->modules->get_all_modules_with_locked() );
	}

	/**
	 * Get active modules list from database.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_active_modules() {
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

		// Check if the request has a body.
		if ( empty( $request->get_json_params() ) ) {
			$response['code']    = 'rest_no_body_params';
			$response['message'] = esc_html__( 'No data provided.', 'squad-modules-for-divi' );
			return new WP_REST_Response( $response, 400 );
		}

		// Collect active modules.
		$active_modules = $request->get_json_params();
		$active_modules = array_map( '\sanitize_text_field', $active_modules );

		$modules = divi_squad()->modules->get_registered_list();
		$modules = array_column( $modules, 'name' );

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
					'methods'  => 'GET',
					'callback' => array( $this, 'get_available_modules' ),
				),
			),
			'/modules/active'   => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'get_active_modules' ),
				),
				array(
					'methods'  => 'POST',
					'callback' => array( $this, 'update_active_modules' ),
				),
			),
			'/modules/inactive' => array(
				array(
					'methods'  => 'GET',
					'callback' => array( $this, 'get_inactive_modules' ),
				),
			),
		);
	}
}
