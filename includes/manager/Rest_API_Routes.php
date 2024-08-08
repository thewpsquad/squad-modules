<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use function DiviSquad\divi_squad;

/**
 * Rest_API_Routes
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Rest_API_Routes {

	/**
	 * Load rest route on init time.
	 *
	 * @return void
	 */
	public function register_all() {
		// Collect product slug and memory.
		$product_slug = divi_squad()->get_name();
		$modules      = divi_squad()->get_modules();
		$extensions   = divi_squad()->get_extensions();

		// Get all rest api loader object.
		$modules_rest_routes    = divi_squad()->get_modules_rest_api_routes();
		$extensions_rest_routes = divi_squad()->get_extensions_rest_api_routes();

		// Get all routes.
		$routes_modules    = $modules_rest_routes->get_routes( $modules );
		$routes_extensions = $extensions_rest_routes->get_routes( $extensions );

		$this->register_routes( $product_slug, array_merge( $routes_modules, $routes_extensions ) );
	}

	/**
	 * We register our routes for our endpoints.
	 *
	 * @param string $slug   The product slug.
	 * @param array  $routes The list of routes.
	 *
	 * @return void
	 */
	public function register_routes( $slug, $routes ) {
		if ( array() !== $routes ) {
			foreach ( $routes as $route => $args ) {
				$namespace = sprintf( '%1$s/v1', $slug );
				register_rest_route( $namespace, $route, $args );
			}
		}
	}
}
