<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Utils Common.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils;

use function esc_html;
use function esc_html__;
use function wp_kses_post;
use function wp_strip_all_tags;

/**
 * Common trait.
 *
 * @package DiviSquad
 * @since   1.0.0
 */
trait Common {

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
	 * @return array
	 * @throws \RuntimeException When json error found.
	 */
	public static function collect_child_json_props( $content ) {
		$raw_props   = static::json_format_raw_props( $content );
		$clean_props = str_replace( array( '},||', '},]' ), array( '},', '}]' ), $raw_props );
		$child_props = json_decode( $clean_props, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			throw new \RuntimeException(
				sprintf(
					/* translators: 1: Error message. */
					esc_html__( '%1$s found when decoding the content: %2$s', 'squad-modules-for-divi' ),
					esc_html( json_last_error_msg() ),
					wp_kses_post( $content )
				)
			);
		}

		return $child_props;
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
	 * Clean order class name from the class list for current module.
	 *
	 * @param array  $classnames All CSS classes name the module has.
	 * @param string $slug       Utils slug.
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
	 * @param string $main_css_element Main css selector of an element.
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
	 * Get background selectors for main and hover
	 *
	 * @param string $main_css_element Main css selector of an element.
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

	/**
	 * Convert field name into css property name.
	 *
	 * @param string $field Field name.
	 *
	 * @return string|string[]
	 */
	public static function field_to_css_prop( $field ) {
		return str_replace( '_', '-', $field );
	}
}
