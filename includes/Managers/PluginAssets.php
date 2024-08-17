<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Assets Manager
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 * @since   3.0.0 Updated class name.
 */

namespace DiviSquad\Managers;

use DiviSquad\Base\Factories\PluginAsset as AssetFactory;

/**
 * Assets Class
 *
 * @package DiviSquad
 * @since   1.0.0
 * @since   3.0.0 Updated class name.
 */
class PluginAssets {

	/**
	 * Load all the branding.
	 *
	 * @return void
	 */
	public static function load() {
		// Load available branding.
		$asset = AssetFactory::get_instance();
		if ( $asset instanceof AssetFactory ) {
			$asset->add( Assets\Admin::class );
			$asset->add( Assets\Modules::class );
		}
	}
}
