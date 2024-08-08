<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Image Gallery Module Class which extend the Divi Builder Module Class.
 *
 * This class provides a gallery adding functionalities for image in the visual builder.
 *
 * @since           1.2.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <wp@thewpsquad.com>
 * @license         GPL-3.0-only
 */

namespace DiviSquad\Modules\ImageGallery;

use DiviSquad\Base\BuilderModule\DISQ_Builder_Module;
use DiviSquad\Utils\Helper;
use WP_Post;
use function esc_html__;
use function et_builder_i18n;
use function et_builder_is_loading_data;
use function get_intermediate_image_sizes;
use function wp_enqueue_script;
use function apply_filters;
use function wp_array_slice_assoc;
use function et_pb_media_options;
use function wp_json_encode;
use function wp_parse_args;
use function get_posts;
use function get_permalink;
use function wp_get_attachment_image_src;
use function get_post_meta;
use function wp_get_attachment_metadata;
use function _wp_get_image_size_from_meta;

/**
 * Image Gallery Module Class.
 *
 * @since           1.2.0
 * @package         squad-modules-for-divi
 */
class ImageGallery extends DISQ_Builder_Module {
	/**
	 * Initiate Module.
	 * Set the module name on init.
	 *
	 * @return void
	 * @since 1.2.0
	 */
	public function init() {
		$this->name      = esc_html__( 'Image Gallery', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Image Galleries', 'squad-modules-for-divi' );
		$this->icon_path = Helper::fix_slash( DISQ_MODULES_ICON_DIR_PATH . '/image-gallery.svg' );

		$this->slug       = 'disq_image_gallery';
		$this->vb_support = 'on';

		$this->child_title_var          = 'title';
		$this->child_title_fallback_var = 'admin_label';

		$this->main_css_element = "%%order_class%%.$this->slug";

		// Declare settings modal toggles for the module.
		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content'     => esc_html__( 'Images', 'squad-modules-for-divi' ),
					'gallery_settings' => esc_html__( 'Gallery Settings', 'squad-modules-for-divi' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'overlay' => et_builder_i18n( 'Overlay' ),
					'image'   => esc_html__( 'Image', 'squad-modules-for-divi' ),
					'text'    => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'squad-modules-for-divi' ),
						'priority' => 90,
					),
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
			'scroll_effects' => array(
				'grid_support' => 'yes',
			),
			'fonts'          => false,
			'image_icon'     => false,
			'text'           => false,
			'button'         => false,
			'filters'        => false,
		);

