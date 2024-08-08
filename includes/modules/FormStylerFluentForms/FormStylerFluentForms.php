<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: Fluent Forms Module Class which extend the Divi Builder Module Class.
 *
 * This class provides Fluent Forms with customization opportunities in the visual builder.
 *
 * @since       1.4.7
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Modules\FormStylerFluentForms;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Form_Styler_Module;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\Module;
use function do_shortcode;
use function esc_html__;

/**
 * The Form Styler: Fluent Forms Module Class.
 *
 * @since       1.4.7
 * @package     squad-modules-for-divi
 */
class FormStylerFluentForms extends Squad_Form_Styler_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.4.7
	 */
	public function init() {
		$this->name      = esc_html__( 'Fluent Forms', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Fluent Forms', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/fluent-forms.svg' );

		$this->slug       = 'disq_form_styler_fluent_forms';
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
			'form_id'                  => $this->disq_add_select_box_field(
				esc_html__( 'Form', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the fluent form.', 'squad-modules-for-divi' ),
					'options'          => $this->disq_form_styler__get_all_forms(),
					'computed_affects' => array(
						'__forms',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_messages__enable'    => $this->disq_add_yes_no_field(
				esc_html__( 'Show Error & Success Messages', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the error and success messages in the visual  builder.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'forms',
				)
			),
			'form_cond_fields__enable' => $this->disq_add_yes_no_field(
				esc_html__( 'Show Conditional Fields', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the conditional hidden fields in the visual  builder.', 'squad-modules-for-divi' ),
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
				),
			),
		);

		$html_background_fields = $this->disq_add_background_field(
			esc_html__( 'Custom HTML Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'custom_html_background',
				'context'     => 'custom_html_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'form_custom_html',
			)
		);
		$html_associated_fields = array(
			'custom_html_margin'  => $this->disq_add_margin_padding_field(
				esc_html__( 'Custom HTML Margin', 'squad-modules-for-divi' ),
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
					'toggle_slug'    => 'form_custom_html',
				)
			),
			'custom_html_padding' => $this->disq_add_margin_padding_field(
				esc_html__( 'Custom HTML Padding', 'squad-modules-for-divi' ),
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
					'toggle_slug'    => 'form_custom_html',
				)
			),
		);

		// Collect all fields from the parent module.
		$parent_fields = parent::get_fields();

		// Remove unneeded fields.
		$parent_fields = $this->disq_remove_pre_assigned_fields(
			$parent_fields,
			array(
				'form_wrapper_background_color',
				'wrapper_margin',
				'wrapper_padding',
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

		return array_merge_recursive( $parent_fields, $general_settings, $html_background_fields, $html_associated_fields );
	}

	/**
	 * Additional new fields for current form styler.
	 *
	 * @return array[]
	 * @since 1.4.7
	 */
	public function get_form_styler_additional_custom_fields() {
		return array(
			'field_label_margin'  => $this->disq_add_margin_padding_field(
				esc_html__( 'Label Margin', 'squad-modules-for-divi' ),
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
					'default'          => '||5px|||',
					'default_on_front' => '||5px|||',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'field',
				)
			),
			'field_label_padding' => $this->disq_add_margin_padding_field(
				esc_html__( 'Label Padding', 'squad-modules-for-divi' ),
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
					'toggle_slug'     => 'field',
				)
			),
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
				'field_text'            => $this->disq_add_font_field(
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
				'field_label_text'      => $this->disq_add_font_field(
					esc_html__( 'Label', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector .ff-el-input--label",
							'hover' => "$form_selector .ff-el-input--label:hover",
						),
					)
				),
				'placeholder_text'      => $this->disq_add_font_field(
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
				'form_custom_html_text' => $this->disq_add_font_field(
					esc_html__( 'Custom HTML', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '14px',
						),
						'css'       => array(
							'main'  => "$form_selector .ff-custom_html",
							'hover' => "$form_selector .ff-custom_html:hover",
						),
					)
				),
				'form_button_text'      => $this->disq_add_font_field(
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
				'message_error_text'    => $this->disq_add_font_field(
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
				'message_success_text'  => $this->disq_add_font_field(
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
			'background'     => Module::selectors_background( $this->main_css_element ),
			'borders'        => array(
				'default'          => Module::selectors_default( $this->main_css_element ),
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
				'form_custom_html' => array(
					'label_prefix' => esc_html__( 'Custom HTML', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$form_selector .ff-custom_html",
							'border_radii_hover'  => "$form_selector .ff-custom_html:hover",
							'border_styles'       => "$form_selector .ff-custom_html",
							'border_styles_hover' => "$form_selector .ff-custom_html:hover",
						),
					),
					'defaults'     => array(
						'border_styles' => array(
							'color' => '#333',
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'form_custom_html',
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
							'color' => '#333',
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
				'default'          => Module::selectors_default( $this->main_css_element ),
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
				'form_custom_html' => array(
					'label'             => esc_html__( 'Custom HTML Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$form_selector .ff-custom_html",
						'hover' => "$form_selector .ff-custom_html:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'form_custom_html',
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
			'margin_padding' => Module::selectors_margin_padding( $this->main_css_element ),
			'max_width'      => Module::selectors_max_width( $this->main_css_element ),
			'height'         => Module::selectors_default( $this->main_css_element ),
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
				'selector' => "$form_selector .ff_upload_btn.ff-btn",
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

		// field label styles.
		$fields['field_margin']        = array( 'margin' => "$form_selector .ff-el-input--content" );
		$fields['field_label_margin']  = array( 'margin' => "$form_selector .ff-el-input--label" );
		$fields['field_label_padding'] = array( 'padding' => "$form_selector .ff-el-input--label" );
		$this->disq_fix_fonts_transition( $fields, 'field_label_text', "$form_selector .ff-el-input--label" );

		// field text and others style.
		$fields['custom_html_background_color'] = array( 'background' => "$form_selector .ff-custom_html" );
		$fields['custom_html_margin']           = array( 'margin' => "$form_selector .ff-custom_html" );
		$fields['custom_html_padding']          = array( 'padding' => "$form_selector .ff-custom_html" );
		$this->disq_fix_fonts_transition( $fields, 'form_custom_html_text', "$form_selector .ff-custom_html" );
		$this->disq_fix_border_transition( $fields, 'form_custom_html', "$form_selector .ff-custom_html" );
		$this->disq_fix_box_shadow_transition( $fields, 'form_custom_html', "$form_selector .ff-custom_html" );

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
		if ( ! function_exists( 'wpFluentForm' ) ) {
			return sprintf(
				'<div class="disq_notice">%s</div>',
				esc_html__( 'Fluent Forms is not installed', 'squad-modules-for-divi' )
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
		if ( count( self::$forms_collection[ $type ] ) === 0 && function_exists( 'wpFluentForm' ) ) {
			// Collect available contact form from the database.
			$forms_table = \wpFluent()->table( 'fluentform_forms' );
			$collections = $forms_table->select( array( 'id', 'title' ) )->orderBy( 'id', 'DESC' )->get();

			/**
			 * Collect form iad and title based on conditions.
			 *
			 * @var array  $collections
			 * @var object $form
			 */
			foreach ( $collections as $form ) {
				self::$forms_collection[ $type ][ md5( $form->id ) ] = 'title' === $type ? $form->title : $form->id;
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

		// Collect all forms from database.
		return array_merge( $forms, self::get_form_styler_forms_collection( 'title' ) );
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
			return do_shortcode( sprintf( '[fluentform id="%s"]', esc_attr( $collection[ $attrs['form_id'] ] ) ) );
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

		// Remove unnecessary fields.
		unset( $options['form_wrapper_background'], $options['wrapper_margin'], $options['wrapper_padding'] );

		// all background type styles.
		$options['custom_html_background'] = array(
			'type'           => 'background',
			'selector'       => "$form_selector .ff-custom_html",
			'selector_hover' => "$form_selector .ff-custom_html:hover",
		);

		// all margin, padding type styles.
		$options['field_label_margin']  = array(
			'type'           => 'margin',
			'selector'       => "$form_selector .ff-el-input--label",
			'hover_selector' => "$form_selector .ff-el-input--label:hover",
		);
		$options['field_label_padding'] = array(
			'type'           => 'padding',
			'selector'       => "$form_selector .ff-el-input--label",
			'hover_selector' => "$form_selector .ff-el-input--label:hover",
		);
		$options['field_margin']        = array(
			'type'           => 'margin',
			'selector'       => "$form_selector .ff-el-input--content",
			'hover_selector' => "$form_selector .ff-el-input--content:hover",
		);
		$options['custom_html_margin']  = array(
			'type'           => 'margin',
			'selector'       => "$form_selector .ff-custom_html",
			'hover_selector' => "$form_selector .ff-custom_html:hover",
		);
		$options['custom_html_padding'] = array(
			'type'           => 'padding',
			'selector'       => "$form_selector .ff-custom_html",
			'hover_selector' => "$form_selector .ff-custom_html:hover",
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
	 * Get the stylesheet selector for form fields.
	 *
	 * @return string
	 */
	protected function get_field_selector_default() {
		$form_selector       = $this->get_form_selector_default();
		$allowed_form_fields = $this->disq_get_allowed_form_fields();

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
		$form_selector       = $this->get_form_selector_default();
		$allowed_form_fields = $this->disq_get_allowed_form_fields();

		$selectors = array();
		foreach ( $allowed_form_fields as $allowed_field ) {
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
		return "$this->main_css_element div .fluentform form";
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	protected function get_form_selector_hover() {
		return "$this->main_css_element div .fluentform form:hover";
	}

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_default() {
		return "$this->main_css_element div .fluentform .ff-el-is-error .text-danger";
	}

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->main_css_element div .fluentform .ff-el-is-error .text-danger:hover";
	}

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->main_css_element div .fluentform .ff-message-success";
	}

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_hover() {
		return "$this->main_css_element div .fluentform .ff-message-success:hover";
	}

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->main_css_element div .fluentform form .ff-btn-submit:not(.ff_btn_no_style)";
	}

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->main_css_element div .fluentform form .ff-btn-submit:not(.ff_btn_no_style):hover";
	}
}

// Load the form styler (Fluent Forms) Module.
new FormStylerFluentForms();
