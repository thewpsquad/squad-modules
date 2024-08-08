<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Utils Class
 *
 * @since       1.5.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\DiviBuilder;

/**
 * Builder Utils Class
 *
 * @since       1.5.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
final class Utils extends Utils\Base {

	/**
	 * Connect with non-static public functions.
	 *
	 * @param DiviSquad_Module $element The instance of ET Builder Element (Squad Module).
	 *
	 * @return Utils
	 */
	public static function connect( $element ) {
		$my_instance          = new self();
		$my_instance->element = $element;

		return $my_instance;
	}
}
