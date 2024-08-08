<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Glitch Text Module Class which extend the Divi Builder Module Class.
 *
 * This class provides glitch text adding functionalities in the visual builder.
 *
 * @since           1.2.2
 * @package         squad-modules-pro-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\GlitchText;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Divi_Builder_Module;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\Module;
use function esc_html__;
use function et_core_esc_previously;

/**
 * Glitch Text Module Class.
 *
 * @since           1.2.2
 * @package         squad-modules-for-divi
 */
class GlitchText extends Squad_Divi_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.2.2
	 */
	public function init() {
		$this->name      = esc_html__( 'Glitch Text', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Glitch Texts', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/glitch-text.svg' );

		$this->slug       = 'disq_glitch_text';
		$this->vb_support = 'on';

		$this->child_title_var          = 'glitch_text';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// initiate the divider.
		$this->disq_initiate_the_divider_element();

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content'    => esc_html__( 'Main Content', 'squad-modules-for-divi' ),
					'glitch_settings' => esc_html__( 'Glitch Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'glitch_text' => esc_html__( 'Glitch Text', 'squad-modules-for-divi' ),
				),
			),
		);

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'glitch_text' => $this->disq_add_font_field(
					'',
					array(
						'font_size' => array(
							'default' => '20px',
						),
						'css'       => array(
							'main'  => "$this->main_css_element div .glitch-text-wrapper .glitch-text-element",
							'hover' => "$this->main_css_element div .glitch-text-wrapper:hover .glitch-text-element",
						),
					)
				),
			),
			'background'     => Module::selectors_background( $this->main_css_element ),
			'borders'        => array( 'default' => Module::selectors_default( $this->main_css_element ) ),
			'box_shadow'     => array( 'default' => Module::selectors_default( $this->main_css_element ) ),
			'margin_padding' => Module::selectors_margin_padding( $this->main_css_element ),
			'max_width'      => Module::selectors_max_width( $this->main_css_element ),
			'height'         => Module::selectors_default( $this->main_css_element ),
			'image_icon'     => false,
			'text'           => false,
			'button'         => false,
			'filters'        => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'glitch_text' => array(
				'label'    => esc_html__( 'Glitch Text', 'squad-modules-for-divi' ),
				'selector' => 'div .glitch-text-wrapper .glitch-text-element',
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
			'glitch_text'     => array(
				'label'           => esc_html__( 'Glitch Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'The text will appear in with your glitch text.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'glitch_text_tag' => $this->disq_add_select_box_field(
				esc_html__( 'Glitch Text Tag', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a tag to display with your glitch text.', 'squad-modules-for-divi' ),
					'options'          => $this->disq_get_html_tag_elements(),
					'default_on_front' => 'p',
					'default'          => 'p',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'main_content',
				)
			),
		);

		// General settings.
		$general_settings = array(
			'glitch_text_effect'           => $this->disq_add_select_box_field(
				esc_html__( 'Glitch Effect', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a effect to display with your glitch text.', 'squad-modules-for-divi' ),
					'options'          => array(
						'one'   => esc_html__( 'Effect 01', 'squad-modules-for-divi' ),
						'two'   => esc_html__( 'Effect 02', 'squad-modules-for-divi' ),
						'three' => esc_html__( 'Effect 03', 'squad-modules-for-divi' ),
						'four'  => esc_html__( 'Effect 04', 'squad-modules-for-divi' ),
						'five'  => esc_html__( 'Effect 05', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'one',
					'default'          => 'one',
					'affects'          => array(
						'glitch_color_primary_one',
						'glitch_color_secondary_one',
						'glitch_color_primary_two',
						'glitch_color_secondary_two',
						'glitch_color_primary_three',
						'glitch_color_secondary_three',
						'glitch_color_primary_four',
						'glitch_color_secondary_four',
						'glitch_color_primary_five',
						'glitch_color_secondary_five',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'glitch_settings',
				)
			),
			'glitch_color_primary_one'     => $this->disq_add_color_field(
				esc_html__( 'Primary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'one',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_secondary_one'   => $this->disq_add_color_field(
				esc_html__( 'Secondary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'one',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_primary_two'     => $this->disq_add_color_field(
				esc_html__( 'Primary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'two',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_secondary_two'   => $this->disq_add_color_field(
				esc_html__( 'Secondary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'two',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_primary_three'   => $this->disq_add_color_field(
				esc_html__( 'Primary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'three',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_secondary_three' => $this->disq_add_color_field(
				esc_html__( 'Secondary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'three',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_primary_four'    => $this->disq_add_color_field(
				esc_html__( 'Primary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'four',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_secondary_four'  => $this->disq_add_color_field(
				esc_html__( 'Secondary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'four',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_primary_five'    => $this->disq_add_color_field(
				esc_html__( 'Primary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'five',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
			'glitch_color_secondary_five'  => $this->disq_add_color_field(
				esc_html__( 'Secondary Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define a custom color for your glitch text.', 'squad-modules-for-divi' ),
					'depends_show_if' => 'five',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'glitch_settings',
				)
			),
		);

		return array_merge( $text_fields, $general_settings );
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
		if ( ! empty( $this->prop( 'glitch_text', '' ) ) ) {
			$glitch_text_effect = $this->prop( 'glitch_text_effect', 'one' );
			$glitch_text_tag    = $this->prop( 'glitch_text_tag', 'p' );
			$glitch_text        = esc_html( $this->prop( 'glitch_text', '' ) );

			// Effect 3: Wrap text with span tag.
			if ( 'three' === $glitch_text_effect ) {
				$glitch_text = "<span>$glitch_text</span>";
			}

			// Effect 5: Wrap text with span tags.
			if ( 'five' === $glitch_text_effect ) {
				$glitch_text = "<span style='--disq-gte-index: 0;'>$glitch_text</span><span style='--disq-gte-index: 1;'>$glitch_text</span><span style='--disq-gte-index: 2;'>$glitch_text</span>";
			}

			$this->disq_generate_additional_styles( $attrs );

			return sprintf(
				'<div class="glitch-text-wrapper et_pb_with_background %3$s"><%4$s class="glitch-text-element" data-text="%2$s">%1$s</%4$s></div>',
				et_core_esc_previously( $glitch_text ),
				esc_html( $this->prop( 'glitch_text', '' ) ),
				et_core_esc_previously( $glitch_text_effect ),
				et_core_esc_previously( $glitch_text_tag )
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
		$text_effect = $this->prop( 'glitch_text_effect', 'one' );

		// Collect colors.
		$color_primary_attribute   = "glitch_color_primary_$text_effect";
		$color_secondary_attribute = "glitch_color_secondary_$text_effect";

		if ( 'one' === $text_effect ) {
			$primary_color   = $this->prop( $color_primary_attribute, '#FF0000FF' );
			$secondary_color = $this->prop( $color_secondary_attribute, '#0000FFFF' );
		}

		if ( 'two' === $text_effect ) {
			$primary_color   = $this->prop( $color_primary_attribute, '#f0f' );
			$secondary_color = $this->prop( $color_secondary_attribute, '#0ff' );
		}

		if ( in_array( $text_effect, array( 'three', 'four' ), true ) ) {
			$primary_color   = $this->prop( $color_primary_attribute, '#0000FFFF' );
			$secondary_color = $this->prop( $color_secondary_attribute, '#FF0000FF' );
		}

		if ( 'five' === $text_effect ) {
			$primary_color   = $this->prop( $color_primary_attribute, '#FF0000FF' );
			$secondary_color = $this->prop( $color_secondary_attribute, '#0000FFFF' );
		}

		if ( ! empty( $primary_color ) && ! empty( $secondary_color ) ) {
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .glitch-text-wrapper.$text_effect .glitch-text-element",
					'declaration' => "--disq-gt-color-primary: $primary_color; --disq-gt-color-secondary:$secondary_color;",
				)
			);
		}
	}
}

new GlitchText();
