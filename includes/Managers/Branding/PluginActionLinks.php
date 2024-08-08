<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The plugin action links management class for the plugin dashboard at admin area.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Managers\Branding;

use DiviSquad\Base\Factories\BrandAsset\BrandAsset;
use function admin_url;
use function esc_html__;

/**
 * Plugin Action Links class
 *
 * @package DiviSquad
 * @since   1.0.0
 */
class PluginActionLinks extends BrandAsset {

	/**
	 * The branding type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'plugin_action_links';
	}

	/**
	 * The branding position.
	 *
	 * @return string
	 */
	public function get_position() {
		return 'before';
	}

	/**
	 * The branding asset is allowed in network.
	 *
	 * @return bool
	 */
	public function is_allow_network() {
		return true;
	}

	/**
	 * The plugin action links.
	 *
	 * @return array
	 */
	public function get_action_links() {
		$manage_modules_url = admin_url( 'admin.php?page=divi_squad_dashboard#/modules' );

		return array(
			sprintf( '<a href="%1$s" aria-label="%2$s">%2$s</a>', $manage_modules_url, esc_html__( 'Manage', 'squad-modules-for-divi' ) ),
		);
	}
}
