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

use function admin_url;
use function DiviSquad\divi_squad;
use function esc_attr;
use function esc_attr__;
use function esc_html__;
use function esc_url;
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
			// Add support url.
			$footer_text = sprintf(
				'<a target="_blank" href="https://wordpress.org/support/plugin/squad-modules-for-divi/#postform">%1$s</a> | ',
				esc_html__( 'Contact Support', 'squad-modules-for-divi' )
			);

			// Add rating to the plugin.
			$footer_text .= str_replace(
				array( '[stars]', '[wp.org]' ),
				array(
					'<a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/squad-modules-for-divi/#postform">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
					'<a target="_blank" href="http://wordpress.org/plugins/squad-modules-for-divi/" >wordpress.org</a>',
				),
				esc_html__( 'Add your [stars] on [wp.org] to spread the love.', 'squad-modules-for-divi' )
			);
		}

		return $footer_text;
	}

	/**
	 * Filters the version/update text displayed in the admin footer.
	 *
	 * @param string $content The content that will be printed.
	 *
	 * @return  string
	 * @since 1.4.8
	 */
	public function add_update_footer_text( $content ) {
		$current_screen = get_current_screen();

		if ( self::get_plugin_screen() === $current_screen->id ) {
			// Add sponsor url.
			$content = sprintf(
				'<a class="divi-squad-link-footer divi-squad-sponsor-link" href="https://www.buymeacoffee.com/mralaminahamed" title="%2$s" target="_blank" style="margin-right: 5px; text-decoration: none"><span class="dashicons dashicons-money-alt"></span> <span style="text-decoration: underline; text-underline-offset: 4px;">%1$s</span></a> | ',
				esc_html__( 'Buy me a coffee', 'squad-modules-for-divi' ),
				esc_attr__( 'Buy me a coffee', 'squad-modules-for-divi' )
			);

			// Add translate url.
			$content .= sprintf(
				'<a class="divi-squad-link-footer divi-squad-translations-link" href="https://translate.wordpress.org/projects/wp-plugins/squad-modules-for-divi" title="%2$s" target="_blank" style="margin-left: 5px; margin-right: 5px; text-decoration: none"><span class="dashicons dashicons-translation"></span> <span style="text-decoration: underline; text-underline-offset: 4px;">%1$s</span></a> | ',
				esc_html__( 'Translate', 'squad-modules-for-divi' ),
				esc_attr__( 'Help us with Translations for the Squad Modules project', 'squad-modules-for-divi' )
			);

			// Add version number.
			$options  = divi_squad()->get_options();
			$content .= sprintf(
				'<a class="divi-squad-link-footer divi-squad-version-link" href="%2$s%4$s" title="%3$s" target="_blank" style="margin-left: 5px; text-decoration: none"><span style="text-decoration: underline; text-underline-offset: 4px;">%1$s%4$s</span></a>',
				esc_html__( 'Version: ', 'squad-modules-for-divi' ),
				esc_url( admin_url( 'admin.php?page=divi_squad_dashboard#/whats-new/' ) ),
				esc_attr__( 'View what changed in this version', 'squad-modules-for-divi' ),
				esc_attr( $options['Version'] )
			);
		}

		return $content;
	}
}
