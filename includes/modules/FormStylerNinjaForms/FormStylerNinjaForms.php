<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: Ninja Forms Module Class which extend the Divi Builder Module Class.
 *
 * This class provides the ninja forms with customization opportunities in the visual builder.
 *
 * @since       1.4.7
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Modules\FormStylerNinjaForms;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Form_Styler_Module;
use DiviSquad\Utils\Helper;
use function esc_html__;
use function et_pb_background_options;
use function wp_json_encode;

/**
 * The Form Styler: WP Forms Module Class.
 *
 * @since       1.4.7
 * @package     squad-modules-for-divi
 */
class FormStylerNinjaForms extends Squad_Form_Styler_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.4.7
	 */
	public function init() {
		$this->name      = esc_html__( 'Ninja Forms', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Ninja Forms', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/ninja-forms.svg' );

		$this->slug       = 'disq_form_styler_ninja_forms';
		$this->vb_support = 'on';

		$this->main_css_element = "%%order_class%%.$this->slug";
	}

	/**
	 * Declare general fields for the module.
	 *
	 * @return array[]
	 * @since 1.4.7
	 */
	public function get_fields() {
		// Add new fields for the current module.
		$general_settings = array(
			'form_id'               => $this->disq_add_select_box_field(
				esc_html__( 'Form', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the ninja form.', 'squad-modules-for-divi' ),
					'options'          => $this->disq_form_styler__get_all_forms(),
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_messages__enable' => $this->disq_add_yes_no_field(
				esc_html__( 'Show Error & Success Messages', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the error and success messages in the visual  builder.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'__forms'               => array(
				'type'                => 'computed',
				'computed_callback'   => array( __CLASS__, 'disq_form_styler__get_form_html' ),
				'computed_depends_on' => array(
					'form_id',
				),
			),
		);

		// Additional new fields for current form styler.
		$title_background_fields = $this->disq_add_background_field(
			esc_html__( 'Title Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'title_background',
				'context'     => 'title_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'title',
			)
		);
		$title_associated_fields = array(
			'title_margin'  => $this->disq_add_margin_padding_field(
				esc_html__( 'Title Margin', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can define a custom margin size.', 'squad-modules-for-divi' ),
					'type'             => 'custom_margin',
					'range_settings'   => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'          => '||20px|||',
					'default_on_front' => '||20px|||',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'title',
				)
			),
			'title_padding' => $this->disq_add_margin_padding_field(
				esc_html__( 'Title Padding', 'squad-modules-for-divi' ),
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
					'toggle_slug'    => 'title',
				)
			),
		);

		// Collect all fields from the parent module.
		$parent_fields = parent::get_fields();

		// Remove unneeded fields.
		$parent_fields = $this->disq_remove_pre_assigned_fields(
			$parent_fields,
			array(
				'form_button_text',
				'form_button_icon_type',
				'form_button_icon',
				'form_button_icon_color',
				'form_button_image',
				'form_button_icon_size',
				'form_button_image_width',
				'form_button_image_height',
				'form_button_icon_gap',
				'form_button_icon_placement',
				'form_button_icon_margin',
				'form_button_icon_on_hover',
				'form_button_icon_hover_move_icon',
				'form_button_hover_animation__enable',
				'form_button_hover_animation_type',
				'form_button_elements_alignment',
			)
		);

		return array_merge_recursive( $parent_fields, $general_settings, $title_background_fields, $title_associated_fields );
	}

	/**
	 * Additional new fields for current form styler.
	 *
	 * @return array[]
	 * @since 1.4.7
	 */
	public function get_form_styler_additional_custom_fields() {
		return array(
			'form_field_height'    => $this->disq_add_range_field(
				esc_html__( 'General Field Height', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose gap between pagination elements.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '50px',
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'field',
				)
			),
			'form_textarea_height' => $this->disq_add_range_field(
				esc_html__( 'Textarea Field Height', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose gap between pagination elements.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '1000',
						'max'       => '1000',
						'step'      => '1',
					),
					'default'         => '200px',
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'field',
				)
			),
			'fields_before_margin' => array(
				'form_button_height' => $this->disq_add_range_field(
					esc_html__( 'Button Height', 'squad-modules-for-divi' ),
					array(
						'description'     => esc_html__( 'Here you can choose gap between pagination elements.', 'squad-modules-for-divi' ),
						'range_settings'  => array(
							'min_limit' => '1',
							'min'       => '1',
							'max_limit' => '200',
							'max'       => '200',
							'step'      => '1',
						),
						'default'         => '50px',
						'default_unit'    => 'px',
						'depends_show_if' => 'on',
						'tab_slug'        => 'advanced',
						'toggle_slug'     => 'form_button',
					)
				),
			),
		);
	}

	/**
	 * Declare advanced fields for the module
	 *
	 * @return array[]
	 */
	public function get_advanced_fields_config() {
		$form_selector         = $this->get_form_selector_default();
		$default_css_selectors = $this->disq_get_module_default_selectors();

		return array(
			'fonts'          => array(
				'title_text'             => $this->disq_add_font_field(
					esc_html__( 'Title', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '22px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .nf-form-cont .nf-form-title *",
							'hover' => "$this->main_css_element div .nf-form-cont .nf-form-title:hover *",
						),
					)
				),
				'form_before_text'       => $this->disq_add_font_field(
					esc_html__( 'Form Before', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '16px',
						),
						'css'       => array(
							'main'  => "$form_selector .nf-before-form-content .nf-form-fields-required",
							'hover' => "$form_selector .nf-before-form-content .nf-form-fields-required:hover",
						),
					)
				),
				'field_text'             => $this->disq_add_font_field(
					esc_html__( 'Field', 'squad-modules-for-divi' ),
					array(
						'font_size'  => array(
							'default' => '16px',
						),
						'text_color' => array(
							'default' => '#787878',
						),
						'css'        => array(
							'main'  => $this->get_field_selector_default(),
							'hover' => $this->get_field_selector_hover(),
						),
					)
				),
				'field_label_text'       => $this->disq_add_font_field(
					esc_html__( 'Label', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '18px',
						),
						'line_height' => array(
							'default' => '1.15em',
						),
						'css'         => array(
							'main'  => "$form_selector label, $form_selector legend, $form_selector .nf-label-span",
							'hover' => "$form_selector label:hover, $form_selector legend:hover, $form_selector .nf-label-span:hover",
						),
					)
				),
				'field_description_text' => $this->disq_add_font_field(
					esc_html__( 'Description', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '18px',
						),
						'line_height' => array(
							'default' => '1.15em',
						),
						'css'         => array(
							'main'  => "$form_selector .nf-field-description",
							'hover' => "$form_selector .nf-field-description:hover",
						),
					)
				),
				'placeholder_text'       => $this->disq_add_font_field(
					esc_html__( 'Placeholder', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector input::placeholder, $form_selector select::placeholder, $form_selector textarea::placeholder",
							'hover' => "$form_selector input:hover::placeholder, $form_selector select:hover::placeholder, $form_selector textarea:hover::placeholder",
						),
					)
				),
				'form_button_text'       => $this->disq_add_font_field(
					esc_html__( 'Button', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => $this->get_submit_button_selector_default(),
							'hover' => $this->get_submit_button_selector_hover(),
						),
					)
				),
				'message_error_text'     => $this->disq_add_font_field(
					esc_html__( 'Message', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .nf-form-wrap .nf-form-errors .nf-error-msg, $this->main_css_element div .nf-form-wrap .nf-error-wrap .nf-error-msg",
							'hover' => "$this->main_css_element div .nf-form-wrap .nf-form-errors:hover .nf-error-msg, $this->main_css_element div .nf-form-wrap .nf-error-wrap:hover .nf-error-msg",
						),
					)
				),
				'message_success_text'   => $this->disq_add_font_field(
					esc_html__( 'Message', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => $this->get_success_message_selector_default(),
							'hover' => $this->get_success_message_selector_hover(),
						),
					)
				),
			),
			'background'     => array_merge(
				$default_css_selectors,
				array(
					'settings' => array(
						'color' => 'alpha',
					),
				)
			),
			'borders'        => array(
				'default'         => $default_css_selectors,
				'wrapper'         => array(
					'label_prefix' => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => $this->get_form_selector_default(),
							'border_radii_hover'  => $this->get_form_selector_hover(),
							'border_styles'       => $this->get_form_selector_default(),
							'border_styles_hover' => $this->get_form_selector_hover(),
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'wrapper',
				),
				'title'           => array(
					'label_prefix' => esc_html__( 'Title', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .nf-form-cont .nf-form-title",
							'border_radii_hover'  => "$this->main_css_element div .nf-form-cont .nf-form-title:hover",
							'border_styles'       => "$this->main_css_element div .nf-form-cont .nf-form-title",
							'border_styles_hover' => "$this->main_css_element div .nf-form-cont .nf-form-title:hover",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'title',
				),
				'field'           => array(
					'label_prefix' => esc_html__( 'Field', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => $this->get_field_selector_default(),
							'border_radii_hover'  => $this->get_field_selector_hover(),
							'border_styles'       => $this->get_field_selector_default(),
							'border_styles_hover' => $this->get_field_selector_hover(),
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '1px|1px|1px|1px',
							'color' => '#bbb',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'field',
				),
				'form_button'     => array(
					'label_prefix' => esc_html__( 'Button', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => $this->get_submit_button_selector_default(),
							'border_radii_hover'  => $this->get_submit_button_selector_hover(),
							'border_styles'       => $this->get_submit_button_selector_default(),
							'border_styles_hover' => $this->get_submit_button_selector_hover(),
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'form_button',
				),
				'message_error'   => array(
					'label_prefix' => esc_html__( 'Message', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => $this->get_error_message_selector_default(),
							'border_radii_hover'  => $this->get_error_message_selector_hover(),
							'border_styles'       => $this->get_error_message_selector_default(),
							'border_styles_hover' => $this->get_error_message_selector_hover(),
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'color' => '#dc3232',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'message_error',
				),
				'message_success' => array(
					'label_prefix' => esc_html__( 'Message', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => $this->get_success_message_selector_default(),
							'border_radii_hover'  => $this->get_success_message_selector_hover(),
							'border_styles'       => $this->get_success_message_selector_default(),
							'border_styles_hover' => $this->get_success_message_selector_hover(),
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'color' => '#46b450',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'message_success',
				),
			),
			'box_shadow'     => array(
				'default'         => $default_css_selectors,
				'wrapper'         => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => $this->get_form_selector_default(),
						'hover' => $this->get_form_selector_hover(),
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'wrapper',
				),
				'title'           => array(
					'label'             => esc_html__( 'Title Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .nf-form-cont .nf-form-title",
						'hover' => "$this->main_css_element div .nf-form-cont .nf-form-title:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'title',
				),
				'field'           => array(
					'label'             => esc_html__( 'Field Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => $this->get_field_selector_default(),
						'hover' => $this->get_field_selector_hover(),
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'field',
				),
				'form_button'     => array(
					'label'             => esc_html__( 'Button Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => $this->get_submit_button_selector_default(),
						'hover' => $this->get_submit_button_selector_hover(),
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'form_button',
				),
				'message_error'   => array(
					'label'             => esc_html__( 'Message Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => $this->get_error_message_selector_default(),
						'hover' => $this->get_error_message_selector_hover(),
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'message_error',
				),
				'message_success' => array(
					'label'             => esc_html__( 'Message Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => $this->get_success_message_selector_default(),
						'hover' => $this->get_success_message_selector_hover(),
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'message_success',
				),
			),
			'margin_padding' => array(
				'use_padding' => true,
				'use_margin'  => true,
				'css'         => array(
					'margin'    => $this->main_css_element,
					'padding'   => $this->main_css_element,
					'important' => 'all',
				),
			),
			'max_width'      => array_merge(
				$default_css_selectors,
				array(
					'css' => array(
						'module_alignment' => "$this->main_css_element.et_pb_module",
					),
				)
			),
			'height'         => $default_css_selectors,
			'image_icon'     => false,
			'link_options'   => false,
			'filters'        => false,
			'text'           => false,
			'button'         => false,
		);
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
				'selector' => "$form_selector",
			),
			'title'           => array(
				'label'    => esc_html__( 'Title', 'squad-modules-for-divi' ),
				'selector' => "$this->main_css_element div .nf-form-cont .nf-form-title",
			),
			'field_label'     => array(
				'label'    => esc_html__( 'Field', 'squad-modules-for-divi' ),
				'selector' => "$this->main_css_element div .nf-form-cont .nf-field-label",
			),
			'field'           => array(
				'label'    => esc_html__( 'Field', 'squad-modules-for-divi' ),
				'selector' => $this->get_field_selector_default(),
			),
			'radio_checkbox'  => array(
				'label'    => esc_html__( 'Radio Checkbox', 'squad-modules-for-divi' ),
				'selector' => "$form_selector input[type=checkbox], $form_selector input[type=radio]",
			),
			'star_ratings'    => array(
				'label'    => esc_html__( 'Star Ratings', 'squad-modules-for-divi' ),
				'selector' => "$this->main_css_element div .nf-form-cont .starrating-wrap .star",
			),
			'form_button'     => array(
				'label'    => esc_html__( 'Button', 'squad-modules-for-divi' ),
				'selector' => $this->get_submit_button_selector_default(),
			),
			'fieldset_remove' => array(
				'label'    => esc_html__( 'Fieldset Remove Button', 'squad-modules-for-divi' ),
				'selector' => "$this->main_css_element div .nf-form-cont .nf-repeater-fieldsets .nf-remove-fieldset",
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
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the field list.
	 *
	 * @since 1.4.7
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// Get form selector.
		$form_selector = $this->get_form_selector_default();

		// title style.
		$fields['title_background_color'] = array( 'background' => "$this->main_css_element div .nf-form-cont .nf-form-title" );
		$fields['title_margin']           = array( 'margin' => "$this->main_css_element div .nf-form-cont .nf-form-title" );
		$fields['title_padding']          = array( 'padding' => "$this->main_css_element div .nf-form-cont .nf-form-title" );
		$this->disq_fix_fonts_transition( $fields, 'title_text', "$this->main_css_element div .nf-form-cont .nf-form-title *" );
		$this->disq_fix_border_transition( $fields, 'title', "$this->main_css_element div .nf-form-cont .nf-form-title" );
		$this->disq_fix_box_shadow_transition( $fields, 'title', "$this->main_css_element div .nf-form-cont .nf-form-title" );

		// Set custom height for field and form buttons.
		$form_field_selectors  = "$this->main_css_element div .nf-form-cont input.ninja-forms-field:not(input[type='submit'], input[type='radio'], input[type='checkbox'])";
		$form_field_selectors .= ", $this->main_css_element div .nf-form-cont select.ninja-forms-field:not([multiple])";
		$form_field_selectors .= ", $this->main_css_element div .nf-form-cont .listselect-wrap .nf-field-element div";
		$form_field_selectors .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-wrap > div div:after";
		$form_field_selectors .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-select-wrap > div div:after";

		// Line height selectors
		$line_height_selectors  = "$this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-wrap > div div:after";
		$line_height_selectors .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-select-wrap > div div:after";

		// Generic styles.
		$fields['form_field_height']    = array(
			'height'      => $form_field_selectors,
			'line-height' => $line_height_selectors,
		);
		$fields['form_textarea_height'] = array( 'height' => "$this->main_css_element div .nf-form-cont textarea.ninja-forms-field" );
		$fields['form_button_height']   = array( 'height' => "$this->main_css_element div .nf-form-cont input[type='submit'].ninja-forms-field, $this->main_css_element div .nf-form-cont button.nf-add-fieldset" );
		$fields['field_margin']         = array( 'margin' => "$form_selector .nf-field-element:not(:has(input[type='hidden']), :empty)" );
		$this->disq_fix_fonts_transition( $fields, 'form_before_text', "$form_selector .nf-before-form-content .nf-form-fields-required" );
		$this->disq_fix_fonts_transition( $fields, 'field_label_text', "$form_selector label, $form_selector legend, $form_selector .nf-label-span" );
		$this->disq_fix_fonts_transition( $fields, 'field_description_text', "$form_selector .nf-field-description" );
		$this->disq_fix_fonts_transition( $fields, 'message_error_text', "$this->main_css_element div .nf-form-wrap .nf-form-errors .nf-error-msg, $this->main_css_element div .nf-form-wrap .nf-error-wrap .nf-error-msg" );

		// checkbox and radio style.
		$fields['form_ch_rad_color'] = array( 'color' => "$this->main_css_element div .nf-form-cont label.nf-checked-label:before" );
		$fields['form_ch_rad_size']  = array(
			'width'  => "$this->main_css_element div .nf-form-cont label.nf-checked-label:before",
			'height' => "$this->main_css_element div .nf-form-cont label.nf-checked-label:before",
		);

		return $fields;
	}

	/**
	 * Render module output.
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string module's rendered output.
	 * @since 1.4.7
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		// Show a notice message in the frontend if the contact form is not installed.
		if ( ! function_exists( '\Ninja_Forms' ) ) {
			return sprintf(
				'<div class="disq_notice">%s</div>',
				esc_html__( 'Ninja Forms is not installed', 'squad-modules-for-divi' )
			);
		}

		if ( ! empty( self::disq_form_styler__get_form_html( $attrs ) ) ) {
			$this->disq_generate_all_styles( $attrs );

			return self::disq_form_styler__get_form_html( $attrs );
		}

		// Show a notice message in the frontend if the form is not selected.
		return sprintf(
			'<div class="disq_notice">%s</div>',
			esc_html__( 'Please select a ninja form.', 'squad-modules-for-divi' )
		);
	}

	/**
	 * Collect all from the database.
	 *
	 * @param string $type The value type.
	 *
	 * @return array the html output.
	 * @since 1.4.7
	 */
	public static function get_form_styler_forms_collection( $type = 'id' ) {
		if ( count( self::$forms_collection[ $type ] ) === 0 && function_exists( '\Ninja_Forms' ) ) {
			// Collect available wp form from a database.
			$ninja_forms = \Ninja_Forms()->form()->get_forms();
			if ( is_array( $ninja_forms ) && count( $ninja_forms ) ) {
				/**
				 * @var \NF_Abstracts_Model[] $ninja_forms
				 * @var \NF_Abstracts_Model   $form
				 */
				foreach ( $ninja_forms as $form ) {
					self::$forms_collection[ $type ][ md5( $form->get_id() ) ] = 'title' === $type ? $form->get_setting( 'title' ) : $form->get_id();
				}
			}
		}

		return self::$forms_collection[ $type ];
	}

	/**
	 * Collect all wp form from the database.
	 *
	 * @return array
	 */
	public function disq_form_styler__get_all_forms() {
		$forms = array(
			md5( 0 ) => esc_html__( 'Select one', 'squad-modules-for-divi' ),
		);

		return array_merge( $forms, $this->get_form_styler_forms_collection( 'title' ) );
	}

	/**
	 * Show form in the frontend
	 *
	 * @param array  $attrs   List of unprocessed attributes.
	 * @param string $content Content being processed.
	 *
	 * @return string the html output.
	 * @since 1.4.7
	 */
	public static function disq_form_styler__get_form_html( $attrs, $content = null ) {
		// Collect all posts from the database.
		$collection = self::get_form_styler_forms_collection();
		if ( ! empty( $attrs['form_id'] ) && self::$default_form_id !== $attrs['form_id'] && isset( $collection[ $attrs['form_id'] ] ) ) {
			// Collect html output from shortcode.
			ob_start();
			\Ninja_Forms()->display( $collection[ $attrs['form_id'] ] );

			if ( is_array( $content ) ) {
				printf(
					'<script id="disq-nf-builder-js-i18n" type="text/javascript">%1$s</script>',
					wp_json_encode( \Ninja_Forms::config( 'i18nFrontEnd' ) )
				);
			}

			return ob_get_clean();
		}

		return null;
	}

	/**
	 * Get the stylesheet configuration for generating styles.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return array
	 */
	protected function disq_get_module_stylesheet_selectors( $attrs ) {
		$options = parent::disq_get_module_stylesheet_selectors( $attrs );

		// Get form selector.
		$form_selector = $this->get_form_selector_default();

		// all background type styles.
		$options['title_background']       = array(
			'type'           => 'background',
			'selector'       => "$this->main_css_element div .nf-form-cont .nf-form-title",
			'selector_hover' => "$this->main_css_element div .nf-form-cont .nf-form-title:hover",
		);
		$options['custom_html_background'] = array(
			'type'           => 'background',
			'selector'       => "$form_selector .ff-custom_html",
			'selector_hover' => "$form_selector .ff-custom_html:hover",
		);

		// Checkbox and radio fields.
		$options['form_ch_rad_color'] = array(
			'type'           => 'default',
			'selector'       => "$this->main_css_element div .nf-form-cont label.nf-checked-label:before",
			'hover_selector' => "$this->main_css_element div .nf-form-cont label.nf-checked-label:hover:before",
			'css_property'   => 'accent-color',
			'data_type'      => 'text',
		);
		$options['form_ch_rad_size']  = array(
			'type'      => 'default',
			'data_type' => 'range',
			'options'   => array(
				array(
					'selector'       => "$this->main_css_element div .nf-form-cont label.nf-checked-label:before",
					'hover_selector' => "$this->main_css_element div .nf-form-cont label.nf-checked-label:hover:before",
					'css_property'   => 'width',
				),
				array(
					'selector'       => "$this->main_css_element div .nf-form-cont label.nf-checked-label:before",
					'hover_selector' => "$this->main_css_element div .nf-form-cont label.nf-checked-label:hover:before",
					'css_property'   => 'height',
				),
			),
		);

		// Set custom height for field and form buttons.
		$form_field_selectors  = "$this->main_css_element div .nf-form-cont input.ninja-forms-field:not(input[type='submit'], input[type='radio'], input[type='checkbox'])";
		$form_field_selectors .= ", $this->main_css_element div .nf-form-cont select.ninja-forms-field:not([multiple])";
		$form_field_selectors .= ", $this->main_css_element div .nf-form-cont .listselect-wrap .nf-field-element div";
		$form_field_selectors .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-wrap > div div:after";
		$form_field_selectors .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-select-wrap > div div:after";

		// hover.
		$form_field_selectors_hover  = "$this->main_css_element div .nf-form-cont input.ninja-forms-field:not(input[type='submit'], input[type='radio'], input[type='checkbox']):hover";
		$form_field_selectors_hover .= ", $this->main_css_element div .nf-form-cont select.ninja-forms-field:not([multiple]):hover";
		$form_field_selectors_hover .= ", $this->main_css_element div .nf-form-cont .listselect-wrap .nf-field-element div:hover";
		$form_field_selectors_hover .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-wrap > div:hover div:after";
		$form_field_selectors_hover .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-select-wrap > div:hover div:after";

		$line_height_selectors  = "$this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-wrap > div div:after";
		$line_height_selectors .= ", $this->main_css_element .disq_form_styler_ninja_forms div form .nf-form-content .list-select-wrap > div div:after";

		$options['form_field_height']    = array(
			'type'      => 'default',
			'data_type' => 'range',
			'options'   => array(
				array(
					'selector'       => $form_field_selectors,
					'hover_selector' => $form_field_selectors_hover,
					'css_property'   => 'height',
				),
				array(
					'selector'     => $line_height_selectors,
					'css_property' => 'line-height',
				),
			),
		);
		$options['form_textarea_height'] = array(
			'type'           => 'default',
			'selector'       => "$this->main_css_element div .nf-form-cont textarea.ninja-forms-field",
			'hover_selector' => "$this->main_css_element div .nf-form-cont textarea.ninja-forms-field:hover",
			'css_property'   => 'height',
			'data_type'      => 'range',
		);
		$options['form_button_height']   = array(
			'type'           => 'default',
			'selector'       => "$this->main_css_element div .nf-form-cont input[type='submit'].ninja-forms-field, $this->main_css_element div .nf-form-cont button.nf-add-fieldset",
			'hover_selector' => "$this->main_css_element div .nf-form-cont input[type='submit'].ninja-forms-field:hover, $this->main_css_element div .nf-form-cont button.nf-add-fieldset:hover",
			'css_property'   => 'height',
			'data_type'      => 'range',
		);

		// all margin, padding type styles.
		$options['title_margin']  = array(
			'type'           => 'margin',
			'selector'       => "$this->main_css_element div .nf-form-cont .nf-form-title",
			'hover_selector' => "$this->main_css_element div .nf-form-cont .nf-form-title:hover",
		);
		$options['title_padding'] = array(
			'type'           => 'padding',
			'selector'       => "$this->main_css_element div .nf-form-cont .nf-form-title",
			'hover_selector' => "$this->main_css_element div .nf-form-cont .nf-form-title:hover",
		);
		$options['field_margin']  = array(
			'type'           => 'margin',
			'selector'       => "$form_selector .nf-field-element:not(:has(input[type='hidden']), :empty)",
			'hover_selector' => "$form_selector .nf-field-element:not(:has(input[type='hidden']), :empty):hover",
		);

		return $options;
	}

	/**
	 * Generate styles.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return void
	 */
	protected function disq_generate_all_styles( $attrs ) {
		// Get merge all attributes.
		$attrs = array_merge( $attrs, $this->props );

		// Get stylesheet selectors.
		$options = $this->disq_get_module_stylesheet_selectors( $attrs );

		// Generate module styles from hook.
		$this->form_styler_generate_module_styles( $attrs, $options );
	}

	/**
	 * Get the stylesheet selector for form tag.
	 *
	 * @return string
	 */
	protected function get_form_selector_default() {
		return "$this->main_css_element div .nf-form-wrap .nf-form-layout form";
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	protected function get_form_selector_hover() {
		return "$this->main_css_element div .nf-form-wrap .nf-form-layout form:hover";
	}

	/**
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	protected function get_field_selector_default() {
		$form_selector       = $this->get_form_selector_default();
		$allowed_form_fields = $this->disq_get_allowed_form_fields();

		// Add new fields
		$allowed_form_fields[] = '.listimage-wrap .nf-field-element label';

		$selectors = array();
		foreach ( $allowed_form_fields as $allowed_field ) {
			$selectors[] = "$form_selector $allowed_field";
		}

		return implode( ', ', $selectors );
	}

	/**
	 * Get the stylesheet selector for form fields to use in hover.
	 *
	 * @return string
	 */
	protected function get_field_selector_hover() {
		$form_selector = $this->get_form_selector_default();

		$selectors = array();
		foreach ( $this->disq_get_allowed_form_fields() as $allowed_field ) {
			$selectors[] = "$form_selector $allowed_field:hover";
		}

		return implode( ', ', $selectors );
	}

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_default() {
		return "$this->main_css_element div .nf-form-wrap .nf-form-errors, $this->main_css_element div .nf-form-wrap .nf-error-wrap";
	}

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->main_css_element div .nf-form-wrap .nf-form-errors:hover, $this->main_css_element div .nf-form-wrap .nf-error-wrap:hover";
	}

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->main_css_element div .nf-form-wrap .nf-response-msg";
	}

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_hover() {
		return "$this->main_css_element div .nf-form-wrap .nf-response-msg:hover";
	}

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->main_css_element div .nf-form-wrap .nf-form-layout form input[type=submit], $this->main_css_element div .nf-form-wrap .nf-form-layout form button:not(.nf-remove-fieldset)";
	}

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->main_css_element div .nf-form-wrap .nf-form-layout form input[type=submit]:hover, $this->main_css_element div .nf-form-wrap .nf-form-layout form button:not(.nf-remove-fieldset):hover";
	}
}

// Load the form styler (Ninja Forms) Module.
new FormStylerNinjaForms();