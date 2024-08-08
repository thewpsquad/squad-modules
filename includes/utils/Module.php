<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Module helper.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Module class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Module {

	/**
	 * Decode json data from properties in module.
	 *
	 * @param string $html_content json data raw content from module.
	 *
	 * @return array
	 */
	public static function decode_json_data( $html_content ) {
		// Collect data as unmanaged json string.
		$data = stripslashes( html_entity_decode( $html_content ) );

		// Return json data as array for better management.
		return json_decode( $data, true );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	public static function collect_raw_props( $content ) {
		return wp_strip_all_tags( $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	public static function json_format_raw_props( $content ) {
		return sprintf( '[%s]', $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return array
	 */
	public static function collect_child_json_props( $content ) {
		$raw_props   = static::json_format_raw_props( $content );
		$clean_props = str_replace( '},]', '}]', $raw_props );
		$child_props = json_decode( $clean_props, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				sprintf(
				/* translators: 1: Error message. */
					esc_html__( __( 'Error when decoding child props: %1$s', 'squad-modules-for-divi' ), 'squad-modules-for-divi' ), // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					json_last_error_msg() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			);

			return array();
		}

		return $child_props;
	}

	/**
	 * Collect all modules from Divi Builder.
	 *
	 * @param array $modules_array  All modules array..
	 * @param array $allowed_prefix The allowed prefix list.
	 *
	 * @return array
	 */
	public static function get_all_modules( $modules_array, $allowed_prefix = array() ) {
		// Initiate default data.
		$default_allowed_prefix = array( 'disq' );
		$clean_modules          = array(
			'none'   => esc_html__( 'Select Module', 'squad-modules-for-divi' ),
			'custom' => esc_html__( 'Custom', 'squad-modules-for-divi' ),
		);

		// Merge new data with default prefix.
		$all_prefix = array_merge( $default_allowed_prefix, $allowed_prefix );

		foreach ( $modules_array as $module ) {
			if ( strpos( $module['label'], '_' ) ) {
				$module_explode = explode( '_', $module['label'] );

				if ( in_array( $module_explode[0], $all_prefix, true ) ) {
					$clean_modules[ $module['label'] ] = $module['title'];
				}
			}
		}

		return $clean_modules;
	}

	/**
	 * Clean order class name from the class list for current module.
	 *
	 * @param array  $classnames All CSS classes name the module has.
	 * @param string $slug       Module slug.
	 *
	 * @return string[]
	 */
	public static function clean_order_class( $classnames, $slug ) {
		$order_classes = array();
		foreach ( $classnames as $key => $classname ) {
			if ( 0 !== strpos( $classname, "{$slug}_" ) ) {
				$order_classes[ $key ] = $classname;
			}
		}

		return $order_classes;
	}

	/**
	 * Get default selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array[]
	 */
	public static function selectors_default( $main_css_element ) {
		return array(
			'css' => array(
				'main'  => $main_css_element,
				'hover' => "$main_css_element:hover",
			),
		);
	}

	/**
	 * Get margin and padding selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array
	 */
	public static function selectors_margin_padding( $main_css_element ) {
		return array(
			'use_padding' => true,
			'use_margin'  => true,
			'css'         => array(
				'margin'    => $main_css_element,
				'padding'   => $main_css_element,
				'important' => 'all',
			),
		);
	}

	/**
	 * Get max_width selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array[]
	 */
	public static function selectors_max_width( $main_css_element ) {
		return array_merge(
			self::selectors_default( $main_css_element ),
			array(
				'css' => array(
					'module_alignment' => "$main_css_element.et_pb_module",
				),
			)
		);
	}

	/**
	 * Get background selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of element.
	 *
	 * @return array[]
	 */
	public static function selectors_background( $main_css_element ) {
		return array_merge(
			self::selectors_default( $main_css_element ),
			array(
				'settings' => array(
					'color' => 'alpha',
				),
			)
		);
	}
}
