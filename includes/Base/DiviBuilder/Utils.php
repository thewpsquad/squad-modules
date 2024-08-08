<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Utils Class
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.5.0
 */

namespace DiviSquad\Base\DiviBuilder;

/**
 * Builder Utils Class
 *
 * @package DiviSquad
 * @since   1.5.0
 */
final class Utils extends Utils\Base implements Utils\UtilsInterface {

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
