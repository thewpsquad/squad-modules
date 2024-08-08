<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Filesystem class.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Utils\Media;

use WP_Filesystem_Base;
use function divi_squad;
use function WP_Filesystem;
use function wp_normalize_path;

/**
 * The Filesystem class.
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Filesystem {

	/**
	 * Get the filesystem.
	 *
	 * @access protected
	 * @return WP_Filesystem_Base
	 */
	protected function get_wp_filesystem() {
		global $wp_filesystem;

		// If the filesystem has not been instantiated yet, do it here.
		if ( ! $wp_filesystem ) {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once wp_normalize_path( divi_squad()->get_wp_path() . '/wp-admin/includes/file.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}
			WP_Filesystem();
		}

		return $wp_filesystem;
	}
}
