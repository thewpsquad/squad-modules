<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Utils;

use function et_get_dynamic_assets_path;
use function et_pb_maybe_fa_font_icon;
use function et_use_dynamic_icons;

/**
 * Divi class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Divi {

	/**
	 * Collect icon type from Divi formatted value.
	 *
	 * @param string $icon_value Divi formatted value for Icon.
	 *
	 * @return string
	 */
	public static function get_icon_type( $icon_value ) {
		return $icon_value && strpos( $icon_value, '||fa||' ) !== - 1 ? 'fa' : 'divi';
	}

	/**
	 * Determine icon font weight
	 *
	 * @param string $icon_value Divi formatted value for Icon.
	 *
	 * @return string
	 */
	public static function get_icon_font_weight( $icon_value ) {
		if ( ! empty( $icon_value ) ) {
			$icon_data = explode( $icon_value, '|' );

			return array_pop( $icon_data );
		} else {
			return 400;
		}
	}

	/**
	 * Get unicode icon data
	 *
	 * @param string $icon_value Icon font value.
	 *
	 * @return string Icon data
	 */
	public static function get_icon_data_to_unicode( $icon_value ) {
		if ( ! empty( $icon_value ) ) {
			$icon_all_data = explode( '||', $icon_value );
			$icon_data     = array_shift( $icon_all_data );
			$icon_data     = str_replace( ';', '', $icon_data );

			return str_replace( '&#x', '\\', $icon_data );
		} else {
			return '';
		}
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
			add_filter( 'et_global_assets_list', array( __CLASS__, 'global_assets_list' ) );
			add_filter( 'et_late_global_assets_list', array( __CLASS__, 'global_assets_list' ) );

			if ( function_exists( 'et_pb_maybe_fa_font_icon' ) && et_pb_maybe_fa_font_icon( $icon_data ) ) {
				add_filter( 'et_global_assets_list', array( __CLASS__, 'global_fa_assets_list' ) );
				add_filter( 'et_late_global_assets_list', array( __CLASS__, 'global_fa_assets_list' ) );
			}
		}
	}
}
