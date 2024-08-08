<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Divi helper class for common functions.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Utils;

use function add_filter;
use function et_get_dynamic_assets_path;
use function et_pb_maybe_fa_font_icon;
use function et_use_dynamic_icons;

/**
 * Divi class.
 *
 * @package DiviSquad
 * @since   1.0.0
 */
class Divi {
	/**
	 * Check if Divi theme builder is enabled.
	 *
	 * @return boolean
	 */
	public static function is_bfb_enabled() {
		return function_exists( '\et_builder_bfb_enabled' ) && \et_builder_bfb_enabled();
	}

	/**
	 * Check if Theme Builder is Used on the page.
	 *
	 * @return boolean
	 */
	public static function is_theme_builder_used() {
		return function_exists( '\et_fb_is_theme_builder_used_on_page' ) && \et_fb_is_theme_builder_used_on_page();
	}

	/**
	 * Check if the current screen is the Theme Builder administration screen.
	 *
	 * @return boolean
	 */
	public static function is_tb_admin_screen() {
		return function_exists( '\et_builder_is_tb_admin_screen' ) && \et_builder_is_tb_admin_screen();
	}

	/**
	 * Check if Divi visual builder is enabled.
	 *
	 * @return boolean
	 */
	public static function is_fb_enabled() {
		return function_exists( '\et_core_is_fb_enabled' ) && \et_core_is_fb_enabled();
	}

	/**
	 * Add Icons css into the divi asset list when the Dynamic CSS option is turn on in current installation
	 *
	 * @param array $global_list The existed global asset list.
	 *
	 * @return array
	 */
	public static function global_assets_list( $global_list = array() ) {
		$assets_prefix = et_get_dynamic_assets_path();

		$assets_list = array(
			'et_icons_all' => array(
				'css' => "{$assets_prefix}/css/icons_all.css",
			),
		);

		return array_merge( $global_list, $assets_list );
	}

	/**
	 * Add Font Awesome css into the divi asset list when the Dynamic CSS option is turn on in current installation
	 *
	 * @param array $global_list The existed global asset list.
	 *
	 * @return array
	 */
	public static function global_fa_assets_list( $global_list = array() ) {
		$assets_prefix = et_get_dynamic_assets_path();

		$assets_list = array(
			'et_icons_fa' => array(
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			),
		);

		return array_merge( $global_list, $assets_list );
	}

	/**
	 * Add Font Awesome css support manually when the Dynamic CSS option is turn on in current installation.
	 *
	 * @param string $icon_data The icon value.
	 *
	 * @return void
	 */
	public static function inject_fa_icons( $icon_data ) {
		if ( function_exists( 'et_use_dynamic_icons' ) && 'on' === et_use_dynamic_icons() ) {
			add_filter( 'et_global_assets_list', array( self::class, 'global_assets_list' ) );
			add_filter( 'et_late_global_assets_list', array( self::class, 'global_assets_list' ) );

			if ( function_exists( 'et_pb_maybe_fa_font_icon' ) && et_pb_maybe_fa_font_icon( $icon_data ) ) {
				add_filter( 'et_global_assets_list', array( self::class, 'global_fa_assets_list' ) );
				add_filter( 'et_late_global_assets_list', array( self::class, 'global_fa_assets_list' ) );
			}
		}
	}
}
