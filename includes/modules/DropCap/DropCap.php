<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Drop Cap Module Class which extend the Divi Builder Module Class.
 *
 * This class provides drop cap adding functionalities in the visual builder.
 *
 * @since           1.4.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\DropCap;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Divi_Builder_Module;
use DiviSquad\Utils\Helper;

/**
 * The Drop Cap Module Class.
 *
 * @since       1.4.0
 * @package     squad-modules-for-divi
 */
class DropCapText extends Squad_Divi_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.4.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Drop Cap Text', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Drop Cap Texts', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/drop-cap-text.svg' );

		$this->slug       = 'disq_drop_cap_text';
		$this->vb_support = 'on';

		$this->child_title_var          = 'admin_label';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'drop_cap_element' => esc_html__( 'Drop Cap Letter', 'squad-modules-for-divi' ),
					'drop_cap_letter'  => esc_html__( 'Drop Cap Letter Text', 'squad-modules-for-divi' ),
					'content'          => esc_html__( 'Content Text', 'squad-modules-for-divi' ),
				),
			),
		);

		$default_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'                => array(
				'drop_cap_letter' => $this->disq_add_font_field(
					esc_html__( 'Letter', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '35px',
						),
						'line_height' => array(
							'default' => '1',
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .drop-cap-letter",
							'hover' => "$this->main_css_element div .drop-cap-letter:hover",
						),
					)
				),
				'content'         => $this->disq_add_font_field(
					esc_html__( 'Body', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array(
							'default' => '16px',
						),
						'line_height' => array(
							'default' => '1.7',
						),
						'font_weight' => array(
							'default' => '400',
						),
						'css'         => array(
							'main'  => "$this->main_css_element div .body-text",
							'hover' => "$this->main_css_element div .body-text:hover ",
						),
					)
				),
			),
			'background'           => array_merge(
				$default_css_selectors,
				array(
					'settings' => array(
						'color' => 'alpha',
					),
				)
			),
			'element_icon_element' => array(
				'css' => array(
					'main' => "$this->main_css_element div .post-elements span.disq-element-icon-wrapper",
				),
			),
			'borders'              => array(
				'default' => $default_css_selectors,
			),
			'box_shadow'           => array(
				'default' => $default_css_selectors,
			),
			'margin_padding'       => array(
				'use_padding' => true,
				'use_margin'  => true,
				'css'         => array(
					'margin'    => $this->main_css_element,
					'padding'   => $this->main_css_element,
					'important' => 'all',
				),
			),
			'max_width'            => array_merge(
				$default_css_selectors,
				array(
					'css' => array(
						'module_alignment' => "$this->main_css_element.et_pb_module",
					),
				)
			),
			'height'               => $default_css_selectors,
			'image_icon'           => false,
			'text'                 => false,
			'button'               => false,
			'link_options'         => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'drop_cap_letter' => array(
				'label'    => esc_html__( 'Drop Cap Letter', 'squad-modules-for-divi' ),
				'selector' => 'div .drop-cap-letter',
			),
		);
	}
	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.4.0
	 */
	public function get_fields() {
		// Text fields definitions.
		$text_fields = array(
			'drop_cap_letter' => array(
				'label'           => esc_html__( 'Drop cap letter', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => esc_html__( 'Your Title Goes Here', 'squad-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
			),
			'body_content'    => array(
				'label'           => et_builder_i18n( 'Body' ),
				'description'     => esc_html__( 'Input the main text content for your module here.', 'squad-modules-for-divi' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
		);

		// Drop cap letter associate fields definitions.
		$drop_cap_letter_background_fields = $this->disq_add_background_field(
			esc_html__( 'Background', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'drop_cap_letter_background',
				'context'     => 'drop_cap_letter_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'drop_cap_element',
			)
		);
		$drop_cap_letter_associated_fields = array(
			'drop_cap_letter_margin'  => $this->disq_add_margin_padding_field(
				esc_html__( 'Margin', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can define a custom margin size for the drop cap letter.', 'squad-modules-for-divi' ),
					'type'           => 'custom_margin',
					'range_settings' => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'drop_cap_element',
				)
			),
			'drop_cap_letter_padding' => $this->disq_add_margin_padding_field(
				esc_html__( 'Padding', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Here you can define a custom padding size for the drop cap letter.', 'squad-modules-for-divi' ),
					'type'           => 'custom_padding',
					'range_settings' => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'drop_cap_element',
				)
			),
		);

		return array_merge( $text_fields, $drop_cap_letter_background_fields, $drop_cap_letter_associated_fields );
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
		$fields['drop_cap_letter_background_color'] = array( 'background' => "$this->main_css_element div .drop-cap-letter" );
		$fields['drop_cap_letter_margin']           = array( 'margin' => "$this->main_css_element div .drop-cap-letter" );
		$fields['drop_cap_letter_padding']          = array( 'padding' => "$this->main_css_element div .drop-cap-letter" );
		$this->disq_fix_fonts_transition( $fields, 'drop_cap_letter', "$this->main_css_element div .drop-cap-letter" );
		$this->disq_fix_fonts_transition( $fields, 'content', "$this->main_css_element div .body-text" );

		// Default styles.
		$fields['background_layout'] = array( 'color' => "$this->main_css_element div" );

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
		$highlighted_text = $this->props['body_content'];
		$drop_cap_letter  = $this->props['drop_cap_letter'];

		// Generate additional styles for frontend.
		$this->generate_additional_styles( $attrs );

		return sprintf(
			'<div class="dropcap-text-container"><span class="drop-cap-letter">%1$s</span><span class="body-text">%2$s</span></div>',
			$drop_cap_letter,
			$highlighted_text
		);
	}

	/**
	 * Renders additional styles for the module output.
	 *
	 * @param array $attrs List of attributes.
	 *
	 * @return void
	 */
	private function generate_additional_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'drop_cap_letter_background',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .drop-cap-letter",
				'selector_hover'         => "$this->main_css_element div .drop-cap-letter:hover",
				'selector_sticky'        => "$this->main_css_element div .drop-cap-letter",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_drop_cap_letter_background_color_gradient' => 'drop_cap_letter_background_use_color_gradient',
					'drop_cap_letter_background' => 'drop_cap_letter_background_color',
				),
			)
		);

		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'drop_cap_letter_margin',
				'selector'       => "$this->main_css_element div .drop-cap-letter",
				'hover_selector' => "$this->main_css_element div .drop-cap-letter:hover",
				'type'           => 'margin',
				'css_property'   => 'margin',
				'important'      => true,
			)
		);
		$this->disq_process_margin_padding_styles(
			array(
				'field'          => 'drop_cap_letter_padding',
				'selector'       => "$this->main_css_element div .drop-cap-letter",
				'hover_selector' => "$this->main_css_element div .drop-cap-letter:hover",
				'type'           => 'padding',
				'css_property'   => 'padding',
				'important'      => true,
			)
		);
	}
}

new DropCapText();
