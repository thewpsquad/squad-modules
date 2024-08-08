<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: WP Forms Module Class which extend the Divi Builder Module Class.
 *
 * This class provides the wp forms with customization opportunities in the visual builder.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Modules\FormStylerWPForms;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Form_Styler_Module;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\Module;
use function do_shortcode;
use function esc_html__;
use function get_posts;

/**
 * The Form Styler: WP Forms Module Class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 */
class FormStylerWPForms extends Squad_Form_Styler_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public function init() {
		$this->name      = esc_html__( 'WP Forms', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'WP Forms', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/wp-forms.svg' );

		$this->slug       = 'disq_form_styler_wp_forms';
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
					'description'      => esc_html__( 'Here you can choose the wp form.', 'squad-modules-for-divi' ),
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
		$form_selector = $this->get_form_selector_default();

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
							'main'  => "$form_selector label, $form_selector legend",
							'hover' => "$form_selector label:hover, $form_selector legend:hover",
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
							'main'  => $this->get_submit_button_selector_default(),
							'hover' => $this->get_submit_button_selector_hover(),
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
							'main'  => $this->get_error_message_selector_default(),
							'hover' => $this->get_error_message_selector_hover(),
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
							'main'  => $this->get_success_message_selector_default(),
							'hover' => $this->get_success_message_selector_hover(),
						),
					)
				),
			),
			'background'     => Module::selectors_background( $this->main_css_element ),
			'borders'        => array(
				'default'         => Module::selectors_default( $this->main_css_element ),
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
				'default'         => Module::selectors_default( $this->main_css_element ),
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
		if ( ! function_exists( 'wpforms' ) ) {
			return sprintf(
				'<div class="disq_notice">%s</div>',
				esc_html__( 'WPForms is not installed', 'squad-modules-for-divi' )
			);
		}

		if ( ! empty( self::disq_form_styler__get_form_html( $attrs ) ) ) {
			$this->disq_generate_all_styles( $attrs );

			return self::disq_form_styler__get_form_html( $attrs );
		}

		// Show a notice message in the frontend if the form is not selected.
		return sprintf(
			'<div class="disq_notice">%s</div>',
			esc_html__( 'Please select a wp form.', 'squad-modules-for-divi' )
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
		if ( count( self::$forms_collection[ $type ] ) === 0 && function_exists( 'wpforms' ) ) {
			$args = array(
				'post_type'      => 'wpforms',
				'posts_per_page' => - 1,
			);

			// Collect available wp form from a database.
			$forms = get_posts( $args );
			if ( count( $forms ) ) {
				foreach ( $forms as $form ) {
					self::$forms_collection[ $type ][ md5( $form->ID ) ] = 'title' === $type ? $form->post_title : $form->ID;
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
		$collection = self::get_form_styler_forms_collection();
		if ( ! empty( $attrs['form_id'] ) && self::$default_form_id !== $attrs['form_id'] && isset( $collection[ $attrs['form_id'] ] ) ) {
			return do_shortcode( sprintf( '[wpforms id="%s"]', esc_attr( $collection[ $attrs['form_id'] ] ) ) );
		}

		return null;
	}

	/**
	 * Get the stylesheet selector for form tag.
	 *
	 * @return string
	 */
	protected function get_form_selector_default() {
		return "$this->main_css_element div .wpforms-container.wpforms-container-full form.wpforms-form";
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	protected function get_form_selector_hover() {
		return "$this->main_css_element div .wpforms-container.wpforms-container-full form.wpforms-form:hover";
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
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_default() {
		return "$this->main_css_element div div.wpforms-container-full .wpforms-error-container-full, $this->main_css_element div div.wpforms-container-full .wpforms-error-container";
	}

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->main_css_element div div.wpforms-container-full .wpforms-error-container-full:hover, $this->main_css_element div div.wpforms-container-full .wpforms-error-container:hover";
	}

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->main_css_element div div.wpforms-container-full .wpforms-confirmation-container-full, $this->main_css_element div div[submit-success]>.wpforms-confirmation-container-full:not(.wpforms-redirection-message)";
	}

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_hover() {
		return "$this->main_css_element div div.wpforms-container-full .wpforms-confirmation-container-full:hover, $this->main_css_element div div[submit-success]>.wpforms-confirmation-container-full:not(.wpforms-redirection-message):hover";
	}

	/**
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->main_css_element div div.wpforms-container-full input[type=submit], $this->main_css_element div div.wpforms-container-full button[type=submit], $this->main_css_element div div.wpforms-container-full .wpforms-page-button";
	}

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->main_css_element div div.wpforms-container-full input[type=submit]:hover, $this->main_css_element div div.wpforms-container-full button[type=submit]:hover, $this->main_css_element div div.wpforms-container-full .wpforms-page-button:hover";
	}
}

// Load the form styler (WP Forms) Module.
new FormStylerWPForms();
