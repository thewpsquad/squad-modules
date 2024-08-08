<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use DiviSquad\Base\Memory;
use function DiviSquad\divi_squad;

/**
 * Rest_API_Routes
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */
class Rest_API_Routes {

	/**
	 * The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * The product slug.
	 *
	 * @var string
	 */
	private static $product_slug;

	/**
	 * The instance of the Memory class.
	 *
	 * @var Memory
	 */
	private static $memory;

	/**
	 * The list of routes.
	 *
	 * @var array
	 */
	private static $routes = array();

	/**
	 * Get the instance of the current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance     = new self();
			self::$memory       = divi_squad()->get_memory();
			self::$product_slug = divi_squad()->get_name();
		}

		return self::$instance;
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @return void
	 */
	public function register_routes() {
		if ( array() !== self::$routes ) {
			foreach ( self::$routes as $route => $args ) {
				register_rest_route(
					sprintf( '%1$s/v1', self::$product_slug ),
					$route,
					$args
				);
			}
		}
	}

	/**
	 * Load rest route on init time.
	 *
	 * @return void
	 */
	public function register_all() {
		// Collect required class object.
		$module = Modules::get_instance();

		// Get all routes.
		self::$routes = Rest_API_Routes\Modules::get_instance( self::$memory, $module )->get_routes();

		// Load on rest api init.
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}
}

