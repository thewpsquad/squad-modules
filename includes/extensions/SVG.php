<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The SVG class for Divi Squad.
 *
 * This class handles svg image upload and used in the WordPress setup.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\Extensions;

/**
 * The SVG class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class SVG extends Extensions {

	/**
	 * The constructor class.
	 */
	public function __construct() {
		parent::__construct();

		if ( ! in_array( 'SVG', $this->name_lists, true ) ) {
			add_filter( 'mime_types', array( $this, 'hook_add_extra_mime_types' ) );
			add_filter( 'upload_mimes', array( $this, 'hook_add_extra_mime_types' ) );
			add_filter( 'wp_check_filetype_and_ext', array( $this, 'enable__upload' ), 10, 4 );
		}
	}

	/**
	 * All mime lists with newly appended mimes.
	 *
	 * @return array
	 */
	public function get_available_mime_types() {
		return array(
			'svg' => 'image/svg+xml',
		);
	}

	/**
	 * Allow extra mime type file upload in the current installation.
	 *
	 * @param array $existing_mimes The existing mime lists.
	 *
	 * @return array All mime lists with newly appended mimes.
	 * @since 1.0.0
	 */
	public function hook_add_extra_mime_types( $existing_mimes ) {
		return array_merge( $existing_mimes, $this->get_available_mime_types() );
	}

	/**
	 * Filters the "real" file type of the given file.
	 *
	 * @param array    $wp_check Values for the extension, mime type, and corrected filename.
	 * @param string   $file     Full path to the file.
	 * @param string   $filename The name of the file.
	 * @param string[] $mimes    Array of mime types keyed by their file extension regex.
	 */
	public function enable__upload( $wp_check, $file, $filename, $mimes ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
		if ( ! $wp_check['type'] ) {
			$check_filetype  = wp_check_filetype( $filename, $mimes );
			$ext             = $check_filetype['ext'];
			$type            = $check_filetype['type'];
			$proper_filename = $filename;

			if ( $type && 'svg' !== $ext && 0 === strpos( $type, 'image/' ) ) {
				$ext  = false;
				$type = false;
			}

			$wp_check = compact( 'ext', 'type', 'proper_filename' );
		}

		return $wp_check;
	}
}

new SVG();
