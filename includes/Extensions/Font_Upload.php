<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The Font Upload extension class for Divi Squad.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.2.0
 */

namespace DiviSquad\Extensions;

use DiviSquad\Base\Extension;
use function add_filter;

/**
 * The Font Upload class.
 *
 * @package DiviSquad
 * @since   1.2.0
 */
class Font_Upload extends Extension {

	/**
	 * Get the extension name.
	 *
	 * @return string
	 */
	protected function get_name() {
		return 'Font_Upload';
	}

	/**
	 * Load the extension.
	 *
	 * @return void
	 */
	protected function load() {
		add_filter( 'mime_types', array( $this, 'hook_add_extra_mime_types' ) );
		add_filter( 'upload_mimes', array( $this, 'hook_add_extra_mime_types' ) );
	}

	/**
	 * Allow extra mime type file upload in the current installation.
	 *
	 * @param array $existing_mimes The existing mime lists.
	 *
	 * @return array All mime lists with newly appended mimes.
	 */
	public function hook_add_extra_mime_types( $existing_mimes ) {
		return array_merge( $existing_mimes, $this->get_available_mime_types() );
	}

	/**
	 * All mime lists with newly appended mimes.
	 *
	 * @return array
	 */
	protected function get_available_mime_types() {
		return array(
			'ttf'   => 'font/ttf|application/font-ttf|application/x-font-ttf|application/octet-stream',
			'otf'   => 'font/otf|application/font-sfnt|application/font-otf|application/x-font-otf|application/octet-stream',
			'woff'  => 'font/woff|application/font-woff|application/x-font-woff|application/octet-stream',
			'woff2' => 'font/woff2|application/font-woff2|application/x-font-woff2|application/octet-stream',
		);
	}
}
