<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Abstract class representing the Branding.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Base\Factories\BrandAsset;

/**
 * Abstract class representing the Branding.
 *
 * @package DiviSquad
 * @since   3.0.0
 */
abstract class Asset implements AssetInterface {

	/**
	 * The plugin base.
	 *
	 * @return string
	 */
	public function get_plugin_base() {
		return divi_squad()->get_basename();
	}

	/**
	 * The branding asset is allowed in network.
	 *
	 * @return bool
	 */
	public function is_allow_network() {
		return false;
	}

	/**
	 * The plugin action links.
	 *
	 * @return array
	 */
	public function get_action_links() {
		return array();
	}

	/**
	 * The plugin row actions.
	 *
	 * @return array
	 */
	public function get_row_actions() {
		return array();
	}

	/**
	 * The plugin footer text.
	 *
	 * @return string
	 */
	public function get_plugin_footer_text() {
		return '';
	}

	/**
	 * The plugin update footer text.
	 *
	 * @return string
	 */
	public function get_update_footer_text() {
		return '';
	}
}
