<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Migrations class for Divi Squad.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Managers;

use DiviSquad\Settings\Migration;

/**
 * Migrations class for Divi Squad.
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Migrations {

	/**
	 * Initialize the migrations.
	 *
	 * @return void
	 * @since  3.0.0
	 */
	public static function init() {
		Migration::init();
	}
}
