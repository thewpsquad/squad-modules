<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Class RestRoute
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */

namespace DiviSquad\Base\Factories;

use DiviSquad\Base\Factories\FactoryBase\Factory;
use DiviSquad\Utils\Singleton;

/**
 * Class RestRoute
 *
 * @package DiviSquad
 * @since   2.0.0
 */
final class RestRoute extends Factory {

	use Singleton;

	/**
	 * Store all registry
	 *
	 * @var RestRoute\RouteInterface[]
	 */
	private static $registries = array();

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	protected function init_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 0 );
	}

	/**
	 * Add a new route to the list of routes.
	 *
	 * @param string $route_class The class name of the route to add to the list. The class must implement the RouteInterface.
	 *
	 * @return bool
	 */
	public function add( $route_class ) {
		$route = new $route_class();

		if ( ! $route instanceof RestRoute\RouteInterface ) {
			return false;
		}

		$product_name   = $route->get_name();
		$rest_namespace = $route->get_namespace();
		$rest_routes    = $route->get_routes();

		// Add namespace.
		if ( ! isset( self::$registries[ $product_name ] ) ) {
			self::$registries[ $product_name ] = array(
				'namespace' => $rest_namespace,
				'routes'    => array(),
			);
		}

		// Add routes.
		if ( count( $rest_routes ) > 0 ) {
			foreach ( $rest_routes as $route => $args ) {
				self::$registries[ $product_name ]['routes'][ $route ] = $args;
			}
		}

		return true;
	}

	/**
	 * Registered all namespace.
	 *
	 * @param string $name Current product name.
	 *
	 * @return string
	 */
	public function get_namespace( $name ) {
		if ( ! empty( self::$registries ) && isset( self::$registries[ $name ] ) && ! empty( self::$registries[ $name ]['namespace'] ) ) {
			return self::$registries[ $name ]['namespace'];
		}

		return '';
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @return void
	 */
	public function register_routes() {
		if ( ! empty( self::$registries ) ) {
			foreach ( self::$registries as $router ) {
				$namespace = $router['namespace'];
				$routes    = $router['routes'];

				if ( ! empty( $routes ) ) {
					foreach ( $routes as $route => $args ) {
						foreach ( $args as $key => $single_route ) {
							if ( is_array( $single_route ) && ! isset( $single_route['permission_callback'] ) ) {
								$args[ $key ]['permission_callback'] = 'is_user_logged_in';
							}
						}

						// register route with its arguments.
						register_rest_route( $namespace, $route, $args );
					}
				}
			}
		}
	}

	/**
	 * Registered all routes.
	 *
	 * @param string $name Current product name.
	 *
	 * @return array
	 */
	public function get_registered_routes( $name ) {
		// Set initial value.
		$results = array();

		if ( ! empty( self::$registries ) && isset( self::$registries[ $name ] ) && ! empty( self::$registries[ $name ]['routes'] ) ) {
			$routes = self::$registries[ $name ]['routes'];

			foreach ( $routes as $route => $args ) {
				$route_name = str_replace( array( '_', '-' ), '/', $route );
				$route_name = explode( '/', $route_name );
				$route_name = array_map( 'ucfirst', $route_name );
				$route_name = implode( '', $route_name );

				$results[ $route_name ] = array(
					'root'    => $route,
					'methods' => array_column( $args, 'methods' ),
				);
			}
		}

		return $results;
	}
}
