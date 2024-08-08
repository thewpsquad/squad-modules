<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The plugin row meta management class for the plugin dashboard at admin area.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

/**
 * Plugin Row Meta class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Plugin_Row_Meta {

	/** The instance
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the instance of self-class
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			add_filter( 'plugin_row_meta', array( self::$instance, 'add_plugin_row_meta' ), 10, 2 );
		}

		return self::$instance;
	}
	/**
	 * Filters the array of row meta for each/specific plugin in the Plugins list table.
	 * Appends additional links below each/specific plugin on the plugin page.
	 *
	 * @access  public
	 *
	 * @param array  $links            An array of the plugin's metadata.
	 * @param string $plugin_file_name Path to the plugin file.
	 *
	 * @return  array
	 */
	public function add_plugin_row_meta( $links, $plugin_file_name ) {
		if ( DISQ_PLUGIN_BASE === $plugin_file_name ) {
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/squad-modules-for-divi/reviews/#new-post' ), esc_html__( 'Rate It', 'squad-modules-for-divi' ) );
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/squad-modules-for-divi/#new-post' ), esc_html__( 'Support', 'squad-modules-for-divi' ) );
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://divi-squad.com/docs' ), esc_html__( 'Documentation', 'squad-modules-for-divi' ) );
			$links[] = sprintf( '<a href="%1$s" target="_blank" aria-label="%2$s">%2$s</a>', esc_url( 'https://divi-squad.com/faq' ), esc_html__( 'FAQ', 'squad-modules-for-divi' ) );

			return $links;
		}

		return $links;
	}


}
