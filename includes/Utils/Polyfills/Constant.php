<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Polyfill for PHP constants.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.0
 */

namespace DiviSquad\Utils\Polyfills;

/**
 * Constant class.
 *
 * @package DiviSquad
 * @since   3.1.0
 */
class Constant {
	/**
	 * PHP_INT_MAX constants.
	 *
	 * @var integer
	 */
	const PHP_INT_MAX = 9223372036854775807;

	/**
	 * PHP_INT_MIN constants.
	 *
	 * @var integer
	 */
	const PHP_INT_MIN = -9223372036854775808; // @phpstan-ignore-line
}
