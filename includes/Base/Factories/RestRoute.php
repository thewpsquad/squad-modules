<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories;

use DiviSquad\Utils\Singleton;

final class RestRoute {

	use Singleton;

	/**
	 * Store all router
	 *
	 * @var RestRoute\RouteInterface[]
	 */
	private static $routers = array();

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function add( $route_class ) {
		$route = new $route_class();

		if ( ! $route instanceof RestRoute\RouteInterface ) {
			return false;
		}

		$product_name   = $route->get_name();
		$rest_namespace = $route->get_namespace();
		$rest_routes    = $route->get_routes();

		// Add namespace.
		if ( ! isset( self::$routers[ $product_name ] ) ) {
			self::$routers[ $product_name ] = array(
				'namespace' => $rest_namespace,
				'routes'    => array(),
			);
		}

		// Add routes.
		if ( is_array( $rest_routes ) && count( $rest_routes ) > 0 ) {
			foreach ( $rest_routes as $route => $args ) {
				self::$routers[ $product_name ]['routes'][ $route ] = $args;
			}
		}

		return true;
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @return void
	 */
	public function register_routes() {
		if ( ! empty( self::$routers ) ) {
			foreach ( self::$routers as $router ) {
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
	 * Registered all namespace.
	 *
	 * @param string $name Current product name.
	 *
	 * @return string
	 */
	public function get_namespace( $name ) {
		if ( ! empty( self::$routers ) && isset( self::$routers[ $name ] ) && ! empty( self::$routers[ $name ]['namespace'] ) ) {
			return self::$routers[ $name ]['namespace'];
		}

		return '';
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

		if ( ! empty( self::$routers ) && isset( self::$routers[ $name ] ) && ! empty( self::$routers[ $name ]['routes'] ) ) {
			$routes = self::$routers[ $name ]['routes'];

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
