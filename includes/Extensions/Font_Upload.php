<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The Font Upload extension class for Divi Squad.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Extensions;

use DiviSquad\Base\Extension;
use function add_filter;

/**
 * The Font Upload class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
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
	 * All mime lists with newly appended mimes.
	 *
	 * @return array
	 */
	public function get_available_mime_types() {
		return array(
			'ttf'  => 'application/x-font-ttf',
			'otf'  => 'application/font-sfnt',
			'woff' => 'application/x-font-woff',
		);
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
}

new Font_Upload();
