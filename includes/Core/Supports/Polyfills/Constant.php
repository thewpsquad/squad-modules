<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Polyfill for PHP constants.
 *
 * @since   3.1.1
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Core\Supports\Polyfills;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Constant class.
 *
 * @since   3.1.1
 * @package DiviSquad
 */
class Constant {
	/**
	 * PHP_INT_MAX constants.
	 *
	 * @var integer
	 */
	public const PHP_INT_MAX = 9223372036854775807;

	/**
	 * PHP_INT_MIN constants.
	 *
	 * @var integer
	 */
	public const PHP_INT_MIN = \PHP_INT_MIN; // Native since PHP 7.0; the literal below PHP_INT_MAX would parse as a float.
}
