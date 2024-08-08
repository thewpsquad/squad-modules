<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The JSON extension class for Divi Squad.
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
 * The JSON class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class JSON extends Extensions {

	/**
	 * The constructor class.
	 */
	public function __construct() {
		parent::__construct();

		// Allow extra mime type file upload in the current installation.
		if ( ! in_array( 'JSON', $this->name_lists, true ) ) {
			add_filter( 'mime_types', array( $this, 'hook_add_extra_mime_types' ) );
			add_filter( 'upload_mimes', array( $this, 'hook_add_extra_mime_types' ) );
			add_filter( 'wp_check_filetype_and_ext', array( $this, 'hook_wp_check_filetype_and_ext' ), 10, 3 );
		}
	}

	/**
	 * All mime lists with newly appended mimes.
	 *
	 * @return array
	 */
	public function get_available_mime_types() {
		return array(
			'json'   => 'application/json',
			'lottie' => 'application/zip',
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
	 * @param array  $wp_checked Values for the extension, mime type, and corrected filename.
	 * @param string $file       Full path to the file.
	 * @param string $filename   The name of the file.
	 */
	public function hook_wp_check_filetype_and_ext( $wp_checked, $file, $filename ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassBeforeLastUsed
		$ext             = false;
		$type            = false;
		$proper_filename = false;
		if ( isset( $wp_checked['ext'] ) ) {
			$ext = $wp_checked['ext'];
		}
		if ( isset( $wp_checked['type'] ) ) {
			$ext = $wp_checked['type'];
		}
		if ( isset( $wp_checked['proper_filename'] ) ) {
			$ext = $wp_checked['proper_filename'];
		}
		if ( false !== $type && false !== $ext ) {
			return $wp_checked;
		}

		// If a file extension is 2 or more.
		$f_sp        = explode( '.', $filename );
		$f_exp_count = count( $f_sp );

		// Filename type is "XXX" (There is not a file extension).
		if ( $f_exp_count <= 1 ) {
			return $wp_checked;
		} else {
			$f_ext = $f_sp[ $f_exp_count - 1 ];
		}

		$flag             = false;
		$mime_type_values = array_keys( self::get_available_mime_types() );
		if ( ! empty( $mime_type_values ) ) {
			foreach ( $mime_type_values as $line ) {
				// Ignore to the right of '#' on a line.
				$line = substr( $line, 0, strcspn( $line, '#' ) );
				// Escape Strings.
				$line = wp_strip_all_tags( $line );

				$line_value = explode( '=', $line );
				if ( 2 !== count( $line_value ) ) {
					continue;
				}
				// "　" is the Japanese multibyte space. If the character is found out, it automatically change the space.
				if ( trim( $line_value[0] ) === $f_ext ) {
					$ext  = $f_ext;
					$type = trim( str_replace( '　', ' ', $line_value[1] ) );
					$flag = true;
					break;
				}
			}
		}

		if ( $flag ) {
			return compact( 'ext', 'type', 'proper_filename' );
		}

		return $wp_checked;
	}
}

new JSON();