		// Declare custom css fields for the module.
		$this->custom_css_fields = array(
			'image' => array(
				'label'    => esc_html__( 'Images', 'squad-modules-for-divi' ),
				'selector' => 'div .gallery-elements .disq-image',
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
		$image_fields = array(
			'gallery_ids'    => array(
				'label'            => esc_html__( 'Images', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Choose the images that you would like to appear in the image gallery.', 'squad-modules-for-divi' ),
				'type'             => 'upload-gallery',
				'computed_affects' => array(
					'__gallery',
				),
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
			),
			'thumbnail_size' => array(
				'label'            => esc_html__( 'Image Size', 'squad-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => $this->get_available_image_sizes(),
				'default_on_front' => 'off',
				'description'      => esc_html__( 'Choose image size.', 'squad-modules-for-divi' ),
				'computed_affects' => array(
					'__gallery',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'main_content',
			),
			'__gallery'      => array(
				'type'                => 'computed',
				'computed_callback'   => array( __CLASS__, 'get_gallery' ),
				'computed_depends_on' => array(
					'gallery_ids',
					'show_title_and_caption',
					'thumbnail_size',
				),
			),
		);

		// Gallery settings fields definitions.
		$gallery_settings_fields = array(
			'gallery_images_gap' => $this->disq_add_range_fields(
				esc_html__( 'Images Gap', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Here you can choose gap between images.', 'squad-modules-for-divi' ),
					'type'             => 'range',
					'range_settings'   => array(
						'min'  => '1',
						'max'  => '100',
						'step' => '1',
					),
					'default_on_front' => '10px',
					'default_unit'     => 'px',
					'hover'            => false,
					'tab_slug'         => 'general',
					'toggle_slug'      => 'gallery_settings',
				)
			),
			'gallery_orderby'    => array(
				'label'            => esc_html__( 'Image Order', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Select an ordering method for the gallery. This controls which gallery items appear first in the list.', 'squad-modules-for-divi' ),
				'type'             => et_builder_is_loading_data() ? 'hidden' : 'select',
				'options'          => array(
					''     => et_builder_i18n( 'Default' ),
					'rand' => esc_html__( 'Random', 'squad-modules-for-divi' ),
				),
				'default'          => 'off',
				'class'            => array( 'et-pb-gallery-ids-field' ),
				'computed_affects' => array(
					'__gallery',
				),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'gallery_settings',
			),
			'gallery_captions'   => array(
				'type'             => 'hidden',
				'class'            => array( 'et-pb-gallery-captions-field' ),
				'computed_affects' => array(
					'__gallery',
				),
			),
			'show_in_lightbox'   => $this->disq_add_yes_no_field(
				esc_html__( 'Open in Lightbox', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( ' Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'affects'     => array(
						'url',
						'url_new_window',
					),
					'tab_slug'    => 'general',
					'toggle_slug' => 'gallery_settings',
				)
			),
		);

		return array_merge(
			$image_fields,
			$gallery_settings_fields
		);
	}

	/**
	 * Gets the available intermediate image size names.
	 *
	 * @return array An array of image size names.
	 */
	public function get_available_image_sizes() {
		$intermediate_sizes = get_intermediate_image_sizes();
		$sizes              = array(
			'full' => esc_html__( 'Full', 'squad-modules-for-divi' ),
		);

		foreach ( $intermediate_sizes as $size ) {
			$size_label     = str_replace( array( '-', '_' ), ' ', str_replace( array( 'et-pb-' ), '', $size ) );
			$sizes[ $size ] = ucfirst( $size_label );
		}

		return $sizes;
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
		// Show a notice message in the frontend if the list item is empty.
		if ( '' === $this->prop( 'gallery_ids', '' ) ) {
			return sprintf(
				'<div class="disq_notice">%s</div>',
				esc_html__( 'Add one or more image(s).', 'squad-modules-for-divi' )
			);
		}

		if ( ! empty( $this->prop( 'gallery_images_gap', '' ) ) ) {
			$images_gap = $this->prop( 'gallery_images_gap', '' );
			self::set_style(
				$this->slug,
				array(
					'selector'    => "$this->main_css_element .gallery-images a",
					'declaration' => sprintf( 'padding-left: %1$s; padding-bottom: %1$s;', $images_gap ),
				)
			);
		}

		if ( ! empty( $this->prop( 'gallery_ids', '' ) ) ) {
			wp_enqueue_script( 'disq-vendor-imagesloaded' );
			wp_enqueue_script( 'disq-vendor-isotope' );

			if ( 'on' === $this->prop( 'show_in_lightbox', 'off' ) ) {
				wp_enqueue_script( 'disq-vendor-lightgallery' );
			}

			wp_enqueue_script( 'disq-module-gallery' );
		}

		return self::get_gallery_html( array_merge( $attrs, $this->props ) );
	}

	/**
	 * Get attachment html data for gallery module
	 *
	 * @param array $args Gallery Options.
	 *
	 * @return string|null Attachments data
	 */
	public function get_gallery_html( $args = array() ) {
		// Get gallery item data.
		$attachments = self::get_gallery(
			array(
				'gallery_ids'     => $args['gallery_ids'],
				'gallery_orderby' => $args['gallery_orderby'],
				'thumbnail_size'  => $args['thumbnail_size'],
			)
		);

		if ( empty( $attachments ) ) {
			return '';
		}

		$gallery_options  = array(
			'speed'   => 500,
			'plugins' => apply_filters( 'divi_squad_module_gallery_plugins', array( 'fullscreen', 'thumbnail' ) ),
		);
		$gallery_settings = wp_array_slice_assoc( $args, array( 'show_in_lightbox' ) );

		ob_start();

		print sprintf( '<div class="gallery-images" data-setting=\'%s\'>', wp_json_encode( array_merge( $gallery_options, $gallery_settings ) ) );

		foreach ( $attachments as $attachment ) {
			$image_attrs      = array( 'alt' => $attachment->image_alt_text );
			$attachment_class = et_pb_media_options()->get_image_attachment_class( $args, '', $attachment->ID );

			if ( ! empty( $attachment_class ) ) {
				$image_attrs['class'] = implode( ' ', array( esc_attr( $attachment_class ) ) );
			}

			print sprintf(
				'<a href="%1$s" data-lg-size="%3$s" title="" data-pinterest-text="%2$s" data-tweet-text="%2$s" class="gallery-item" data-src="%1$s" data-sub-html="">',
				esc_url( $attachment->image_src_full[0] ),
				esc_attr( $attachment->post_excerpt ),
				esc_attr( $attachment->lg_size )
			);

			$this->render_image( $attachment->image_src_thumb[0], $image_attrs );

			print '</a>';
		}

		print '</div>';

		return ob_get_clean();
	}

	/**
	 * Get attachment data for gallery module
	 *
	 * @param array $args             Gallery Options.
	 * @param array $conditional_tags Additional conditionals tags.
	 * @param array $current_page     Current page.
	 *
	 * @return array|WP_Post[] Attachments data
	 */
	static function get_gallery( $args = array(), $conditional_tags = array(), $current_page = array() ) { //phpcs:ignore
		$attachments = array();

		$defaults = array(
			'gallery_ids'     => array(),
			'thumbnail_size'  => 'thumbnail',
			'gallery_orderby' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$attachments_args = array(
			'include'        => $args['gallery_ids'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'post__in',
		);

		// Woo Gallery module shouldn't display placeholder image when no Gallery image is available.
		if ( isset( $args['attachment_id'] ) ) {
			$attachments_args['attachment_id'] = $args['attachment_id'];
		}

		if ( 'rand' === $args['gallery_orderby'] ) {
			$attachments_args['orderby'] = 'rand';
		}

		$_attachments = get_posts( $attachments_args );

		foreach ( $_attachments as $key => $attachment ) {
			$attachments[ $key ]                    = $attachment;
			$attachments[ $key ]->image_title       = $attachment->post_title;
			$attachments[ $key ]->image_caption     = $attachment->post_excerpt;
			$attachments[ $key ]->image_description = $attachment->post_content;
			$attachments[ $key ]->image_href        = get_permalink( $attachment );
			$attachments[ $key ]->image_src_full    = wp_get_attachment_image_src( $attachment->ID, 'full' );
			$attachments[ $key ]->image_src_thumb   = wp_get_attachment_image_src( $attachment->ID, $args['thumbnail_size'] );
			$attachments[ $key ]->image_alt_text    = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );

			// Collect image sizes.
			$image_meta = wp_get_attachment_metadata( $attachment->ID );
			$image_size = _wp_get_image_size_from_meta( 'full', $image_meta );

			$attachments[ $key ]->lg_size = implode( '-', $image_size );
		}

		return $attachments;
	}
}

new ImageGallery();
