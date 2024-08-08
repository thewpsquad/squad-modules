<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The main class for Divi Squad.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

use DiviSquad\Base\BuilderIntegrationAPI;
use DiviSquad\Manager\Modules;
use function DiviSquad\divi_squad;

/**
 * Divi Squad Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class DiviSquad extends BuilderIntegrationAPI {

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	public function get_version() {
		return divi_squad()->get_version();
	}

	/**
	 * Loads custom modules when the builder is ready.
	 *
	 * @since 1.0.0
	 */
	public function hook_et_builder_ready() {
		divi_squad()->get_modules()->load_modules( dirname( __DIR__ ) );
	}
}
