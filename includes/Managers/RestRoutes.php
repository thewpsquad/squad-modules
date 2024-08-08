<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers;

/**
 * Rest API Routes
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class RestRoutes {

	/**
	 * Load rest route on init time.
	 *
	 * @return void
	 */
	public static function load() {
		// Register all rest api routes.
		$rest_api = \DiviSquad\Base\Factories\RestRoute::get_instance();
		$rest_api->add( RestRoutes\Modules::class );
		$rest_api->add( RestRoutes\Extensions::class );
		$rest_api->add( RestRoutes\PluginReview::class );
		$rest_api->add( RestRoutes\WhatsNew::class );
		$rest_api->add( RestRoutes\ProActivation::class );
	}
}
