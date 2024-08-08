<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Base\BuilderModule\Traits\Elements;

use ET_Global_Settings;
use function et_builder_accent_color;
use function et_builder_get_border_styles;

trait Divider {

	/**
	 * The default options for divider.
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * The show options for divider.
	 *
	 * @var array
	 */
	protected $show_divider_options = array();

	/**
	 * Get the default data for initiate.
	 *
	 * @return void The shape
	 */
	protected function disq_initiate_the_divider_element() {
		$style_option_name       = sprintf( '%1$s-divider_style', $this->slug );
		$global_divider_style    = ET_Global_Settings::get_value( $style_option_name );
		$position_option_name    = sprintf( '%1$s-divider_position', $this->slug );
		$global_divider_position = ET_Global_Settings::get_value( $position_option_name );
		$weight_option_name      = sprintf( '%1$s-divider_weight', $this->slug );
		$global_divider_weight   = ET_Global_Settings::get_value( $weight_option_name );

		$this->defaults = array(
			'divider_style'    => $global_divider_style && '' !== $global_divider_style ? $global_divider_style : 'solid',
			'divider_position' => $global_divider_position && '' !== $global_divider_position ? $global_divider_position : 'bottom',
			'divider_weight'   => $global_divider_weight && '' !== $global_divider_weight ? $global_divider_weight : '2px',
		);

		// Show divider options are modifiable via customizer.
		$this->show_divider_options = array(
			'off' => et_builder_i18n( 'No' ),
			'on'  => et_builder_i18n( 'Yes' ),
		);
	}

	/**
	 * Get the field for divider element
	 *
	 * @param string $toggle_slug The toggle slug for the general and advanced tabs.
	 * @param array  $options The options for divider element fields.
	 *
	 * @return array the field
	 */
	protected function disq_get_divider_element_fields( $toggle_slug = '', $options = array() ) {
		$main_fields_defaults = array(
			'label'            => esc_html__( 'Show Divider', 'squad-modules-for-divi' ),
			'description'      => esc_html__( 'This settings turns on and off the 1px divider line, but does not affect the divider height.', 'squad-modules-for-divi' ),
			'default'          => 'on',
			'default_on_front' => 'on',
			'type'             => 'yes_no_button',
			'option_category'  => 'configuration',
			'options'          => $this->show_divider_options,
			'affects'          => array(
				'divider_color',
				'divider_style',
				'divider_position',
				'divider_weight',
				'divider_max_width',
				'divider_border_radius',
			),
			'toggle_slug'      => $toggle_slug,
			'mobile_options'   => true,
		);

		return array(
			'show_divider'          => array_merge( $main_fields_defaults, $options ),
			'divider_color'         => array(
				'label'            => esc_html__( 'Line Color', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'This will adjust the color of the 1px divider line.', 'squad-modules-for-divi' ),
				'type'             => 'color-alpha',
				'default'          => et_builder_accent_color(),
				'default_on_front' => et_builder_accent_color(),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => $toggle_slug,
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'divider_style'         => array(
				'label'            => esc_html__( 'Line Style', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Select the shape of the dividing line used for the divider.', 'squad-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => et_builder_get_border_styles(),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => $toggle_slug,
				'default'          => $this->defaults['divider_style'],
				'default_on_front' => $this->defaults['divider_style'],
				'mobile_options'   => true,
			),
			'divider_position'      => array(
				'label'            => esc_html__( 'Line Position', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'The dividing line can be placed either above, below or in the center of the module.', 'squad-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'top'    => et_builder_i18n( 'Top' ),
					'center' => esc_html__( 'Vertically Centered', 'squad-modules-for-divi' ),
					'bottom' => et_builder_i18n( 'Bottom' ),
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => $toggle_slug,
				'default'          => $this->defaults['divider_position'],
				'default_on_front' => $this->defaults['divider_position'],
				'mobile_options'   => true,
			),
			'divider_weight'        => array(
				'label'            => esc_html__( 'Divider Weight', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Increasing the divider weight will increase the thickness of the dividing line.', 'squad-modules-for-divi' ),
				'type'             => 'range',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
				'option_category'  => 'layout',
				'depends_show_if'  => 'on',
				'allowed_units'    => array( 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'     => 'px',
				'default'          => $this->defaults['divider_weight'],
				'default_on_front' => $this->defaults['divider_weight'],
				'tab_slug'         => 'advanced',
				'toggle_slug'      => $toggle_slug,
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'divider_max_width'     => $this->disq_add_range_fields(
				esc_html__( 'Divider Max Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose divider max width.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '1000',
						'step' => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => $toggle_slug,
				)
			),
			'divider_border_radius' => $this->disq_add_range_fields(
				esc_html__( 'Divider Border Radius', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can choose divider border  radius.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min'  => '1',
						'max'  => '200',
						'step' => '1',
					),
					'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
					'allow_empty'     => true,
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => $toggle_slug,
				)
			),
		);
	}
}
