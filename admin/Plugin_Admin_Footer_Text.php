<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The plugin admin footer text management class for the plugin dashboard at admin area.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use function get_current_screen;

/**
 * Plugin Admin Footer Text class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Plugin_Admin_Footer_Text {

	/**
	 * Get the plugin screen name.
	 *
	 * @return string
	 */
	public static function get_plugin_screen() {
		return 'toplevel_page_divi_squad_dashboard';
	}

	/**
	 * Filters the "Thank you" text displayed in the admin footer.
	 *
	 * @param string $footer_text The content that will be printed.
	 *
	 * @return  string
	 * @since 1.3.2
	 */
	public function add_plugin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		if ( self::get_plugin_screen() === $current_screen->id ) {
			$footer_text = sprintf(
			/* translators: 1: Squad Modules Lite 2:: five stars */
				__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'squad-modules-for-divi' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'Squad Modules Lite', 'squad-modules-for-divi' ) ),
				'<a href="https://wordpress.org/support/plugin/squad-modules-for-divi/reviews/?rate=5#new-post" target="_blank" class="disq-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'squad-modules-for-divi' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}
}
