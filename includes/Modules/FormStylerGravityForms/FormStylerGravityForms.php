<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: Gravity Forms Module Class which extend the Divi Builder Module Class.
 *
 * This class provides the gravity form customization functionalities in the visual builder.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Modules\FormStylerGravityForms;

use DiviSquad\Base\DiviBuilder\DiviSquad_Form_Styler as Squad_Form_Styler;
use DiviSquad\Base\DiviBuilder\Utils;
use DiviSquad\Utils\Helper;
use function esc_html__;

/**
 * The Form Styler: Gravity Forms Module Class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 */
class FormStylerGravityForms extends Squad_Form_Styler {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Gravity Forms', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Gravity Forms', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DIVI_SQUAD_MODULES_ICON_DIR_PATH . '/gravity-forms.svg' );

		$this->slug             = 'disq_form_styler_gravity_forms';
		$this->vb_support       = 'on';
		$this->main_css_element = "%%order_class%%.$this->slug";

		// Connect with utils.
		$this->squad_utils = Utils::connect( $this );
	}

	/**
	 * Get toggles for the module's settings modal.
	 */
	public function get_settings_modal_toggles() {
		// Collect all modals from parent.
		$parent_fields = parent::get_settings_modal_toggles();

		// Remove unnecessary fields.
		unset( $parent_fields['general']['toggles']['field_icons'] );

		// Collect the necessary fields.
		$advanced_toggles = array_slice( $parent_fields['advanced']['toggles'], 1 );

		// Define new fields for advanced tab.
		$advanced_toggles_new = array(
			'wrapper'               => esc_html__( 'Form Wrapper', 'squad-modules-for-divi' ),
			'form_title'            => esc_html__( 'Title', 'squad-modules-for-divi' ),
			'form_title_text'       => esc_html__( 'Title Text', 'squad-modules-for-divi' ),
			'form_description'      => esc_html__( 'Description', 'squad-modules-for-divi' ),
			'form_description_text' => esc_html__( 'Description Text', 'squad-modules-for-divi' ),
		);

		return array(
			'general'  => array(
				'toggles' => array(
					'forms' => esc_html__( 'Forms Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array_merge( $advanced_toggles_new, $advanced_toggles ),
			),
		);
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
			'form_id'                  => Utils::add_select_box_field(
				esc_html__( 'Form', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the gravity form.', 'squad-modules-for-divi' ),
					'options'          => Utils::form_get_all_items( 'gravity_forms' ),
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_title__enable'       => Utils::add_yes_no_field(
				esc_html__( 'Show Form Title', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the form title.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'affects'          => array(
						'form_title_text',
						'form_title_text_font',
						'form_title_text_text_color',
						'form_title_text_text_align',
						'form_title_text_font_size',
						'form_title_text_letter_spacing',
						'form_title_text_line_height',
						'form_title_background_color',
						'form_title_margin',
						'form_title_padding',
					),
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_description__enable' => Utils::add_yes_no_field(
				esc_html__( 'Show Form Description', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the form description.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'affects'          => array(
						'form_description_text',
						'form_description_text_font',
						'form_description_text_text_color',
						'form_description_text_text_align',
						'form_description_text_font_size',
						'form_description_text_letter_spacing',
						'form_description_text_line_height',
						'form_description_background_color',
						'form_description_margin',
						'form_description_padding',
					),
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_with_ajax__enable'   => Utils::add_yes_no_field(
				esc_html__( 'Use AJAX On Submit', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not use the ajax on form submission.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_messages__enable'    => Utils::add_yes_no_field(
				esc_html__( 'Show Error & Success Messages', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the error and success messages in the visual  builder.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'__forms'                  => array(
				'type'                => 'computed',
				'computed_callback'   => array( __CLASS__, 'disq_form_styler__get_form_html' ),
				'computed_depends_on' => array(
					'form_id',
					'form_title__enable',
					'form_description__enable',
					'form_with_ajax__enable',
				),
			),
		);

		$form_title_background_fields   = $this->squad_utils->add_background_field(
			array(
				'label'       => esc_html__( 'Title Background', 'squad-modules-for-divi' ),
				'base_name'   => 'form_title_background',
				'context'     => 'form_title_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'form_title',
			)
		);
		$form_descrip_background_fields = $this->squad_utils->add_background_field(
			array(
				'label'       => esc_html__( 'Description Background', 'squad-modules-for-divi' ),
				'base_name'   => 'form_description_background',
				'context'     => 'form_description_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'form_description',
			)
		);
		$form_header_associated_fields  = array(
			'form_title_margin'        => Utils::add_margin_padding_field(
				esc_html__( 'Title Margin', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size.', 'squad-modules-for-divi' ),
					'type'            => 'custom_margin',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'form_title',
				)
			),
			'form_title_padding'       => Utils::add_margin_padding_field(
				esc_html__( 'Title Padding', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'squad-modules-for-divi' ),
					'type'            => 'custom_padding',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'form_title',
				)
			),
			'form_description_margin'  => Utils::add_margin_padding_field(
				esc_html__( 'Description Margin', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size.', 'squad-modules-for-divi' ),
					'type'            => 'custom_margin',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'form_description',
				)
			),
			'form_description_padding' => Utils::add_margin_padding_field(
				esc_html__( 'Description Padding', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom padding size.', 'squad-modules-for-divi' ),
					'type'            => 'custom_padding',
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'form_description',
				)
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

		return array_merge_recursive(
			$parent_fields,
			$general_settings,
			$form_title_background_fields,
			$form_descrip_background_fields,
			$form_header_associated_fields
		);
	}

	/**
	 * Declare advanced fields for the module
	 *
	 * @return array[]
	 */
	public function get_advanced_fields_config() {
		$form_selector = $this->get_form_selector_default();

		return array(
			'fonts'          => array(
				'form_title_text'       => Utils::add_font_field(
					esc_html__( 'Title', 'squad-modules-for-divi' ),
					array(
						'font_weight' => array(
							'default' => '500',
						),
						'font_size'   => array(
							'default' => '26px',
						),
						'text_shadow' => array(
							'show_if' => array(
								'form_title__enable' => 'on',
							),
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title",
							'hover' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title:hover",
						),
					)
				),
				'form_description_text' => Utils::add_font_field(
					esc_html__( 'Description', 'squad-modules-for-divi' ),
					array(
						'font_weight' => array(
							'default' => '400',
						),
						'font_size'   => array(
							'default' => '14px',
						),
						'text_shadow' => array(
							'show_if' => array(
								'form_description__enable' => 'on',
							),
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description",
							'hover' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description:hover",
						),
					)
				),
				'field_text'            => Utils::add_font_field(
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
				'field_label_text'      => Utils::add_font_field(
					esc_html__( 'Label', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector label, $form_selector legend",
							'hover' => "$form_selector label:hover, $form_selector legend:hover",
						),
					)
				),
				'placeholder_text'      => Utils::add_font_field(
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
				'form_button_text'      => Utils::add_font_field(
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
				'message_error_text'    => Utils::add_font_field(
					esc_html__( 'Message', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => $this->get_error_message_selector_default(),
							'hover' => $this->get_error_message_selector_hover(),
						),
					)
				),
				'message_success_text'  => Utils::add_font_field(
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
			'background'     => Utils::selectors_background( $this->main_css_element ),
			'borders'        => array(
				'default'          => Utils::selectors_default( $this->main_css_element ),
				'wrapper'          => array(
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
				'form_title'       => array(
					'label_prefix' => esc_html__( 'Title', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title",
							'border_radii_hover'  => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title:hover",
							'border_styles'       => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title",
							'border_styles_hover' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title:hover",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
							'color' => '#bbb',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'form_title',
				),
				'form_description' => array(
					'label_prefix' => esc_html__( 'Description', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description",
							'border_radii_hover'  => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description:hover",
							'border_styles'       => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description",
							'border_styles_hover' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description:hover",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
							'color' => '#bbb',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'form_description',
				),
				'field'            => array(
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
				'form_button'      => array(
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
							'width' => '1px|1px|1px|1px',
							'color' => '#bbb',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'form_button',
				),
				'message_error'    => array(
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
				'message_success'  => array(
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
				'default'          => Utils::selectors_default( $this->main_css_element ),
				'wrapper'          => array(
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
				'form_title'       => array(
					'label'             => esc_html__( 'Title Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title",
						'hover' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'form_title',
				),
				'form_description' => array(
					'label'             => esc_html__( 'Desription Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description",
						'hover' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'form_description',
				),
				'field'            => array(
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
				'form_button'      => array(
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
				'message_error'    => array(
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
				'message_success'  => array(
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
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the field list.
	 *
	 * @since 1.4.7
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// title style.
		$fields['form_title_background_color'] = array( 'background' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title" );
		$fields['form_title_margin']           = array( 'margin' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title" );
		$fields['form_title_padding']          = array( 'padding' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title" );
		Utils::fix_fonts_transition( $fields, 'form_title_text', "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title" );
		Utils::fix_border_transition( $fields, 'form_title', "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title" );
		Utils::fix_box_shadow_transition( $fields, 'form_title', "$this->main_css_element div .gform_wrapper.gravity-theme .gform_title" );

		// description style.
		$fields['form_description_background_color'] = array( 'background' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description" );
		$fields['form_description_margin']           = array( 'margin' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description" );
		$fields['form_description_padding']          = array( 'padding' => "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description" );
		Utils::fix_fonts_transition( $fields, 'form_description_text', "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description" );
		Utils::fix_border_transition( $fields, 'form_description', "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description" );
		Utils::fix_box_shadow_transition( $fields, 'form_description', "$this->main_css_element div .gform_wrapper.gravity-theme .gform_description" );

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
		if ( ! function_exists( 'gravity_form' ) ) {
			return sprintf(
				'<div class="divi_squad_notice">%s</div>',
				esc_html__( 'Gravity Forms is not installed', 'squad-modules-for-divi' )
			);
		}

		if ( ! empty( self::disq_form_styler__get_form_html( $attrs ) ) ) {
			$this->squad_generate_all_styles( $attrs );

			return self::disq_form_styler__get_form_html( $attrs );
		}

		// Show a notice message in the frontend if the form is not selected.
		return sprintf(
			'<div class="divi_squad_notice">%s</div>',
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
	public static function disq_form_styler__get_form_html( $attrs, $content = null ) {
		if ( ! empty( $attrs['form_id'] ) && Utils::$default_form_id !== $attrs['form_id'] && function_exists( 'gravity_form' ) ) {
			// Collect all posts from the database.
			$collection       = Utils::form_get_all_items( 'gravity_forms', 'id' );
			$form_id          = $collection[ $attrs['form_id'] ];
			$form_title       = isset( $attrs['form_title__enable'] ) && 'on' === $attrs['form_title__enable'];
			$form_description = isset( $attrs['form_description__enable'] ) && 'on' === $attrs['form_description__enable'];
			$form_ajax        = isset( $attrs['form_with_ajax__enable'] ) && 'on' === $attrs['form_with_ajax__enable'];

			return \gravity_form( $form_id, $form_title, $form_description, false, null, $form_ajax, '', false );
		}

		return null;
	}

	/**
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	protected function get_field_selector_default() {
		$form_selector  = $this->get_form_selector_default();
		$allowed_fields = Utils::form_get_allowed_fields();

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
		$allowed_fields = Utils::form_get_allowed_fields();

		$selectors = array();
		foreach ( $allowed_fields as $allowed_field ) {
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
		return "$this->main_css_element div .gform_wrapper.gravity-theme form";
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	protected function get_form_selector_hover() {
		return "$this->main_css_element div .gform_wrapper.gravity-theme form:hover";
	}

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_default() {
		return "$this->main_css_element div .gform_wrapper .gform_validation_errors, $this->main_css_element div .gform_wrapper.gravity-theme .validation_error";
	}

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->main_css_element div .gform_wrapper .gform_validation_errors:hover, $this->main_css_element div .gform_wrapper.gravity-theme .validation_error:hover";
	}

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->main_css_element div .gform_wrapper.gform_confirmation_wrapper .gform_confirmation_message";
	}

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_hover() {
		return "$this->main_css_element div .gform_wrapper.gform_confirmation_wrapper .gform_confirmation_message:hover";
	}

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->main_css_element div .gform_wrapper form input[type=submit], $this->main_css_element div .gform_wrapper form button[type=submit], $this->main_css_element div .gform_wrapper form .gform-button";
	}

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->main_css_element div .gform_wrapper form input[type=submit]:hover, $this->main_css_element div .gform_wrapper form button[type=submit]:hover, $this->main_css_element div .gform_wrapper form .gform-button:hover";
	}
}

// Load the form styler (Gravity Forms) Utils.
new FormStylerGravityForms();
