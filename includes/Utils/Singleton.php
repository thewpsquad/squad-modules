<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Utils;

trait Singleton {

	/**
	 * The instance of current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 *  The instance of current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @access private
	 */
	private function __clone() {}

	/**
	 * Serializing instances of this class is forbidden.
	 *
	 * @access public
	 */
	public function __wakeup() {}
}
