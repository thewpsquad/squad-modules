<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Business Hours Module Class which extend the Divi Builder Module Class.
 *
 * This class provides listed working hours day adding functionalities in the visual builder.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\BusinessHours;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;
use DiviSquad\Utils\Helper;

/**
 * Business Hours Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class BusinessHours extends DISQ_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name   = esc_html__( 'Business Hours', 'squad-modules-for-divi' );
		$this->plural = esc_html__( 'Business Hours', 'squad-modules-for-divi' );

		$this->icon_path = Helper::fix_slash( __DIR__ . '/clock-history.svg' );

		$this->slug       = 'disq_business_hours';
		$this->child_slug = 'disq_business_day';
		$this->vb_support = 'on';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// initiate the divider.
		$this->disq_initiate_the_divider_element();

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'title_content'    => esc_html__( 'Title Content', 'squad-modules-for-divi' ),
					'general_settings' => esc_html__( 'General Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'day_wrapper'       => esc_html__( 'Day & Time Wrapper', 'squad-modules-for-divi' ),
					'title_element'     => esc_html__( 'Title', 'squad-modules-for-divi' ),
					'title_text'        => esc_html__( 'Title Text', 'squad-modules-for-divi' ),
					'separator_element' => esc_html__( 'Separator', 'squad-modules-for-divi' ),
					'day_n_time_text'   => array(
						'title'             => esc_html__( 'Day & Time Text', 'squad-modules-for-divi' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'day'  => array(
								'name' => esc_html__( 'Day', 'squad-modules-for-divi' ),
							),
							'time' => array(
								'name' => esc_html__( 'Time', 'squad-modules-for-divi' ),
							),
						),
					),
				),
			),
		);

		$default_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'title_text' => $this->disq_add_font_field(
					esc_html__( 'Title', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '26px',
						),
						'line_height' => array(
							'default' => '1.2em',
						),
						'css'         => array(
							'main'  => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper .bh-title-text",
							'hover' => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper:hover .bh-title-text",
						),
						'tab_slug'    => 'advanced',
						'toggle_slug' => 'title_text',
					)
				),
				'day_text'   => $this->disq_add_font_field(
					esc_html__( 'Day', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '16px',
						),
						'font_weight' => array(
							'default' => '400',
						),
						'css'         => array(
							'main'  => "$this->main_css_element .day-elements .day-name-text",
							'hover' => "$this->main_css_element .day-elements:hover .day-name-text",
						),
						'tab_slug'    => 'advanced',
						'toggle_slug' => 'day_n_time_text',
						'sub_toggle'  => 'day',
					)
				),
				'time_text'  => $this->disq_add_font_field(
					esc_html__( 'Time', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '16px',
						),
						'font_weight' => array(
							'default' => '400',
						),
						'css'         => array(
							'main'  => "$this->main_css_element .day-elements .day-element-time",
							'hover' => "$this->main_css_element .day-elements:hover .day-element-time",
						),
						'tab_slug'    => 'advanced',
						'toggle_slug' => 'day_n_time_text',
						'sub_toggle'  => 'time',
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
				'default'       => $default_css_selectors,
				'day_wrapper'   => array(
					'label_prefix' => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element .day-elements",
							'border_radii_hover'  => "$this->main_css_element .day-elements:hover",
							'border_styles'       => "$this->main_css_element .day-elements",
							'border_styles_hover' => "$this->main_css_element .day-elements:hover",
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'day_wrapper',
				),
				'title_element' => array(
					'label_prefix' => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
							'border_radii_hover'  => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper:hover",
							'border_styles'       => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
							'border_styles_hover' => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper:hover",
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'title_element',
				),
			),
			'box_shadow'     => array(
				'default'       => $default_css_selectors,
				'day_wrapper'   => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element .day-elements",
						'hover' => "$this->main_css_element .day-elements:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'day_wrapper',
				),
				'title_element' => array(
					'label'             => esc_html__( 'Title Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
						'hover' => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'title_element',
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
			'text'           => false,
			'button'         => false,
			'link_options'   => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'title'   => array(
				'label'    => esc_html__( 'Title', 'squad-modules-for-divi' ),
				'selector' => '.bh-element.bh-title-wrapper .bh-title-text',
			),
			'day'     => array(
				'label'    => esc_html__( 'Day', 'squad-modules-for-divi' ),
				'selector' => '.day-elements .day-name-text',
			),
			'time'    => array(
				'label'    => esc_html__( 'Time', 'squad-modules-for-divi' ),
				'selector' => '.day-elements .day-time-text',
			),
			'wrapper' => array(
				'label'    => esc_html__( 'Day Wrapper', 'squad-modules-for-divi' ),
				'selector' => '.day-elements',
			),
		);
	}

	/**
	 * Return an add new item(module) text.
	 *
	 * @return string
	 */
	public function add_new_child_text() {
		return esc_html__( 'Add New Business Day', 'squad-modules-for-divi' );
	}

	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.0.0
	 */
	public function get_fields() {
		$text_fields      = array(
			'title'     => array(
				'label'           => et_builder_i18n( 'Title' ),
				'description'     => esc_html__( 'The title text will appear before at your business hours.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'title_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'title_tag' => $this->disq_add_select_box_field(
				esc_html__( 'Title Tag', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a tag to display with your title.', 'squad-modules-for-divi' ),
					'options'          => $this->disq_get_html_tag_elements(),
					'default'          => 'h2',
					'default_on_front' => 'h2',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'title_content',
				)
			),
		);
		$general_settings = array(
			'title__enable'    => $this->disq_add_yes_no_field(
				esc_html__( 'Show Title', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the title text.', 'squad-modules-for-divi' ),
					'default'          => 'on',
					'default_on_front' => 'on',
					'affects'          => array(
						'wrapper_gap',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'general_settings',
				)
			),
			'day_elements_gap' => $this->disq_add_range_fields(
				esc_html__( 'Gap Between Days', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose the gap between days.', 'squad-modules-for-divi' ),
					'type'             => 'range',
					'range_settings'   => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'          => '10px',
					'default_on_front' => '10px',
					'default_unit'     => 'px',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'general_settings',
				),
				array( 'use_hover' => false )
			),
			'wrapper_gap'      => $this->disq_add_range_fields(
				esc_html__( 'Gap between Title and Day Wrapper', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Adjust the gap between the title and the day wrapper.', 'squad-modules-for-divi' ),
					'range_settings'   => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1100',
						'max'       => '1100',
						'step'      => '1',
					),
					'allow_empty'      => true,
					'default_unit'     => 'px',
					'default'          => '30px',
					'default_on_front' => '30px',
					'hover'            => false,
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'general_settings',
				)
			),
		);
		$divider_fields   = $this->disq_get_divider_element_fields(
			'separator_element',
			array(
				'label'       => esc_html__( 'Show Text Separator', 'squad-modules-for-divi' ),
				'description' => esc_html__( 'Here you can choose whether or not show the separator between day and time text.', 'squad-modules-for-divi' ),
				'tab_slug'    => 'general',
				'toggle_slug' => 'general_settings',
			)
		);

		$wrapper_background_fields = $this->disq_add_background_field(
			esc_html__( 'Wrapper Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'day_wrapper_background',
				'context'     => 'day_wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'day_wrapper',
			)
		);
		$title_background_fields   = $this->disq_add_background_field(
			esc_html__( 'Title Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'title_background',
				'context'     => 'title_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'title_element',
			)
		);
		$wrapper_associated_fields = array(
			'day_text_width'               => $this->disq_add_range_fields(
				esc_html__( 'Day Text Width', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Adjust the width of the day text.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1100',
						'max'       => '1100',
						'step'      => '1',
					),
					'allow_empty'    => true,
					'default_unit'   => 'px',
					'hover'          => false,
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'day_wrapper',
				)
			),
			'time_text_width'              => $this->disq_add_range_fields(
				esc_html__( 'Time Text Width', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Adjust the width of the time text.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1100',
						'max'       => '1100',
						'step'      => '1',
					),
					'allow_empty'    => true,
					'default_unit'   => 'px',
					'hover'          => false,
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'day_wrapper',
				)
			),
			'day_wrapper_text_orientation' => $this->disq_add_alignment_field(
				esc_html__( 'Text Alignment', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This controls how your text is aligned within the module.', 'squad-modules-for-divi' ),
					'type'             => 'text_align',
					'options'          => et_builder_get_text_orientation_options(
						array( 'justified' ),
						array( 'justify' => 'Justified' )
					),
					'default'          => 'left',
					'default_on_front' => 'left',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'day_wrapper',
				)
			),
			'day_wrapper_margin'           => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Margin', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom margin size for the wrapper.', 'squad-modules-for-divi' ),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'day_wrapper',
				)
			),
			'day_wrapper_padding'          => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can define a custom padding size for the wrapper.', 'squad-modules-for-divi' ),
					'type'             => 'custom_padding',
					'default'          => '10px|15px|10px|15px',
					'default_on_front' => '10px|15px|10px|15px',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'day_wrapper',
				)
			),
		);
		$text_associated_fields    = array(
			'title_margin'  => $this->disq_add_margin_padding_field(
				esc_html__( 'Title Margin', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can define a custom margin size for the title.', 'squad-modules-for-divi' ),
					'type'           => 'custom_margin',
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'title_element',
				)
			),
			'title_padding' => $this->disq_add_margin_padding_field(
				esc_html__( 'Title Padding', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can define a custom padding size for the title.', 'squad-modules-for-divi' ),
					'type'             => 'custom_padding',
					'range_settings'   => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'          => '4px|8px|4px|8px',
					'default_on_front' => '4px|8px|4px|8px',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'title_element',
				)
			),
		);

		// clean unnecessary fields.
		unset( $divider_fields['divider_position'] );

		return array_merge(
			$text_fields,
			$general_settings,
			$wrapper_background_fields,
			$wrapper_associated_fields,
			$divider_fields,
			$title_background_fields,
			$text_associated_fields
		);
	}

	/**
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the fields list.
	 *
	 * @since 1.0.0
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// wrapper styles.
		$fields['day_wrapper_background_color'] = array(
			'background' => "$this->main_css_element .day-elements",
		);
		$fields['day_wrapper_margin']           = array(
			'margin' => "$this->main_css_element .day-elements",
		);
		$fields['day_wrapper_padding']          = array(
			'padding' => "$this->main_css_element .day-elements",
		);
		$this->disq_fix_border_transition( $fields, 'item_wrapper', "$this->main_css_element .day-elements" );
		$this->disq_fix_box_shadow_transition( $fields, 'item_wrapper', "$this->main_css_element .day-elements" );

		// title styles.
		$fields['title_background_color'] = array(
			'background' => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
		);
		$fields['title_margin']           = array(
			'margin' => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
		);
		$fields['title_padding']          = array(
			'padding' => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
		);
		$this->disq_fix_fonts_transition( $fields, 'title_text', "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper .bh-title-text" );
		$this->disq_fix_border_transition( $fields, 'title_element', "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper" );
		$this->disq_fix_box_shadow_transition( $fields, 'title_element', "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper" );

		// divider styles.
		$fields['divider_color']  = array(
			'border-top-color' => "$this->main_css_element .day-element.day-element-divider:before",
		);
		$fields['divider_weight'] = array(
			'border-top-width' => "$this->main_css_element .day-element.day-element-divider:before",
			'height'           => "$this->main_css_element .day-element.day-element-divider:before",
		);

		// Default styles.
		$fields['background_layout'] = array(
			'color' => $this->main_css_element,
		);

		return $fields;
	}

	/**
	 * Render module output
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string module's rendered output
	 * @since 1.0.0
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		// Show a notice message in the frontend if the list item is empty.
		$content_warning  = sprintf( '<div class="disq_notice">%s</div>', esc_html__( 'Add one or more business day.', 'squad-modules-for-divi' ) );
		$title_verified   = 'on' === $this->prop( 'title__enable', 'off' ) ? $this->disq_render_title_text() : null;
		$content_verified = '' === $this->content ? $content_warning : $this->content;

		// Process styles for module output.
		$this->disq_generate_all_styles( $attrs );
		$this->disq_generate_divider_styles();

		return sprintf(
			'<div class="disq-bh-elements"> %1$s <div class="disq-list-container business-days"> %2$s </div> </div>',
			$title_verified,
			$content_verified
		);
	}

	/**
	 * Process styles for module output.
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return void
	 */
	private function disq_generate_all_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// List gap with default, responsive.
		$this->disq_process_additional_styles(
			array(
				'field'        => 'day_elements_gap',
				'selector'     => "$this->main_css_element div .disq-list-container",
				'css_property' => 'gap',
				'type'         => 'grid',
			)
		);
		if ( 'on' === $this->prop( 'title__enable', 'on' ) ) {
			$this->disq_process_additional_styles(
				array(
					'field'        => 'wrapper_gap',
					'selector'     => "$this->main_css_element .disq-bh-elements",
					'css_property' => 'gap',
					'type'         => 'grid',
				)
			);
		}

		// wrapper style.
		// wrapper background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'day_wrapper_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element .day-elements",
				'selector_hover'         => "$this->main_css_element .day-elements:hover",
				'selector_sticky'        => "$this->main_css_element .day-elements",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_day_wrapper_background_color_gradient' => 'day_wrapper_background_use_color_gradient',
					'day_wrapper_background' => 'day_wrapper_background_color',
				),
			)
		);
		// text align with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'day_text_width',
				'selector'       => "$this->main_css_element .day-elements .day-name-text",
				'css_property'   => 'width',
				'render_slug'    => $this->slug,
				'type'           => 'range',
			)
		);
		// text align with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'time_text_width',
				'selector'       => "$this->main_css_element .day-elements .day-element-time",
				'css_property'   => 'width',
				'render_slug'    => $this->slug,
				'type'           => 'range',
			)
		);
		// text align with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'day_wrapper_text_orientation',
				'selector'       => "$this->main_css_element .day-elements",
				'hover_selector' => "$this->main_css_element .day-elements:hover",
				'css_property'   => 'text-align',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);
		// wrapper margin with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'day_wrapper_margin',
				'selector'       => "$this->main_css_element .day-elements",
				'hover_selector' => "$this->main_css_element .day-elements:hover",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		// wrapper padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'day_wrapper_padding',
				'selector'       => "$this->main_css_element .day-elements",
				'hover_selector' => "$this->main_css_element .day-elements:hover",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);

		// title styles
		// title background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'title_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
				'selector_hover'         => "$this->main_css_element .disq-bh-elements:hover .bh-element.bh-title-wrapper",
				'selector_sticky'        => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_title_background_color_gradient' => 'title_background_use_color_gradient',
					'title_background'                    => 'title_background_color',
				),
			)
		);
		// title margin with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'        => 'title_margin',
				'selector'     => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
				'hover'        => "$this->main_css_element .disq-bh-elements:hover .bh-element.bh-title-wrapper",
				'css_property' => 'margin',
				'type'         => 'margin',
			)
		);
		// title padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'        => 'title_padding',
				'selector'     => "$this->main_css_element .disq-bh-elements .bh-element.bh-title-wrapper",
				'hover'        => "$this->main_css_element .disq-bh-elements:hover .bh-element.bh-title-wrapper",
				'css_property' => 'padding',
				'type'         => 'padding',
			)
		);
	}

	/**
	 * Generate styles for divider
	 */
	private function disq_generate_divider_styles() {
		if ( 'on' === $this->prop( 'show_divider', 'off' ) ) {
			$this->disq_process_divider(
				array(
					'selector'  => "$this->main_css_element .day-elements .day-element-divider:before",
					'important' => true,
				)
			);
		} else {
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element .day-element.day-element-divider",
					'declaration' => 'display: none !important;',
				)
			);
		}
	}

	/**
	 * Render title
	 *
	 * @return null|string
	 */
	private function disq_render_title_text() {
		$multi_view = et_pb_multi_view_options( $this );
		// title tag, by default is h4.
		$title_tag = isset( $this->props['title_tag'] ) ? $this->props['title_tag'] : 'h2';

		$title_text = $multi_view->render_element(
			array(
				'tag'     => $title_tag,
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'bh-title-text',
				),
			)
		);

		return sprintf(
			'<div class="bh-element bh-title-wrapper">%1$s</div>',
			et_core_esc_previously( $title_text )
		);
	}
}

new BusinessHours();
