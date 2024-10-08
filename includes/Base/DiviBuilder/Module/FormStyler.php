<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Base for Form Styler Class
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.0.0
 */

namespace DiviSquad\Base\DiviBuilder\Module;

use DiviSquad\Base\DiviBuilder\Module;
use DiviSquad\Base\DiviBuilder\Utils;
use function esc_html__;
use function et_pb_background_options;
use function wp_parse_args;

/**
 * Builder Utils Helper Class which help to the all module class
 *
 * @package DiviSquad
 * @since   1.0.0
 */
abstract class FormStyler extends Module {

	/**
	 * Collect all posts from the database.
	 *
	 * @param array  $attrs   List of unprocessed attributes.
	 * @param string $content Content being processed.
	 *
	 * @return string the html output.
	 */
	public static function squad_form_styler__get_form_html( $attrs, $content = null ) {
		return '';
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
					'wrapper'                => esc_html__( 'Form Wrapper', 'squad-modules-for-divi' ),
					'title'                  => esc_html__( 'Form Title', 'squad-modules-for-divi' ),
					'title_text'             => esc_html__( 'Form Title Text', 'squad-modules-for-divi' ),
					'form_before_text'       => esc_html__( 'Form Before Text', 'squad-modules-for-divi' ),
					'field'                  => esc_html__( 'Field', 'squad-modules-for-divi' ),
					'field_text'             => esc_html__( 'Field Text', 'squad-modules-for-divi' ),
					'field_label_text'       => esc_html__( 'Field Label Text', 'squad-modules-for-divi' ),
					'field_description_text' => esc_html__( 'Field Description Text', 'squad-modules-for-divi' ),
					'placeholder_text'       => esc_html__( 'Field Placeholder Text', 'squad-modules-for-divi' ),
					'form_custom_html'       => esc_html__( 'Custom HTML', 'squad-modules-for-divi' ),
					'form_custom_html_text'  => esc_html__( 'Custom HTML Text', 'squad-modules-for-divi' ),
					'form_button'            => esc_html__( 'Button', 'squad-modules-for-divi' ),
					'form_button_text'       => esc_html__( 'Button Text', 'squad-modules-for-divi' ),
					'message_error'          => esc_html__( 'Error Message', 'squad-modules-for-divi' ),
					'message_error_text'     => esc_html__( 'Error Message Text', 'squad-modules-for-divi' ),
					'message_success'        => esc_html__( 'Success Message', 'squad-modules-for-divi' ),
					'message_success_text'   => esc_html__( 'Success Message Text', 'squad-modules-for-divi' ),
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
		$wrapper_background_fields         = $this->squad_utils->add_background_field(
			array(
				'label'       => esc_html__( 'Wrapper Background', 'squad-modules-for-divi' ),
				'base_name'   => 'form_wrapper_background',
				'context'     => 'form_wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'wrapper',
			)
		);
		$fields_background_fields          = $this->squad_utils->add_background_field(
			array(
				'label'       => esc_html__( 'Field Background', 'squad-modules-for-divi' ),
				'base_name'   => 'fields_background',
				'context'     => 'fields_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'field',
			)
		);
		$message_error_background_fields   = $this->squad_utils->add_background_field(
			array(
				'label'       => esc_html__( 'Message Background', 'squad-modules-for-divi' ),
				'base_name'   => 'message_error_background',
				'context'     => 'message_error_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'message_error',
			)
		);
		$message_success_background_fields = $this->squad_utils->add_background_field(
			array(
				'label'       => esc_html__( 'Message Background', 'squad-modules-for-divi' ),
				'base_name'   => 'message_success_background',
				'context'     => 'message_success_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'message_success',
			)
		);

		$custom_spacing_prefixes  = Utils\Elements\Forms::get_custom_spacing_prefixes();
		$additional_custom_fields = array();
		$custom_spacing_fields    = array();

		foreach ( $custom_spacing_prefixes as $prefix => $options ) {
			$label = ! empty( $options['label'] ) ? $options['label'] : '';

			/* translators: The Element Label */
			$label_margin = sprintf( esc_html__( '%s Margin', 'squad-modules-for-divi' ), $label );
			/* translators: The Element Label */
			$label_padding = sprintf( esc_html__( '%s Padding', 'squad-modules-for-divi' ), $label );

			// The default attributes for margin and padding field.
			$field_attributes = array(
				'range_settings' => array(
					'min_limit' => '1',
					'min'       => '1',
					'max_limit' => '100',
					'max'       => '100',
					'step'      => '1',
				),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => $prefix,
			);

			// Set the margin & padding field for this element.
			$custom_spacing_fields[ "{$prefix}_margin" ]  = Utils::add_margin_padding_field(
				$label_margin,
				array_merge(
					$field_attributes,
					array(
						'type'        => 'custom_margin',
						'description' => esc_html__( 'Here you can define a custom margin size.', 'squad-modules-for-divi' ),
					)
				)
			);
			$custom_spacing_fields[ "{$prefix}_padding" ] = Utils::add_margin_padding_field(
				$label_padding,
				array_merge(
					$field_attributes,
					array(
						'type'        => 'custom_padding',
						'description' => esc_html__( 'Here you can define a custom padding size.', 'squad-modules-for-divi' ),
					)
				)
			);
		}

		if ( method_exists( $this, 'get_form_styler_additional_custom_fields' ) ) {
			$additional_custom_fields = $this->get_form_styler_additional_custom_fields();
		}

		$fields_after_background = array();
		$fields_before_margin    = array();
		if ( isset( $additional_custom_fields['button_fields_after_background'] ) ) {
			$fields_after_background = $additional_custom_fields['button_fields_after_background'];
			unset( $additional_custom_fields['button_fields_after_background'] );
		}
		if ( isset( $additional_custom_fields['button_fields_before_margin'] ) ) {
			$fields_before_margin = $additional_custom_fields['button_fields_before_margin'];
			unset( $additional_custom_fields['button_fields_before_margin'] );
		}

		// Button fields definitions.
		$form_submit_button = $this->squad_utils->get_button_fields(
			array(
				'base_attr_name'          => 'form_button',
				'fields_after_background' => $fields_after_background,
				'fields_before_margin'    => $fields_before_margin,
				'toggle_slug'             => 'form_button',
				'depends_show_if'         => 'on',
			)
		);

		// Checkbox and Radio fields definitions.
		$checkbox_radio_fields = array(
			'form_ch_rad_color' => Utils::add_color_field(
				esc_html__( 'Checkbox & Radio Active Color', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom color for checkbox and radio fields.', 'squad-modules-for-divi' ),
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'field',
				)
			),
			'form_ch_rad_size'  => Utils::add_range_field(
				esc_html__( 'Checkbox & Radio Field Size', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can choose size for checkbox and radio fields.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default_unit'   => 'px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'field',
				)
			),
		);

		return array_merge_recursive(
			$form_submit_button,
			$wrapper_background_fields,
			$fields_background_fields,
			$message_error_background_fields,
			$message_success_background_fields,
			$checkbox_radio_fields,
			$additional_custom_fields,
			$custom_spacing_fields
		);
	}

