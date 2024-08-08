<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories\AdminNotice;

/**
 * Interface for the Notice class.
 */
interface NoticeInterface {

	/**
	 * Say that current notice can view or not.
	 *
	 * @return bool
	 */
	public function can_render_it();

	/**
	 * Show Notice.
	 *
	 * @return bool
	 */
	public function show_admin_notice();

	/**
	 * Add the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 */
	public function get_body_classes();
}
