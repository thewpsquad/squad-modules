<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * REST API Routes for Plugin Activation Notice
 *
 * This file contains the ProActivation class which handles REST API endpoints
 * for managing the Plugin Activation Notice in Divi Squad.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */

namespace DiviSquad\Managers\RestRoutes\Version1\Notices;

use DiviSquad\Base\Factories\RestRoute\Route;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Pro Activation Notice REST API Handler
 *
 * Manages REST API endpoints for the Plugin Activation Notice,
 * including functionality to close the notice.
 *
 * @package DiviSquad
 * @since   2.0.0
 */
class ProActivation extends Route {

	/**
	 * Get available routes for the Pro Activation Notice API.
	 *
	 * @return array Available routes for the Pro Activation Notice API.
	 */
	public function get_routes() {
		return array(
			'/notice/pro-activation-close' => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'close_activation_notice' ),
					'permission_callback' => array( $this, 'check_admin_permissions' ),
				),
			),
		);
	}

	/**
	 * Check if the current user has admin permissions.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return bool|WP_Error True if the request has admin access, WP_Error object otherwise.
	 */
	public function check_admin_permissions( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'You do not have permissions to perform this action.', 'squad-modules-for-divi' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Close the activation notice.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object or WP_Error object.
	 */
	public function close_activation_notice( $request ) {
		if ( divi_squad()->memory->get( 'pro_activation_notice_close', false ) ) {
			return new WP_Error(
				'rest_notice_unavailable',
				esc_html__( 'The notice is not available.', 'squad-modules-for-divi' ),
				array( 'status' => 403 )
			);
		}

		divi_squad()->memory->set( 'pro_activation_notice_close', true );

		return rest_ensure_response(
			array(
				'code'    => 'success',
				'message' => 'closed',
			)
		);
	}
}
