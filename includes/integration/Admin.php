<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The WordPress connection class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration;

use DiviSquad\Admin\Assets;
use DiviSquad\Admin\Menu;
use DiviSquad\Admin\Plugin_Action_Links;
use DiviSquad\Admin\Plugin_Row_Meta;

/**
 * Admin Class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Admin {

	/** The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the instance of the current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();

			Assets::get_instance();
			Menu::get_instance();
			Plugin_Action_Links::get_instance();
			Plugin_Row_Meta::get_instance();
		}

		return self::$instance;
	}

}
