<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Builder Utils Interface
 *
 * @package DiviSquad
 * @since   1.5.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils;

use DiviSquad\Base\DiviBuilder\DiviSquad_Module;
use DiviSquad\Base\DiviBuilder\Utils;

/**
 * Builder Utils Interface
 *
 * @package DiviSquad
 * @since   1.5.0
 */
interface UtilsInterface {

	/**
	 * Connect with non-static public functions.
	 *
	 * @param DiviSquad_Module $element The instance of ET Builder Element (Squad Module).
	 *
	 * @return Utils
	 */
	public static function connect( $element );
}