	/**
	 * Declare custom css fields for the module
	 *
	 * @param array $fields   List of fields.
	 * @param array $removals List of removable fields.
	 *
	 * @return array[]
	 */
	public function squad_remove_pre_assigned_fields( $fields, $removals ) {
		if ( count( $fields ) > 1 && count( $removals ) > 1 ) {
			foreach ( $removals as $removal ) {
				unset( $fields[ $removal ] );
			}

			return $fields;
		}

		return $fields;
	}

	/**
	 * Declare custom css fields for the module
	 *
	 * @return array[]
	 */
	public function get_custom_css_fields_config() {
		$form_selector = $this->get_form_selector_default();

		return array(
			'wrapper'         => array(
				'label'    => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
				'selector' => $form_selector,
			),
			'field'           => array(
				'label'    => esc_html__( 'Field', 'squad-modules-for-divi' ),
				'selector' => $this->get_field_selector_default(),
			),
			'radio_checkbox'  => array(
				'label'    => esc_html__( 'Radio Checkbox', 'squad-modules-for-divi' ),
				'selector' => "$form_selector input[type=checkbox], $form_selector input[type=radio]",
			),
			'form_button'     => array(
				'label'    => esc_html__( 'Button', 'squad-modules-for-divi' ),
				'selector' => $this->get_submit_button_selector_default(),
			),
			'message_error'   => array(
				'label'    => esc_html__( 'Error Message', 'squad-modules-for-divi' ),
				'selector' => $this->get_error_message_selector_default(),
			),
			'message_success' => array(
				'label'    => esc_html__( 'Success Message', 'squad-modules-for-divi' ),
				'selector' => $this->get_success_message_selector_default(),
			),
		);
	}

