<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Abstract class representing the Route.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */

namespace DiviSquad\Base\Factories\RestRoute;

/**
 * Abstract class representing the Route.
 *
 * @package DiviSquad
 * @since   2.0.0
 */
abstract class Route implements RouteInterface {

	const VERSION = 'v1';

	/**
	 * The route namespace
	 *
	 * @return string
	 */
	public function get_namespace() {
		return sprintf( '%1$s/%2$s', $this->get_name(), self::VERSION );
	}

	/**
	 * The route name
	 *
	 * @return string
	 */
	public function get_name() {
		return divi_squad()->get_name();
	}

	/**
	 * Available routes for current Rest Route
	 *
	 * @return array
	 */
	public function get_routes() {
		return array();
	}
}
