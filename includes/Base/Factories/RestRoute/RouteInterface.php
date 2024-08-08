<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Interface for the Route class.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */


namespace DiviSquad\Base\Factories\RestRoute;

/**
 * Interface for the Route class.
 *
 * @package DiviSquad
 * @since   2.0.0
 */
interface RouteInterface {

	/**
	 * The route name
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * The route namespace
	 *
	 * @return string
	 */
	public function get_namespace();

	/**
	 * Available routes for current Rest Route
	 *
	 * @return array
	 */
	public function get_routes();
}
