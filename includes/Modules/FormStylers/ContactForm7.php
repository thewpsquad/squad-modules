<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: Contact Form 7 Module Class which extend the Divi Builder Module Class.
 *
 * This class provides contact form 7 with customization opportunities in the visual builder.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.2.0
 */

namespace DiviSquad\Modules\FormStylers;

use DiviSquad\Base\DiviBuilder\Module\FormStyler;
use DiviSquad\Base\DiviBuilder\Utils;
use DiviSquad\Utils\Helper;
use function do_shortcode;
use function esc_html__;

/**
 * The Form Styler: Contact Form 7 Module Class.
 *
 * @package DiviSquad
 * @since   1.2.0
 */
class ContactForm7 extends FormStyler {
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
		$this->icon_path = Helper::fix_slash( divi_squad()->get_icon_path() . '/contact-form-7.svg' );

		$this->slug             = 'disq_form_styler_cf7';
		$this->vb_support       = 'on';
		$this->main_css_element = "%%order_class%%.$this->slug";

		// Connect with utils.
		$this->squad_utils = Utils::connect( $this );
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
			'form_id'               => Utils::add_select_box_field(
				esc_html__( 'Form', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the contact form 7.', 'squad-modules-for-divi' ),
					'options'          => Utils\Elements\Forms::get_all_forms( 'cf7' ),
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_messages__enable' => Utils::add_yes_no_field(
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
				'computed_callback'   => array( self::class, 'squad_form_styler__get_form_html' ),
				'computed_depends_on' => array(
					'form_id',
				),
			),
		);

		// Remove unneeded fields.
		$parent_fields = $this->squad_remove_pre_assigned_fields(
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
			)
		);

		return array_merge_recursive( $parent_fields, $general_settings );
	}

	/**
	 * Declare advanced fields for the module
	 *
	 * @return array
	 */
	public function get_advanced_fields_config() {
		$form_selector = $this->get_form_selector_default();

		return array(
			'fonts'          => array(
				'field_text'           => Utils::add_font_field(
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
				'field_label_text'     => Utils::add_font_field(
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
				'placeholder_text'     => Utils::add_font_field(
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
				'form_button_text'     => Utils::add_font_field(
					esc_html__( 'Button', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector .wpcf7-form-control.wpcf7-submit",
							'hover' => "$form_selector .wpcf7-form-control.wpcf7-submit:hover",
						),
					)
				),
				'message_error_text'   => Utils::add_font_field(
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
				'message_success_text' => Utils::add_font_field(
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
			'background'     => Utils::selectors_background( $this->main_css_element ),
			'borders'        => array(
				'default'         => Utils::selectors_default( $this->main_css_element ),
				'wrapper'         => array(
					'label_prefix' => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => $form_selector,
							'border_radii_hover'  => "$form_selector:hover",
							'border_styles'       => $form_selector,
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
							'border_radii'        => "$form_selector .wpcf7-form-control.wpcf7-submit",
							'border_radii_hover'  => "$form_selector .wpcf7-form-control.wpcf7-submit:hover",
							'border_styles'       => "$form_selector .wpcf7-form-control.wpcf7-submit",
							'border_styles_hover' => "$form_selector .wpcf7-form-control.wpcf7-submit:hover",
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
				'default'         => Utils::selectors_default( $this->main_css_element ),
				'wrapper'         => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => $form_selector,
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
						'main'  => "$form_selector .wpcf7-form-control.wpcf7-submit",
						'hover' => "$form_selector .wpcf7-form-control.wpcf7-submit:hover",
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
			'margin_padding' => Utils::selectors_margin_padding( $this->main_css_element ),
			'max_width'      => Utils::selectors_max_width( $this->main_css_element ),
			'height'         => Utils::selectors_default( $this->main_css_element ),
			'image_icon'     => false,
			'link_options'   => false,
			'filters'        => false,
			'text'           => false,
			'button'         => false,
		);
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
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	protected function get_field_selector_default() {
		$form_selector  = $this->get_form_selector_default();
		$allowed_fields = Utils\Elements\Forms::get_allowed_fields();

		$selectors = array();
		foreach ( $allowed_fields as $allowed_field ) {
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
		$form_selector  = $this->get_form_selector_default();
		$allowed_fields = Utils\Elements\Forms::get_allowed_fields();

		$selectors = array();
		foreach ( $allowed_fields as $allowed_field ) {
			$selectors[] = "$form_selector $allowed_field:hover";
		}

		return implode( ', ', $selectors );
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
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit";
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
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors";
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
		Utils::fix_fonts_transition( $fields, 'field_label_text', "$form_selector label, $form_selector legend" );
		Utils::fix_fonts_transition( $fields, 'placeholder_text', "$form_selector input::placeholder, $form_selector select::placeholder, $form_selector textarea::placeholder" );
		Utils::fix_fonts_transition( $fields, 'form_button_text', "$form_selector .wpcf7-form-control.wpcf7-submit" );
		Utils::fix_fonts_transition( $fields, 'message_error_text', "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors" );
		Utils::fix_fonts_transition( $fields, 'message_success_text', "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok" );
		Utils::fix_border_transition( $fields, 'wrapper', $form_selector );
		Utils::fix_border_transition( $fields, 'form_button', "$form_selector .wpcf7-form-control.wpcf7-submit" );
		Utils::fix_border_transition( $fields, 'message_error', "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors" );
		Utils::fix_border_transition( $fields, 'message_success', "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok" );
		Utils::fix_box_shadow_transition( $fields, 'wrapper', $form_selector );
		Utils::fix_box_shadow_transition( $fields, 'form_button', "$form_selector .wpcf7-form-control.wpcf7-submit" );
		Utils::fix_box_shadow_transition( $fields, 'message_error', "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors" );
		Utils::fix_box_shadow_transition( $fields, 'message_success', "$this->main_css_element div .wpcf7 form.sent .wpcf7-response-output, $form_selector .wpcf7-response-output.wpcf7-mail-sent-ok" );

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
				'<div class="squad-notice">%s</div>',
				esc_html__( 'Contact Form 7 is not installed', 'squad-modules-for-divi' )
			);
		}

		if ( ! empty( self::squad_form_styler__get_form_html( $attrs ) ) ) {
			$this->squad_generate_all_styles( $attrs );

			return self::squad_form_styler__get_form_html( $attrs );
		}

		// Show a notice message in the frontend if the form is not selected.
		return sprintf(
			'<div class="squad-notice">%s</div>',
			esc_html__( 'Please select a form.', 'squad-modules-for-divi' )
		);
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
	public static function squad_form_styler__get_form_html( $attrs, $content = null ) {
		// Check if the form id is empty or not.
		if ( empty( $attrs['form_id'] ) || Utils\Elements\Forms::DEFAULT_FORM_ID === $attrs['form_id'] || ! class_exists( '\WPCF7' ) ) {
				return '';
		}

		// Collect all from the database.
		$collection = Utils\Elements\Forms::get_all_forms( 'cf7', 'id' );

		// Check if the form id is existing.
		if ( ! isset( $collection[ $attrs['form_id'] ] ) ) {
			return '';
		}

		return do_shortcode( sprintf( '[contact-form-7 id="%s"]', esc_attr( $collection[ $attrs['form_id'] ] ) ) );
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
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->main_css_element div .wpcf7 form.invalid .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.unaccepted .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.payment-required .wpcf7-response-output:hover, $this->main_css_element div .wpcf7 form.init .wpcf7-response-output.wpcf7-validation-errors:hover";
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
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->main_css_element div .wpcf7 form.wpcf7-form .wpcf7-form-control.wpcf7-submit:hover";
	}
}
