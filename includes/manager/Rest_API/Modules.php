<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager\Rest_API;

use DiviSquad\Base\Memory;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Rest_API_Routes
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */
class Modules {

	/**
	 * The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The instance of the memory class.
	 *
	 * @var Memory
	 */
	private static $memory;

	/**
	 * The instance of the Modules class.
	 *
	 * @var \DiviSquad\Manager\Modules
	 */
	private static $modules;

	/**
	 * Get the instance of the current class.
	 *
	 * @param Memory                     $memory                        The memory object.
	 * @param \DiviSquad\Manager\Modules $modules   The module manager object.
	 *
	 * @return self
	 */
	public static function get_instance( $memory, $modules ) {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$memory   = $memory;
			self::$modules  = $modules;
		}

		return self::$instance;
	}

	/**
	 * Get active modules list from database.
	 *
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function get_active_modules() {
		$default_activates = self::$memory->get( 'default_active_modules', array() );
		$current_activates = self::$memory->get( 'active_modules', null );

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
	 * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
	 */
	public function update_active_modules( $request ) {
		self::$memory->set( 'active_modules', $request->get_json_params() );
		self::$memory->get( 'active_module_version', DISQ_VERSION );

		return rest_ensure_response( 'Active modules are updated' );
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @return array
	 */
	public function get_routes() {
		return array(
			'/modules'        => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( self::$modules, 'get_available_modules' ),
					'permission_callback' => '__return_true',
				),
			),
			'/active_modules' => array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_active_modules' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_active_modules' ),
					'permission_callback' => '__return_true',
				),
			),
		);
	}
}

