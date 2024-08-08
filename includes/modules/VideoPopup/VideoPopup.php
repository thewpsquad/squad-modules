<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Video Popup Module Class which extend the Divi Builder Module Class.
 *
 * This class provides video popup adding functionalities in the visual builder.
 *
 * @since           1.4.1
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\VideoPopup;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\BuilderModule\Squad_Divi_Builder_Module;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\Module;
use function esc_html__;
use function esc_attr__;
use function wp_enqueue_script;
use function et_builder_accent_color;
use function et_builder_get_text_orientation_options;
use function et_pb_background_options;
use function str_contains;
use function str_replace;

/**
 * The Drop Cap Module Class.
 *
 * @since       1.4.1
 * @package     squad-modules-for-divi
 */
class VideoPopup extends Squad_Divi_Builder_Module {

	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.4.1
	 */
	public function init() {
		$this->name      = esc_html__( 'Video Popup', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Video Popups', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/video-popup.svg' );

		$this->slug       = 'disq_video_popup';
		$this->vb_support = 'on';

		$this->child_title_var          = 'admin_label';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'content'  => esc_html__( 'Content', 'squad-modules-for-divi' ),
					'settings' => esc_html__( 'Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'image' => esc_html__( 'Image', 'squad-modules-for-divi' ),
					'icon'  => esc_html__( 'Icon', 'squad-modules-for-divi' ),
					'text'  => esc_html__( 'Text', 'squad-modules-for-divi' ),
					'popup' => esc_html__( 'Popup', 'squad-modules-for-divi' ),
				),
			),
		);

		// Declare advanced fields for the module.
		$this->advanced_fields = array(
			'fonts'          => array(
				'trigger' => $this->disq_add_font_field(
					esc_html__( 'Before', 'squad-modules-for-divi' ),
					array(
						'font_size'       => array(
							'default' => '16px',
						),
						'hide_text_align' => true,
						'css'             => array(
							'main'      => "$this->main_css_element div .video-popup .video-popup-text",
							'hover'     => "$this->main_css_element:hover div .video-popup .video-popup-text",
							'important' => 'all',
						),
						'tab_slug'        => 'advanced',
						'toggle_slug'     => 'text',
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
			'text_shadow'    => false,
			'button'         => false,
			'filters'        => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'image'        => array(
				'label'    => esc_html__( 'Image', 'squad-modules-for-divi' ),
				'selector' => '.video-popup .video-popup-figure img',
			),
			'icon_wrapper' => array(
				'label'    => esc_html__( 'Icon Wrapper', 'squad-modules-for-divi' ),
				'selector' => '.video-popup .video-popup-icon',
			),
			'icon'         => array(
				'label'    => esc_html__( 'Icon', 'squad-modules-for-divi' ),
				'selector' => '.video-popup .video-popup-icon svg',
			),
			'text'         => array(
				'label'    => esc_html__( 'Text', 'squad-modules-for-divi' ),
				'selector' => '.video-popup .video-popup-text',
			),
		);
	}

	/**
	 * Declare general fields for the module
	 *
	 * @return array[]
	 * @since 1.4.1
	 */
	public function get_fields() {
		$fields = array(
			'use_overlay'      => $this->disq_add_yes_no_field(
				esc_html__( 'Use Overlay Image', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can choose whether overlay image should be used.', 'squad-modules-for-divi' ),
					'affects'     => array(
						'image',
						'alt',
						'icon_alignment',
						'img_height',
					),
					'tab_slug'    => 'general',
					'toggle_slug' => 'content',
				)
			),
			'image'            => array(
				'label'              => esc_html__( 'Image', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Here you can define placeholder image for the video.', 'squad-modules-for-divi' ),
				'type'               => 'upload',
				'data_type'          => 'image',
				'upload_button_text' => esc_attr__( 'Upload an image', 'squad-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'squad-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'squad-modules-for-divi' ),
				'depends_show_if'    => 'on',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'content',
			),
			'alt'              => array(
				'label'           => esc_html__( 'Image Alt Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Here you can define the HTML ALT text for your overlay image.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'depends_show_if' => 'on',
				'tab_slug'        => 'general',
				'toggle_slug'     => 'content',
			),
			'trigger_element'  => $this->disq_add_select_box_field(
				esc_html__( 'Button Element', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can select button element for the video popup.', 'squad-modules-for-divi' ),
					'options'          => array(
						'icon'      => esc_html__( 'Icon', 'squad-modules-for-divi' ),
						'text'      => esc_html__( 'Text', 'squad-modules-for-divi' ),
						'icon_text' => esc_html__( 'Icon & Text', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'icon',
					'affects'          => array(
						'icon',
						'text',
						'icon_spacing',
						'icon_color',
						'icon_bg',
						'icon_size',
						'icon_opacity',
						'icon_height',
						'icon_width',
						'icon_radius',
						'use_text_box',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'content',
				)
			),
			'icon'             => $this->disq_add_select_box_field(
				esc_html__( 'Select Play Icon', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can select different type of play icon from the video.', 'squad-modules-for-divi' ),
					'options'             => array(
						'1' => esc_html__( 'Icon 1', 'squad-modules-for-divi' ),
						'2' => esc_html__( 'Icon 2', 'squad-modules-for-divi' ),
						'3' => esc_html__( 'Icon 3', 'squad-modules-for-divi' ),
						'4' => esc_html__( 'Icon 4', 'squad-modules-for-divi' ),
						'5' => esc_html__( 'Icon 5', 'squad-modules-for-divi' ),
						'6' => esc_html__( 'Icon 6', 'squad-modules-for-divi' ),
					),
					'default_on_front'    => '1',
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'general',
					'toggle_slug'         => 'content',
				)
			),
			'text'             => array(
				'label'               => esc_html__( 'Trigger Text', 'squad-modules-for-divi' ),
				'description'         => esc_html__( 'Define the trigger text for your popup.', 'squad-modules-for-divi' ),
				'type'                => 'text',
				'default'             => 'Play',
				'depends_show_if_not' => array( 'icon' ),
				'tab_slug'            => 'general',
				'toggle_slug'         => 'content',
			),
			'type'             => $this->disq_add_select_box_field(
				esc_html__( 'Video Type', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Define video type for the popup.', 'squad-modules-for-divi' ),
					'options'          => array(
						'yt'    => esc_html__( 'Youtube', 'squad-modules-for-divi' ),
						'vm'    => esc_html__( 'Vimeo', 'squad-modules-for-divi' ),
						'video' => esc_html__( 'Custom Upload', 'squad-modules-for-divi' ),
					),
					'default_on_front' => 'yt',
					'affects'          => array(
						'video_link',
						'video',
					),
					'tab_slug'         => 'general',
					'toggle_slug'      => 'content',
				)
			),
			'video_link'       => array(
				'label'               => esc_html__( 'Video URL', 'squad-modules-for-divi' ),
				'description'         => esc_html__( 'Type youtube or vimeo video url which you would like to display in the popup.', 'squad-modules-for-divi' ),
				'type'                => 'text',
				'depends_show_if_not' => array( 'video' ),
				'tab_slug'            => 'general',
				'toggle_slug'         => 'content',
			),
			'video'            => array(
				'label'              => esc_html__( 'Video MP4 File', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Upload your desired video in .MP4 format, or type in the URL to the video you would like to display', 'squad-modules-for-divi' ),
				'type'               => 'upload',
				'data_type'          => 'video',
				'upload_button_text' => esc_attr__( 'Upload a video', 'squad-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose a Video MP4 File', 'squad-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Video', 'squad-modules-for-divi' ),
				'depends_show_if'    => 'video',
				'tab_slug'           => 'general',
				'toggle_slug'        => 'content',
			),
			'use_animation'    => $this->disq_add_yes_no_field(
				esc_html__( 'Use Animated Icon', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Use animated wave for your icon. For better experience please set icon background color from icon design toggle.', 'squad-modules-for-divi' ),
					'affects'     => array(
						'wave_bg',
					),
					'tab_slug'    => 'general',
					'toggle_slug' => 'settings',
				)
			),
			'wave_bg'          => $this->disq_add_color_field(
				esc_html__( 'Animated Wave Color', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define custom color for the animated wave of your icon.', 'squad-modules-for-divi' ),
					'default'         => '#ffffff',
					'depends_show_if' => 'on',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'settings',
				)
			),
			'icon_alignment'   => $this->disq_add_alignment_field(
				esc_html__( 'Icon/Text Alignment', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Align content to the left, right or center.', 'squad-modules-for-divi' ),
					'type'            => 'text_align',
					'options_icon'    => 'module_align',
					'default'         => 'center',
					'depends_show_if' => 'off',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'settings',
					'mobile_options'  => true,
				)
			),
			'icon_spacing'     => $this->disq_add_range_field(
				esc_html__( 'Spacing Between Icon and Text', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define spacing between icon and text.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'default'         => '20px',
					'default_unit'    => 'px',
					'depends_show_if' => 'icon_text',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'settings',
					'mobile_options'  => true,
				)
			),
			'img_height'       => $this->disq_add_range_field(
				esc_html__( 'Image Height', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define static height for your image.', 'squad-modules-for-divi' ),
					'default_unit'    => 'px',
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'mobile_options'  => true,
				)
			),
			'icon_color'       => $this->disq_add_color_field(
				esc_html__( 'Color', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define custom color for your icon.', 'squad-modules-for-divi' ),
					'default'             => et_builder_accent_color(),
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
					'hover'               => 'tabs',
				)
			),
			'icon_bg'          => $this->disq_add_color_field(
				esc_html__( 'Background', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define custom background for your icon.', 'squad-modules-for-divi' ),
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
					'hover'               => 'tabs',
				)
			),
			'icon_size'        => $this->disq_add_range_field(
				esc_html__( 'Size', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define custom size for your icon.', 'squad-modules-for-divi' ),
					'range_settings'      => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
					'default'             => '60px',
					'default_unit'        => 'px',
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
					'hover'               => 'tabs',
					'mobile_options'      => true,
				)
			),
			'icon_opacity'     => $this->disq_add_range_field(
				esc_html__( 'Opacity', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Define the opacity for the icon. Set the value from 0 - 1. The lower value, the more transparent.', 'squad-modules-for-divi' ),
					'range_settings'      => array(
						'min'  => 0,
						'max'  => 1,
						'step' => .02,
					),
					'default'             => '1',
					'unitless'            => true,
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
					'hover'               => 'tabs',
				)
			),
			'icon_height'      => $this->disq_add_range_field(
				esc_html__( 'Height', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define static height for your icon.', 'squad-modules-for-divi' ),
					'range_settings'      => array(
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					),
					'default'             => 'initial',
					'default_unit'        => 'px',
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
					'mobile_options'      => true,
				)
			),
			'icon_width'       => $this->disq_add_range_field(
				esc_html__( 'Width', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define static width for your icon.', 'squad-modules-for-divi' ),
					'range_settings'      => array(
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					),
					'default'             => 'initial',
					'default_unit'        => 'px',
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
					'mobile_options'      => true,
				)
			),
			'icon_radius'      => $this->disq_add_range_field(
				esc_html__( 'Border Radius', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can define the radius value for your icon border.', 'squad-modules-for-divi' ),
					'range_settings'      => array(
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					),
					'default'             => '0px',
					'default_unit'        => 'px',
					'depends_show_if_not' => array( 'text' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'icon',
				)
			),

			// Popup.
			'popup_bg'         => $this->disq_add_color_field(
				esc_html__( 'Popup Background', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define custom background color for your popup.', 'squad-modules-for-divi' ),
					'default'     => 'rgba(0,0,0,.8)',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'popup',
				)
			),
			'close_icon_color' => $this->disq_add_color_field(
				esc_html__( 'Close Icon Color', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Here you can define custom color for your popup close icon.', 'squad-modules-for-divi' ),
					'default'     => '#ffffff',
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'popup',
				)
			),
		);

		$text = array(
			'use_text_box'    => $this->disq_add_yes_no_field(
				esc_html__( 'Use Text Box', 'squad-modules-for-divi' ),
				array(
					'description'         => esc_html__( 'Here you can choose whether overlay image should be used.', 'squad-modules-for-divi' ),
					'depends_show_if_not' => array( 'icon' ),
					'affects'             => array(
						'text_box_height',
						'text_box_width',
						'text_box_bg',
						'text_box_radius',
					),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'text',
				)
			),
			'text_box_height' => $this->disq_add_range_field(
				esc_html__( 'Text Box Height', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define static height for your text box.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					),
					'default'         => '80px',
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'text',
					'mobile_options'  => true,
				)
			),
			'text_box_width'  => $this->disq_add_range_field(
				esc_html__( 'Text Box Width', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define static width for your text box.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 300,
						'step' => 1,
					),
					'default'         => '80px',
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'text',
					'mobile_options'  => true,
				)
			),
			'text_box_bg'     => $this->disq_add_color_field(
				esc_html__( 'Text Box Background', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define custom background for your text box.', 'squad-modules-for-divi' ),
					'default'         => et_builder_accent_color(),
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'text',
					'hover'           => 'tabs',
				)
			),
			'text_box_radius' => $this->disq_add_range_field(
				esc_html__( 'Text Box Border Radius', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Here you can define the radius value for your text box border.', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					),
					'default'         => '0px',
					'default_unit'    => 'px',
					'depends_show_if' => 'on',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'text',
				)
			),
		);

		$img_overlay = $this->disq_add_background_field(
			esc_html__( 'Image Overlay', 'squad-modules-for-divi' ),
			array(
				'base_name'   => 'image_overlay_background',
				'context'     => 'image_overlay_background_color',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'image',
			)
		);

		return array_merge( $fields, $img_overlay, $text );
	}

	/**
	 * Get CSS fields transition.
	 *
	 * Add form field options group and background image on the field list.
	 *
	 * @since 1.4.1
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['image_overlay_background_color'] = array( 'background' => "$this->main_css_element div .video-popup-figure:before" );

		$fields['icon_color']   = array( 'fill' => "$this->main_css_element div .video-popup .video-popup-icon svg" );
		$fields['icon_bg']      = array( 'background-color' => "$this->main_css_element div .video-popup .video-popup-icon" );
		$fields['icon_size']    = array( 'width' => "$this->main_css_element div .video-popup .video-popup-icon svg" );
		$fields['icon_opacity'] = array( 'opacity' => "$this->main_css_element div .video-popup .video-popup-icon svg" );
		$fields['text_box_bg']  = array( 'background-color' => "$this->main_css_element div .video-popup .video-popup-text" );

		$this->disq_fix_fonts_transition( $fields, 'trigger', "$this->main_css_element div .video-popup .video-popup-text" );

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
		$inline_modal = '';
		$image        = $this->props['image'];
		$image_alt    = $this->props['alt'];
		$icon         = $this->props['icon'];
		$video_link   = $this->props['video_link'];
		$type         = $this->props['type'];
		$video        = $this->props['video'];
		$img_overlay  = '';
		$order_class  = self::get_module_order_class( $render_slug );
		$order_number = str_replace( '_', '', str_replace( $this->slug, '', $order_class ) );
		$data_modal   = 'video' === $type ? sprintf( 'data-mfp-src="#disq-vp-modal-video-popup-%1$s"', $order_number ) : '';

		// Set popup style.
		self::set_style(
			$render_slug,
			array(
				'selector'    => ".disq-vp-modal-open.disq-vp-video-popup-{$order_number} .mfp-bg",
				'declaration' => sprintf( 'opacity:1!important;background: %1$s!important;', $this->prop( 'popup_bg', '' ) ),
			)
		);
		self::set_style(
			$render_slug,
			array(
				'selector'    => ".disq-vp-modal-open.disq-vp-video-popup-{$order_number} .mfp-iframe-holder .mfp-close",
				'declaration' => sprintf( 'color: %1$s!important;', $this->prop( 'close_icon_color', '' ) ),
			)
		);

		if ( 'video' === $type ) {
			$inline_modal = sprintf(
				'<div class="mfp-hide disq-vp-modal" id="disq-vp-modal-video-popup-%1$s" data-order="%1$s"> <div class="video-wrap"><video controls><source type="video/mp4" src="%2$s"></video></div> </div>',
				$order_number,
				$video
			);
		}

		if ( 'on' === $this->prop( 'use_overlay', 'on' ) ) {
			$img_overlay = sprintf( '<div class="video-popup-figure"><img src="%1$s" alt="%2$s"/></div>', $image, $image_alt );
		}

		if ( str_contains( $video_link, 'youtu.be' ) ) {
			$video_link = str_replace( 'youtu.be/', 'youtube.com/watch?v=', $video_link );
		}

		wp_enqueue_script( 'disq-module-video-popup' );

		$this->generate_additional_styles( $attrs );

		return sprintf(
			'<div class="video-popup"> %5$s <div class="video-popup-wrap"> <a class="video-popup-trigger popup-%6$s" data-order="%4$s" data-type="%6$s" href="%3$s" %7$s>%1$s</a></div>%2$s</div>',
			$this->render_trigger( $icon ),
			$img_overlay,
			$video_link,
			$order_number,
			$inline_modal,
			$type,
			$data_modal
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

		if ( 'on' === $this->prop( 'use_overlay', 'on' ) ) {
			et_pb_background_options()->get_background_style(
				array(
					'base_prop_name'         => 'image_overlay_background',
					'props'                  => $this->props,
					'selector'               => "$this->main_css_element div .video-popup-figure:before",
					'selector_hover'         => "$this->main_css_element:hover div .video-popup-figure:before",
					'selector_sticky'        => "$this->main_css_element div .video-popup-figure:before",
					'function_name'          => $this->slug,
					'use_background_video'   => false,
					'use_background_pattern' => false,
					'use_background_mask'    => false,
					'prop_name_aliases'      => array(
						'use_image_overlay_background_color_gradient' => 'image_overlay_background_use_color_gradient',
						'image_overlay_background' => 'image_overlay_background_color',
					),
				)
			);

			$this->generate_styles(
				array(
					'base_attr_name' => 'img_height',
					'selector'       => "$this->main_css_element div .video-popup-figure",
					'css_property'   => 'height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);

			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element div .video-popup-trigger",
					'declaration' => 'justify-content: center; position: absolute; left: 0; top: 0;',
				)
			);
		}

		$this->generate_styles(
			array(
				'base_attr_name' => 'icon_alignment',
				'selector'       => "$this->main_css_element div .video-popup-trigger",
				'hover_selector' => "$this->main_css_element:hover div .video-popup-trigger",
				'css_property'   => 'justify-content',
				'render_slug'    => $this->slug,
				'type'           => 'align',
			)
		);

		if ( 'text' !== $this->prop( 'trigger_element', 'icon' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_color',
					'selector'       => "$this->main_css_element div .video-popup .video-popup-icon svg",
					'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-icon svg",
					'css_property'   => 'fill',
					'render_slug'    => $this->slug,
					'type'           => 'color',
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_size',
					'selector'       => "$this->main_css_element div .video-popup .video-popup-icon svg",
					'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-icon svg",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_opacity',
					'selector'       => "$this->main_css_element div .video-popup-icon .video-popup-icon svg",
					'hover_selector' => "$this->main_css_element:hover div .video-popup-icon .video-popup-icon svg",
					'css_property'   => 'opacity',
					'render_slug'    => $this->slug,
					'type'           => 'number',
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_bg',
					'selector'       => "$this->main_css_element div .video-popup .video-popup-icon",
					'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-icon",
					'css_property'   => 'background-color',
					'render_slug'    => $this->slug,
					'type'           => 'color',
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_width',
					'selector'       => "$this->main_css_element div .video-popup .video-popup-icon",
					'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-icon",
					'css_property'   => 'width',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_height',
					'selector'       => "$this->main_css_element div .video-popup .video-popup-icon",
					'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-icon",
					'css_property'   => 'height',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_radius',
					'selector'       => "$this->main_css_element div .video-popup .video-popup-icon",
					'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-icon",
					'css_property'   => 'border-radius',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);
		}

		if ( 'icon' !== $this->prop( 'trigger_element', 'icon' ) ) {
			if ( 'on' === $this->prop( 'use_text_box', 'off' ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => 'text_box_width',
						'selector'       => "$this->main_css_element div .video-popup .video-popup-text",
						'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-text",
						'css_property'   => 'width',
						'render_slug'    => $this->slug,
						'type'           => 'range',
					)
				);
				$this->generate_styles(
					array(
						'base_attr_name' => 'text_box_height',
						'selector'       => "$this->main_css_element div .video-popup .video-popup-text",
						'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-text",
						'css_property'   => 'height',
						'render_slug'    => $this->slug,
						'type'           => 'range',
					)
				);
				$this->generate_styles(
					array(
						'base_attr_name' => 'text_box_bg',
						'selector'       => "$this->main_css_element div .video-popup .video-popup-text",
						'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-text",
						'css_property'   => 'background-color',
						'render_slug'    => $this->slug,
						'type'           => 'color',
					)
				);
				$this->generate_styles(
					array(
						'base_attr_name' => 'text_box_radius',
						'selector'       => "$this->main_css_element div .video-popup .video-popup-text",
						'hover_selector' => "$this->main_css_element:hover div .video-popup .video-popup-text",
						'css_property'   => 'border-radius',
						'render_slug'    => $this->slug,
						'type'           => 'range',
					)
				);
			}

			if ( 'on' === $this->prop( 'use_overlay', 'on' ) ) {
				self::set_style(
					$this->slug,
					array(
						'selector'    => "$this->main_css_element .video-popup-trigger",
						'declaration' => 'justify-content: center; position: absolute; left: 0; top: 0;',
					)
				);
			}
		}

		if ( 'icon_text' === $this->prop( 'trigger_element', 'icon' ) ) {
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_spacing',
					'selector'       => "$this->main_css_element div .video-popup-icon",
					'css_property'   => 'margin-right',
					'render_slug'    => $this->slug,
					'type'           => 'range',
				)
			);
		}

		// Animation.
		if ( 'on' === $this->prop( 'use_animation', 'off' ) ) {
			$selector = "$this->main_css_element .video-popup a:after";
			if ( 'icon_text' === $this->prop( 'trigger_element', 'icon' ) ) {
				$selector = "$this->main_css_element div .video-popup .video-popup-icon:after";
			}

			if ( 'icon' !== $this->prop( 'trigger_element', 'icon' ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => 'text_box_radius',
						'selector'       => $selector,
						'css_property'   => 'border-radius',
						'render_slug'    => $this->slug,
						'type'           => 'range',
					)
				);
			}

			if ( 'text' !== $this->prop( 'trigger_element', 'icon' ) ) {
				$this->generate_styles(
					array(
						'base_attr_name' => 'icon_radius',
						'selector'       => $selector,
						'css_property'   => 'border-radius',
						'render_slug'    => $this->slug,
						'type'           => 'range',
					)
				);
			}

			self::set_style(
				$this->slug,
				array(
					'selector'    => $selector,
					'declaration' => sprintf(
						'content: "";
						-webkit-box-shadow: 0 0 0 15px %1$s, 0 0 0 30px %1$s, 0 0 0 45px %1$s;
						box-shadow: 0 0 0 15px %1$s, 0 0 0 30px %1$s, 0 0 0 45px %1$s;',
						$this->prop( 'wave_bg', '' )
					),
				)
			);
		}
	}

	/**
	 * Generate render trigger.
	 *
	 * @param string $icon The icon value.
	 *
	 * @return string
	 */
	protected function render_trigger( $icon ) {
		$svg_icon = '';
		$text     = '';

		if ( 'text' !== $this->prop( 'trigger_element', 'icon' ) ) {
			$icons = array(
				'1' => '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 511.999 511.999"><g><path d="M443.86,196.919L141.46,10.514C119.582-2.955,93.131-3.515,70.702,9.016c-22.429,12.529-35.819,35.35-35.819,61.041  v371.112c0,38.846,31.3,70.619,69.77,70.829c0.105,0,0.21,0.001,0.313,0.001c12.022-0.001,24.55-3.769,36.251-10.909 c9.413-5.743,12.388-18.029,6.645-27.441c-5.743-9.414-18.031-12.388-27.441-6.645c-5.473,3.338-10.818,5.065-15.553,5.064 c-14.515-0.079-30.056-12.513-30.056-30.898V70.058c0-11.021,5.744-20.808,15.364-26.183c9.621-5.375,20.966-5.135,30.339,0.636 l302.401,186.405c9.089,5.596,14.29,14.927,14.268,25.601c-0.022,10.673-5.261,19.983-14.4,25.56L204.147,415.945 c-9.404,5.758-12.36,18.049-6.602,27.452c5.757,9.404,18.048,12.36,27.452,6.602l218.611-133.852  c20.931-12.769,33.457-35.029,33.507-59.55C477.165,232.079,464.729,209.767,443.86,196.919z"/></g></svg>',

				'2' => '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 494.148 494.148"><g><g><path d="M405.284,201.188L130.804,13.28C118.128,4.596,105.356,0,94.74,0C74.216,0,61.52,16.472,61.52,44.044v406.124 c0,27.54,12.68,43.98,33.156,43.98c10.632,0,23.2-4.6,35.904-13.308l274.608-187.904c17.66-12.104,27.44-28.392,27.44-45.884 C432.632,229.572,422.964,213.288,405.284,201.188z"/> </g></g></svg>',

				'3' => '<svg viewBox="0 0 494.942 494.942" xmlns="http://www.w3.org/2000/svg"><path d="m35.353 0 424.236 247.471-424.236 247.471z"/></svg>',

				'4' => '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 60 60"><path d="M30,0C13.458,0,0,13.458,0,30s13.458,30,30,30s30-13.458,30-30S46.542,0,30,0z M45.563,30.826l-22,15 C23.394,45.941,23.197,46,23,46c-0.16,0-0.321-0.038-0.467-0.116C22.205,45.711,22,45.371,22,45V15c0-0.371,0.205-0.711,0.533-0.884 c0.328-0.174,0.724-0.15,1.031,0.058l22,15C45.836,29.36,46,29.669,46,30S45.836,30.64,45.563,30.826z"/> <g></g></svg>',

				'5' => '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 485 485"><g><path d="M413.974,71.026C368.171,25.225,307.274,0,242.5,0S116.829,25.225,71.026,71.026C25.225,116.829,0,177.726,0,242.5 s25.225,125.671,71.026,171.474C116.829,459.775,177.726,485,242.5,485s125.671-25.225,171.474-71.026 C459.775,368.171,485,307.274,485,242.5S459.775,116.829,413.974,71.026z M242.5,455C125.327,455,30,359.673,30,242.5 S125.327,30,242.5,30S455,125.327,455,242.5S359.673,455,242.5,455z"/><polygon points="181.062,336.575 343.938,242.5 181.062,148.425"/></g></svg>',

				'6' => '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 310 310"><g><path d="M297.917,64.645c-11.19-13.302-31.85-18.728-71.306-18.728H83.386c-40.359,0-61.369,5.776-72.517,19.938 C0,79.663,0,100.008,0,128.166v53.669c0,54.551,12.896,82.248,83.386,82.248h143.226c34.216,0,53.176-4.788,65.442-16.527 C304.633,235.518,310,215.863,310,181.835v-53.669C310,98.471,309.159,78.006,297.917,64.645z M199.021,162.41l-65.038,33.991 c-1.454,0.76-3.044,1.137-4.632,1.137c-1.798,0-3.592-0.484-5.181-1.446c-2.992-1.813-4.819-5.056-4.819-8.554v-67.764 c0-3.492,1.822-6.732,4.808-8.546c2.987-1.814,6.702-1.938,9.801-0.328l65.038,33.772c3.309,1.718,5.387,5.134,5.392,8.861 C204.394,157.263,202.325,160.684,199.021,162.41z"/></g></svg>',
			);

			$svg_icon = sprintf( '<span class="video-popup-icon">%1$s</span>', $icons[ $icon ] );
		}

		if ( 'icon' !== $this->prop( 'trigger_element', 'icon' ) ) {
			$text = sprintf( '<span class="video-popup-text">%1$s</span>', $this->props['text'] );
		}

		return $svg_icon . $text;
	}
}

new VideoPopup();