	/**
	 * Get the stylesheet selector for form tag.
	 *
	 * @return string
	 */
	abstract protected function get_form_selector_default();

	/**
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	abstract protected function get_field_selector_default();

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	abstract protected function get_submit_button_selector_default();

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	abstract protected function get_error_message_selector_default();

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	abstract protected function get_success_message_selector_default();

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
		Utils::fix_border_transition( $fields, 'wrapper', $this->get_form_selector_default() );
		Utils::fix_box_shadow_transition( $fields, 'wrapper', $this->get_form_selector_default() );

		// field text and others style.
		$fields['fields_background_color'] = array( 'background' => $this->get_field_selector_default() );
		$fields['field_margin']            = array( 'margin' => $this->get_field_selector_default() );
		$fields['field_padding']           = array( 'padding' => $this->get_field_selector_default() );
		Utils::fix_fonts_transition( $fields, 'field_text', $this->get_field_selector_default() );
		Utils::fix_border_transition( $fields, 'field', $this->get_field_selector_default() );
		Utils::fix_box_shadow_transition( $fields, 'field', $this->get_field_selector_default() );

		// error message text and others styles.
		$fields['message_error_background_color'] = array( 'background' => $this->get_error_message_selector_default() );
		$fields['message_error_margin']           = array( 'margin' => $this->get_error_message_selector_default() );
		$fields['message_error_padding']          = array( 'padding' => $this->get_error_message_selector_default() );
		Utils::fix_fonts_transition( $fields, 'message_error_text', $this->get_error_message_selector_default() );
		Utils::fix_border_transition( $fields, 'message_error', $this->get_error_message_selector_default() );
		Utils::fix_box_shadow_transition( $fields, 'message_error', $this->get_error_message_selector_default() );

		// success message text and other styles.
		$fields['message_success_background_color'] = array( 'background' => $this->get_success_message_selector_default() );
		$fields['message_success_margin']           = array( 'margin' => $this->get_success_message_selector_default() );
		$fields['message_success_padding']          = array( 'padding' => $this->get_success_message_selector_default() );
		Utils::fix_fonts_transition( $fields, 'message_success_text', $this->get_success_message_selector_default() );
		Utils::fix_border_transition( $fields, 'message_success', $this->get_success_message_selector_default() );
		Utils::fix_box_shadow_transition( $fields, 'message_success', $this->get_success_message_selector_default() );

		// button styles.
		$fields['form_button_background_color'] = array( 'background' => $this->get_submit_button_selector_default() );
		$fields['form_button_width']            = array( 'width' => $this->get_submit_button_selector_default() );
		$fields['form_button_height']           = array( 'height' => $this->get_submit_button_selector_default() );
		$fields['form_button_margin']           = array( 'margin' => $this->get_submit_button_selector_default() );
		$fields['form_button_padding']          = array( 'padding' => $this->get_submit_button_selector_default() );
		Utils::fix_fonts_transition( $fields, 'form_button_text', $this->get_submit_button_selector_default() );
		Utils::fix_border_transition( $fields, 'form_button', $this->get_submit_button_selector_default() );
		Utils::fix_box_shadow_transition( $fields, 'form_button', $this->get_submit_button_selector_default() );

		// Get form selector.
		$form_selector = $this->get_form_selector_default();

		// Generic styles.
		Utils::fix_fonts_transition( $fields, 'field_label_text', "$form_selector label, $form_selector legend" );
		Utils::fix_fonts_transition( $fields, 'placeholder_text', "$form_selector input::placeholder, $form_selector select::placeholder, $form_selector textarea::placeholder" );

		// checkbox and radio style.
		$fields['form_ch_rad_color'] = array( 'color' => "$this->main_css_element input[type=checkbox], $this->main_css_element input[type=radio]" );
		$fields['form_ch_rad_size']  = array(
			'width'  => "$this->main_css_element input[type=checkbox], $this->main_css_element input[type=radio]",
			'height' => "$this->main_css_element input[type=checkbox], $this->main_css_element input[type=radio]",
		);

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
	protected function squad_generate_all_styles( $attrs ) {
		// Get merge all attributes.
		$attrs = array_merge( $attrs, $this->props );

		// Get stylesheet selectors.
		$options = $this->squad_get_module_stylesheet_selectors( $attrs );

		// Generate module styles from hook.
		$this->squad_form_styler_generate_module_styles( $attrs, $options );
	}

	/**
	 * Get the stylesheet configuration for generating styles.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return array
	 */
	protected function squad_get_module_stylesheet_selectors( $attrs ) {
		$options = array();

		// Get form selector.
		$form_selector = $this->get_form_selector_default();

		// all background type styles.
		$options['form_wrapper_background']    = array(
			'type'           => 'background',
			'selector'       => $this->get_form_selector_default(),
			'selector_hover' => $this->get_form_selector_hover(),
		);
		$options['fields_background']          = array(
			'type'           => 'background',
			'selector'       => $this->get_field_selector_default(),
			'selector_hover' => $this->get_field_selector_hover(),
		);
		$options['form_button_background']     = array(
			'type'           => 'background',
			'selector'       => $this->get_submit_button_selector_default(),
			'selector_hover' => $this->get_submit_button_selector_hover(),
		);
		$options['message_error_background']   = array(
			'type'           => 'background',
			'selector'       => $this->get_error_message_selector_default(),
			'selector_hover' => $this->get_error_message_selector_hover(),
		);
		$options['message_success_background'] = array(
			'type'           => 'background',
			'selector'       => $this->get_success_message_selector_default(),
			'selector_hover' => $this->get_success_message_selector_hover(),
		);

		// Checkbox and radio fields.
		$options['form_ch_rad_color'] = array(
			'type'           => 'default',
			'selector'       => "$form_selector input[type=checkbox], $form_selector input[type=radio]",
			'hover_selector' => "$form_selector input[type=checkbox]:hover, $form_selector input[type=radio]:hover",
			'css_property'   => 'accent-color',
			'data_type'      => 'text',
		);
		$options['form_ch_rad_size']  = array(
			'type'      => 'default',
			'data_type' => 'range',
			'options'   => array(
				array(
					'selector'       => "$form_selector input[type=checkbox], $form_selector input[type=radio]",
					'hover_selector' => "$form_selector input[type=checkbox]:hover, $form_selector input[type=radio]:hover",
					'css_property'   => 'width',
				),
				array(
					'selector'       => "$form_selector input[type=checkbox], $form_selector input[type=radio]",
					'hover_selector' => "$form_selector input[type=checkbox]:hover, $form_selector input[type=radio]:hover",
					'css_property'   => 'height',
				),
			),
		);

		// Set width for form button with default, responsive, hover.
		if ( ! empty( $attrs['form_button_custom_width'] ) && 'on' === $attrs['form_button_custom_width'] ) {
			$options['form_button_width'] = array(
				'type'           => 'default',
				'selector'       => $this->get_submit_button_selector_default(),
				'hover_selector' => $this->get_submit_button_selector_hover(),
				'css_property'   => 'width',
				'data_type'      => 'range',
			);
		}

		// all margins, padding type styles.
		$options['wrapper_margin']          = array(
			'type'           => 'margin',
			'selector'       => $this->get_form_selector_default(),
			'hover_selector' => $this->get_form_selector_hover(),
		);
		$options['wrapper_padding']         = array(
			'type'           => 'padding',
			'selector'       => $this->get_form_selector_default(),
			'hover_selector' => $this->get_form_selector_hover(),
		);
		$options['field_margin']            = array(
			'type'           => 'margin',
			'selector'       => $this->get_field_selector_default(),
			'hover_selector' => $this->get_field_selector_hover(),
		);
		$options['field_padding']           = array(
			'type'           => 'padding',
			'selector'       => $this->get_field_selector_default(),
			'hover_selector' => $this->get_field_selector_hover(),
		);
		$options['form_button_margin']      = array(
			'type'           => 'margin',
			'selector'       => $this->get_submit_button_selector_default(),
			'hover_selector' => $this->get_submit_button_selector_hover(),
		);
		$options['form_button_padding']     = array(
			'type'           => 'padding',
			'selector'       => $this->get_submit_button_selector_default(),
			'hover_selector' => $this->get_submit_button_selector_hover(),
		);
		$options['message_error_margin']    = array(
			'type'           => 'margin',
			'selector'       => $this->get_error_message_selector_default(),
			'hover_selector' => $this->get_error_message_selector_hover(),
		);
		$options['message_error_padding']   = array(
			'type'           => 'padding',
			'selector'       => $this->get_error_message_selector_default(),
			'hover_selector' => $this->get_error_message_selector_hover(),
		);
		$options['message_success_margin']  = array(
			'type'           => 'margin',
			'selector'       => $this->get_success_message_selector_default(),
			'hover_selector' => $this->get_success_message_selector_hover(),
		);
		$options['message_success_padding'] = array(
			'type'           => 'padding',
			'selector'       => $this->get_success_message_selector_default(),
			'hover_selector' => $this->get_success_message_selector_hover(),
		);

		return $options;
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_form_selector_hover();

	/**
	 * Get the stylesheet selector for form fields to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_field_selector_hover();

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_submit_button_selector_hover();

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_error_message_selector_hover();

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	abstract protected function get_success_message_selector_hover();

	/**
	 * Generate styles.
	 *
	 * @param array $attrs   List of unprocessed attributes.
	 * @param array $options Control attributes.
	 *
	 * @return void
	 */
	protected function squad_form_styler_generate_module_styles( $attrs, $options ) {
		if ( count( $attrs ) > 1 && count( $options ) > 1 ) {
			foreach ( $options as $option_key => $option ) {
				if ( ! empty( $option['type'] ) && 'background' === $option['type'] ) {
					$defaults = array(
						'base_prop_name' => '',
						'selector'       => '',
						'selector_hover' => '',
					);
					$option   = wp_parse_args( $option, $defaults );

					// Set the prop name aliases.
					$prop_name_aliases = array(
						"use_{$option_key}_color_gradient" => "{$option_key}_use_color_gradient",
						$option_key                        => "{$option_key}_color",
					);

					// Generate background styles with default, responsive, hover.
					et_pb_background_options()->get_background_style(
						array(
							'base_prop_name'         => $option_key,
							'props'                  => $attrs,
							'function_name'          => $this->slug,
							'selector'               => $option['selector'],
							'selector_hover'         => $option['selector_hover'],
							'selector_sticky'        => $option['selector'],
							'important'              => ' !important',
							'use_background_video'   => false,
							'use_background_pattern' => false,
							'use_background_mask'    => false,
							'prop_name_aliases'      => $prop_name_aliases,
						)
					);
				}
				if ( ! empty( $option['type'] ) && 'default' === $option['type'] ) {
					$defaults = array(
						'selector'       => '',
						'hover_selector' => '',
						'css_property'   => '',
					);
					$option   = wp_parse_args( $option, $defaults );

					// Generate responsive + hover + sticky style using the same configuration at once.
					if ( isset( $option['options'] ) && is_array( $option['options'] ) && count( $option['options'] ) > 0 ) {
						foreach ( $option['options'] as $nested_option ) {
							$this->generate_styles(
								array(
									'base_attr_name' => $option_key,
									'selector'       => $nested_option['selector'],
									'hover_selector' => ! empty( $nested_option['hover_selector'] ) ? $nested_option['hover_selector'] : '',
									'css_property'   => $nested_option['css_property'],
									'render_slug'    => $this->slug,
									'type'           => $option['data_type'],
								)
							);
						}
					} else {
						$this->generate_styles(
							array(
								'base_attr_name' => $option_key,
								'selector'       => $option['selector'],
								'hover_selector' => ! empty( $nested_option['hover_selector'] ) ? $nested_option['hover_selector'] : '',
								'css_property'   => $option['css_property'],
								'render_slug'    => $this->slug,
								'type'           => $option['data_type'],
							)
						);
					}
				}
				if ( ! empty( $option['type'] ) && in_array( $option['type'], array( 'margin', 'padding' ), true ) ) {
					$defaults = array(
						'selector'       => '',
						'hover_selector' => '',
						'css_property'   => $option['type'],
					);
					$option   = wp_parse_args( $option, $defaults );

					// Generate margin and padding for module.
					$this->squad_utils->generate_margin_padding_styles(
						array(
							'field'          => $option_key,
							'selector'       => $option['selector'],
							'hover_selector' => $option['hover_selector'],
							'css_property'   => $option['type'],
							'type'           => $option['type'],
						)
					);
				}
			}
		}
	}
}
