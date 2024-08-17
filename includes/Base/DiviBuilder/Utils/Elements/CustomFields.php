<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Custom Fields Utils Helper
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements;

use InvalidArgumentException;

/**
 * Custom Fields Utils Helper Class
 *
 * @package DiviSquad
 * @since   3.1.0
 */
class CustomFields {

	/**
	 * Supported post types.
	 *
	 * @var array Supported post types.
	 */
	protected static $post_types = array( 'post' );

	/**
	 * Supported field types with their corresponding processor classes.
	 *
	 * @var array<string, array<string, class-string>>
	 */
	protected static $processors = array(
		'collections' => array(
			'custom_fields' => CustomFields\Processors\WordPress::class,
			'acf_fields'    => CustomFields\Processors\Advanced::class,
		),
		'definitions' => array(
			'custom_fields' => CustomFields\Definitions\WordPress::class,
			'acf_fields'    => CustomFields\Definitions\Advanced::class,
		),
	);

	/**
	 * Runtime data storage.
	 *
	 * @var array<string, array>
	 */
	protected static $storage = array(
		'instances'   => array(),
		'options'     => array(),
		'definitions' => array(),
	);

	/**
	 * Field Manager Instance
	 *
	 * @var CustomFields\Managers\Fields
	 */
	private static $fields_manager;

	/**
	 * Initialize the CustomFields class.
	 *
	 * @return void
	 * @throws \Exception If the manager class does not exist or is not instantiable.
	 */
	public static function init() {
		self::$fields_manager = new CustomFields\Managers\Fields( static::get_supported_post_types() );
	}

	/**
	 * Get all fields of a specific type.
	 *
	 * @param string $field_type The field type (acf, WordPress, etc.).
	 * @param int    $post_id    The current post id.
	 *
	 * @return array<string, string>
	 * @throws InvalidArgumentException If the field type is not supported.
	 */
	public static function get_fields( $field_type, $post_id ) {
		$processor = static::get_class( $field_type );

		if ( ! isset( static::$storage['options'][ $processor ][ $post_id ] ) ) {
			static::$storage['options'][ $processor ] = array(
				$post_id => static::get( $field_type )->get_fields( $post_id ),
			);
		}

		return static::$storage['options'][ $processor ][ $post_id ];
	}

	/**
	 * Get module definitions for module usages
	 *
	 * @param string $field_type The field type (acf, WordPress, etc.).
	 *
	 * @return array<string, mixed>
	 * @throws InvalidArgumentException If the field type is not supported.
	 */
	public static function get_definitions( $field_type ) {
		$processor = static::get_class( $field_type, 'definitions' );

		if ( ! isset( static::$storage['definitions'][ $processor ] ) ) {
			$empty_fields        = array();
			$default_fields      = array();
			$associated_fields   = array();
			$not_eligible_fields = array();

			// Verify that WordPress custom field is eligible or not.
			$fields      = static::get( $field_type, 'collections' );
			$definitions = static::get( $field_type, 'definitions' );
			if ( $fields->is_eligible() ) {
				$wp_fields_options = $fields->get_formatted_fields();

				// Add regular custom fields.
				foreach ( $wp_fields_options as $post_type => $options ) {
					if ( empty( $options ) ) {
						continue;
					}

					if ( ! in_array( $post_type, static::get_supported_post_types(), true ) ) {
						continue;
					}

					$new_fields     = $definitions->get_default_fields( $post_type, $options );
					$default_fields = array_merge_recursive( $default_fields, $new_fields );
				}

				// Verify and set default fields.
				$empty_fields      = $definitions->get_empty_fields();
				$field_types       = $fields->get_formatted_fields_types();
				$associated_fields = $definitions->get_associated_fields( $field_types );
			} else {
				$not_eligible_fields = $definitions->get_not_eligible_fields();
			}

			$associated_fields = empty( $default_fields ) ? array() : $associated_fields;
			$default_fields    = empty( $not_eligible_fields ) ? ( empty( $default_fields ) ? $empty_fields : $default_fields ) : $not_eligible_fields;

			static::$storage['definitions'][ $processor ] = array_merge_recursive(
				$definitions->get_common_fields(),
				$default_fields,
				$associated_fields
			);
		}

		return static::$storage['definitions'][ $processor ];
	}

	/**
	 * Get the CustomFieldsManager instance.
	 *
	 * @return CustomFields\Managers\Fields
	 * @throws InvalidArgumentException If the manager is not supported.
	 * @throws \Exception If the manager class does not exist or is not instantiable.
	 */
	public static function get_fields_manager() {
		return self::$fields_manager;
	}

	/**
	 * Get supported post types.
	 *
	 * @return array|string[]
	 */
	public static function get_supported_post_types() {
		return static::$post_types;
	}

	/**
	 * Fetch fields of a specific type.
	 *
	 * @param string $field_type The field type (acf, WordPress, etc.).
	 * @param string $storage    The storage type (collections, definitions.).
	 *
	 * @return CustomFields\Definition|CustomFields\Processor
	 * @throws InvalidArgumentException If the field type is not supported.
	 */
	public static function get( $field_type, $storage = 'collections' ) {
		if ( ! isset( static::$processors[ $storage ][ $field_type ] ) ) {
			throw new InvalidArgumentException(
				sprintf(
					/* translators: %s: The unsupported field type */
					esc_html__( 'Unsupported field type: %s', 'squad-modules-for-divi' ),
					esc_html( $field_type )
				)
			);
		}

		$processor = static::get_class( $field_type, $storage );
		if ( ! isset( static::$storage['instances'][ $processor ] ) ) {
			if ( ! in_array( $storage, array( 'collections', 'definitions' ), true ) ) {
				throw new InvalidArgumentException(
					sprintf(
						/* translators: %s: The unsupported storage type */
						esc_html__( 'Unsupported storage type: %s', 'squad-modules-for-divi' ),
						esc_html( $storage )
					)
				);
			}

			static::$storage['instances'][ $processor ] = new static::$processors[ $storage ][ $field_type ]();
		}

		return static::$storage['instances'][ $processor ];
	}

	/**
	 * Get current field processor class name
	 *
	 * @param string $field_type The field type (acf_fields, custom_fields, etc.).
	 * @param string $storage    The storage type (collections, definitions.).
	 *
	 * @return class-string
	 */
	protected static function get_class( $field_type, $storage = 'collections' ) {
		return static::$processors[ $storage ][ $field_type ];
	}
}
