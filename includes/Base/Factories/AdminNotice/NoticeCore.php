<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories\AdminNotice;

/**
 * Abstract class representing the Notice.
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 */
abstract class NoticeCore implements NoticeInterface {

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	abstract public function get_template();

	/**
	 * Show Notice.
	 *
	 * @return void
	 */
	public function show_admin_notice() {
		if ( file_exists( $this->get_template() ) ) {
			load_template( $this->get_template() );
		}
	}
}
