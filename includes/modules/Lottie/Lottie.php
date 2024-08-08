<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Lottie Image Module Class which extend the Divi Builder Module Class.
 *
 * This class provides item adding functionalities for Lottie Image in the visual builder.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\Lottie;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;
use DiviSquad\Utils\Helper;
use ET_Builder_Module_Helper_MultiViewOptions;

/**
 * Lottie Image Module Class.
 *
 * @since           1.0.0
 * @package         squad-modules-for-divi
 */
class Lottie extends DISQ_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Lottie Image', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Lottie Images', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( __DIR__ . '/icon.svg' );

		$this->slug       = 'disq_lottie';
		$this->vb_support = 'on';

		$this->child_title_var          = 'name';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'lottie_image'     => esc_html__( 'Lottie', 'squad-modules-for-divi' ),
					'lottie_animation' => esc_html__( 'Animation', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'lottie_image' => esc_html__( 'Lottie', 'squad-modules-for-divi' ),
				),
			),
		);

		$default_css_selectors = $this->disq_get_module_default_selectors();

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
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
			),
			'box_shadow'     => array(
				'default' => $default_css_selectors,
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
			'fonts'          => false,
			'text'           => false,
			'button'         => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'lottie-wrapper'   => array(
				'label'    => esc_html__( 'Lottie Wrapper', 'squad-modules-for-divi' ),
				'selector' => '.disq-lottie-wrapper',
			),
			'lottie-container' => array(
				'label'    => esc_html__( 'Lottie Container', 'squad-modules-for-divi' ),
				'selector' => '.lottie-player-container',
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
		// All field types.
		$lottie_fields            = array(
			'lottie_src_type'   => $this->disq_add_select_box_field(
				esc_html__( 'Source Type', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose a source type to display from your lottie.', 'squad-modules-for-divi' ),
					'options'          => array(
						'remote' => esc_html__( 'External URL', 'squad-modules-for-divi' ),
						'local'  => esc_html__( 'Upload', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'remote',
					'affects'          => array(
						'lottie_src_upload',
						'lottie_src_remote',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_image',
				)
			),
			'lottie_src_remote' => array(
				'label'           => et_builder_i18n( 'External URL' ),
				'description'     => esc_html__( 'The title of your list item will appear in bold below your list item image.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'remote',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'lottie_image',
				'dynamic_content' => 'url',
			),
			'lottie_src_upload' => array(
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
				'toggle_slug'        => 'lottie_image',
			),
		);
		$lottie_animation_fields  = array(
			'lottie_trigger_method'  => $this->disq_add_select_box_field(
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
						'lottie_mouseout_action',
						'lottie_click_action',
						'lottie_scroll',
						'lottie_play_on_hover',
						'lottie_loop',
						'lottie_delay',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_mouseout_action' => $this->disq_add_select_box_field(
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
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_click_action'    => $this->disq_add_select_box_field(
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
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_scroll'          => $this->disq_add_select_box_field(
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
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_play_on_hover'   => $this->disq_add_yes_no_field(
				esc_html__( 'Play On Hover', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose whether or not your Lottie will animate on hover.', 'squad-modules-for-divi' ),
					'default_on_front' => 'off',
					'depends_show_if'  => 'freeze-click',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_loop'            => $this->disq_add_yes_no_field(
				esc_html__( 'Loop', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose whether or not your Lottie will animate in loop.', 'squad-modules-for-divi' ),
					'default_on_front'    => 'off',
					'depends_show_if_not' => array( 'scroll' ),
					'affects'             => array(
						'lottie_loop_no_times',
					),
					'tab_slug'            => 'general',
					'toggle_slug'         => 'lottie_animation',
				)
			),
			'lottie_loop_no_times'   => $this->disq_add_range_fields(
				esc_html__( 'Amount Of Loops', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'This option is only available if Yes is selected for Loop. Enter the number of times you wish to have the animation loop before stopping.', 'squad-modules-for-divi' ),
					'range_settings'   => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '100',
						'max'       => '100',
						'step'      => '1',
					),
					'validate_unit'    => false,
					'unitless'         => true,
					'default_on_front' => '0',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_animation',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'lottie_delay'           => $this->disq_add_range_fields(
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
					'toggle_slug'         => 'lottie_animation',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'lottie_speed'           => $this->disq_add_range_fields(
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
					'toggle_slug'      => 'lottie_animation',
				),
				array(
					'use_hover'      => false,
					'mobile_options' => false,
				)
			),
			'lottie_mode'            => $this->disq_add_select_box_field(
				esc_html__( 'Play Mode', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose play mode for your Lottie animation.', 'squad-modules-for-divi' ),
					'options'          => array(
						'normal' => esc_html__( 'Normal', 'squad-modules-for-divi' ),
						'bounce' => esc_html__( 'Reverse on complete', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'normal',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_direction'       => $this->disq_add_select_box_field(
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
					'toggle_slug'      => 'lottie_animation',
				)
			),
			'lottie_renderer'        => $this->disq_add_select_box_field(
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
						'lottie_mode',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'lottie_animation',
				)
			),
		);
		$lottie_associated_fields = array(
			'lottie_color'  => $this->disq_add_color_field(
				esc_html__( 'Lottie Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__(
						'Here you can define a custom color for lottie image.',
						'squad-modules-for-divi'
					),
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_image',
				)
			),
			'lottie_width'  => $this->disq_add_range_fields(
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
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_image',
				),
				array(
					'use_hover' => false,
				)
			),
			'lottie_height' => $this->disq_add_range_fields(
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
					'depends_show_if' => 'lottie',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'lottie_image',
				),
				array(
					'use_hover' => false,
				)
			),
		);

		return array_merge(
			$lottie_fields,
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
		$fields                 = parent::get_transition_fields_css_props();
		$fields['lottie_color'] = array(
			'fill' => "$this->main_css_element .disq-lottie-wrapper .lottie-image svg path",
		);

		$fields['lottie_width'] = array(
			'width' => "$this->main_css_element .disq-lottie-wrapper .lottie-image",
		);

		$fields['lottie_height'] = array(
			'height' => "$this->main_css_element .disq-lottie-wrapper .lottie-image",
		);

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
		$multi_view = et_pb_multi_view_options( $this );

		wp_enqueue_script( 'disq-module-lottie' );

		return sprintf(
			'<div class="disq-lottie-wrapper">%1$s</div>',
			et_core_esc_previously( $this->disq_render_lottie( $multi_view ) )
		);
	}

	/**
	 * Render item lottie image
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return null|string
	 */
	private function disq_render_lottie( $multi_view ) {
		if ( '' !== $this->props['lottie_src_type'] && ( '' !== $this->props['lottie_src_upload'] || '' !== $this->props['lottie_src_remote'] ) ) {
			$lottie_image_classes = array( 'disq-lottie-player', 'lottie-player-container' );

			$lottie_type     = isset( $this->props['lottie_src_type'] ) ? $this->props['lottie_src_type'] : '';
			$lottie_src_prop = 'local' === $lottie_type ? '{{lottie_src_upload}}' : '{{lottie_src_remote}}';

			// Set background color for Icon.
			$this->generate_styles(
				array(
					'base_attr_name' => 'lottie_color',
					'selector'       => "$this->main_css_element .disq-lottie-wrapper .disq-lottie-player svg path",
					'css_property'   => 'fill',
					'render_slug'    => $this->slug,
					'type'           => 'color',
					'important'      => true,
				)
			);
			// Set width for Image.
			$this->generate_styles(
				array(
					'base_attr_name' => 'lottie_width',
					'selector'       => "$this->main_css_element .disq-lottie-wrapper .disq-lottie-player",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);
			// Set height for Image.
			$this->generate_styles(
				array(
					'base_attr_name' => 'lottie_height',
					'selector'       => "$this->main_css_element .disq-lottie-wrapper .disq-lottie-player",
					'css_property'   => 'height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
					'important'      => true,
				)
			);

			$module_references = array(
				'lottie_trigger_method'  => isset( $this->props['lottie_trigger_method'] ) ? $this->props['lottie_trigger_method'] : '',
				'lottie_mouseout_action' => isset( $this->props['lottie_mouseout_action'] ) ? $this->props['lottie_mouseout_action'] : '',
				'lottie_click_action'    => isset( $this->props['lottie_click_action'] ) ? $this->props['lottie_click_action'] : '',
				'lottie_scroll'          => isset( $this->props['lottie_scroll'] ) ? $this->props['lottie_scroll'] : '',
				'lottie_play_on_hover'   => isset( $this->props['lottie_play_on_hover'] ) ? $this->props['lottie_play_on_hover'] : '',
				'lottie_loop'            => isset( $this->props['lottie_loop'] ) ? $this->props['lottie_loop'] : '',
				'lottie_loop_no_times'   => isset( $this->props['lottie_loop_no_times'] ) ? $this->props['lottie_loop_no_times'] : '',
				'lottie_delay'           => isset( $this->props['lottie_delay'] ) ? $this->props['lottie_delay'] : '',
				'lottie_speed'           => isset( $this->props['lottie_speed'] ) ? $this->props['lottie_speed'] : '',
				'lottie_mode'            => isset( $this->props['lottie_mode'] ) ? $this->props['lottie_mode'] : '',
				'lottie_direction'       => isset( $this->props['lottie_direction'] ) ? $this->props['lottie_direction'] : '',
				'lottie_renderer'        => isset( $this->props['lottie_renderer'] ) ? $this->props['lottie_renderer'] : '',
			);

			return $multi_view->render_element(
				array(
					'tag'   => 'div',
					'attrs' => array(
						'style'        => 'margin: 0px auto; outline: none; overflow: hidden;',
						'class'        => implode( ' ', $lottie_image_classes ),
						'data-src'     => $lottie_src_prop,
						'data-options' => wp_json_encode(
							array(
								'fieldPrefix'     => '',
								'moduleReference' => $module_references,
							)
						),
					),
				)
			);
		}

		return null;
	}

}

new Lottie();
