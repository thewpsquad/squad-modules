<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories\RestRoute;

/**
 * Abstract class representing the Route.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class RouteCore implements RouteInterface {

	/**
	 * The route name
	 *
	 * @return string
	 */
	public function get_name() {
		return divi_squad()->get_name();
	}

	/**
	 * The route namespace
	 *
	 * @return string
	 */
	public function get_namespace() {
		return sprintf( '%1$s/v1', $this->get_name() );
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
