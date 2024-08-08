<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Scrolling Text Module Class which extend the Divi Builder Module Class.
 *
 * This class provides scrolling-text adding functionalities for a text element in the visual builder.
 *
 * @since           1.3.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\ScrollingText;

use DiviSquad\Base\DiviBuilder\DiviSquad_Module as Squad_Module;
use DiviSquad\Base\DiviBuilder\Utils;
use DiviSquad\Utils\Helper;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function wp_enqueue_script;

/**
 * Scrolling Text Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class ScrollingText extends Squad_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Scrolling Text', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Scrolling Texts', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DIVI_SQUAD_MODULES_ICON_DIR_PATH . '/scrolling-text.svg' );

		$this->slug             = 'disq_scrolling_text';
		$this->vb_support       = 'on';
		$this->main_css_element = "%%order_class%%.$this->slug";

		$this->child_title_var          = 'scrolling_text';
		$this->child_title_fallback_var = 'admin_label';

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'       => esc_html__( 'Content', 'squad-modules-for-divi' ),
					'scrolling_settings' => esc_html__( 'Scrolling Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'scrolling_text' => esc_html__( 'Scrolling Text', 'squad-modules-for-divi' ),
				),
			),
		);

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'scrolling_text' => Utils::add_font_field(
					esc_html__( 'Scrolling', 'squad-modules-for-divi' ),
					array(
						'font_size'      => array(
							'default' => '20px',
						),
						'line_height'    => array(
							'default' => '1.2em',
						),
						'letter_spacing' => array(
							'default' => '0px',
						),
						'css'            => array(
							'main'  => "$this->main_css_element div .text-elements .scrolling-element",
							'hover' => "$this->main_css_element div .text-elements:hover .scrolling-element",
						),
					)
				),
			),
			'background'     => Utils::selectors_background( $this->main_css_element ),
			'borders'        => array( 'default' => Utils::selectors_default( $this->main_css_element ) ),
			'box_shadow'     => array( 'default' => Utils::selectors_default( $this->main_css_element ) ),
			'margin_padding' => Utils::selectors_margin_padding( $this->main_css_element ),
			'max_width'      => Utils::selectors_max_width( $this->main_css_element ),
			'height'         => Utils::selectors_default( $this->main_css_element ),
			'image_icon'     => false,
			'text'           => false,
			'button'         => false,
			'filters'        => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'scrolling_element' => array(
				'label'    => esc_html__( 'Scrolling Text', 'squad-modules-for-divi' ),
				'selector' => 'div .text-elements .scrolling-element',
			),
		);
	}

	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.3.0
	 */
	public function get_fields() {
		// Text fields definitions.
		$text_fields = array(
			'scrolling_text'   => array(
				'label'           => esc_html__( 'Scrolling Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your scrolling text.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'text_element_tag' => Utils::add_select_box_field(
				esc_html__( 'Text Tag', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a tag to display with your texts.', 'squad-modules-for-divi' ),
					'options'          => Utils::get_html_tag_elements(),
					'default_on_front' => 'h2',
					'default'          => 'h2',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
				)
			),
		);

		// The settings definitions for scrolling texts.
		$settings_fields = array(
			'scrolling_direction'    => Utils::add_select_box_field(
				esc_html__( 'Scrolling Direction', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose Scrolling Text Direction.', 'squad-modules-for-divi' ),
					'options'          => array(
						'left'  => esc_html__( 'Left', 'squad-modules-for-divi' ),
						'right' => esc_html__( 'Right', 'squad-modules-for-divi' ),
					),
					'default'          => 'left',
					'default_on_front' => 'left',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'scrolling_settings',
				)
			),
			'outline_text__enable'   => Utils::add_yes_no_field(
				esc_html__( 'Enable Scrolling Text Outline', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Enable Scrolling Text Outline for better look.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'tab_slug'    => 'general',
					'toggle_slug' => 'scrolling_settings',
				)
			),
			'pause_on_hover__enable' => Utils::add_yes_no_field(
				esc_html__( 'Pause on Hover', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'You can pause Scrolling on Hover by enabling this option.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'tab_slug'    => 'general',
					'toggle_slug' => 'scrolling_settings',
				)
			),
			'repeat_text__enable'    => Utils::add_yes_no_field(
				esc_html__( 'Repeat Scrolling Text', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'You will need more repeats to create the infinite scrolling effect.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'tab_slug'    => 'general',
					'toggle_slug' => 'scrolling_settings',
				)
			),
			'scrolling_speed'        => Utils::add_range_field(
				esc_html__( 'Scrolling Text Speed (ms)', 'squad-modules-for-divi' ),
				array(
					'description'       => esc_html__( 'Choose the speed for your scrolling text in milliseconds.', 'squad-modules-for-divi' ),
					'type'              => 'range',
					'range_settings'    => array(
						'min'       => '100',
						'max'       => '10000',
						'step'      => '100',
						'min_limit' => '100',
					),
					'default'           => '7500',
					'default_on_front'  => '7500',
					'number_validation' => true,
					'fixed_range'       => true,
					'unitless'          => true,
					'hover'             => false,
					'mobile_options'    => false,
					'responsive'        => false,
					'tab_slug'          => 'general',
					'toggle_slug'       => 'scrolling_settings',
				)
			),
		);

		return array_merge( $text_fields, $settings_fields );
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

		// scrolling text styles.
		Utils::fix_fonts_transition( $fields, 'scrolling_text', "$this->main_css_element div .text-elements .scrolling-element" );

		// Default styles.
		$fields['background_layout'] = array( 'color' => "$this->main_css_element div .text-elements .scrolling-element" );

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
		if ( ! empty( $this->prop( 'scrolling_text', '' ) ) ) {
			$text_tag = $this->prop( 'text_element_tag', 'h2' );

			// Set text outline style.
			if ( 'on' === $this->prop( 'outline_text__enable', 'off' ) ) {
				self::set_style(
					$render_slug,
					array(
						'selector'    => "$this->main_css_element div .text-elements .scrolling-element",
						'declaration' => '-webkit-text-stroke-width: 1px;webkit-text-stroke-color: inherit; -webkit-text-fill-color: transparent;',
					)
				);
			}

			wp_enqueue_script( 'squad-vendor-scrolling-text' );
			wp_enqueue_script( 'squad-module-scrolling-text' );

			return sprintf(
				'<div class="text-elements et_pb_with_background"><%1$s class="scrolling-element" data-scroll-direction="%3$s" data-scroll-speed="%4$s" data-repeat-text ="%5$s" data-scroll-pause="%6$s">%2$s</%1$s></div>',
				et_core_esc_previously( $text_tag ),
				esc_html( $this->prop( 'scrolling_text', '' ) ),
				esc_attr( $this->prop( 'scrolling_direction', 'left' ) ),
				esc_attr( $this->prop( 'scrolling_speed', '' ) ),
				esc_attr( $this->prop( 'repeat_text__enable', 'off' ) ),
				esc_attr( $this->prop( 'pause_on_hover__enable', 'off' ) )
			);
		}

		return null;
	}
}
