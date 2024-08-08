<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories\RestRoute;

/**
 * Interface for the Route class.
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

