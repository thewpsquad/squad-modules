<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: Contact Form 7 Module Class which extend the Divi Builder Module Class.
 *
 * This class provides contact form 7 with customization opportunities in the visual builder.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Modules\FormStylerContactForm7;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Form_Styler_Module;
use DiviSquad\Utils\Divi;
use DiviSquad\Utils\Helper;
use function do_shortcode;
use function esc_html__;
use function get_posts;

/**
 * The Form Styler: Contact Form 7 Module Class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 */
class FormStylerContactForm7 extends Squad_Form_Styler_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Contact Form 7', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Contact Form 7', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/contact-form-7.svg' );

		$this->slug       = 'disq_form_styler_cf7';
		$this->vb_support = 'on';

		$this->main_css_element = "%%order_class%%.$this->slug";
	}

	/**
	 * Declare general fields for the module.
	 *
	 * @return array[]
	 * @since 1.0.0
	 */
	public function get_fields() {
		// Collect all fields from the parent module.
		$parent_fields = parent::get_fields();

		// Add new fields for the current module.
		$general_settings = array(
			'form_id'               => $this->disq_add_select_box_field(
				esc_html__( 'Form', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the contact form 7.', 'squad-modules-for-divi' ),
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

		// update fields.
		$parent_fields['form_button_icon_type']['toggle_slug'] = 'field_icons';
		$parent_fields['form_button_icon']['toggle_slug']      = 'field_icons';
		$parent_fields['form_button_image']['toggle_slug']     = 'field_icons';

		// Remove unneeded fields.
		$parent_fields = $this->disq_remove_pre_assigned_fields(
			$parent_fields,
			array(
				'form_button_text',
				'form_button_icon_size',
				'form_button_icon_gap',
				'form_button_icon_hover_move_icon',
				'form_button_hover_animation__enable',
				'form_button_hover_animation_type',
			)
		);

		return array_merge_recursive( $parent_fields, $general_settings );
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
				'field_text'           => $this->disq_add_font_field(
					esc_html__( 'Field', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => $this->get_field_selector_default(),
							'hover' => $this->get_field_selector_hover(),
						),
					)
				),
				'field_label_text'     => $this->disq_add_font_field(
					esc_html__( 'Label', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector label",
							'hover' => "$form_selector label:hover",
						),
					)
				),
				'placeholder_text'     => $this->disq_add_font_field(
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
				'form_button_text'     => $this->disq_add_font_field(
					esc_html__( 'Button', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button",
							'hover' => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button:hover",
						),
					)
				),
				'message_error_text'   => $this->disq_add_font_field(
					esc_html__( 'Message', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors",
							'hover' => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover",
						),
					)
				),
				'message_success_text' => $this->disq_add_font_field(
					esc_html__( 'Message', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok",
							'hover' => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output:hover, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok:hover",
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
							'border_radii'        => "$form_selector",
							'border_radii_hover'  => "$form_selector:hover",
							'border_styles'       => "$form_selector",
							'border_styles_hover' => "$form_selector:hover",
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
							'border_radii'        => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button",
							'border_radii_hover'  => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button:hover",
							'border_styles'       => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button",
							'border_styles_hover' => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button:hover",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
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
							'border_radii'        => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors",
							'border_radii_hover'  => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover",
							'border_styles'       => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors",
							'border_styles_hover' => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover",
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
							'border_radii'        => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok",
							'border_radii_hover'  => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output:hover, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok:hover",
							'border_styles'       => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok",
							'border_styles_hover' => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output:hover, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok:hover",
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
						'main'  => "$form_selector",
						'hover' => "$form_selector:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'wrapper',
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
						'main'  => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button",
						'hover' => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button:hover",
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
						'main'  => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors",
						'hover' => "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover",
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
						'main'  => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok",
						'hover' => "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output:hover, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok:hover",
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
			'field'           => array(
				'label'    => esc_html__( 'Field', 'squad-modules-for-divi' ),
				'selector' => $this->get_field_selector_default(),
			),
			'radio_checkbox'  => array(
				'label'    => esc_html__( 'Radio Checkbox', 'squad-modules-for-divi' ),
				'selector' => "$form_selector input[type=checkbox], $form_selector input[type=radio]",
			),
			'file_upload'     => array(
				'label'    => esc_html__( 'File Upload', 'squad-modules-for-divi' ),
				'selector' => "$form_selector .wpcf7-form-control.wpcf7-file",
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

		// Generic styles.
		$this->disq_fix_fonts_transition( $fields, 'field_label_text', "$form_selector label, $form_selector legend" );
		$this->disq_fix_fonts_transition( $fields, 'placeholder_text', "$form_selector input::placeholder, $form_selector select::placeholder, $form_selector textarea::placeholder" );
		$this->disq_fix_fonts_transition( $fields, 'form_button_text', "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button" );
		$this->disq_fix_fonts_transition( $fields, 'message_error_text', "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors" );
		$this->disq_fix_fonts_transition( $fields, 'message_success_text', "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok" );
		$this->disq_fix_border_transition( $fields, 'wrapper', $form_selector );
		$this->disq_fix_border_transition( $fields, 'form_button', "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button" );
		$this->disq_fix_border_transition( $fields, 'message_error', "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors" );
		$this->disq_fix_border_transition( $fields, 'message_success', "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok" );
		$this->disq_fix_box_shadow_transition( $fields, 'wrapper', $form_selector );
		$this->disq_fix_box_shadow_transition( $fields, 'form_button', "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button" );
		$this->disq_fix_box_shadow_transition( $fields, 'message_error', "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors" );
		$this->disq_fix_box_shadow_transition( $fields, 'message_success', "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok" );

		// button styles.
		$fields['form_button_icon_color']  = array( 'color' => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button::before, $form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button::after" );
		$fields['form_button_icon_margin'] = array( 'margin' => "$form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button::before, $form_selector .wpcf7-form-control.wpcf7-submit.et_pb_button::after" );

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
	 * @since 1.0.0
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		// Show a notice message in the frontend if the contact form is not installed.
		if ( ! class_exists( 'WPCF7' ) ) {
			return sprintf(
				'<div class="disq_notice">%s</div>',
				esc_html__( 'Contact Form 7 is not installed', 'squad-modules-for-divi' )
			);
		}

		if ( ! empty( self::disq_form_styler__get_form_html( $attrs ) ) ) {
			$this->disq_generate_all_styles( $attrs );

			return self::disq_form_styler__get_form_html( $attrs );
		}

		// Show a notice message in the frontend if the form is not selected.
		return sprintf(
			'<div class="disq_notice">%s</div>',
			esc_html__( 'Please select a contact form.', 'squad-modules-for-divi' )
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
		if ( count( self::$forms_collection[ $type ] ) === 0 && class_exists( 'WPCF7' ) ) {
			$args = array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => - 1,
			);

			// Collect available contact form from the database.
			$forms = get_posts( $args );
			if ( count( $forms ) ) {
				/**
				 * @var \WP_Post[] $forms
				 * @var \WP_Post   $form
				 */
				foreach ( $forms as $form ) {
					self::$forms_collection[ $type ][ md5( $form->ID ) ] = 'title' === $type ? $form->post_title : $form->ID;
				}
			}
		}

		return self::$forms_collection[ $type ];
	}

	/**
	 * Collect all contact form from the database.
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
	 * Collect all posts from the database.
	 *
	 * @param array  $attrs   List of unprocessed attributes.
	 * @param string $content Content being processed.
	 *
	 * @return string the html output.
	 * @since 1.0.0
	 */
	public static function disq_form_styler__get_form_html( $attrs, $content = null ) {
		// Collect all from the database.
		$data = self::get_form_styler_forms_collection();
		if ( ! empty( $attrs['form_id'] ) && self::$default_form_id !== $attrs['form_id'] && isset( $data[ $attrs['form_id'] ] ) ) {
			return do_shortcode( sprintf( '[contact-form-7 id="%s"]', esc_attr( $data[ $attrs['form_id'] ] ) ) );
		}

		return null;
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

		// Load generated styles from parent.
		parent::disq_generate_all_styles( $attrs );

		if ( ! empty( $this->props['form_button_icon'] ) ) {
			// Load font Awesome css for frontend.
			Divi::inject_fa_icons( $this->props['form_button_icon'] );

			$form_button_icon       = $this->props['form_button_icon'];
			$clean_form_button_icon = Divi::get_icon_data_to_unicode( $form_button_icon );
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:before, $this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:after",
					'declaration' => sprintf( 'content: "%s";', $clean_form_button_icon ),
				)
			);
		}

		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $this->slug,
				'base_attr_name' => 'form_button_icon',
				'important'      => true,
				'selector'       => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:before, $this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:after",
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		// Set color for form button icon with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'form_button_icon_color',
				'selector'       => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit.et_pb_button:before, $this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit.et_pb_button:after",
				'hover_selector' => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit.et_pb_button:hover:before, $this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit.et_pb_button:hover:after",
				'css_property'   => 'color',
				'render_slug'    => $this->slug,
				'type'           => 'color',
			)
		);

		// View the icon on the estimated position.
		if ( 'row-reverse' === $this->prop( 'form_button_icon_placement', 'row' ) ) {
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:before",
					'declaration' => 'line-height: inherit; font-size: inherit !important;  margin-left: -1.3em; right: auto; opacity: 0; display: inline-block;',
				)
			);
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:hover:before",
					'declaration' => 'margin-right: 0.3em;  right: auto; opacity: 1;',
				)
			);
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:after",
					'declaration' => "content: ''; display: none;",
				)
			);

			if ( 'off' === $this->prop( 'form_button_icon_on_hover', 'off' ) ) {
				self::set_style(
					$this->slug,
					array(
						'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button",
						'declaration' => 'background-image: initial; padding-left: 2em; padding-right: 0.7em;',
					)
				);
				self::set_style(
					$this->slug,
					array(
						'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:before",
						'declaration' => 'opacity: 1;',
					)
				);
				self::set_style(
					$this->slug,
					array(
						'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:after",
						'declaration' => "content: ''; display: none;",
					)
				);
			} else {
				self::set_style(
					$this->slug,
					array(
						'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:hover",
						'declaration' => 'padding-left: 2em; padding-right: 0.7em;',
					)
				);
			}
		} elseif ( 'off' === $this->prop( 'form_button_icon_on_hover', 'off' ) ) {
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button",
					'declaration' => 'padding-left: 0.7em; padding-right: 2em;',
				)
			);
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-submit.et_pb_button:after",
					'declaration' => 'line-height: inherit; font-size: inherit !important;  margin-left: 0.3em; left: auto; opacity: 1; display: inline-block;',
				)
			);
		}
	}

	/**
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	protected function get_field_selector_default() {
		$form_selector = $this->get_form_selector_default();

		$selectors = array();
		foreach ( $this->disq_get_allowed_form_fields() as $allowed_field ) {
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
	 * Get the stylesheet selector for form tag.
	 *
	 * @return string
	 */
	protected function get_form_selector_default() {
		return "$this->main_css_element div .wpcf7 form.wpcf7-form";
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	protected function get_form_selector_hover() {
		return "$this->main_css_element div .wpcf7 form.wpcf7-form:hover";
	}

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_default() {
		return "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors";
	}

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover";
	}

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors";
	}

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_hover() {
		return "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover";
	}

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit.et_pb_button";
	}

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit.et_pb_button:hover";
	}
}

// Load the form styler (Contact Form 7) Module.
new FormStylerContactForm7();
