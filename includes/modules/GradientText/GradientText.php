<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Gradient Text Module Class which extend the Divi Builder Module Class.
 *
 * This class provides gradient text adding functionalities in the visual builder.
 *
 * @since           1.2.2
 * @package         squad-modules-pro-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\GradientText;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\Module;
use function esc_html__;
use function et_core_esc_previously;

/**
 * Gradient Text Module Class.
 *
 * @since           1.2.6
 * @package         squad-modules-for-divi
 */
class GradientText extends DISQ_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.2.6
	 */
	public function init() {
		$this->name      = esc_html__( 'Gradient Text', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Gradient Texts', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/gradient-text.svg' );

		$this->slug       = 'disq_gradient_text';
		$this->vb_support = 'on';

		$this->child_title_var          = 'gradient_text';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// initiate the divider.
		$this->disq_initiate_the_divider_element();

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Main Content', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'gradient'      => esc_html__( 'Gradient', 'squad-modules-for-divi' ),
					'gradient_text' => esc_html__( 'Gradient Text', 'squad-modules-for-divi' ),
				),
			),
		);

		$default_module_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'gradient_text' => $this->disq_add_font_field(
					'',
					array(
						'font_size'       => array(
							'default' => '40px',
						),
						'hide_text_color' => true,
						'line_height'     => array(
							'default'        => '1.2em',
							'range_settings' => array(
								'min'  => '1',
								'max'  => '3',
								'step' => '.1',
							),
						),
						'important'       => 'all',
						'css'             => array(
							'main'  => "$this->main_css_element div .gradient-text-wrapper .gradient-text-element",
							'hover' => "$this->main_css_element div .gradient-text-wrapper:hover .gradient-text-element",
						),
					)
				),
			),
			'background'     => array_merge(
				$default_module_css_selectors,
				array(
					'settings' => array(
						'color' => 'alpha',
					),
				)
			),
			'borders'        => array( 'default' => $default_module_css_selectors ),
			'box_shadow'     => array( 'default' => $default_module_css_selectors ),
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
				$default_module_css_selectors,
				array(
					'css' => array(
						'module_alignment' => "$this->main_css_element.et_pb_module",
					),
				)
			),
			'height'         => $default_module_css_selectors,
			'image_icon'     => false,
			'text'           => false,
			'button'         => false,
			'filters'        => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'gradient_text' => array(
				'label'    => esc_html__( 'Gradient Text', 'squad-modules-for-divi' ),
				'selector' => 'div .gradient-text-wrapper .gradient-text-element',
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
		$text_fields = array(
			'gradient_text'     => array(
				'label'           => esc_html__( 'Gradient Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your gradient texts.', 'squad-modules-for-divi' ),
				'type'            => 'options_list',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
			),
			'gradient_text_tag' => $this->disq_add_select_box_field(
				esc_html__( 'Gradient Text Tag', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a tag to display with your gradient text.', 'squad-modules-for-divi' ),
					'options'          => $this->disq_get_html_tag_elements(),
					'default_on_front' => 'p',
					'default'          => 'p',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
				)
			),
		);

		// Gradient settings.
		$gradient_styles = $this->disq_add_background_gradient_field(
			esc_html__( 'Gradient Colors', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'text_gradient',
				'context'     => 'text_gradient_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'gradient',
			)
		);

		// remove unneeded fields.
		unset( $gradient_styles['text_gradient_color']['background_fields']['text_gradient_color_gradient_overlays_image'] );

		// Set default color.
		$gradient_styles['text_gradient_color']['background_fields']['text_gradient_use_color_gradient']['default']   = 'on';
		$gradient_styles['text_gradient_color']['background_fields']['text_gradient_color_gradient_stops']['default'] = '#1f7016 0%|#29c4a9 100%';

		return array_merge( $text_fields, $gradient_styles );
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
		if ( ! empty( $this->prop( 'gradient_text', array() ) ) ) {
			$gradient_text_tag  = $this->prop( 'gradient_text_tag', 'p' );
			$gradient_texts     = Module::decode_json_data( $this->prop( 'gradient_text', array() ) );
			$gradient_text_html = '';

			if ( is_array( $gradient_texts ) && count( $gradient_texts ) ) {
				foreach ( $gradient_texts as $gradient_text ) {
					$gradient_text_html .= "<span>{$gradient_text['value']}</span> <br/>";
				}
			}

			$this->disq_generate_additional_styles( $attrs );

			return sprintf(
				'<div class="gradient-text-wrapper et_pb_with_background"><%2$s class="gradient-text-element">%1$s</%2$s></div>',
				et_core_esc_previously( $gradient_text_html ),
				et_core_esc_previously( $gradient_text_tag )
			);
		}

		return null;
	}

	/**
	 * Renders additional styles for the module output.
	 *
	 * @param array $attrs List of attributes.
	 */
	private function disq_generate_additional_styles( $attrs ) {
		// Fixed: the custom background doesn't work at frontend.
		$this->props = array_merge( $attrs, $this->props );

		// the typed text background with default, responsive, hover.
		et_pb_background_options()->get_background_style(
			array(
				'base_prop_name'         => 'text_gradient',
				'props'                  => $this->props,
				'selector'               => "$this->main_css_element div .gradient-text-wrapper .gradient-text-element",
				'selector_hover'         => "$this->main_css_element div .gradient-text-wrapper .gradient-text-element:hover",
				'selector_sticky'        => "$this->main_css_element div .gradient-text-wrapper .gradient-text-element",
				'function_name'          => $this->slug,
				'important'              => ' !important',
				'use_background_video'   => false,
				'use_background_pattern' => false,
				'use_background_mask'    => false,
				'prop_name_aliases'      => array(
					'use_text_gradient_color_gradient' => 'text_gradient_use_color_gradient',
					'text_gradient'                    => 'text_gradient_color',
				),
			)
		);
	}
}

new GradientText();