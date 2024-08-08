<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\Factories;

use DiviSquad\Utils\Singleton;

final class AdminNotice {

	use Singleton;

	/**
	 * Save an indicator for save state.
	 *
	 * @var bool
	 */
	private static $is_notices_registered = false;

	/**
	 * Save an indicator for save state.
	 *
	 * @var bool
	 */
	private static $is_body_classes_added = false;

	/**
	 * Store all notices
	 *
	 * @var AdminNotice\NoticeInterface[]
	 */
	private static $notices = array();

	private function __construct() {
		// Load all admin notices and body classes for admin.
		add_action( 'admin_notices', array( $this, 'add_admin_notices' ) );
		add_filter( 'admin_body_class', array( $this, 'add_body_classes' ) );
	}

	public function add( $notice_class ) {
		$notice = new $notice_class();

		if ( ! $notice instanceof AdminNotice\NoticeInterface ) {
			return false;
		}

		self::$notices[] = $notice;

		return true;
	}

	/**
	 * Prints admin screen notices in the WordPress admin area.
	 *
	 * @return void
	 */
	public function add_admin_notices() {
		if ( ! empty( self::$notices ) ) {
			/**
			 * Store of all notices
			 *
			 * @var AdminNotice\NoticeInterface[] $notices
			 */
			foreach ( self::$notices as $notice ) {
				if ( $notice->can_render_it() && file_exists( $notice->get_template() ) ) {
					load_template( $notice->get_template() );
				}
			}
		}
	}

	/**
	 * Filters the CSS classes for the body tag in the admin.
	 *
	 * @param string $classes Space-separated list of CSS classes.
	 *
	 * @return string
	 * @since 1.0.4
	 */
	public function add_body_classes( $classes ) {
		if ( ! empty( self::$notices ) ) {
			/**
			 * Store of all notices
			 *
			 * @var AdminNotice\NoticeInterface[] $notices
			 */
			foreach ( self::$notices as $notice ) {
				if ( $notice->can_render_it() ) {
					$classes .= ' ' . $notice->get_body_classes();
				}
			}
		}

		return $classes;
	}

}
