<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Before After Image Slider Module Class which extend the Divi Builder Module Class.
 *
 * This class provides comprehension image adding functionalities for comparable slider in the visual builder.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\BeforeAfterImageSlider;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Divi_Builder_Module;
use DiviSquad\Utils\Helper;
use function esc_html__;
use function esc_attr__;
use function et_builder_i18n;
use function et_core_esc_previously;
use function et_pb_media_options;
use function et_pb_multi_view_options;
use function et_pb_background_options;
use function wp_strip_all_tags;
use function wp_enqueue_script;
use function wp_json_encode;

/**
 * Before After Image Slider Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class BeforeAfterImageSlider extends Squad_Divi_Builder_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Before After Image Slider', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Before After Image Sliders', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/before-after-image-slider.svg' );

		$this->slug             = 'disq_bai_slider';
		$this->main_css_element = "%%order_class%%.$this->slug";
		$this->vb_support       = 'on';

		$this->child_title_var          = 'title';
		$this->child_title_fallback_var = 'admin_label';

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'        => array(
						'title'             => esc_html__( 'Images', 'squad-modules-for-divi' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'before' => array( 'name' => esc_html__( 'Before', 'squad-modules-for-divi' ) ),
							'after'  => array( 'name' => esc_html__( 'After', 'squad-modules-for-divi' ) ),
						),
					),
					'comparable_settings' => esc_html__( 'General Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'before_image_filter'  => esc_html__( 'Before Image Filter', 'squad-modules-for-divi' ),
					'before_label_element' => esc_html__( 'Before Label', 'squad-modules-for-divi' ),
					'before_label_text'    => esc_html__( 'Before Label Text', 'squad-modules-for-divi' ),
					'after_image_filter'   => esc_html__( 'After Image Filter', 'squad-modules-for-divi' ),
					'after_label_element'  => esc_html__( 'After Label', 'squad-modules-for-divi' ),
					'after_label_text'     => esc_html__( 'After Label Text', 'squad-modules-for-divi' ),
				),
			),
		);

		$default_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'before_label_text' => $this->disq_add_font_field(
					esc_html__( 'Label', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '16px',
						),
						'text_shadow' => array(
							'show_if' => array(
								'image_label__enable' => 'on',
							),
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
							'hover' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
						),
					)
				),
				'after_label_text'  => $this->disq_add_font_field(
					esc_html__( 'Label', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '16px',
						),
						'text_shadow' => array(
							'show_if' => array(
								'image_label__enable' => 'on',
							),
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
							'hover' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
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
			'filters'        => array(
				'child_filters_target' => array(
					'label'       => '',
					'css'         => array(
						'main'  => "$this->main_css_element div .compare-images.icv .icv__img.icv__img-b",
						'hover' => "$this->main_css_element div .compare-images.icv:hover .icv__img.icv__img-b",
					),
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'before_image_filter',
				),
				'css'                  => array(
					'main'  => "$this->main_css_element div .compare-images.icv .icv__img.icv__img-a",
					'hover' => "$this->main_css_element div .compare-images.icv:hover .icv__img.icv__img-a",
				),
				'tab_slug'             => 'advanced',
				'toggle_slug'          => 'after_image_filter',
			),
			'borders'        => array(
				'default'              => $default_css_selectors,
				'before_label_element' => array(
					'label_prefix'    => esc_html__( 'Label', 'squad-modules-for-divi' ),
					'css'             => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
							'border_radii_hover'  => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
							'border_styles'       => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
							'border_styles_hover' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
						),
					),
					'depends_on'      => array( 'image_label__enable' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'before_label_element',
				),
				'after_label_element'  => array(
					'label_prefix'    => esc_html__( 'Label', 'squad-modules-for-divi' ),
					'css'             => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
							'border_radii_hover'  => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
							'border_styles'       => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
							'border_styles_hover' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
						),
					),
					'depends_on'      => array( 'image_label__enable' ),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'after_label_element',
				),
			),
			'box_shadow'     => array(
				'default'              => $default_css_selectors,
				'before_label_element' => array(
					'label'             => esc_html__( 'Label Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
						'hover' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'depends_on'        => array( 'image_label__enable' ),
					'depends_show_if'   => 'on',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'before_label_element',
				),
				'after_label_element'  => array(
					'label'             => esc_html__( 'Label Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
						'hover' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'depends_on'        => array( 'image_label__enable' ),
					'depends_show_if'   => 'on',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'after_label_element',
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
			'width'          => $default_css_selectors,
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
			'before_label' => array(
				'label'    => esc_html__( 'Before Label', 'squad-modules-for-divi' ),
				'selector' => 'div .compare-images.icv .icv__label.icv__label-before',
			),
			'after_label'  => array(
				'label'    => esc_html__( 'After Label', 'squad-modules-for-divi' ),
				'selector' => 'div .compare-images.icv .icv__label.icv__label-after',
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
		// Image fields definitions.
		$image_fields = array_merge(
			$this->disq_get_image_fields( 'before' ),
			$this->disq_get_image_fields( 'after' )
		);

		$settings_fields = array(
			'image_label__enable'               => $this->disq_add_yes_no_field(
				esc_html__( 'Show Label', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not use label for image.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'image_label_hover__enable',
						'before_label_text',
						'before_label_text_font',
						'before_label_text_text_color',
						'before_label_text_text_align',
						'before_label_text_font_size',
						'before_label_text_letter_spacing',
						'before_label_text_line_height',
						'before_label_background_color',
						'before_label_margin',
						'before_label_padding',
						'after_label_text',
						'after_label_text_font',
						'after_label_text_text_color',
						'after_label_text_text_align',
						'after_label_text_font_size',
						'after_label_text_letter_spacing',
						'after_label_text_line_height',
						'after_label_background_color',
						'after_label_margin',
						'after_label_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'image_label_hover__enable'         => $this->disq_add_yes_no_field(
				esc_html__( 'Show Label On Hover', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show the label on hover effect.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_direction_mode'              => $this->disq_add_select_box_field(
				esc_html__( 'Direction', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Slide image to the vertical or horizontal.', 'squad-modules-for-divi' ),
					'options'          => array(
						'horizontal' => esc_html__( 'Horizontal', 'squad-modules-for-divi' ),
						'vertical'   => esc_html__( 'Vertical', 'squad-modules-for-divi' ),
					),
					'default'          => 'horizontal',
					'default_on_front' => 'horizontal',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_trigger_type'                => $this->disq_add_select_box_field(
				esc_html__( 'Movement Trigger', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Move slide image with hover or click.', 'squad-modules-for-divi' ),
					'options'          => array(
						'click' => esc_html__( 'Drag', 'squad-modules-for-divi' ),
						'hover' => esc_html__( 'Hover', 'squad-modules-for-divi' ),
					),
					'default'          => 'click',
					'default_on_front' => 'click',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_control_color'               => $this->disq_add_color_field(
				esc_html__( 'Control Color', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can define a custom color for your slide control.', 'squad-modules-for-divi' ),
					'default'          => '#FFFFFF',
					'default_on_front' => '#FFFFFF',
					'mobile_options'   => false,
					'sticky'           => false,
					'hover'            => false,
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_control_start_point'         => $this->disq_add_range_field(
				esc_html__( 'Control Starting Point', 'squad-modules-for-divi' ),
				array(
					'description'       => esc_html__( 'Increase the order number to position the item lower.', 'squad-modules-for-divi' ),
					'range_settings'    => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'           => 25,
					'default_on_front'  => 25,
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'mobile_options'    => false,
					'responsive'        => false,
					'hover'             => false,
					'tab_slug'          => 'general',
					'toggle_slug'       => 'comparable_settings',
				)
			),
			'slide_control_shadow__enable'      => $this->disq_add_yes_no_field(
				esc_html__( 'Show Control Shadow', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not show shadow for slide control.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_control_circle__enable'      => $this->disq_add_yes_no_field(
				esc_html__( 'Enable Circle Control', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not circle slide control.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'slide_control_circle_blur__enable',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_control_circle_blur__enable' => $this->disq_add_yes_no_field(
				esc_html__( 'Enable Circle Control Blur', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not blur for circle slide control.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_control_smoothing__enable'   => $this->disq_add_yes_no_field(
				esc_html__( 'Enable Control Smoothness', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not smoothness for slide control.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'slide_control_smoothing_amount',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'comparable_settings',
				)
			),
			'slide_control_smoothing_amount'    => $this->disq_add_range_field(
				esc_html__( 'Control Smoothing Amount', 'squad-modules-for-divi' ),
				array(
					'description'       => esc_html__( 'Increase the slide control smoothing.', 'squad-modules-for-divi' ),
					'range_settings'    => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'mobile_options'    => false,
					'responsive'        => false,
					'hover'             => false,
					'depends_show_if'   => 'on',
					'tab_slug'          => 'general',
					'toggle_slug'       => 'comparable_settings',
				)
			),
		);

		// Fields definitions.
		return array_merge(
			$image_fields,
			$settings_fields
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

		// before label styles.
		$fields['before_label_background_color'] = array( 'background' => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before" );
		$fields['before_label_margin']           = array( 'margin' => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before" );
		$fields['before_label_padding']          = array( 'padding' => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before" );
		$this->disq_fix_fonts_transition( $fields, 'before_label_text', "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before" );
		$this->disq_fix_border_transition( $fields, 'before_label_element', "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before" );
		$this->disq_fix_box_shadow_transition( $fields, 'before_label_element', "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before" );

		// after label styles.
		$fields['after_label_background_color'] = array( 'background' => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after" );
		$fields['after_label_margin']           = array( 'margin' => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after" );
		$fields['after_label_padding']          = array( 'padding' => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after" );
		$this->disq_fix_fonts_transition( $fields, 'after_label_text', "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after" );
		$this->disq_fix_border_transition( $fields, 'after_label_element', "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after" );
		$this->disq_fix_box_shadow_transition( $fields, 'after_label_element', "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after" );

		// Default styles.
		$fields['background_layout'] = array( 'color' => $this->main_css_element );

		return $fields;
	}

	/**
	 * Renders the module output.
	 *
	 * @param array  $attrs       List of attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClassAfterLastUsed
		$multi_view   = et_pb_multi_view_options( $this );
		$before_label = $multi_view->render_element(
			array(
				'content'        => '{{before_label}}',
				'hover_selector' => "$this->main_css_element div .compare-images.icv",
			)
		);
		$after_label  = $multi_view->render_element(
			array(
				'content'        => '{{after_label}}',
				'hover_selector' => "$this->main_css_element div .compare-images.icv",
			)
		);

		$settings = array(
			'controlColor'    => $this->prop( 'slide_control_color', '#FFFFFF' ),
			'controlShadow'   => 'on' === $this->prop( 'slide_control_shadow__enable', 'off' ),
			'addCircle'       => 'on' === $this->prop( 'slide_control_circle__enable', 'off' ),
			'addCircleBlur'   => 'on' === $this->prop( 'slide_control_circle_blur__enable', 'off' ),
			'showLabels'      => 'on' === $this->prop( 'image_label__enable', 'off' ),
			'labelOptions'    => array(
				'before'  => wp_strip_all_tags( $before_label ),
				'after'   => wp_strip_all_tags( $after_label ),
				'onHover' => 'on' === $this->prop( 'image_label_hover__enable', 'off' ),
			),
			'smoothing'       => 'on' === $this->prop( 'slide_control_smoothing__enable', 'off' ),
			'smoothingAmount' => (int) $this->prop( 'slide_control_smoothing_amount', 100 ),
			'hoverStart'      => 'hover' === $this->prop( 'slide_trigger_type', 'drag' ),
			'verticalMode'    => 'vertical' === $this->prop( 'slide_direction_mode', 'horizontal' ),
			'startingPoint'   => (int) $this->prop( 'slide_control_start_point', 25 ),
		);

		$default_image_url = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==';
		$default_class     = 'disq-image et_pb_image_wrap';
		$empty_notice      = '';

		// Generate fallback image for before and after.
		$default_before_image = sprintf( '<img alt="" src="%1$s" class="%2$s"/>', $default_image_url, $default_class );
		$default_after_image  = sprintf( '<img alt="" src="%1$s" class="%2$s" style="%3$s;"/>', $default_image_url, $default_class, 'filter: brightness(60%)' );

		// Verify and set actual and fallback image for before and after.
		$before_image = ! empty( $this->prop( 'before_image', '' ) ) ? $this->disq_render_image( 'before' ) : $default_before_image;
		$after_image  = ! empty( $this->prop( 'after_image', '' ) ) ? $this->disq_render_image( 'after' ) : $default_after_image;

		if ( empty( $this->prop( 'before_image', '' ) ) && empty( $this->prop( 'after_image', '' ) ) ) {
			$empty_notice = sprintf(
				'<div class="disq_notice" style="margin-bottom: 20px;">%s</div>',
				esc_html__( 'Add before and after images for comprehension. You are see a preview.', 'squad-modules-for-divi' )
			);
		}

		// Process styles for module output.
		$this->disq_generate_all_styles( $attrs );

		// Images: Add CSS Filters and Mix Blend Mode rules.
		$this->generate_css_filters( $this->slug, '', "$this->main_css_element div .compare-images.icv .icv__img.icv__img-a" );
		$this->generate_css_filters( $this->slug, 'child_', "$this->main_css_element div .compare-images.icv .icv__wrapper" );

		wp_enqueue_script( 'disq-module-bais' );

		return sprintf(
			'%1$s<div class="compare-images" data-setting="%4$s">%2$s%3$s</div>',
			et_core_esc_previously( $empty_notice ),
			et_core_esc_previously( $before_image ),
			et_core_esc_previously( $after_image ),
			esc_attr( wp_json_encode( $settings ) )
		);
	}

	/**
	 * Render image.
	 *
	 * @param string $image_type The image type.
	 *
	 * @return null|string
	 */
	private function disq_render_image( $image_type ) {
		$multi_view = et_pb_multi_view_options( $this );
		$alt_text   = $this->_esc_attr( "{$image_type}_alt" );

		$image_classes          = 'disq-image et_pb_image_wrap';
		$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, "'{$image_type}_image' " );
		if ( ! empty( $image_attachment_class ) ) {
			$image_classes .= " $image_attachment_class";
		}

		return $multi_view->render_element(
			array(
				'tag'            => 'img',
				'attrs'          => array(
					'src'   => "{{{$image_type}_image}}",
					'class' => $image_classes,
					'alt'   => $alt_text,
				),
				'required'       => "{$image_type}_image",
				'hover_selector' => "$this->main_css_element div .compare-images.icv",
			)
		);
	}

	/**
	 * Get image and associated fields.
	 *
	 * @param string $image_type The current image name.
	 *
	 * @return array image and associated fields.
	 */
	private function disq_get_image_fields( $image_type ) {
		// Image fields definitions.
		$image_fields_all = array(
			"{$image_type}_image" => array(
				'label'              => et_builder_i18n( 'Image' ),
				'description'        => esc_html__( 'Upload an image to display.', 'squad-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'squad-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'squad-modules-for-divi' ),
				'tab_slug'           => 'general',
				'toggle_slug'        => 'main_content',
				'sub_toggle'         => $image_type,
				'dynamic_content'    => 'image',
			),
			"{$image_type}_alt"   => array(
				'label'           => esc_html__( 'Image Alt Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'image',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'sub_toggle'      => $image_type,
				'dynamic_content' => 'text',
			),
			"{$image_type}_label" => array(
				'label'           => esc_html__( 'Image Label Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The label of your image will appear in with image.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'sub_toggle'      => $image_type,
				'dynamic_content' => 'text',
			),
		);

		// background fields definitions.
		$label_background_fields = $this->disq_add_background_field(
			esc_html__( 'Label Background', 'squad-modules-for-divi' ),
			array(
				'base_name'       => "{$image_type}_label_background",
				'context'         => "{$image_type}_label_background_color",
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => "{$image_type}_label_element",
			)
		);

		// label associated fields definitions.
		$label_associate_fields = array(
			"{$image_type}_label_margin"  => $this->disq_add_margin_padding_field(
				esc_html__( 'Label Margin', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom margin size for the before label.', 'squad-modules-for-divi' ),
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
					'toggle_slug'     => "{$image_type}_label_element",
				)
			),
			"{$image_type}_label_padding" => $this->disq_add_margin_padding_field(
				esc_html__( 'Label Padding', 'squad-modules-for-divi' ),
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
					'toggle_slug'    => "{$image_type}_label_element",
				)
			),
		);

		return array_merge(
			$image_fields_all,
			$label_background_fields,
			$label_associate_fields
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

		// background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'before_label_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
				'selector_hover'         => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
				'selector_sticky'        => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_before_label_background_color_gradient' => 'before_label_background_use_color_gradient',
					'before_label_background' => 'before_label_background_color',
				),
			)
		);
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'after_label_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
				'selector_hover'         => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
				'selector_sticky'        => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_after_label_background_color_gradient' => 'after_label_background_use_color_gradient',
					'after_label_background' => 'after_label_background_color',
				),
			)
		);

		// margin and padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'before_label_margin',
				'selector'       => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
				'hover_selector' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'before_label_padding',
				'selector'       => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-before",
				'hover_selector' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-before",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'after_label_margin',
				'selector'       => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
				'hover_selector' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'after_label_padding',
				'selector'       => "$this->main_css_element div .compare-images.icv .icv__label.icv__label-after",
				'hover_selector' => "$this->main_css_element div .compare-images.icv:hover .icv__label.icv__label-after",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);

		// phpcs:disable

		// Add height and width support for images.
		// $additional_props = array( 'width', 'max_width', 'height', 'min_height', 'max_height' );
		// foreach ( $additional_props as $additional_prop ) {
		// $css_property = str_replace( '_', '-', $additional_prop );
		// $this->generate_styles(
		// array(
		// 'attrs'          => $this->props,
		// 'base_attr_name' => $additional_prop,
		// 'selector'       => "$this->main_css_element div .compare-images, $this->main_css_element div .compare-images img",
		// 'css_property'   => $css_property,
		// 'render_slug'    => $this->slug,
		// 'type'           => 'range',
		// )
		// );
		// }

		// phpcs:enable
	}
}

new BeforeAfterImageSlider();
