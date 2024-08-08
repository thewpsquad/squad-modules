<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Dual-Button Module Class which extend the Divi Builder Module Class.
 *
 * This class provides two buttons adding functionalities in the visual builder.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\DualButton;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;
use DiviSquad\Utils\Divi;
use DiviSquad\Utils\Helper;
use ET_Builder_Module_Helper_MultiViewOptions;
use function esc_html__;
use function esc_attr__;
use function et_builder_i18n;
use function et_core_esc_previously;
use function et_pb_multi_view_options;
use function et_pb_background_options;
use function et_pb_media_options;
use function et_pb_get_extended_font_icon_value;

/**
 * Dual-Button Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class DualButton extends DISQ_Builder_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Dual Button', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Dual Buttons', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/dual-button.svg' );

		$this->slug       = 'disq_dual_button';
		$this->vb_support = 'on';

		$this->child_title_var          = 'admin_label';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'left_button_element'  => esc_html__( 'Left Button', 'squad-modules-for-divi' ),
					'right_button_element' => esc_html__( 'Right Button', 'squad-modules-for-divi' ),
					'separator_element'    => esc_html__( 'Separator', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'wrapper'              => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
					'left_button_element'  => esc_html__( 'Left Button', 'squad-modules-for-divi' ),
					'left_button_text'     => esc_html__( 'Left Button Text', 'squad-modules-for-divi' ),
					'right_button_element' => esc_html__( 'Right Button', 'squad-modules-for-divi' ),
					'right_button_text'    => esc_html__( 'Right Button Text', 'squad-modules-for-divi' ),
					'separator_element'    => esc_html__( 'Separator', 'squad-modules-for-divi' ),
					'separator_text'       => esc_html__( 'Separator Text', 'squad-modules-for-divi' ),
				),
			),
		);

		$default_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'left_button_text'  => $this->disq_add_font_field(
					esc_html__( 'Left Button', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '18px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .elements .disq-button.left_button",
							'hover' => "$this->main_css_element div .elements .disq-button.left_button:hover",
						),
					)
				),
				'right_button_text' => $this->disq_add_font_field(
					esc_html__( 'Right Button', 'squad-modules-for-divi' ),
					array(
						'font_size' => array(
							'default' => '18px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .elements .disq-button.right_button",
							'hover' => "$this->main_css_element div .elements .disq-button.right_button:hover",
						),
					)
				),
				'separator_text'    => $this->disq_add_font_field(
					esc_html__( 'Separator', 'squad-modules-for-divi' ),
					array(
						'font_size'       => array(
							'default' => '18px',
						),
						'text_shadow'     => array(
							'show_if' => array(
								'separator_icon__enable' => 'off',
							),
						),
						'hide_text_align' => true,
						'css'             => array(
							'main'  => "$this->main_css_element div .elements .disq-separator .separator-text",
							'hover' => "$this->main_css_element div .elements .disq-separator:hover .separator-text",
						),
						'depends_show_if' => 'off',
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
				'default'              => $default_css_selectors,
				'wrapper'              => array(
					'label_prefix' => et_builder_i18n( 'Wrapper' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .elements",
							'border_radii_hover'  => "$this->main_css_element div .elements:hover",
							'border_styles'       => "$this->main_css_element div .elements",
							'border_styles_hover' => "$this->main_css_element div .elements:hover",
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'wrapper',
				),
				'left_button_element'  => array(
					'label_prefix' => esc_html__( 'Button', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .elements .disq-button.left_button",
							'border_radii_hover'  => "$this->main_css_element div .elements .disq-button.left_button:hover",
							'border_styles'       => "$this->main_css_element div .elements .disq-button.left_button",
							'border_styles_hover' => "$this->main_css_element div .elements .disq-button.left_button:hover",
						),
					),
					'defaults'     => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '2px|2px|2px|2px',
							'color' => et_builder_accent_color(),
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'left_button_element',
				),
				'right_button_element' => array(
					'label_prefix' => esc_html__( 'Button', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .elements .disq-button.right_button",
							'border_radii_hover'  => "$this->main_css_element div .elements .disq-button.right_button:hover",
							'border_styles'       => "$this->main_css_element div .elements .disq-button.right_button",
							'border_styles_hover' => "$this->main_css_element div .elements .disq-button.right_button:hover",
						),
					),
					'defaults'     => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '2px|2px|2px|2px',
							'color' => et_builder_accent_color(),
							'style' => 'solid',
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'right_button_element',
				),
				'separator_element'    => array(
					'label_prefix' => esc_html__( 'Separator', 'squad-modules-for-divi' ),
					'css'          => array(
						'main' => array(
							'border_radii'        => "$this->main_css_element div .elements .disq-separator",
							'border_radii_hover'  => "$this->main_css_element div .elements .disq-separator:hover",
							'border_styles'       => "$this->main_css_element div .elements .disq-separator",
							'border_styles_hover' => "$this->main_css_element div .elements .disq-separator:hover",
						),
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'separator_element',
				),
			),
			'box_shadow'     => array(
				'default'              => $default_css_selectors,
				'wrapper'              => array(
					'label'             => esc_html__( 'Wrapper Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .elements",
						'hover' => "$this->main_css_element div .elements:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'wrapper',
				),
				'left_button_element'  => array(
					'label'             => esc_html__( 'Button Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .elements .disq-button.left_button",
						'hover' => "$this->main_css_element div .elements .disq-button.left_button:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'left_button_element',
				),
				'right_button_element' => array(
					'label'             => esc_html__( 'Button Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .elements .disq-button.right_button",
						'hover' => "$this->main_css_element div .elements .disq-button.right_button:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'right_button_element',
				),
				'separator_element'    => array(
					'label'             => esc_html__( 'Separator Box Shadow', 'squad-modules-for-divi' ),
					'option_category'   => 'layout',
					'css'               => array(
						'main'  => "$this->main_css_element div .elements .disq-separator",
						'hover' => "$this->main_css_element div .elements .disq-separator:hover",
					),
					'default_on_fronts' => array(
						'color'    => 'rgba(0,0,0,0.3)',
						'position' => 'outer',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'separator_element',
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
			'filters'        => false,
			'text'           => false,
			'button'         => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'left_button'       => array(
				'label'    => esc_html__( 'Left Button', 'squad-modules-for-divi' ),
				'selector' => 'div .elements .disq-button.left_button',
			),
			'left_button_icon'  => array(
				'label'    => esc_html__( 'Left Button Icon', 'squad-modules-for-divi' ),
				'selector' => 'div .elements .disq-button.left_button .icon-element',
			),
			'right_button'      => array(
				'label'    => esc_html__( 'Right Button', 'squad-modules-for-divi' ),
				'selector' => 'div .elements .disq-button.right_button',
			),
			'right_button_icon' => array(
				'label'    => esc_html__( 'Right Button Icon', 'squad-modules-for-divi' ),
				'selector' => 'div .elements .disq-button.right_button .icon-element',
			),
			'button_separator'  => array(
				'label'    => esc_html__( 'Separator', 'squad-modules-for-divi' ),
				'selector' => 'div .elements .disq-separator',
			),
			'wrapper'           => array(
				'label'    => esc_html__( 'Wrapper', 'squad-modules-for-divi' ),
				'selector' => 'div .elements',
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
		// Button fields definitions.
		$left_button  = $this->disq_get_button_fields(
			array(
				'base_attr_name' => 'left_button',
				'button_icon'    => '&#x4e;||divi||400',
				'toggle_slug'    => 'left_button_element',
			)
		);
		$right_button = $this->disq_get_button_fields(
			array(
				'base_attr_name' => 'right_button',
				'button_icon'    => '&#x24;||divi||400',
				'toggle_slug'    => 'right_button_element',
			)
		);

		// Separator fields definitions.
		$separator = array(
			'separator_text'         => array(
				'label'           => esc_html__( 'Separator Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The text of your separator will appear in with your button separator.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'separator_element',
				'dynamic_content' => 'text',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'separator_icon__enable' => $this->disq_add_yes_no_field(
				esc_html__( 'Use Separator Icon', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__(
						'By default, Separator to always be displayed not as icon. If you would like use icon for separator, then you can enable this option.',
						'squad-modules-for-divi'
					),
					'default_on_front' => 'off',
					'affects'          => array(
						'separator_text',
						'separator_text_font',
						'separator_text_text_color',
						'separator_text_text_align',
						'separator_text_font_size',
						'separator_text_letter_spacing',
						'separator_text_line_height',
						'separator_icon_type',
						'separator_icon_margin',
						'separator_icon_padding',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'separator_element',
				)
			),
			'separator_icon_type'    => $this->disq_add_select_box_field(
				esc_html__( 'Separator Icon Type', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose an icon type to display with your separator.', 'squad-modules-for-divi' ),
					'options'          => array(
						'icon'  => esc_html__( 'Icon', 'squad-modules-for-divi' ),
						'image' => et_builder_i18n( 'Image' ),
					),
					'default_on_front' => 'icon',
					'depends_show_if'  => 'on',
					'affects'          => array(
						'separator_icon',
						'separator_image',
						'separator_icon_color',
						'separator_icon_size',
						'separator_image_width',
						'separator_image_height',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'separator_element',
				)
			),
			'separator_icon'         => array(
				'label'            => esc_html__( 'Choose an icon', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your separator.', 'squad-modules-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '&#x4e;||divi||400',
				'depends_show_if'  => 'icon',
				'tab_slug'         => 'general',
				'toggle_slug'      => 'separator_element',
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
			'separator_image'        => array(
				'label'              => et_builder_i18n( 'Image' ),
				'description'        => esc_html__( 'Upload an image to display at the top of your separator.', 'squad-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'squad-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'squad-modules-for-divi' ),
				'depends_show_if'    => 'image',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'separator_element',
				'hover'              => 'tabs',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
			),
		);

		$separator_background_fields = $this->disq_add_background_field(
			esc_html__( 'Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'separator_background',
				'context'     => 'separator_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'separator_element',
			)
		);
		$separator_associated_fields = array(
			'separator_icon_color'     => $this->disq_add_color_field(
				esc_html__( 'Icon Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your separator icon.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'icon',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'separator_element',
				)
			),
			'separator_icon_size'      => $this->disq_add_range_fields(
				esc_html__( 'Icon Size', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose separator icon size.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min-limit' => '1',
						'min'       => '1',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'         => '16px',
					'default_unit'    => 'px',
					'depends_show_if' => 'icon',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'separator_element',
				)
			),
			'separator_image_width'    => $this->disq_add_range_fields(
				esc_html__( 'Image Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose image width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'default'         => '16px',
					'default_unit'    => 'px',
					'depends_show_if' => 'image',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'separator_element',
				)
			),
			'separator_image_height'   => $this->disq_add_range_fields(
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
					'default'         => '16px',
					'default_unit'    => 'px',
					'depends_show_if' => 'image',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'separator_element',
				)
			),
			'separator_custom_width'   => $this->disq_add_yes_no_field(
				esc_html__( 'Resize Separator', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'By default, the separator element will be get default width. If you would like resize the separator, then you can enable this option.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'affects'          => array(
						'separator_width',
						'separator_item_alignment',
					),
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'separator_element',
				)
			),
			'separator_width'          => $this->disq_add_range_fields(
				esc_html__( 'Separator Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Adjust the width of the separator.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '1100',
						'max'       => '1100',
						'step'      => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'separator_element',
				)
			),
			'separator_item_alignment' => $this->disq_add_alignment_field(
				esc_html__( 'Alignment', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Align icon to the left, right or center.', 'squad-modules-for-divi' ),
					'default_on_front' => 'left',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'separator_element',
				)
			),
			'separator_icon_margin'    => $this->disq_add_margin_padding_field(
				esc_html__( 'Icon/Image Margin', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__(
						'Here you can define a custom padding size for the icon.',
						'squad-modules-for-divi'
					),
					'type'            => 'custom_margin',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'separator_element',
				)
			),
			'separator_margin'         => $this->disq_add_margin_padding_field(
				esc_html__( 'Separator Margin', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__(
						'Here you can define a custom margin size for the separator.',
						'squad-modules-for-divi'
					),
					'type'        => 'custom_margin',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'separator_element',
				)
			),
			'separator_padding'        => $this->disq_add_margin_padding_field(
				esc_html__( 'Separator Padding', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__(
						'Here you can define a custom padding size for the separator.',
						'squad-modules-for-divi'
					),
					'type'        => 'custom_padding',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'separator_element',
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
		$wrapper_fields            = array(
			'wrapper_horizontal_alignment' => $this->disq_add_alignment_field(
				esc_html__( 'Wrapper Alignment', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Align icon to the left, right or center.', 'squad-modules-for-divi' ),
					'default_on_front' => 'left',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'wrapper',
				)
			),
			'wrapper_elements_layout'      => $this->disq_add_select_box_field(
				esc_html__( 'Button Position', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose an icon type to display with your separator.', 'squad-modules-for-divi' ),
					'options'          => array(
						'row'    => esc_html__( 'Horizontal', 'squad-modules-for-divi' ),
						'column' => esc_html__( 'Vertical', 'squad-modules-for-divi' ),
					),
					'affects'          => array(
						'buttons_horizontal_alignment',
					),
					'default_on_front' => 'row',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'wrapper',
					'mobile_options'   => true,
				)
			),
			'buttons_horizontal_alignment' => $this->disq_add_alignment_field(
				esc_html__( 'Buttons Alignment', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Align icon to the left, right or center.', 'squad-modules-for-divi' ),
					'default_on_front' => 'left',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => 'wrapper',
					'depends_show_if'  => 'column',
				)
			),
			'wrapper_elements_gap'         => $this->disq_add_range_fields(
				esc_html__( 'Gap Between Elements', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__(
						'Adjust the width of the content within the blurb content.',
						'squad-modules-for-divi'
					),
					'range_settings' => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'allowed_units'  => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'    => true,
					'default_unit'   => 'px',
					'default'        => '10px',
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'wrapper',
				)
			),
			'wrapper_margin'               => $this->disq_add_margin_padding_field(
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
			'wrapper_padding'              => $this->disq_add_margin_padding_field(
				esc_html__( 'Wrapper Padding', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__(
						'Here you can define a custom padding size for the wrapper.',
						'squad-modules-for-divi'
					),
					'type'        => 'custom_padding',
					'default'     => '10px|15px|10px|15px|false|false',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'wrapper',
				)
			),
		);

		// URL fields definitions.
		$url_fields = array(
			'left_button_url'             => array(
				'label'           => esc_html__( 'Left Button Link URL', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'If you would like to make your button link, input your destination URL here.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'left_button_url_new_window'  => array(
				'label'            => esc_html__( 'Left Button Link Target', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'squad-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'squad-modules-for-divi' ),
					'on'  => esc_html__( 'In The New Tab', 'squad-modules-for-divi' ),
				),
				'toggle_slug'      => 'link_options',
				'default_on_front' => 'off',
			),
			'right_button_url'            => array(
				'label'           => esc_html__( 'Right Button Link URL', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'If you would like to make your button link, input your destination URL here.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'right_button_url_new_window' => array(
				'label'            => esc_html__( 'Right Button Link Target', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'squad-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'squad-modules-for-divi' ),
					'on'  => esc_html__( 'In The New Tab', 'squad-modules-for-divi' ),
				),
				'toggle_slug'      => 'link_options',
				'default_on_front' => 'off',
			),
		);

		return array_merge(
			$left_button,
			$right_button,
			$separator,
			$separator_background_fields,
			$separator_associated_fields,
			$wrapper_background_fields,
			$wrapper_fields,
			$url_fields
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
		$fields['wrapper_background_color'] = array( 'background' => "$this->main_css_element div .elements" );
		$fields['wrapper_elements_gap']     = array( 'gap' => "$this->main_css_element div .elements" );
		$fields['wrapper_margin']           = array( 'margin' => "$this->main_css_element div .elements" );
		$fields['wrapper_padding']          = array( 'padding' => "$this->main_css_element div .elements" );
		$this->disq_fix_border_transition( $fields, 'wrapper', "$this->main_css_element div .elements" );
		$this->disq_fix_box_shadow_transition( $fields, 'wrapper', "$this->main_css_element div .elements" );

		// left button styles.
		$fields['left_button_background_color'] = array( 'background' => "$this->main_css_element div .elements .disq-button.left_button" );
		$fields['left_button_width']            = array( 'width' => "$this->main_css_element div .elements .disq-button.left_button" );
		$fields['left_button_margin']           = array( 'margin' => "$this->main_css_element div .elements .disq-button.left_button" );
		$fields['left_button_padding']          = array( 'padding' => "$this->main_css_element div .elements .disq-button.left_button" );
		$this->disq_fix_fonts_transition( $fields, 'left_button_text', "$this->main_css_element div .elements .disq-button.left_button" );
		$this->disq_fix_border_transition( $fields, 'left_button_element', "$this->main_css_element div .elements .disq-button.left_button" );
		$this->disq_fix_box_shadow_transition( $fields, 'left_button_element', "$this->main_css_element div .elements .disq-button.left_button" );

		// left button icon styles.
		$fields['left_button_icon_color']   = array( 'color' => "$this->main_css_element div .elements .disq-button.left_button .disq-icon-wrapper .icon-element .et-pb-icon" );
		$fields['left_button_icon_size']    = array( 'font-size' => "$this->main_css_element div .elements .disq-button.left_button .disq-icon-wrapper .icon-element.et-pb-icon" );
		$fields['left_button_image_width']  = array( 'width' => "$this->main_css_element div .elements .disq-button.left_button .disq-icon-wrapper .icon-element img" );
		$fields['left_button_image_height'] = array( 'height' => "$this->main_css_element div .elements .disq-button.left_button .disq-icon-wrapper .icon-element img" );
		$fields['left_button_icon_margin']  = array( 'margin' => "$this->main_css_element div .elements .disq-button.left_button .disq-icon-wrapper .icon-element" );

		// right button styles.
		$fields['right_button_background_color'] = array( 'background' => "$this->main_css_element div .elements .disq-button.right_button" );
		$fields['right_button_width']            = array( 'width' => "$this->main_css_element div .elements .disq-button.right_button" );
		$fields['right_button_margin']           = array( 'margin' => "$this->main_css_element div .elements .disq-button.right_button" );
		$fields['right_button_padding']          = array( 'padding' => "$this->main_css_element div .elements .disq-button.right_button" );
		$this->disq_fix_fonts_transition( $fields, 'right_button_text', "$this->main_css_element div .elements .disq-button.right_button" );
		$this->disq_fix_border_transition( $fields, 'right_button_element', "$this->main_css_element div .elements .disq-button.right_button" );
		$this->disq_fix_box_shadow_transition( $fields, 'right_button_element', "$this->main_css_element div .elements .disq-button.right_button" );

		// right button icon styles.
		$fields['right_button_icon_color']   = array( 'color' => "$this->main_css_element div .elements .disq-button.right_button .disq-icon-wrapper .icon-element .et-pb-icon" );
		$fields['right_button_icon_size']    = array( 'font-size' => "$this->main_css_element div .elements .disq-button.right_button .disq-icon-wrapper .icon-element .et-pb-icon" );
		$fields['right_button_image_width']  = array( 'width' => "$this->main_css_element div .elements .disq-button.right_button .disq-icon-wrapper .icon-element img" );
		$fields['right_button_image_height'] = array( 'height' => "$this->main_css_element div .elements .disq-button.right_button .disq-icon-wrapper .icon-element img" );
		$fields['right_button_icon_margin']  = array( 'margin' => "$this->main_css_element div .elements .disq-button.right_button .disq-icon-wrapper .icon-element" );

		// separator styles.
		$fields['separator_background_color'] = array( 'background' => "$this->main_css_element div .elements .disq-separator" );
		$fields['separator_width']            = array( 'width' => "$this->main_css_element div .elements .disq-separator" );
		$fields['separator_margin']           = array( 'margin' => "$this->main_css_element div .elements .disq-separator" );
		$fields['separator_padding']          = array( 'padding' => "$this->main_css_element div .elements .disq-separator" );
		$this->disq_fix_border_transition( $fields, 'separator_element', "$this->main_css_element div .elements .disq-separator" );
		$this->disq_fix_box_shadow_transition( $fields, 'separator_element', "$this->main_css_element div .elements .disq-separator" );

		// separator icon styles.
		$fields['separator_icon_color']   = array( 'color' => "$this->main_css_element div .elements .disq-separator .disq-icon-wrapper .icon-element .et-pb-icon" );
		$fields['separator_icon_size']    = array( 'font-size' => "$this->main_css_element div .elements .disq-separator .disq-icon-wrapper .icon-element .et-pb-icon" );
		$fields['separator_image_width']  = array( 'width' => "$this->main_css_element div .elements .disq-separator .disq-icon-wrapper .icon-element img" );
		$fields['separator_image_height'] = array( 'height' => "$this->main_css_element div .elements .disq-separator .disq-icon-wrapper .icon-element img" );
		$fields['separator_icon_margin']  = array( 'margin' => "$this->main_css_element div .elements .disq-separator .disq-icon-wrapper .icon-element" );

		return $fields;
	}

	/**
	 * Filter multi view value.
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args      Context data.
	 *
	 * @return mixed
	 * @since 3.27.1
	 *
	 * @see   ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 */
	public function multi_view_filter_value( $raw_value, $args ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';

		// process font for dual button.
		$icon_fields = array(
			'left_button_icon',
			'right_button_icon',
			'separator_icon',
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
		$left_button_content  = $this->render_element_text( $attrs, 'left_button' );
		$right_button_content = $this->render_element_text( $attrs, 'right_button' );

		if ( null !== $left_button_content || null !== $right_button_content ) {
			$this->generate_additional_styles( $attrs );
			$separator_element = $this->render_element_separator( $attrs );

			return sprintf(
				'<div class="elements et_pb_with_background">%1$s%3$s%2$s</div>',
				et_core_esc_previously( $left_button_content ),
				et_core_esc_previously( $right_button_content ),
				et_core_esc_previously( $separator_element )
			);
		}

		return null;
	}

	/**
	 * Render element text with icon
	 *
	 * @param array  $attrs   List of unprocessed attributes.
	 * @param string $element Dynamic element key.
	 *
	 * @return null|string
	 */
	private function render_element_text( $attrs, $element ) {
		$multi_view = et_pb_multi_view_options( $this );

		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// Initiate the button tag, button url, and url target with default value.
		$element_url        = isset( $this->props[ "{$element}_url" ] ) ? $this->props[ "{$element}_url" ] : '';
		$element_url_target = isset( $this->props[ "{$element}_url_new_window" ] ) ? $this->props[ "{$element}_url_new_window" ] : '';
		$url_target         = 'on' === $element_url_target ? '_blank' : '_self';

		$element_text = $multi_view->render_element(
			array(
				'content'        => "{{{$element}_text}}",
				'attrs'          => array( 'class' => 'button-text' ),
				'hover_selector' => "$this->main_css_element div .elements .disq-button.$element",
			)
		);

		// Assign variables for icon wrapper.
		$icon_elements      = '';
		$icon_wrapper_class = array( 'disq-icon-wrapper' );
		$button_classes     = $this->disq_add_background_class(
			array(
				'background_field' => "{$element}_background_color",
				'classes'          => array(
					'disq-button',
					'button-element',
					$element,
				),
			)
		);

		if ( 'on' === $this->prop( "{$element}_hover_animation__enable", 'off' ) ) {
			$button_classes[] = $this->prop( "{$element}_hover_animation_type", 'fill' );
		}

		// Render text output when it is not empty.
		if ( ! empty( $element_text ) ) {
			// button background with default, responsive, hover.
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => "{$element}_background",
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .elements .disq-button.$element",
					'selector_hover'         => "$this->main_css_element div .elements .disq-button.$element:hover",
					'selector_sticky'        => "$this->main_css_element div .elements .disq-button.$element",
					'function_name'          => $this->slug,
					'important'              => ' !important',
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						"use_{$element}_background_color_gradient" => "{$element}_background_use_color_gradient",
						"{$element}_background" => "{$element}_background_color",
					),
				)
			);

			if ( 'on' === $this->prop( "{$element}_custom_width" ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => "{$element}_width",
						'selector'       => "$this->main_css_element div .elements .disq-button.$element",
						'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover",
						'css_property'   => 'width',
						'render_slug'    => $this->slug,
						'type'           => 'input',
						'important'      => true,
					)
				);
				$this->generate_styles(
					array(
						'base_attr_name' => "{$element}_elements_alignment",
						'selector'       => "$this->main_css_element div .elements .disq-button.$element",
						'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover",
						'css_property'   => 'justify-content',
						'render_slug'    => $this->slug,
						'type'           => 'align',
						'important'      => true,
					)
				);
			}

			$this->generate_styles(
				array(
					'base_attr_name' => "{$element}_icon_placement",
					'selector'       => "$this->main_css_element div .elements .disq-button.$element",
					'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover",
					'css_property'   => 'flex-direction',
					'render_slug'    => $this->slug,
					'type'           => 'align',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => "{$element}_icon_gap",
					'selector'       => "$this->main_css_element div .elements .disq-button.$element",
					'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'input',
					'important'      => true,
				)
			);

			// button margin with default, responsive, hover.
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => "{$element}_icon_margin",
					'selector'       => "$this->main_css_element div .elements .disq-button.$element .disq-icon-wrapper .icon-element",
					'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover .disq-icon-wrapper .icon-element",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => "{$element}_margin",
					'selector'       => "$this->main_css_element div .elements .disq-button.$element",
					'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => "{$element}_padding",
					'selector'       => "$this->main_css_element div .elements .disq-button.$element",
					'hover_selector' => "$this->main_css_element div .elements .disq-button.$element:hover",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);

			$font_icon_element = $this->render_element_font_icon( $element );
			$image_element     = $this->render_element_icon_image( $element );

			if ( ( 'none' !== $this->props[ "{$element}_icon_type" ] ) && ( ! empty( $font_icon_element ) || ! empty( $image_element ) ) ) {
				if ( ( 'on' === $this->props[ "{$element}_icon_on_hover" ] ) ) {
					$icon_wrapper_class[] = 'show-on-hover';

					$mapping_values = array(
						'inherit'     => '0 0 0 0',
						'column'      => '0 0 -#px 0',
						'row'         => '0 -#px 0 0',
						'row-reverse' => '0 0 0 -#px',
					);

					if ( 'on' === $this->props[ "{$element}_icon_hover_move_icon" ] ) {
						$mapping_values = array(
							'inherit'     => '0 0 0 0',
							'column'      => '#px 0 -#px 0',
							'row'         => '0 -#px 0 #px',
							'row-reverse' => '0 #px 0 -#px',
						);
					}

					// set icon placement for button image with default, hover and responsive.
					$this->process_show_icon_on_hover_styles(
						array(
							'field'          => "{$element}_icon_placement",
							'trigger'        => "{$element}_icon_type",
							'depends_on'     => array(
								'icon'  => "{$element}_icon_size",
								'image' => "{$element}_image_width",
							),
							'selector'       => "$this->main_css_element div .elements .disq-button.$element .disq-icon-wrapper.show-on-hover",
							'hover'          => "$this->main_css_element div .elements .disq-button.$element:hover .disq-icon-wrapper.show-on-hover",
							'css_property'   => 'margin',
							'type'           => 'margin',
							'mapping_values' => $mapping_values,
							'defaults'       => array(
								'icon'  => '16px',
								'image' => '16px',
								'field' => 'row',
							),
						)
					);
				}

				$icon_elements = sprintf(
					'<span class="%1$s"><span class="icon-element">%2$s%3$s</span></span>',
					implode( ' ', $icon_wrapper_class ),
					et_core_esc_previously( $font_icon_element ),
					et_core_esc_previously( $image_element )
				);
			}

			return sprintf(
				'<a class="%5$s" href="%3$s" target="%4$s">%1$s%2$s</a>',
				et_core_esc_previously( $element_text ),
				et_core_esc_previously( $icon_elements ),
				esc_url_raw( $element_url ),
				esc_attr( $url_target ),
				et_core_esc_previously( implode( ' ', $button_classes ) )
			);
		}

		return null;
	}

	/**
	 * Render element icon
	 *
	 * @param string $element Dynamic element key.
	 *
	 * @return null|string
	 */
	private function render_element_font_icon( $element ) {
		$multi_view = et_pb_multi_view_options( $this );

		if ( ! empty( $this->props[ "{$element}_icon_type" ] ) && 'icon' === $this->props[ "{$element}_icon_type" ] ) {
			$icon_classes = array( 'et-pb-icon', "disq-$element-icon" );

			// Load font Awesome css for frontend.
			Divi::inject_fa_icons( $this->props[ "{$element}_icon" ] );

			$element_class = 'separator' !== $element ? ".disq-button.$element" : ".disq-$element";

			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $this->slug,
					'base_attr_name' => "{$element}_icon",
					'important'      => true,
					'selector'       => "$this->main_css_element div .elements $element_class .disq-icon-wrapper .icon-element .et-pb-icon",
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => "{$element}_icon_color",
					'selector'       => "$this->main_css_element div .elements $element_class .disq-icon-wrapper .icon-element .et-pb-icon",
					'hover_selector' => "$this->main_css_element div .elements $element_class:hover .disq-icon-wrapper .icon-element .et-pb-icon",
					'css_property'   => 'color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => "{$element}_icon_size",
					'selector'       => "$this->main_css_element div .elements $element_class .disq-icon-wrapper .icon-element .et-pb-icon",
					'hover_selector' => "$this->main_css_element div .elements $element_class:hover .disq-icon-wrapper .icon-element .et-pb-icon",
					'css_property'   => 'font-size',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			return $multi_view->render_element(
				array(
					'content'        => "{{{$element}_icon}}",
					'attrs'          => array(
						'class' => implode( ' ', $icon_classes ),
					),
					'hover_selector' => "$this->main_css_element div .elements $element_class",
				)
			);
		}

		return null;
	}

	/**
	 * Render element image
	 *
	 * @param string $element Dynamic element key.
	 *
	 * @return null|string
	 */
	private function render_element_icon_image( $element ) {
		$multi_view = et_pb_multi_view_options( $this );
		if ( ! empty( $this->props[ "{$element}_icon_type" ] ) && 'image' === $this->props[ "{$element}_icon_type" ] ) {
			$element_class          = 'separator' !== $element ? ".disq-button.$element" : ".disq-$element";
			$image_classes          = array( "disq-$element-image", 'et_pb_image_wrap' );
			$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, "{$element}_image" );

			if ( ! empty( $image_attachment_class ) ) {
				$image_classes[] = esc_attr( $image_attachment_class );
			}

			$this->generate_styles(
				array(
					'base_attr_name' => "{$element}_image_width",
					'selector'       => "$this->main_css_element div .elements $element_class .disq-icon-wrapper .icon-element img",
					'hover_selector' => "$this->main_css_element div .elements $element_class:hover .disq-icon-wrapper .icon-element img",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => "{$element}_image_height",
					'selector'       => "$this->main_css_element div .elements $element_class .disq-icon-wrapper .icon-element img",
					'hover_selector' => "$this->main_css_element div .elements $element_class:hover .disq-icon-wrapper .icon-element img",
					'css_property'   => 'height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			return $multi_view->render_element(
				array(
					'tag'            => 'img',
					'attrs'          => array(
						'src'   => "{{{$element}_image}}",
						'class' => implode( ' ', $image_classes ),
						'alt'   => '',
					),
					'required'       => "{$element}_image",
					'hover_selector' => "$this->main_css_element div .elements $element_class",
				)
			);
		}

		return null;
	}

	/**
	 * Renders additional styles for the module output.
	 *
	 * @param array $attrs List of attributes.
	 */
	private function generate_additional_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// wrapper background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'wrapper_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .elements",
				'selector_hover'         => "$this->main_css_element div .elements:hover",
				'selector_sticky'        => "$this->main_css_element div .elements",
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

		// wrapper horizontal aligns with default, responsive, hover.
		$this->generate_styles(
			array(
				'base_attr_name' => 'wrapper_horizontal_alignment',
				'selector'       => $this->main_css_element,
				'hover_selector' => "$this->main_css_element:hover",
				'css_property'   => 'justify-content',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);

		if ( 'column' === $this->prop( 'wrapper_elements_layout', 'row' ) ) {
			$this->disq_process_additional_styles(
				array(
					'field'          => 'buttons_horizontal_alignment',
					'selector'       => "$this->main_css_element div .elements",
					'hover_selector' => "$this->main_css_element div .elements:hover",
					'css_property'   => 'align-items',
					'type'           => 'align',
					'mappingValues'  => array(
						'left'   => 'flex-start',
						'center' => 'center',
						'right'  => 'flex-end',
					),
				)
			);
		}

		$this->generate_styles(
			array(
				'base_attr_name' => 'wrapper_elements_layout',
				'selector'       => "$this->main_css_element div .elements",
				'hover_selector' => "$this->main_css_element div .elements:hover",
				'css_property'   => 'flex-direction',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);
		$this->generate_styles(
			array(
				'base_attr_name' => 'wrapper_elements_gap',
				'selector'       => "$this->main_css_element div .elements",
				'hover_selector' => "$this->main_css_element div .elements:hover",
				'css_property'   => 'gap',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);
		// wrapper margin with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_margin',
				'selector'       => "$this->main_css_element div .elements",
				'hover_selector' => "$this->main_css_element div .elements:hover",
				'css_property'   => 'margin',
				'type'           => 'margin',
			)
		);
		// wrapper padding with default, responsive, hover.
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'wrapper_padding',
				'selector'       => "$this->main_css_element div .elements",
				'hover_selector' => "$this->main_css_element div .elements:hover",
				'css_property'   => 'padding',
				'type'           => 'padding',
			)
		);
	}

	/**
	 * Render separator text with icon
	 *
	 * @param array $attrs List of unprocessed attributes.
	 *
	 * @return null|string
	 */
	private function render_element_separator( $attrs ) {
		$multi_view     = et_pb_multi_view_options( $this );
		$is_icon_enable = $this->prop( 'separator_icon__enable', 'off' );
		$text_element   = null;
		$icon_elements  = null;

		if ( 'on' === $is_icon_enable ) {
			$icon_type         = $this->prop( 'separator_icon_type', 'none' );
			$font_icon_element = $this->render_element_font_icon( 'separator' );
			$image_element     = $this->render_element_icon_image( 'separator' );

			if ( ( 'none' !== $icon_type ) && ( ! empty( $font_icon_element ) || ! empty( $image_element ) ) ) {
				$icon_elements = sprintf(
					'<span class="disq-icon-wrapper"><span class="icon-element">%1$s%2$s</span></span>',
					et_core_esc_previously( $font_icon_element ),
					et_core_esc_previously( $image_element )
				);
			}
		} else {
			$separator_text = $multi_view->render_element(
				array(
					'content'        => '{{separator_text}}',
					'attrs'          => array(
						'class' => 'separator-text',
					),
					'hover_selector' => "$this->main_css_element div .elements .disq-separator",
				)
			);
			if ( '' !== $separator_text ) {
				$text_element = $separator_text;
			}
		}

		if ( ( 'on' === $is_icon_enable && ! empty( $icon_elements ) ) || ( 'on' !== $is_icon_enable && ! empty( $text_element ) ) ) {
			// Fixed: the custom background doesn't work at frontend.
			$this->props = array_merge( $attrs, $this->props );

			$separator_text_classes = $this->disq_add_background_class(
				array(
					'background_field' => 'separator_background_color',
					'classes'          => array(
						'disq-separator',
						'separator-element',
					),
				)
			);

			// separator background with default, responsive, hover.
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'separator_background',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .elements .disq-separator",
					'selector_hover'         => "$this->main_css_element div .elements .disq-separator:hover",
					'selector_sticky'        => "$this->main_css_element div .elements .disq-separator",
					'function_name'          => $this->slug,
					'important'              => ' !important',
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_separator_background_color_gradient' => 'separator_background_use_color_gradient',
						'separator_background' => 'separator_background_color',
					),
				)
			);

			$this->generate_styles(
				array(
					'base_attr_name' => 'separator_icon_placement',
					'selector'       => "$this->main_css_element div .elements .disq-separator",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover",
					'css_property'   => 'flex-direction',
					'render_slug'    => $this->slug,
					'type'           => 'align',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'separator_icon_gap',
					'selector'       => "$this->main_css_element div .elements .disq-separator",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover",
					'css_property'   => 'gap',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'separator_width',
					'selector'       => "$this->main_css_element div .elements .disq-separator",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'separator_item_alignment',
					'selector'       => "$this->main_css_element div .elements .disq-separator",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover",
					'css_property'   => 'justify-content',
					'render_slug'    => $this->slug,
					'type'           => 'align',
					'important'      => true,
				)
			);

			// button margin with default, responsive, hover.
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'separator_icon_margin',
					'selector'       => "$this->main_css_element div .elements .disq-separator .icon-element",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover .icon-element",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'separator_margin',
					'selector'       => "$this->main_css_element div .elements .disq-separator",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover",
					'css_property'   => 'margin',
					'type'           => 'margin',
				)
			);
			$this->disq_process_margin_padding_styles(
				array(
					'field'          => 'separator_padding',
					'selector'       => "$this->main_css_element div .elements .disq-separator",
					'hover_selector' => "$this->main_css_element div .elements .disq-separator:hover",
					'css_property'   => 'padding',
					'type'           => 'padding',
				)
			);

			return sprintf(
				'<div class="%3$s">%1$s%2$s</div>',
				et_core_esc_previously( $text_element ),
				et_core_esc_previously( $icon_elements ),
				et_core_esc_previously( implode( ' ', $separator_text_classes ) )
			);
		}

		return null;
	}
}

new DualButton();
