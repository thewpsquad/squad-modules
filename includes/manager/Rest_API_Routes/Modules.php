<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager\Rest_API_Routes;

use DiviSquad\Base\Memory;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function DiviSquad\divi_squad;

/**
 * Rest API Routes for Modules
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Modules {

	/**
	 * The instance of the current class.
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * The instance of the memory class.
	 *
	 * @var Memory
	 */
	protected static $memory;

	/**
	 * Get active modules list from database.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function get_active_modules() {
		$memory            = divi_squad()->get_memory();
		$default_activates = $memory->get( 'default_active_modules', array() );
		$current_activates = $memory->get( 'active_modules' );

		if ( is_array( $current_activates ) ) {
			return rest_ensure_response( $current_activates );
		}

		return rest_ensure_response( $default_activates );
	}

	/**
	 * Get update modules list from database.
	 *
	 * @param WP_REST_Request $request The wp rest api request.
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function update_active_modules( $request ) {
		$memory = divi_squad()->get_memory();
		$memory->set( 'active_modules', $request->get_json_params() );
		$memory->get( 'active_module_version', divi_squad()->get_version() );

		return rest_ensure_response(
			array(
				'code'      => 'success',
				'message'   => 'The list of active modules are updated.',
				'activates' => $memory->get( 'active_modules', array() ),
			)
		);
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @param \DiviSquad\Manager\Modules $modules The module manager object.
	 *
	 * @return array
	 */
	public function get_routes( $modules ) {
		return array(
			'/modules'        => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $modules, 'get_available_modules' ),
				),
			),
			'/active_modules' => array(
				array(
					'methods'  => WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_active_modules' ),
				),
				array(
					'methods'  => WP_REST_Server::CREATABLE,
					'callback' => array( $this, 'update_active_modules' ),
				),
			),
		);
	}
}
