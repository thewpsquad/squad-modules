<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Form Utils Helper Class
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.5.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements;

/**
 * Main class for handling various form types.
 *
 * @package DiviSquad
 * @since 1.5.0
 */
class Forms {
	const DEFAULT_FORM_ID = 'cfcd208495d565ef66e7dff9f98764da';

	/**
	 * Supported form types with their corresponding processor classes.
	 *
	 * @var array<string, string>
	 */
	private static $supported_form_types = array(
		'cf7'           => Forms\Processors\ContactForm7::class,
		'wpforms'       => Forms\Processors\WPForms::class,
		'fluent_forms'  => Forms\Processors\FluentForms::class,
		'ninja_forms'   => Forms\Processors\NinjaForms::class,
		'gravity_forms' => Forms\Processors\GravityForms::class,
		'forminator'    => Forms\Processors\Forminator::class,
		'formidable'    => Forms\Processors\Formidable::class,
	);

	/**
	 * Form collections.
	 *
	 * @var array<string, array<string, string>>
	 */
	private static $form_collections = array();

	/**
	 * Form processors.
	 *
	 * @var array<string, Forms\FormInterface>
	 */
	private static $form_processors = array();

	/**
	 * Get allowed fields for the module.
	 *
	 * @return array List of allowed field types
	 */
	public static function get_allowed_fields() {
		static $allowed_fields = null;
		if ( null === $allowed_fields ) {
			$allowed_fields = array(
				'input[type=email]',
				'input[type=text]',
				'input[type=url]',
				'input[type=tel]',
				'input[type=number]',
				'input[type=date]',
				'input[type=file]',
				'select',
				'textarea',
			);
		}
		return $allowed_fields;
	}

	/**
	 * Get custom spacing prefixes for the module.
	 *
	 * @return array Custom spacing prefixes
	 */
	public static function get_custom_spacing_prefixes() {
		static $prefixes = null;
		if ( null === $prefixes ) {
			$prefixes = array(
				'wrapper'         => array( 'label' => __( 'Wrapper', 'squad-modules-for-divi' ) ),
				'field'           => array( 'label' => __( 'Field', 'squad-modules-for-divi' ) ),
				'message_error'   => array( 'label' => __( 'Message', 'squad-modules-for-divi' ) ),
				'message_success' => array( 'label' => __( 'Message', 'squad-modules-for-divi' ) ),
			);
		}
		return $prefixes;
	}

	/**
	 * Get all forms of a specific type.
	 *
	 * @param string $form_type The form type (cf7, fluent_forms, etc.).
	 * @param string $collection The collection type (title or id).
	 *
	 * @return array<string, string>
	 * @throws \InvalidArgumentException If the form type is not supported.
	 */
	public static function get_all_forms( $form_type, $collection = 'title' ) {
		if ( ! isset( self::$supported_form_types[ $form_type ] ) ) {
			throw new \InvalidArgumentException( esc_html__( 'Unsupported form type.', 'squad-modules-for-divi' ) );
		}

		if ( ! isset( self::$form_collections[ $form_type ][ $collection ] ) ) {
			self::$form_collections[ $form_type ][ $collection ] = self::fetch_forms( $form_type, $collection );
		}

		return array( self::DEFAULT_FORM_ID => esc_html__( 'Select one', 'squad-modules-for-divi' ) ) + self::$form_collections[ $form_type ][ $collection ];
	}

	/**
	 * Fetch forms of a specific type.
	 *
	 * @param string $form_type The form type (cf7, fluent_forms, etc.).
	 * @param string $collection The collection type (title or id).
	 *
	 * @return array<string, string>
	 */
	private static function fetch_forms( $form_type, $collection ) {
		if ( ! isset( self::$form_processors[ $form_type ] ) ) {
			self::$form_processors[ $form_type ] = new self::$supported_form_types[ $form_type ]();
		}

		return self::$form_processors[ $form_type ]->get_forms( $collection );
	}
}
