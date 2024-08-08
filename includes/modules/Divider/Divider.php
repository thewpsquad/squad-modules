<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Divider Module Class which extend the Divi Builder Module Class.
 *
 * This class provides dividers adding functionalities in the visual builder.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\Divider;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;
use DiviSquad\Utils\Divi;
use DiviSquad\Utils\Helper;
use ET_Builder_Module_Helper_MultiViewOptions;

/**
 * Divider Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class Divider extends DISQ_Builder_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Advanced Divider', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Advanced Dividers', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( __DIR__ . '/icon.svg' );

		$this->slug             = 'disq_divider';
		$this->main_css_element = "%%order_class%%.$this->slug";
		$this->vb_support       = 'on';

		$this->child_title_var          = 'admin_label';
		$this->child_title_fallback_var = 'admin_label';

		$this->disq_initiate_the_divider_element();

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'divider'        => esc_html__( 'Divider', 'squad-modules-for-divi' ),
					'icon_element'   => esc_html__( 'Icon & Image', 'squad-modules-for-divi' ),
					'lottie_element' => esc_html__( 'Lottie Animation', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'wrapper'           => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
					'divider'           => esc_html__( 'Divider', 'squad-modules-for-divi' ),
					'divider_element'   => esc_html__( 'Divider Elements', 'squad-modules-for-divi' ),
					'icon_element'      => esc_html__( 'Icon & Image', 'squad-modules-for-divi' ),
					'divider_icon_text' => esc_html__( 'Divider Text', 'squad-modules-for-divi' ),
					'lottie_element'    => esc_html__( 'Lottie', 'squad-modules-for-divi' ),
				),
			),
		);

		$default_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'divider_icon_text' => $this->disq_add_font_field(
					esc_html__( 'Icon Text', 'squad-modules-for-divi' ),
					array(
						'font_size'       => array(
							'default' => '40px',
						),
						'text_shadow'     => array(
							'show_if' => array(
								'divider_type' => 'text',
							),
						),
						'hide_text_align' => true,
						'css'             => array(
							'main'  => "$this->main_css_element div .divider-elements .divider-icon-wrapper .divider-icon-text",
							'hover' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .divider-icon-text",
						),
						'depends_show_if' => 'text',
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
				'default'      => $default_css_selectors,
				'wrapper'      => array(
					'label_prefix' => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .divider-elements",
							'border_radii_hover'  => "$this->main_css_element div .divider-elements:hover",
							'border_styles'       => "$this->main_css_element div .divider-elements",
							'border_styles_hover' => "$this->main_css_element div .divider-elements:hover",
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'wrapper',
				),
				'icon_element' => array(
					'label_prefix'        => esc_html__( 'Icon', 'squad-modules-for-divi' ),
					'css'                 => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
							'border_radii_hover'  => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
							'border_styles'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
							'border_styles_hover' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
						),
					),
					'depends_on'          => array( 'divider_icon_type' ),
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon_element',
				),
			),
			'box_shadow'     => array(
				'default'      => $default_css_selectors,
				'wrapper'      => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .divider-elements",
						'hover' => "$this->main_css_element div .divider-elements:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'wrapper',
				),
				'icon_element' => array(
					'label'             => esc_html__( 'Icon Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
						'hover' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'show_if_not'       => array(
						'divider_type' => array( 'none', 'text' ),
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'icon_element',
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
			'filters'        => array(
				'child_filters_target' => array(
					'label'               => et_builder_i18n( 'Icon' ),
					'css'                 => array(
						'main'  => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
						'hover' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
					),
					'depends_on'          => array( 'divider_icon_type' ),
					'depends_show_if_not' => array( 'none', 'icon', 'lottie' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon_element',
				),
			),
			'image_icon'     => false,
			'text'           => false,
			'button'         => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array();
	}

	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.0.0
	 */
	public function get_fields() {
		$divider_default_fields     = array(
			'divider_type'          => $this->disq_add_select_box_field(
				esc_html__( 'Divider Type', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose an divider type to display.', 'squad-modules-for-divi' ),
					'options'          => array(
						'none' => esc_html__( 'Only Line', 'squad-modules-for-divi' ),
						'text' => esc_html__( 'Line With Text', 'squad-modules-for-divi' ),
						'icon' => esc_html__( 'Liner With Icon', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'icon',
					'default'          => 'icon',
					'depends_show_if'  => 'on',
					'affects'          => array(
						'divider_icon_type',
						'divider_element_gap',
						'divider_icon_text',
						'divider_icon_text_font',
						'divider_icon_text_text_color',
						'divider_icon_text_font_size',
						'divider_icon_text_letter_spacing',
						'divider_icon_text_line_height',
						'divider_icon_text_tag',
						'divider_icon_text_clip__enable',
						'divider_element_placement',
						'divider_element_margin',
						'divider_element_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'divider',
				)
			),
			'divider_icon_text'     => array(
				'label'           => et_builder_i18n( 'Text' ),
				'description'     => esc_html__( 'The title of your divider will appear in bold below your divider image.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'text',
				'dynamic_content' => 'text',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'divider',
			),
			'divider_icon_text_tag' => $this->disq_add_select_box_field(
				esc_html__( 'Icon Text Tag', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a tag to display with your icon text.', 'squad-modules-for-divi' ),
					'options'          => array(
						'h1'   => esc_html__( 'H1 tag', 'squad-modules-for-divi' ),
						'h2'   => esc_html__( 'H2 tag', 'squad-modules-for-divi' ),
						'h3'   => esc_html__( 'H3 tag', 'squad-modules-for-divi' ),
						'h4'   => esc_html__( 'H4 tag', 'squad-modules-for-divi' ),
						'h5'   => esc_html__( 'H5 tag', 'squad-modules-for-divi' ),
						'h6'   => esc_html__( 'H6 tag', 'squad-modules-for-divi' ),
						'p'    => esc_html__( 'P tag', 'squad-modules-for-divi' ),
						'span' => esc_html__( 'SPAN tag', 'squad-modules-for-divi' ),
						'div'  => esc_html__( 'DIV tag', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'h2',
					'depends_show_if'  => 'text',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'divider',
				)
			),
			'multiple_divider'      => $this->disq_add_yes_no_field(
				esc_html__( 'Use Multiple Line', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This settings turns on and off the multiple divider line.', 'squad-modules-for-divi' ),
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'multiple_divider_no',
						'multiple_divider_gap',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'divider',
				)
			),
			'multiple_divider_no'   => $this->disq_add_range_fields(
				esc_html__( 'Amount Of Line', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This option is only available if Yes is selected for Loop. Enter the number of times you wish to have the animation loop before stopping.', 'squad-modules-for-divi' ),
					'range_settings'   => array(
						'min_limit' => '2',
						'min'       => '2',
						'max_limit' => '10',
						'max'       => '10',
						'step'      => '1',
					),
					'validate_unit'    => false,
					'unitless'         => true,
					'default'          => '2',
					'default_on_front' => '2',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'divider',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'multiple_divider_gap'  => $this->disq_add_range_fields(
				esc_html__( 'Gap Between Multiple Line', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose gap between multiple line.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'default'         => '10px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'divider',
				)
			),
			'divider_color'         => $this->disq_add_color_field(
				esc_html__( 'Line Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'This will adjust the color of the 1px divider line.', 'squad-modules-for-divi' ),
					'default'         => et_builder_accent_color(),
					'depends_show_if' => 'off',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'divider',
					'mobile_options'  => true,
					'sticky'          => true,
				)
			),
		);
		$divider_custom_colors      = array(
			'use_divider_custom_color' => $this->disq_add_yes_no_field(
				esc_html__( 'Customize Divider Side Color', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This settings turns on and off the divider custom color', 'squad-modules-for-divi' ),
					'options'          => $this->show_divider_options,
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'divider_color',
						'divider_left_color',
						'divider_right_color',
					),
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'divider',
				)
			),
		);
		$divider_custom_color_left  = $this->disq_add_background_field(
			esc_html__( 'Left Side', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'divider_left',
				'context'     => 'divider_left_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'divider',
			)
		);
		$divider_custom_color_right = $this->disq_add_background_field(
			esc_html__( 'Right Side', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'divider_right',
				'context'     => 'divider_right_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'divider',
			)
		);
		$divider_additional_fields  = array(
			'divider_style'    => array(
				'label'           => esc_html__( 'Line Style', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Select the shape of the dividing line used for the divider.', 'squad-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => et_builder_get_border_styles(),
				'depends_show_if' => 'on',
				'default'         => $this->defaults['divider_style'],
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'divider',
				'mobile_options'  => true,
			),
			'divider_position' => array(
				'label'           => esc_html__( 'Line Position', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The dividing line can be placed either above, below or in the center of the module.', 'squad-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'flex-start' => et_builder_i18n( 'Top' ),
					'center'     => esc_html__( 'Vertically Centered', 'squad-modules-for-divi' ),
					'flex-end'   => et_builder_i18n( 'Bottom' ),
				),
				'depends_show_if' => 'on',
				'default'         => $this->defaults['divider_position'],
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'divider',
				'mobile_options'  => true,
			),
			'divider_weight'   => array(
				'label'           => esc_html__( 'Divider Weight', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Increasing the divider weight will increase the thickness of the dividing line.', 'squad-modules-for-divi' ),
				'type'            => 'range',
				'range_settings'  => array(
					'min_limit' => '1',
					'min'       => '1',
					'max_limit' => '100',
					'max'       => '100',
					'step'      => '1',
				),
				'option_category' => 'layout',
				'depends_show_if' => 'on',
				'allowed_units'   => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'    => 'px',
				'default'         => $this->defaults['divider_weight'],
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'divider',
				'mobile_options'  => true,
				'sticky'          => true,
			),
		);
		$divider_custom_sizes       = array(
			'divider_custom_size'       => $this->disq_add_yes_no_field(
				esc_html__( 'Customize Divider Size', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This settings turns on and off the divider custom size.', 'squad-modules-for-divi' ),
					'options'          => $this->show_divider_options,
					'default'          => 'off',
					'default_on_front' => 'off',
					'affects'          => array(
						'divider_custom_size_left',
						'divider_custom_size_right',
					),
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'divider',
				)
			),
			'divider_custom_size_left'  => $this->disq_add_range_fields(
				esc_html__( 'Divider Left Side Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose divider left side width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1000',
						'max'       => '1000',
						'step'      => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'divider',
				)
			),
			'divider_custom_size_right' => $this->disq_add_range_fields(
				esc_html__( 'Divider Right Side Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose divider right side width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1000',
						'max'       => '1000',
						'step'      => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'divider',
				)
			),
		);
		$divider_extra_fields       = array(
			'divider_max_width'     => $this->disq_add_range_fields(
				esc_html__( 'Divider Max Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose divider max width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '1000',
						'max'       => '1000',
						'step'      => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'divider',
				)
			),
			'divider_border_radius' => $this->disq_add_range_fields(
				esc_html__( 'Divider Border Radius', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose divider border  radius.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'divider',
				)
			),
		);

		$divider_fields = array_merge(
			$divider_default_fields,
			$divider_custom_colors,
			$divider_custom_color_left,
			$divider_custom_color_right,
			$divider_additional_fields,
			$divider_custom_sizes,
			$divider_extra_fields
		);

		// Icon & Image fields definitions.
		$icon_image_fields_all = array(
			'divider_icon_type'              => $this->disq_add_select_box_field(
				esc_html__( 'Icon Type', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose an icon type to display with your divider.', 'squad-modules-for-divi' ),
					'options'          => array(
						'icon'   => esc_html__( 'Icon', 'squad-modules-for-divi' ),
						'image'  => et_builder_i18n( 'Image' ),
						'lottie' => esc_html__( 'Lottie', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'icon',
					'default'          => 'icon',
					'depends_show_if'  => 'icon',
					'affects'          => array(
						'divider_icon',
						'divider_image',
						'divider_icon_color',
						'divider_icon_size',
						'divider_image_width',
						'divider_image_height',
						'alt',
						'divider_icon_lottie_src_type',
						'divider_icon_lottie_trigger_method',
						'divider_icon_lottie_speed',
						'divider_icon_lottie_direction',
						'divider_icon_lottie_renderer',
						'divider_icon_lottie_color',
						'divider_icon_lottie_background_color',
						'divider_icon_lottie_width',
						'divider_icon_lottie_height',
						'divider_image_icon_background_color',
						'divider_icon_margin',
						'divider_icon_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'icon_element',
				)
			),
			'divider_icon'                   => array(
				'label'            => esc_html__( 'Choose an icon', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your divider.', 'squad-modules-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '&#x4e;||divi||400',
				'depends_show_if'  => 'icon',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'icon_element',
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
			'divider_image'                  => array(
				'label'              => et_builder_i18n( 'Image' ),
				'description'        => esc_html__( 'Upload an image to display at the top of your divider.', 'squad-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'squad-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'squad-modules-for-divi' ),
				'depends_show_if'    => 'image',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'icon_element',
				'hover'              => 'tabs',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
			),
			'alt'                            => array(
				'label'           => esc_html__( 'Image Alt Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'image',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'icon_element',
				'dynamic_content' => 'text',
			),
			'divider_icon_lottie_src_type'   => $this->disq_add_select_box_field(
				esc_html__( 'Source Type', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a source type to display from your lottie.', 'squad-modules-for-divi' ),
					'options'          => array(
						'remote' => esc_html__( 'External URL', 'squad-modules-for-divi' ),
						'local'  => esc_html__( 'Upload', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'remote',
					'depends_show_if'  => 'lottie',
					'affects'          => array(
						'divider_icon_lottie_src_upload',
						'divider_icon_lottie_src_remote',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'icon_element',
				)
			),
			'divider_icon_lottie_src_remote' => array(
				'label'           => et_builder_i18n( 'External URL' ),
				'description'     => esc_html__( 'The title of your divider will appear in bold below your divider image.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'remote',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'icon_element',
				'dynamic_content' => 'url',
			),
			'divider_icon_lottie_src_upload' => array(
				'label'              => esc_html__( 'Upload a Lottie json', 'squad-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'description'        => esc_html__( 'A json file is chosen for lottie.', 'squad-modules-for-divi' ),
				'upload_button_text' => esc_attr__( 'Upload a lottie json file', 'squad-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose a lottie json file', 'squad-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As a lottie json', 'squad-modules-for-divi' ),
				'data_type'          => 'json',
				'depends_show_if'    => 'local',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'icon_element',
			),
		);

		$icon_text_clip = $this->disq_text_clip_fields(
			array(
				'base_attr_name'  => 'divider_icon_text',
				'toggle_slug'     => 'divider_element',
				'tab_slug'        => 'advanced',
				'depends_show_if' => 'text',
			)
		);

		// Divider_element fields definitions.
		$divider_element_fields = array(
			'divider_element_gap'       => $this->disq_add_range_fields(
				esc_html__( 'Gap Between Element and Divider', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose gap between element and divider.', 'squad-modules-for-divi' ),
					'range_settings'      => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'             => '10px',
					'default_unit'        => 'px',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'divider_element',
					'mobile_options'      => true,
				),
				array( 'use_hover' => false )
			),
			'divider_element_placement' => $this->disq_add_placement_field(
				esc_html__( 'Element Placement', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose where to place the element.', 'squad-modules-for-divi' ),
					'options'             => array(
						'center' => et_builder_i18n( 'Default' ),
						'left'   => et_builder_i18n( 'Left' ),
						'right'  => et_builder_i18n( 'Right' ),
					),
					'default_on_front'    => 'center',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'divider_element',
				)
			),
			'divider_element_margin'    => $this->disq_add_margin_padding_field(
				esc_html__( 'Element Margin', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom margin size for the element.', 'squad-modules-for-divi' ),
					'type'                => 'custom_margin',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'divider_element',
				)
			),
			'divider_element_padding'   => $this->disq_add_margin_padding_field(
				esc_html__( 'Element Padding', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom padding size for the element.', 'squad-modules-for-divi' ),
					'type'                => 'custom_padding',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'divider_element',
				)
			),
		);

		// Icon & Image associate fields definitions.
		$icon_image_associated_fields_all = array(
			'divider_icon_color'                  => $this->disq_add_color_field(
				esc_html__( 'Icon Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'squad-modules-for-divi' ),
					'default'         => et_builder_accent_color(),
					'depends_show_if' => 'icon',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_element',
				)
			),
			'divider_image_icon_background_color' => $this->disq_add_color_field(
				esc_html__( 'Icon Background Color', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define a custom background color.', 'squad-modules-for-divi' ),
					'depends_show_if_not' => array( 'none', 'image', 'lottie' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon_element',
				)
			),
			'divider_icon_size'                   => $this->disq_add_range_fields(
				esc_html__( 'Icon Size', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose icon size.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '40px',
					'default_unit'    => 'px',
					'depends_show_if' => 'icon',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_element',
				)
			),
			'divider_image_width'                 => $this->disq_add_range_fields(
				esc_html__( 'Image Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose image width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '40px',
					'default_unit'    => 'px',
					'depends_show_if' => 'image',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_element',
				)
			),
			'divider_image_height'                => $this->disq_add_range_fields(
				esc_html__( 'Image Height', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose image height.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '40px',
					'default_unit'    => 'px',
					'depends_show_if' => 'image',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'icon_element',
				)
			),
			'divider_icon_margin'                 => $this->disq_add_margin_padding_field(
				esc_html__( 'Icon Margin', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__(
						'Here you can define a custom margin size for the icon.',
						'squad-modules-for-divi'
					),
					'type'                => 'custom_margin',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon_element',
				)
			),
			'divider_icon_padding'                => $this->disq_add_margin_padding_field(
				esc_html__( 'Icon Padding', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__(
						'Here you can define a custom padding size for the icon.',
						'squad-modules-for-divi'
					),
					'type'                => 'custom_padding',
					'depends_show_if_not' => array( 'none' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon_element',
				)
			),
		);

		// Lottie's animation fields definitions.
		$lottie_animation_fields = array(
			'divider_icon_lottie_trigger_method'  => $this->disq_add_select_box_field(
				esc_html__( 'Animation Interaction', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose interactivity with your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'hover'        => esc_html__( 'Play on hover/mouse over', 'squad-modules-for-divi' ),
						'click'        => esc_html__( 'Play on click', 'squad-modules-for-divi' ),
						'scroll'       => esc_html__( 'Play on scroll', 'squad-modules-for-divi' ),
						'play-on-show' => esc_html__( 'Play when container is visible', 'squad-modules-for-divi' ),
						'freeze-click' => esc_html__( 'Freeze on click', 'squad-modules-for-divi' ),
						'none'         => esc_html__( 'None', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'freeze-click',
					'depends_show_if'  => 'lottie',
					'affects'          => array(
						'divider_icon_lottie_mouseout_action',
						'divider_icon_lottie_click_action',
						'divider_icon_lottie_scroll',
						'divider_icon_lottie_play_on_hover',
						'divider_icon_lottie_loop',
						'divider_icon_lottie_delay',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_mouseout_action' => $this->disq_add_select_box_field(
				esc_html__( 'On Mouseout Action', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose an action when mouse out with your Lottie animation.', 'squad-modules-for-divi' ),
					'type'             => 'select',
					'option_category'  => 'layout',
					'options'          => array(
						'no_action' => esc_html__( 'No action', 'squad-modules-for-divi' ),
						'reverse'   => esc_html__( 'Reverse', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'no_action',
					'depends_show_if'  => 'hover',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_click_action'    => $this->disq_add_select_box_field(
				esc_html__( 'On Click Action', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose an action when click with your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'no_action' => esc_html__( 'No action', 'squad-modules-for-divi' ),
						'lock'      => esc_html__( 'Lock animation', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'no_action',
					'depends_show_if'  => 'hover',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_scroll'          => $this->disq_add_select_box_field(
				esc_html__( 'Relative To', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose a relation when scroll event with your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'page' => esc_html__( 'Entire page', 'squad-modules-for-divi' ),
						'row'  => esc_html__( 'Within this section/row', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'row',
					'depends_show_if'  => 'scroll',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_play_on_hover'   => $this->disq_add_yes_no_field(
				esc_html__( 'Play On Hover', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not your Lottie will animate on hover.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'freeze-click',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_loop'            => $this->disq_add_yes_no_field(
				esc_html__( 'Loop', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose whether or not your Lottie will animate in loop.', 'squad-modules-for-divi' ),
					'default_on_front'    => 'off',
					'depends_show_if_not' => array( 'scroll' ),
					'affects'             => array(
						'divider_icon_lottie_loop_no_times',
					),
					'tab_slug'            => 'general',
					'toggle_slug'         => 'lottie_element',
				)
			),
			'divider_icon_lottie_loop_no_times'   => $this->disq_add_range_fields(
				esc_html__( 'Amount Of Loops', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This option is only available if Yes is selected for Loop. Enter the number of times you wish to have the animation loop before stopping.', 'squad-modules-for-divi' ),
					'range_settings'   => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '10',
						'max'       => '10',
						'step'      => '1',
					),
					'validate_unit'    => false,
					'unitless'         => true,
					'default_on_front' => '0',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'divider_icon_lottie_delay'           => $this->disq_add_range_fields(
				esc_html__( 'Delay', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Delay the lottie animation (in ms).', 'squad-modules-for-divi' ),
					'validate_unit'       => false,
					'unitless'            => true,
					'range_settings'      => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '8000',
						'max'       => '8000',
						'step'      => '1',
					),
					'default_on_front'    => '0',
					'depends_show_if_not' => array( 'scroll' ),
					'tab_slug'            => 'general',
					'toggle_slug'         => 'lottie_element',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'divider_icon_lottie_speed'           => $this->disq_add_range_fields(
				esc_html__( 'Animation Speed', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'The speed of the animation.', 'squad-modules-for-divi' ),
					'validate_unit'    => false,
					'unitless'         => true,
					'range_settings'   => array(
						'min_limit' => '0.1',
						'min'       => '0.1',
						'max_limit' => '2.5',
						'max'       => '2.5',
						'step'      => '0.1',
					),
					'default'          => '1',
					'default_on_front' => '1',
					'depends_show_if'  => 'lottie',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'divider_icon_lottie_mode'            => $this->disq_add_select_box_field(
				esc_html__( 'Play Mode', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose play mode for your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'normal' => esc_html__( 'Normal', 'squad-modules-for-divi' ),
						'bounce' => esc_html__( 'Reverse on complete', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'normal',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_direction'       => $this->disq_add_select_box_field(
				esc_html__( 'Direction', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose play direction for your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'1'  => esc_html__( 'Normal', 'squad-modules-for-divi' ),
						'-1' => esc_html__( 'Reverse', 'squad-modules-for-divi' ),
					),
					'default_on_front' => '1',
					'depends_show_if'  => 'lottie',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
			'divider_icon_lottie_renderer'        => $this->disq_add_select_box_field(
				esc_html__( 'Render', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose renderer for your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'svg'    => esc_html__( 'SVG', 'squad-modules-for-divi' ),
						'canvas' => esc_html__( 'Canvas', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'svg',
					'depends_show_if'  => 'lottie',
					'affects'          => array(
						'divider_icon_lottie_mode',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_element',
				)
			),
		);

		// Lottie associate fields definitions.
		$lottie_associated_fields = array(
			'divider_icon_lottie_background_color' => $this->disq_add_color_field(
				esc_html__( 'Lottie Background Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__(
						'Here you can define a custom background color for lottie image.',
						'squad-modules-for-divi'
					),
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_element',
				)
			),
			'divider_icon_lottie_color'            => $this->disq_add_color_field(
				esc_html__( 'Lottie Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__(
						'Here you can define a custom color for lottie image.',
						'squad-modules-for-divi'
					),
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_element',
				)
			),
			'divider_icon_lottie_width'            => $this->disq_add_range_fields(
				esc_html__( 'Lottie Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose lottie width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default_unit'    => 'px',
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_element',
				),
				array(
					'use_hover' => false,
				)
			),
			'divider_icon_lottie_height'           => $this->disq_add_range_fields(
				esc_html__( 'Lottie Height', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose lottie height.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default_unit'    => 'px',
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_element',
				),
				array(
					'use_hover' => false,
				)
			),
		);

		// wrapper fields definitions.
		$wrapper_background_fields = $this->disq_add_background_field(
			esc_html__( 'Wrapper Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'wrapper_background',
				'context'     => 'wrapper_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'wrapper',
			)
		);
		$wrapper_spacing_fields    = array(
			'wrapper_margin'  => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Margin', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom margin size for the wrapper.', 'squad-modules-for-divi' ),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
			'wrapper_padding' => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define a custom padding size for the wrapper.', 'squad-modules-for-divi' ),
					'type'        => 'custom_padding',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
		);

		return array_merge(
			$wrapper_background_fields,
			$wrapper_spacing_fields,
			$divider_fields,
			$icon_image_fields_all,
			$icon_text_clip,
			$divider_element_fields,
			$icon_image_associated_fields_all,
			$lottie_animation_fields,
			$lottie_associated_fields
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

		// wrapper styles.
		$fields['wrapper_background_color'] = array( 'background' => "$this->main_css_element div .divider-elements" );
		$fields['wrapper_margin']           = array( 'margin' => "$this->main_css_element div .divider-elements" );
		$fields['wrapper_padding']          = array( 'padding' => "$this->main_css_element div .divider-elements" );
		$this->disq_fix_border_transition( $fields, 'wrapper', "$this->main_css_element div .divider-elements" );
		$this->disq_fix_box_shadow_transition( $fields, 'wrapper', "$this->main_css_element div .divider-elements" );

		// divider styles.
		$fields['divider_color']  = array( 'border-top-color' => "$this->main_css_element div .divider-elements .divider-item.divider-element hr" );
		$fields['divider_weight'] = array(
			'border-top-width' => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
			'height'           => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
		);

		// Default styles.
		$fields['background_layout'] = array( 'color' => "$this->main_css_element div .divider-elements .divider-item" );

		return $fields;
	}

	/**
	 * Filter multi view value.
	 *
	 * @param mixed $raw_value                                        Props raw value.
	 * @param array $args                                             {
	 *                                                                Context data.
	 *
	 * @type string $context                                          Context param: content, attrs, visibility, classes.
	 * @type string $name                                             Module options props name.
	 * @type string $mode                                             Current data mode: desktop, hover, tablet, phone.
	 * @type string $attr_key                                         Attribute key for attrs context data. Example: src, class, etc.
	 * @type string $attr_sub_key                                     Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 *                                                                }
	 *
	 * @return mixed
	 * @since 3.27.1
	 *
	 * @see   ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 */
	public function multi_view_filter_value( $raw_value, $args ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';

		$icon_fields = array(
			'divider_icon',
		);
		if ( $raw_value && in_array( $name, $icon_fields, true ) ) {
			return et_pb_get_extended_font_icon_value( $raw_value, true );
		}

		return $raw_value;
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
		if ( 'lottie' === $this->props['divider_icon_type'] ) {
			// Load the lottie library for lottie image in the frontend.
			wp_enqueue_script( 'disq-module-lottie' );
			// Load the module scripts for frontend rendering.
			wp_enqueue_script( 'disq-module-divider' );
		}

		$multi_view = et_pb_multi_view_options( $this );
		$this->generate_additional_styles( $attrs );

		$wrapper_classes = array(
			'divider-elements',
			'et_pb_with_background',
			$this->prop( 'divider_element_placement', 'center' ),
		);

		if ( 'on' === $this->prop( 'use_divider_custom_color', 'off' ) ) {
			$wrapper_classes[] = 'customize';
		}

		return sprintf(
			'<div class="%2$s">%1$s</div>',
			et_core_esc_previously( $this->disq_render_divider( $multi_view, $attrs ) ),
			et_core_esc_previously( implode( ' ', $wrapper_classes ) )
		);
	}

	/**
	 * Renders additional styles for the module output.
	 *
	 * @param array $attrs List of attributes.
	 */
	private function generate_additional_styles( $attrs ) {
		// Fixed: a custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// wrapper background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'wrapper_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .divider-elements",
				'selector_hover'         => "$this->main_css_element div .divider-elements:hover",
				'selector_sticky'        => "$this->main_css_element div .divider-elements",
				'function_name'          => $this->slug,
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_wrapper_background_color_gradient' => 'wrapper_background_use_color_gradient',
					'wrapper_background'                    => 'wrapper_background_color',
				),
			)
		);

		// wrapper margin and padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_margin',
				'selector'       => "$this->main_css_element div .divider-elements",
				'hover_selector' => "$this->main_css_element div .divider-elements:hover",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_padding',
				'selector'       => "$this->main_css_element div .divider-elements",
				'hover_selector' => "$this->main_css_element div .divider-elements:hover",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
	}

	/**
	 * Render divider.
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 * @param array                                     $attrs      List of unprocessed attributes.
	 *
	 * @return null|string
	 */
	private function disq_render_divider( $multi_view, $attrs ) {
		// Fixed: a custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		$divider_classes = array( 'divider-item', 'divider-element' );
		$divider_left    = array_merge( $divider_classes, array( 'left' ) );
		$divider_right   = array_merge( $divider_classes, array( 'right' ) );

		if ( 'none' !== $this->prop( 'divider_type', 'none' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_element_gap',
					'selector'       => "$this->main_css_element div .divider-elements",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			// Divider Element margin with default, responsive, hover.
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'divider_element_margin',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper",
					'css_property'   => 'margin',
					'type'           => 'margin',
					'important'      => true,
				)
			);
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'divider_element_padding',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper",
					'css_property'   => 'padding',
					'type'           => 'padding',
					'important'      => true,
				)
			);
		}

		if ( 'on' === $this->prop( 'multiple_divider', 'off' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'multiple_divider_gap',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
		}

		if ( 'on' === $this->prop( 'use_divider_custom_color', 'off' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_weight',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
					'css_property'   => 'min-height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'divider_left',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .divider-elements .divider-item.divider-element.left hr",
					'selector_hover'         => "$this->main_css_element div .divider-elements:hover .divider-item.divider-element.left hr",
					'selector_sticky'        => "$this->main_css_element div .divider-elements .divider-item.divider-element.left hr",
					'function_name'          => $this->slug,
					'use_background_image'   => false,
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_divider_left_color_gradient' => 'divider_left_use_color_gradient',
						'divider_left'                    => 'divider_left_color',
					),
				)
			);
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'divider_right',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .divider-elements .divider-item.divider-element.right hr",
					'selector_hover'         => "$this->main_css_element div .divider-elements:hover .divider-item.divider-element.right hr",
					'selector_sticky'        => "$this->main_css_element div .divider-elements .divider-item.divider-element.right hr",
					'function_name'          => $this->slug,
					'use_background_image'   => false,
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_divider_right_color_gradient' => 'divider_right_use_color_gradient',
						'divider_right'                    => 'divider_right_color',
					),
				)
			);
		} else {
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_color',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
					'css_property'   => 'border-top-color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_style',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
					'css_property'   => 'border-top-style',
					'render_slug'    => $this->slug,
					'type'           => 'style',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_weight',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
					'css_property'   => 'border-top-width',
					'render_slug'    => $this->slug,
					'type'           => 'input',
					'important'      => true,
				)
			);
		}

		if ( 'on' === $this->prop( 'divider_custom_size', 'off' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_custom_size_left',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element.left",
					'selector_hover' => "$this->main_css_element div .divider-elements:hover .divider-item.divider-element.left",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_custom_size_right',
					'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element.right",
					'selector_hover' => "$this->main_css_element div .divider-elements:hover .divider-item.divider-element.right",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
		}

		$this->generate_styles(
			array(
				'base_attr_name' => 'divider_weight',
				'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
				'css_property'   => 'height',
				'render_slug'    => $this->slug,
				'type'           => 'input',
				'important'      => true,
			)
		);
		$this->generate_styles(
			array(
				'base_attr_name' => 'divider_max_width',
				'selector'       => "$this->main_css_element div .divider-elements",
				'css_property'   => 'max-width',
				'render_slug'    => $this->slug,
				'type'           => 'input',
				'important'      => true,
			)
		);
		$this->generate_styles(
			array(
				'base_attr_name' => 'divider_border_radius',
				'selector'       => "$this->main_css_element div .divider-elements .divider-item.divider-element hr",
				'css_property'   => 'border-radius',
				'render_slug'    => $this->slug,
				'type'           => 'input',
				'important'      => true,
			)
		);


		$no_of_line = 'on' === $this->prop( 'multiple_divider', 'off' ) ? (int) $this->prop( 'multiple_divider_no', '2' ) : 1;
		$hr_tags    = array_fill( 0, $no_of_line, '<hr/>' );

		return sprintf(
			'<span class="%1$s">%4$s</span>%3$s<span class="%2$s">%4$s</span>',
			et_core_esc_previously( implode( ' ', $divider_left ) ),
			et_core_esc_previously( implode( ' ', $divider_right ) ),
			et_core_esc_previously( $this->disq_render_divider_icon( $multi_view ) ),
			et_core_esc_previously( implode( '', $hr_tags ) )
		);
	}

	/**
	 * Render divider icon which on is active
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return string
	 */
	private function disq_render_divider_icon( $multi_view ) {
		if ( 'icon' !== $this->props['divider_type'] ) {
			// Set icon background color.
			if ( ! in_array( $this->props['divider_icon_type'], array( 'image', 'lottie' ), true ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => 'divider_image_icon_background_color',
						'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
						'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element",
						'css_property'   => 'background-color',
						'render_slug'    => $this->slug,
						'type'           => 'color',
						'important'      => true,
					)
				);
			}

			// Icon wrapper margin with default, responsive, hover.
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'divider_icon_margin',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
					'css_property'   => 'margin',
					'type'           => 'margin',
					'important'      => true,
				)
			);
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'divider_icon_padding',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
					'css_property'   => 'padding',
					'type'           => 'padding',
					'important'      => true,
				)
			);

			// Images: Add CSS Filters and Mix Blend Mode rules (if set).
			$this->generate_css_filters( $this->slug, 'child_', "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element" );

			return sprintf(
				'<span class="divider-icon-wrapper"><span class="icon-element">%1$s%2$s%3$s%4$s</span></span>',
				et_core_esc_previously( $this->disq_render_divider_font_icon( $multi_view ) ),
				et_core_esc_previously( $this->disq_render_divider_icon_image( $multi_view ) ),
				et_core_esc_previously( $this->disq_render_divider_icon_text( $multi_view ) ),
				et_core_esc_previously( $this->disq_render_divider_icon_lottie( $multi_view ) )
			);
		}

		return null;
	}

	/**
	 * Render divider icon.
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return null|string
	 */
	private function disq_render_divider_font_icon( $multi_view ) {
		if ( 'icon' === $this->props['divider_type'] && 'icon' === $this->props['divider_icon_type'] ) {
			$icon_classes = array( 'et-pb-icon', 'divider-icon' );

			// Load font Awesome css for frontend.
			Divi::inject_fa_icons( $this->props['divider_icon'] );

			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $this->slug,
					'base_attr_name' => 'divider_icon',
					'important'      => true,
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .et-pb-icon",
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_color',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .et-pb-icon",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element .et-pb-icon",
					'css_property'   => 'color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_size',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .et-pb-icon",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element .et-pb-icon",
					'css_property'   => 'font-size',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_size',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element",
					'css_property'   => 'font-size',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			return $multi_view->render_element(
				array(
					'content'        => '{{divider_icon}}',
					'attrs'          => array( 'class' => implode( ' ', $icon_classes ) ),
					'hover_selector' => "$this->main_css_element div .divider-elements",
				)
			);
		}

		return null;
	}

	/**
	 * Render divider image.
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return null|string
	 */
	private function disq_render_divider_icon_image( $multi_view ) {
		if ( 'icon' === $this->props['divider_type'] && 'image' === $this->props['divider_icon_type'] ) {
			$alt_text      = $this->_esc_attr( 'alt' );
			$title_text    = $this->_esc_attr( 'title_text' );
			$image_classes = array( 'divider-image', 'et_pb_image_wrap' );

			$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'divider_image' );

			if ( ! empty( $image_attachment_class ) ) {
				$image_classes[] = esc_attr( $image_attachment_class );
			}

			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_image_width',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element img",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element img",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'item_image_height',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element img",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element img",
					'css_property'   => 'height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_image_width',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .divider-elements:hover divider-icon-wrapper .icon-element",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			return $multi_view->render_element(
				array(
					'tag'            => 'img',
					'attrs'          => array(
						'src'   => '{{divider_image}}',
						'class' => implode( ' ', $image_classes ),
						'alt'   => $alt_text,
						'title' => $title_text,
					),
					'required'       => 'divider_image',
					'hover_selector' => "$this->main_css_element div .divider-elements",
				)
			);
		}

		return null;
	}

	/**
	 * Render divider icon text.
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return null|string
	 */
	private function disq_render_divider_icon_text( $multi_view ) {
		if ( 'text' === $this->props['divider_type'] ) {
			$icon_text_classes = array( 'divider-icon-text' );

			$this->disq_process_text_clip(
				array(
					'base_attr_name' => 'divider_icon_text',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element",
					'hover'          => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element",
				)
			);

			return $multi_view->render_element(
				array(
					'content' => '{{divider_icon_text}}',
					'attrs'   => array(
						'class'          => implode( ' ', $icon_text_classes ),
						'hover_selector' => "$this->main_css_element div .divider-elements",
					),
				)
			);
		}

		return null;
	}

	/**
	 * Render divider lottie image.
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return null|string
	 */
	private function disq_render_divider_icon_lottie( $multi_view ) {
		if ( 'lottie' === $this->props['divider_icon_type'] ) {
			$lottie_image_classes = array( 'disq-lottie-player', 'lottie-player-container' );

			$lottie_type     = ! empty( $this->props['divider_icon_lottie_src_type'] ) ? $this->props['divider_icon_lottie_src_type'] : '';
			$lottie_src_prop = 'local' === $lottie_type ? '{{divider_icon_lottie_src_upload}}' : '{{divider_icon_lottie_src_remote}}';

			// Set background color for Icon.
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_lottie_color',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .disq-lottie-player svg path",
					'selector_hover' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element .disq-lottie-player",
					'css_property'   => 'fill',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			// Set background color for Icon.
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_lottie_background_color',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .disq-lottie-player",
					'selector_hover' => "$this->main_css_element div .divider-elements:hover .divider-icon-wrapper .icon-element .disq-lottie-player",
					'css_property'   => 'background-color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			// Set width for Image.
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_lottie_width',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .disq-lottie-player",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			// Set height for Image.
			$this->generate_styles(
				array(
					'base_attr_name' => 'divider_icon_lottie_height',
					'selector'       => "$this->main_css_element div .divider-elements .divider-icon-wrapper .icon-element .disq-lottie-player",
					'css_property'   => 'height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			$module_references = array(
				'lottie_trigger_method'  => ! empty( $this->props['divider_icon_lottie_trigger_method'] ) ? $this->props['divider_icon_lottie_trigger_method'] : '',
				'lottie_mouseout_action' => ! empty( $this->props['divider_icon_lottie_mouseout_action'] ) ? $this->props['divider_icon_lottie_mouseout_action'] : '',
				'lottie_click_action'    => ! empty( $this->props['divider_icon_lottie_click_action'] ) ? $this->props['divider_icon_lottie_click_action'] : '',
				'lottie_scroll'          => ! empty( $this->props['divider_icon_lottie_scroll'] ) ? $this->props['divider_icon_lottie_scroll'] : '',
				'lottie_play_on_hover'   => ! empty( $this->props['divider_icon_lottie_play_on_hover'] ) ? $this->props['divider_icon_lottie_play_on_hover'] : '',
				'lottie_loop'            => ! empty( $this->props['divider_icon_lottie_loop'] ) ? $this->props['divider_icon_lottie_loop'] : '',
				'lottie_loop_no_times'   => ! empty( $this->props['divider_icon_lottie_loop_no_times'] ) ? $this->props['divider_icon_lottie_loop_no_times'] : '',
				'lottie_delay'           => ! empty( $this->props['divider_icon_lottie_delay'] ) ? $this->props['divider_icon_lottie_delay'] : '',
				'lottie_speed'           => ! empty( $this->props['divider_icon_lottie_speed'] ) ? $this->props['divider_icon_lottie_speed'] : '',
				'lottie_mode'            => ! empty( $this->props['divider_icon_lottie_mode'] ) ? $this->props['divider_icon_lottie_mode'] : '',
				'lottie_direction'       => ! empty( $this->props['divider_icon_lottie_direction'] ) ? $this->props['divider_icon_lottie_direction'] : '',
				'lottie_renderer'        => ! empty( $this->props['divider_icon_lottie_renderer'] ) ? $this->props['divider_icon_lottie_renderer'] : '',
			);

			return $multi_view->render_element(
				array(
					'tag'            => 'span',
					'attrs'          => array(
						'class'        => implode( ' ', $lottie_image_classes ),
						'data-src'     => $lottie_src_prop,
						'data-options' => wp_json_encode(
							array(
								'fieldPrefix'     => '',
								'moduleReference' => $module_references,
							)
						),
					),
					'hover_selector' => "$this->main_css_element div .divider-elements",
				)
			);
		}

		return null;
	}
}

new Divider();
