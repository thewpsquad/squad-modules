<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Business Hours Day Module Class which extend the Divi Builder Module Class.
 *
 * This class provides day adding functionalities for the parent module in the visual builder.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\BusinessHoursChild;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;

/**
 * Business Hours Day Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class BusinessHoursChild extends DISQ_Builder_Module {


	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name   = esc_html__( 'Business Day', 'squad-modules-for-divi' );
		$this->plural = esc_html__( 'Business Days', 'squad-modules-for-divi' );

		$this->slug       = 'disq_business_day';
		$this->type       = 'child';
		$this->vb_support = 'on';

		$this->child_title_var          = 'day';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// initiate the divider.
		$this->disq_initiate_the_divider_element();

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'day_n_time_content' => esc_html__( 'Day & Time', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'wrapper'           => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
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
				'day_text'  => $this->disq_add_font_field(
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
				'time_text' => $this->disq_add_font_field(
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
				'default' => $default_css_selectors,
				'wrapper' => array(
					'label_prefix' => et_builder_i18n( 'Wrapper' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .day-elements",
							'border_radii_hover'  => "$this->main_css_element div .day-elements:hover",
							'border_styles'       => "$this->main_css_element div .day-elements",
							'border_styles_hover' => "$this->main_css_element div .day-elements:hover",
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'wrapper',
				),
			),
			'box_shadow'     => array(
				'default' => $default_css_selectors,
				'wrapper' => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .day-elements",
						'hover' => "$this->main_css_element div .day-elements:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'wrapper',
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
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'day'     => array(
				'label'    => esc_html__( 'Day', 'squad-modules-for-divi' ),
				'selector' => 'div .day-elements .day-element.day-name-text',
			),
			'time'    => array(
				'label'    => esc_html__( 'Time', 'squad-modules-for-divi' ),
				'selector' => 'div .day-elements .day-element.day-time-text',
			),
			'wrapper' => array(
				'label'    => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
				'selector' => 'div .day-elements',
			),
		);
	}

	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.0.0
	 */
	public function get_fields() {
		// Text fields definitions.
		$text_fields    = array(
			'day'               => array(
				'label'           => esc_html__( 'Day', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The day name will appear in with your day element.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'day_n_time_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'time'              => array(
				'label'           => esc_html__( 'Time', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The time text will appear in with your time element.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'day_n_time_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'dual_time__enable' => $this->disq_add_yes_no_field(
				esc_html__( 'Add Start And Time', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the start and end time.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'depends_show_if'  => 'off',
					'affects'          => array(
						'time',
						'start_time',
						'end_time',
						'time_separator',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'day_n_time_content',
				)
			),
			'start_time'        => array(
				'label'           => esc_html__( 'Start Time', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The start time text will appear in with your time element.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'day_n_time_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'end_time'          => array(
				'label'           => esc_html__( 'End Time', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The end time text will appear in with your time element.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'day_n_time_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'time_separator'    => array(
				'label'           => esc_html__( 'Time Separator', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Here you can set the separator between the start time and end time.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'day_n_time_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'off_day__enable'   => $this->disq_add_yes_no_field(
				esc_html__( 'Mark as Off Day', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not mark as a off day.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'off_day_label',
						'dual_time__enable',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'day_n_time_content',
				)
			),
			'off_day_label'     => array(
				'label'           => esc_html__( 'Off Day Label', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The title of your list item will appear in with your list item.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'day_n_time_content',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
		);
		$divider_fields = $this->disq_get_divider_element_fields(
			'separator_element',
			array(
				'label'            => esc_html__( 'Show Text Separator', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Here you can choose whether or not show the separator for day text.', 'squad-modules-for-divi' ),
				'type'             => 'skip',
				'default'          => 'on',
				'default_on_front' => 'on',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'day_n_time_content',
			)
		);

		// Item wrapper fields definitions.
		$wrapper_background_fields = $this->disq_add_background_field(
			esc_html__( 'Wrapper Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'wrapper_background',
				'context'     => 'wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'wrapper',
			)
		);
		$wrapper_fields            = array(
			'wrapper_text_orientation' => $this->disq_add_alignment_field(
				esc_html__( 'Text Alignment', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'This controls how your text is aligned within the module.', 'squad-modules-for-divi' ),
					'type'        => 'text_align',
					'options'     => et_builder_get_text_orientation_options(
						array( 'justified' ),
						array( 'justify' => 'Justified' )
					),
					'default'     => 'left',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
			'wrapper_margin'           => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Margin', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__(
						'Here you can define a custom margin size for the wrapper.',
						'squad-modules-for-divi'
					),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
			'wrapper_padding'          => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__(
						'Here you can define a custom padding size for the wrapper.',
						'squad-modules-for-divi'
					),
					'type'        => 'custom_padding',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
		);

		// update some fields.
		$divider_fields['divider_color']['default']           = '';
		$divider_fields['divider_color']['default_on_front']  = '';
		$divider_fields['divider_style']['default']           = '';
		$divider_fields['divider_style']['default_on_front']  = '';
		$divider_fields['divider_weight']['default']          = '';
		$divider_fields['divider_weight']['default_on_front'] = '';

		// clean unnecessary fields.
		unset( $divider_fields['divider_position'] );

		return array_merge(
			$text_fields,
			$divider_fields,
			$wrapper_background_fields,
			$wrapper_fields,
			$this->disq_get_general_fields()
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

		// Item wrapper styles.
		$fields['wrapper_background_color'] = array( 'background' => "$this->main_css_element div .day-elements" );
		$fields['wrapper_margin']           = array( 'margin' => "$this->main_css_element div .day-elements" );
		$fields['wrapper_padding']          = array( 'padding' => "$this->main_css_element div .day-elements" );
		$this->disq_fix_border_transition( $fields, 'wrapper', "$this->main_css_element div .day-elements" );
		$this->disq_fix_box_shadow_transition( $fields, 'wrapper', "$this->main_css_element div .day-elements" );

		// divider styles.
		$fields['divider_color']  = array( 'border-top-color' => "$this->main_css_element div .day-elements .day-element.day-element-divider:before" );
		$fields['divider_weight'] = array(
			'border-top-width' => "$this->main_css_element div .day-elements .day-element.day-element-divider:before",
			'height'           => "$this->main_css_element div .day-elements .day-element.day-element-divider:before",
		);

		// Default styles.
		$fields['background_layout'] = array( 'color' => $this->main_css_element );

		return $fields;
	}

	/**
	 * Renders the module output.
	 *
	 * @param array  $attrs List of attributes.
	 * @param string $content Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		$multi_view = et_pb_multi_view_options( $this );
		$divider    = null;

		if ( 'on' === $this->prop( 'show_divider', 'off' ) ) {
			$divider = '<span class="day-element day-element-divider"></span>';
		}

		$day_name_text = $multi_view->render_element(
			array(
				'tag'     => 'span',
				'content' => '{{day}}',
				'attrs'   => array(
					'class' => 'day-element day-name-text',
				),
			)
		);

		$this->disq_generate_all_styles( $attrs );
		$this->disq_generate_divider_styles();

		return sprintf(
			'<div class="day-elements et_pb_with_background">%1$s%2$s%3$s</div>',
			et_core_esc_previously( $day_name_text ),
			et_core_esc_previously( $divider ),
			et_core_esc_previously( $this->disq_render_day_time_text() )
		);
	}

	/**
	 * Render day name
	 *
	 * @return null|string
	 */
	private function disq_render_day_time_text() {
		$multi_view = et_pb_multi_view_options( $this );

		// Show start time and end time when enabled it.
		if ( 'on' === $this->prop( 'dual_time__enable', 'off' ) ) {
			$start_time     = $multi_view->render_element( array( 'content' => '{{start_time}}' ) );
			$end_time       = $multi_view->render_element( array( 'content' => '{{end_time}}' ) );
			$time_separator = $multi_view->render_element( array( 'content' => '{{time_separator}}' ) );

			return sprintf(
				'<span class="day-element day-element-time">%1$s%2$s%3$s</span>',
				et_core_esc_previously( $start_time ),
				et_core_esc_previously( $time_separator ),
				et_core_esc_previously( $end_time )
			);
		}

		// Show off day label when enabled it.
		if ( 'on' === $this->prop( 'off_day__enable', 'off' ) ) {
			$off_day_label = $multi_view->render_element( array( 'content' => '{{off_day_label}}' ) );

			return sprintf(
				'<span class="day-element day-element-time">%1$s</span>',
				et_core_esc_previously( $off_day_label )
			);
		}

		return $multi_view->render_element(
			array(
				'content' => '{{time}}',
				'attrs'   => array(
					'class' => 'day-element day-element-time',
				),
			)
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

		// wrapper style
		// wrapper background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'wrapper_background',
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
					'use_wrapper_background_color_gradient' => 'wrapper_background_use_color_gradient',
					'wrapper_background' => 'wrapper_background_color',
				),
			)
		);
		// text align with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'wrapper_text_orientation',
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
				'field'          => 'wrapper_margin',
				'selector'       => "$this->main_css_element .day-elements",
				'hover_selector' => "$this->main_css_element .day-elements:hover",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		// wrapper padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_padding',
				'selector'       => "$this->main_css_element .day-elements",
				'hover_selector' => "$this->main_css_element .day-elements:hover",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
	}

	/**
	 * Generate styles for divider
	 */
	private function disq_generate_divider_styles() {
		$this->disq_process_divider(
			array(
				'selector'  => "$this->main_css_element div .day-elements .day-element.day-element-divider:before",
				'important' => true,
			)
		);
	}
}

new BusinessHoursChild();
