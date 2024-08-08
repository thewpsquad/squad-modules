<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Form Styler: WP Forms Module Class which extend the Divi Builder Module Class.
 *
 * This class provides the wp forms with customization opportunities in the visual builder.
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
 * The Form Styler: WP Forms Module Class.
 *
 * @package DiviSquad
 * @since   1.2.0
 */
class WPForms extends FormStyler {

	/**
	 * The css selector for the form container.
	 *
	 * @var string
	 */
	private $form_container = '';

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 2.1.1
	 */
	public function init() {
		$this->name      = esc_html__( 'WP Forms', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'WP Forms', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( divi_squad()->get_icon_path() . '/wp-forms.svg' );

		$this->slug       = 'disq_form_styler_wp_forms';
		$this->vb_support = 'on';

		// Update css selector.
		$this->main_css_element = "body #et-main-area .et-l %%order_class%%.$this->slug.et_pb_module";
		$this->form_container   = "$this->main_css_element div div.wpforms-container.wpforms-container-full";

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
		// Add new fields for the current module.
		$general_settings = array(
			'form_id'               => Utils::add_select_box_field(
				esc_html__( 'Form', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the wp form.', 'squad-modules-for-divi' ),
					'options'          => Utils\Elements\Forms::get_all_forms( 'wpforms' ),
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

		// Collect all fields from the parent module.
		$parent_fields = parent::get_fields();

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
	 * Additional new fields for current form styler.
	 *
	 * @return array[]
	 * @since 1.4.7
	 */
	public function get_form_styler_additional_custom_fields() {
		return array(
			'form_field_width' => Utils::add_range_field(
				esc_html__( 'Field Width', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Set the width of the form fields.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default_unit'   => '%',
					'hover'          => false,
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'field',
				)
			),
		);
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
							'main'  => "$form_selector label, $form_selector legend",
							'hover' => "$form_selector label:hover, $form_selector legend:hover",
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
							'main'  => $this->get_submit_button_selector_default(),
							'hover' => $this->get_submit_button_selector_hover(),
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
							'main'  => $this->get_error_message_selector_default(),
							'hover' => $this->get_error_message_selector_hover(),
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
							'main'  => $this->get_success_message_selector_default(),
							'hover' => $this->get_success_message_selector_hover(),
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
							'border_radii'        => $this->get_form_selector_default(),
							'border_radii_hover'  => $this->get_form_selector_hover(),
							'border_styles'       => $this->get_form_selector_default(),
							'border_styles_hover' => $this->get_form_selector_hover(),
						),
					),
					'defaults'     => array(
						'border_radii'  => 'on||||',
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
						'border_radii'  => 'on||||',
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
						'border_radii'  => 'on||||',
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
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
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
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px|0px|0px|0px',
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
				'<div class="squad-notice">%s</div>',
				esc_html__( 'WPForms is not installed', 'squad-modules-for-divi' )
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
		if ( empty( $attrs['form_id'] ) || Utils\Elements\Forms::DEFAULT_FORM_ID === $attrs['form_id'] || ! function_exists( 'wpforms' ) ) {
			return '';
		}

		// Collect all from the database.
		$collection = Utils\Elements\Forms::get_all_forms( 'wpforms', 'id' );

		if ( ! isset( $collection[ $attrs['form_id'] ] ) ) {
			return '';
		}

		return do_shortcode( sprintf( '[wpforms id="%s"]', esc_attr( $collection[ $attrs['form_id'] ] ) ) );
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
		$options = parent::squad_get_module_stylesheet_selectors( $attrs );

		// Set max-width for form fields (medium single text input).
		// .wpforms-container input.wpforms-field-medium,
		// .wpforms-container select.wpforms-field-medium,
		// .wpforms-container .wpforms-field-row.wpforms-field-medium,
		// .wp-core-ui div.wpforms-container input.wpforms-field-medium,
		// .wp-core-ui div.wpforms-container select.wpforms-field-medium,
		// .wp-core-ui div.wpforms-container .wpforms-field-row.wpforms-field-medium.
		$medium_field_selectors  = "$this->main_css_element .wpforms-container input.wpforms-field-medium";
		$medium_field_selectors .= ", $this->main_css_element .wpforms-container select.wpforms-field-medium";
		$medium_field_selectors .= ", $this->main_css_element .wpforms-container .wpforms-field-row.wpforms-field-medium";
		$medium_field_selectors .= ", $this->main_css_element .wp-core-ui div.wpforms-container input.wpforms-field-medium";
		$medium_field_selectors .= ", $this->main_css_element .wp-core-ui div.wpforms-container select.wpforms-field-medium";
		$medium_field_selectors .= ", $this->main_css_element .wp-core-ui div.wpforms-container .wpforms-field-row.wpforms-field-medium";

		// Set max-width for form fields (small single text input).
		// .wpforms-container input.wpforms-field-small,
		// .wpforms-container select.wpforms-field-small,
		// .wpforms-container .wpforms-field-row.wpforms-field-small,
		// .wp-core-ui div.wpforms-container input.wpforms-field-small,
		// .wp-core-ui div.wpforms-container select.wpforms-field-small,
		// .wp-core-ui div.wpforms-container .wpforms-field-row.wpforms-field-small.
		$small_field_selectors  = "$this->main_css_element .wpforms-container input.wpforms-field-small";
		$small_field_selectors .= ", $this->main_css_element .wpforms-container select.wpforms-field-small";
		$small_field_selectors .= ", $this->main_css_element .wpforms-container .wpforms-field-row.wpforms-field-small";
		$small_field_selectors .= ", $this->main_css_element .wp-core-ui div.wpforms-container input.wpforms-field-small";
		$small_field_selectors .= ", $this->main_css_element .wp-core-ui div.wpforms-container select.wpforms-field-small";
		$small_field_selectors .= ", $this->main_css_element .wp-core-ui div.wpforms-container .wpforms-field-row.wpforms-field-small";

		$options['form_field_width'] = array(
			'type'      => 'default',
			'data_type' => 'range',
			'options'   => array(
				array(
					'selector'     => $medium_field_selectors,
					'css_property' => 'max-width',
				),
				array(
					'selector'     => $small_field_selectors,
					'css_property' => 'max-width',
				),
			),
		);

		return $options;
	}

	/**
	 * Get the stylesheet selector for form tag.
	 *
	 * @return string
	 */
	protected function get_form_selector_default() {
		return "$this->form_container form.wpforms-form";
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
	 * Get the stylesheet selector for form submit button.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_default() {
		return "$this->form_container input[type=submit], $this->form_container button[type=submit], $this->form_container .wpforms-page-button";
	}

	/**
	 * Get the stylesheet selector for form submit button to use in hover.
	 *
	 * @return string
	 */
	protected function get_submit_button_selector_hover() {
		return "$this->form_container input[type=submit]:hover, $this->form_container button[type=submit]:hover, $this->form_container .wpforms-page-button:hover";
	}

	/**
	 * Get the stylesheet selector for the error message.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_default() {
		return "$this->form_container .wpforms-error-container-full, $this->form_container .wpforms-error-container";
	}

	/**
	 * Get the stylesheet selector for the error message to use in hover.
	 *
	 * @return string
	 */
	protected function get_error_message_selector_hover() {
		return "$this->form_container .wpforms-error-container-full:hover, $this->form_container .wpforms-error-container:hover";
	}

	/**
	 * Get the stylesheet selector for the success message.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_default() {
		return "$this->form_container .wpforms-confirmation-container-full, $this->main_css_element div div[submit-success]>.wpforms-confirmation-container-full:not(.wpforms-redirection-message)";
	}

	/**
	 * Get the stylesheet selector for the success message to use in hover.
	 *
	 * @return string
	 */
	protected function get_success_message_selector_hover() {
		return "$this->form_container .wpforms-confirmation-container-full:hover, $this->main_css_element div div[submit-success]>.wpforms-confirmation-container-full:not(.wpforms-redirection-message):hover";
	}

	/**
	 * Get the stylesheet selector for form tag to use in hover.
	 *
	 * @return string
	 */
	protected function get_form_selector_hover() {
		return "$this->form_container form.wpforms-form:hover";
	}
}
