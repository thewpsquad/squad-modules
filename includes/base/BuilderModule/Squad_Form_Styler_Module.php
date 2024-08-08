<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Module Helper Class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\BuilderModule;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use function esc_html__;
use function et_pb_background_options;

/**
 * Builder Module Helper Class which help to the all module class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
abstract class Squad_Form_Styler_Module extends Squad_Builder_Module {

	/**
	 * Get the stylesheet selector for form tag.
	 *
	 * @return string
	 */
	abstract protected function get_form_selector_default();

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_form_selector_hover();

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	abstract protected function get_error_message_selector_default();

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_error_message_selector_hover();

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	abstract protected function get_success_message_selector_default();

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_success_message_selector_hover();

	/**
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	abstract protected function get_field_selector_default();

	/**
	 * Get the stylesheet selector for form fields to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_field_selector_hover();

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	abstract protected function get_submit_button_selector_default();

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_submit_button_selector_hover();

	/**
	 * Declare allowed fields for the module.
	 *
	 * @return array
	 */
	public function disq_get_allowed_form_fields() {
		return array( 'input[type=email]', 'input[type=text]', 'input[type=url]', 'input[type=tel]', 'input[type=number]', 'input[type=date]', 'input[type=file]', 'select', 'textarea' );
	}

	/**
	 * Declare all prefixes for custom spacing fields for the module.
	 *
	 * @return array
	 */
	protected function disq_get_custom_spacing_prefixes() {
		return array(
			'wrapper'         => array( 'label' => esc_html__( 'Wrapper', 'squad-modules-for-divi' ) ),
			'field'           => array( 'label' => esc_html__( 'Field', 'squad-modules-for-divi' ) ),
			'message_error'   => array( 'label' => esc_html__( 'Message', 'squad-modules-for-divi' ) ),
			'message_success' => array( 'label' => esc_html__( 'Message', 'squad-modules-for-divi' ) ),
		);
	}

	/**
	 * Get toggles for the module's settings modal.
	 */
	public function get_settings_modal_toggles() {
		return array(
			'general'  => array(
				'toggles' => array(
					'forms'       => esc_html__( 'Forms Settings', 'squad-modules-for-divi' ),
					'field_icons' => esc_html__( 'Field Icons', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'wrapper'              => esc_html__( 'Form Wrapper', 'squad-modules-for-divi' ),
					'field'                => esc_html__( 'Field', 'squad-modules-for-divi' ),
					'field_text'           => esc_html__( 'Field Text', 'squad-modules-for-divi' ),
					'field_label_text'     => esc_html__( 'Field Label Text', 'squad-modules-for-divi' ),
					'placeholder_text'     => esc_html__( 'Field Placeholder Text', 'squad-modules-for-divi' ),
					'form_button'          => esc_html__( 'Button', 'squad-modules-for-divi' ),
					'form_button_text'     => esc_html__( 'Button Text', 'squad-modules-for-divi' ),
					'message_error'        => esc_html__( 'Error Message', 'squad-modules-for-divi' ),
					'message_error_text'   => esc_html__( 'Error Message Text', 'squad-modules-for-divi' ),
					'message_success'      => esc_html__( 'Success Message', 'squad-modules-for-divi' ),
					'message_success_text' => esc_html__( 'Success Message Text', 'squad-modules-for-divi' ),
				),
			),
		);
	}

	/**
	 * Declare general fields for the module.
	 *
	 * @return array[]
	 */
	public function get_fields() {
		// Button fields definitions.
		$form_submit_button = $this->disq_get_button_fields(
			array(
				'base_attr_name'  => 'form_button',
				'toggle_slug'     => 'form_button',
				'depends_show_if' => 'on',
			)
		);

		$wrapper_background_fields         = $this->disq_add_background_field(
			esc_html__( 'Wrapper Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'form_wrapper_background',
				'context'     => 'form_wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'wrapper',
			)
		);
		$fields_background_fields          = $this->disq_add_background_field(
			esc_html__( 'Field Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'fields_background',
				'context'     => 'fields_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'field',
			)
		);
		$message_error_background_fields   = $this->disq_add_background_field(
			esc_html__( 'Message Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'message_error_background',
				'context'     => 'message_error_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'message_error',
			)
		);
		$message_success_background_fields = $this->disq_add_background_field(
			esc_html__( 'Message Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'message_success_background',
				'context'     => 'message_success_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'message_success',
			)
		);

		$custom_spacing_prefixes = $this->disq_get_custom_spacing_prefixes();
		$custom_spacing_fields   = array();

		foreach ( $custom_spacing_prefixes as $prefix => $options ) {
			$label  = ! empty( $options['label'] ) ? $options['label'] : '';
			$label .= ! empty( $label ) ? ' ' : '';

			// Set the margin field for this element.
			$custom_spacing_fields[ "{$prefix}_margin" ] = $this->disq_add_margin_padding_field(
			/* translators: 1: The Element Label */
				sprintf( esc_html__( '%s Margin', 'squad-modules-for-divi' ), $label ),
				array(
					'description'    => esc_html__( 'Here you can define a custom margin size.', 'squad-modules-for-divi' ),
					'type'           => 'custom_margin',
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => $prefix,
				)
			);

			// Set the padding field for this element.
			$custom_spacing_fields[ "{$prefix}_padding" ] = $this->disq_add_margin_padding_field(
			/* translators: 1: The Element Label */
				sprintf( esc_html__( '%s Padding', 'squad-modules-for-divi' ), $label ),
				array(
					'description'    => esc_html__( 'Here you can define a custom padding size.', 'squad-modules-for-divi' ),
					'type'           => 'custom_padding',
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => $prefix,
				)
			);
		}

		return array_merge_recursive(
			$form_submit_button,
			$wrapper_background_fields,
			$fields_background_fields,
			$message_error_background_fields,
			$message_success_background_fields,
			$custom_spacing_fields
		);
	}

	/**
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the field list.
	 *
	 * @since 1.0.0
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// wrapper styles.
		$fields['wrapper_background_color'] = array( 'background' => $this->get_form_selector_default() );
		$fields['wrapper_margin']           = array( 'margin' => $this->get_form_selector_default() );
		$fields['wrapper_padding']          = array( 'padding' => $this->get_form_selector_default() );
		$this->disq_fix_border_transition( $fields, 'wrapper', $this->get_form_selector_default() );
		$this->disq_fix_box_shadow_transition( $fields, 'wrapper', $this->get_form_selector_default() );

		// field text and others style.
		$fields['fields_background_color'] = array( 'background' => $this->get_field_selector_default() );
		$fields['field_margin']            = array( 'margin' => $this->get_field_selector_default() );
		$fields['field_padding']           = array( 'padding' => $this->get_field_selector_default() );
		$this->disq_fix_fonts_transition( $fields, 'field_text', $this->get_field_selector_default() );
		$this->disq_fix_border_transition( $fields, 'field', $this->get_field_selector_default() );
		$this->disq_fix_box_shadow_transition( $fields, 'field', $this->get_field_selector_default() );

		// error message text and others styles.
		$fields['message_error_background_color'] = array( 'background' => $this->get_error_message_selector_default() );
		$fields['message_error_margin']           = array( 'margin' => $this->get_error_message_selector_default() );
		$fields['message_error_padding']          = array( 'padding' => $this->get_error_message_selector_default() );
		$this->disq_fix_fonts_transition( $fields, 'message_error_text', $this->get_error_message_selector_default() );
		$this->disq_fix_border_transition( $fields, 'message_error', $this->get_error_message_selector_default() );
		$this->disq_fix_box_shadow_transition( $fields, 'message_error', $this->get_error_message_selector_default() );

		// success message text and other style.
		$fields['message_success_background_color'] = array( 'background' => $this->get_success_message_selector_default() );
		$fields['message_success_margin']           = array( 'margin' => $this->get_success_message_selector_default() );
		$fields['message_success_padding']          = array( 'padding' => $this->get_success_message_selector_default() );
		$this->disq_fix_fonts_transition( $fields, 'message_success_text', $this->get_success_message_selector_default() );
		$this->disq_fix_border_transition( $fields, 'message_success', $this->get_success_message_selector_default() );
		$this->disq_fix_box_shadow_transition( $fields, 'message_success', $this->get_success_message_selector_default() );

		// button styles.
		$fields['form_button_background_color'] = array( 'background' => $this->get_submit_button_selector_default() );
		$fields['form_button_width']            = array( 'width' => $this->get_submit_button_selector_default() );
		$fields['form_button_margin']           = array( 'margin' => $this->get_submit_button_selector_default() );
		$fields['form_button_padding']          = array( 'padding' => $this->get_submit_button_selector_default() );
		$this->disq_fix_fonts_transition( $fields, 'form_button_text', $this->get_submit_button_selector_default() );
		$this->disq_fix_border_transition( $fields, 'form_button', $this->get_submit_button_selector_default() );
		$this->disq_fix_box_shadow_transition( $fields, 'form_button', $this->get_submit_button_selector_default() );

		// Default styles.
		$fields['background_layout'] = array( 'color' => $this->get_form_selector_default() );

		return $fields;
	}

	/**
	 * Generate styles.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return void
	 */
	protected function disq_generate_all_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'form_wrapper_background',
				'props'                  => $this->props,
				'selector'               => $this->get_form_selector_default(),
				'selector_hover'         => $this->get_form_selector_hover(),
				'selector_sticky'        => $this->get_form_selector_default(),
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_form_wrapper_background_color_gradient' => 'form_wrapper_background_use_color_gradient',
					'form_wrapper_background' => 'form_wrapper_background_color',
				),
			)
		);
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'fields_background',
				'props'                  => $this->props,
				'selector'               => $this->get_field_selector_default(),
				'selector_hover'         => $this->get_field_selector_hover(),
				'selector_sticky'        => $this->get_field_selector_default(),
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_fields_background_color_gradient' => 'fields_background_use_color_gradient',
					'fields_background'                    => 'fields_background_color',
				),
			)
		);

		// Working for an error message.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'message_error_background',
				'props'                  => $this->props,
				'selector'               => $this->get_error_message_selector_default(),
				'selector_hover'         => $this->get_error_message_selector_hover(),
				'selector_sticky'        => $this->get_error_message_selector_default(),
				'function_name'          => $this->slug,
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_message_error_background_color_gradient' => 'message_error_background_use_color_gradient',
					'message_error_background' => 'message_error_background_color',
				),
			)
		);
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'message_success_background',
				'props'                  => $this->props,
				'selector'               => $this->get_success_message_selector_default(),
				'selector_hover'         => $this->get_success_message_selector_hover(),
				'selector_sticky'        => $this->get_success_message_selector_default(),
				'function_name'          => $this->slug,
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_message_success_background_color_gradient' => 'message_success_background_use_color_gradient',
					'message_success_background' => 'message_success_background_color',
				),
			)
		);
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'form_button_background',
				'props'                  => $this->props,
				'selector'               => $this->get_submit_button_selector_default(),
				'selector_hover'         => $this->get_submit_button_selector_hover(),
				'selector_sticky'        => $this->get_submit_button_selector_default(),
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_form_button_background_color_gradient' => 'form_button_background_use_color_gradient',
					'form_button_background' => 'form_button_background_color',
				),
			)
		);

		// Set width for form button with default, responsive, hover.
		if ( 'on' === $this->prop( 'form_button_custom_width', 'off' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'form_button_width',
					'selector'       => $this->get_submit_button_selector_default(),
					'hover_selector' => $this->get_submit_button_selector_hover(),
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);
		}

		// margin, padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_margin',
				'selector'       => $this->get_form_selector_default(),
				'hover_selector' => $this->get_form_selector_hover(),
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_padding',
				'selector'       => $this->get_form_selector_default(),
				'hover_selector' => $this->get_form_selector_hover(),
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'field_margin',
				'selector'       => $this->get_field_selector_default(),
				'hover_selector' => $this->get_field_selector_hover(),
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'field_padding',
				'selector'       => $this->get_field_selector_default(),
				'hover_selector' => $this->get_field_selector_hover(),
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'message_error_margin',
				'selector'       => $this->get_error_message_selector_default(),
				'hover_selector' => $this->get_error_message_selector_hover(),
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'message_error_padding',
				'selector'       => $this->get_error_message_selector_default(),
				'hover_selector' => $this->get_error_message_selector_hover(),
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'message_success_margin',
				'selector'       => $this->get_success_message_selector_default(),
				'hover_selector' => $this->get_success_message_selector_hover(),
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'message_success_padding',
				'selector'       => $this->get_success_message_selector_default(),
				'hover_selector' => $this->get_success_message_selector_hover(),
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
	}

	/**
	 * Collect all forms from the database.
	 *
	 * @return array
	 */
	protected function disq_form_styler__get_all_forms() {
		return array(
			'0' => esc_html__( 'Select one', 'squad-modules-for-divi' ),
		);
	}

	/**
	 * Collect all posts from the database.
	 *
	 * @param array  $attrs   List of unprocessed attributes.
	 * @param string $content Content being processed.
	 *
	 * @return string the html output.
	 */
	public static function disq_form_styler__get_form_html( $attrs, $content = null ) {
		return null;
	}
}
