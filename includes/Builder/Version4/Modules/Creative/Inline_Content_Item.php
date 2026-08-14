<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Inline Content Item (child) Module Class.
 *
 * A single inline element within the Inline Content parent module.
 * Supports five content types: text, icon, image, button, divider.
 * No frontend JS.
 *
 * @since   4.1.0
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Version4\Modules\Creative;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Builder\Shared\Modules\Creative\Inline_Content\Inline_Helper;
use DiviSquad\Builder\Version4\Abstracts\Module\Child_Module;
use DiviSquad\Utils\Divi;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function esc_url;
use function et_pb_get_extended_font_icon_value;
use function in_array;
use function sprintf;
use function wp_kses_post;

/**
 * Inline Content Item (child) module class.
 *
 * @since 4.1.0
 */
class Inline_Content_Item extends Child_Module {

	/**
	 * Initiate Module.
	 *
	 * @since 4.1.0
	 * @return void
	 */
	public function init(): void {
		$this->name   = esc_html__( 'Inline Content Item', 'squad-modules-for-divi' );
		$this->plural = esc_html__( 'Inline Content Items', 'squad-modules-for-divi' );

		$this->slug             = 'disq_inline_content_item';
		$this->vb_support       = 'on';
		$this->main_css_element = "%%order_class%%.{$this->slug}";

		$this->child_title_var          = 'content_type';
		$this->child_title_fallback_var = 'admin_label';

		$this->squad_utils = divi_squad()->d4_module_helper->connect( $this );

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'item_content' => esc_html__( 'Item', 'squad-modules-for-divi' ),
					'link_options' => esc_html__( 'Link', 'squad-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text_style'    => esc_html__( 'Text', 'squad-modules-for-divi' ),
					'icon_style'    => esc_html__( 'Icon', 'squad-modules-for-divi' ),
					'image_style'   => esc_html__( 'Image', 'squad-modules-for-divi' ),
					'divider_style' => esc_html__( 'Divider', 'squad-modules-for-divi' ),
				),
			),
		);

		$this->advanced_fields = array(
			'background'     => false,
			'borders'        => array(
				'default' => divi_squad()->d4_module_helper->selectors_default( $this->main_css_element ),
			),
			'box_shadow'     => array(
				'default' => divi_squad()->d4_module_helper->selectors_default( $this->main_css_element ),
			),
			'margin_padding' => divi_squad()->d4_module_helper->selectors_margin_padding( $this->main_css_element ),
			'fonts'          => array(
				'text_font' => divi_squad()->d4_module_helper->add_font_field(
					esc_html__( 'Text', 'squad-modules-for-divi' ),
					array(
						'font_size'   => array( 'default' => '16px' ),
						'css'         => array( 'main' => "{$this->main_css_element} .squad-inline__text" ),
						'tab_slug'    => 'advanced',
						'toggle_slug' => 'text_style',
					)
				),
			),
			'image_icon'     => false,
			'text'           => false,
			'button'         => false,
			'filters'        => false,
		);
	}

	/**
	 * Declare fields for the module.
	 *
	 * Mirrors the Divi 5 attribute contract in
	 * `src/divi-builder/divi-5/modules/builder/inline-content-item/module.json-source.ts`,
	 * converting the camelCase subNames to Divi 4 snake_case field names and
	 * keeping the exact same defaults.
	 *
	 * @since 4.1.0
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_fields(): array {
		return array(
			'content_type'      => divi_squad()->d4_module_helper->add_select_box_field(
				esc_html__( 'Content Type', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'What this item renders.', 'squad-modules-for-divi' ),
					'options'     => array(
						'text'    => esc_html__( 'Text', 'squad-modules-for-divi' ),
						'icon'    => esc_html__( 'Icon', 'squad-modules-for-divi' ),
						'image'   => esc_html__( 'Image', 'squad-modules-for-divi' ),
						'button'  => esc_html__( 'Button', 'squad-modules-for-divi' ),
						'divider' => esc_html__( 'Divider', 'squad-modules-for-divi' ),
					),
					'default'     => 'text',
					'tab_slug'    => 'general',
					'toggle_slug' => 'item_content',
				)
			),
			'text'              => array(
				'label'           => esc_html__( 'Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Text content for the item.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
				'show_if'         => array( 'content_type' => 'text' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'item_content',
			),
			'icon'              => array(
				'label'            => esc_html__( 'Icon', 'squad-modules-for-divi' ),
				'description'      => esc_html__( 'Divi icon for the item.', 'squad-modules-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => '',
				'show_if'          => array( 'content_type' => 'icon' ),
				'tab_slug'         => 'general',
				'toggle_slug'      => 'item_content',
			),
			'image'             => divi_squad()->d4_module_helper->add_media_upload_field(
				esc_html__( 'Image', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Image URL for the item.', 'squad-modules-for-divi' ),
					'show_if'     => array( 'content_type' => 'image' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'item_content',
				)
			),
			'image_alt'         => array(
				'label'           => esc_html__( 'Image Alt Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Alt text for the image.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
				'show_if'         => array( 'content_type' => 'image' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'item_content',
			),
			'button_text'       => array(
				'label'           => esc_html__( 'Button Text', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Label for the button.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
				'show_if'         => array( 'content_type' => 'button' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'item_content',
			),
			'button_url'        => array(
				'label'           => esc_html__( 'Button URL', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'URL for the button link.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
				'show_if'         => array( 'content_type' => 'button' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'item_content',
			),
			'button_new_window' => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Open Button in New Tab', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Open the button link in a new browser tab.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'show_if'     => array( 'content_type' => 'button' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'item_content',
				)
			),
			'divider_style'     => divi_squad()->d4_module_helper->add_select_box_field(
				esc_html__( 'Divider Style', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Line or dot.', 'squad-modules-for-divi' ),
					'options'     => array(
						'line' => esc_html__( 'Line', 'squad-modules-for-divi' ),
						'dot'  => esc_html__( 'Dot', 'squad-modules-for-divi' ),
					),
					'default'     => 'line',
					'show_if'     => array( 'content_type' => 'divider' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'item_content',
				)
			),
			'use_link'          => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Enable Link', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Wrap text/icon/image in a link. No-op for button/divider.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'tab_slug'    => 'general',
					'toggle_slug' => 'link_options',
				)
			),
			'link_url'          => array(
				'label'           => esc_html__( 'Link URL', 'squad-modules-for-divi' ),
				'description'     => esc_html__( 'Destination URL for the wrapping link.', 'squad-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'default'         => '',
				'show_if'         => array( 'use_link' => 'on' ),
				'tab_slug'        => 'general',
				'toggle_slug'     => 'link_options',
			),
			'link_new_window'   => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Open Link in New Tab', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Open the wrapping link in a new browser tab.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'show_if'     => array( 'use_link' => 'on' ),
					'tab_slug'    => 'general',
					'toggle_slug' => 'link_options',
				)
			),
		);
	}

	/**
	 * Render the module output.
	 *
	 * Produces the same markup as the Divi 5 render callback in
	 * `DiviSquad\Builder\Version5\Modules\Creative\Inline_Content_Item`.
	 *
	 * @since 4.1.0
	 *
	 * @param array<string, mixed> $attrs       List of attributes.
	 * @param string               $content     Content being processed.
	 * @param string               $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ): string {
		$raw_type = (string) $this->prop( 'content_type', 'text' );

		// Validate against the allowlist — never inject raw user values into class names.
		$type = Inline_Helper::is_valid_type( $raw_type ) ? $raw_type : 'text';

		$inner_html = $this->squad_render_type( $type );

		// Empty-content guard: skip the child entirely when required content is empty.
		if ( '' === $inner_html ) {
			return '';
		}

		return sprintf(
			'<span class="squad-inline__item squad-inline__item--%s">%s</span>',
			esc_attr( $type ),
			$inner_html
		);
	}

	/**
	 * Dispatch rendering to the correct per-type method.
	 *
	 * @since 4.1.0
	 *
	 * @param string $type Validated content type.
	 *
	 * @return string Inner HTML (empty = skip child).
	 */
	protected function squad_render_type( string $type ): string {
		switch ( $type ) {
			case 'text':
				return $this->squad_render_text();
			case 'icon':
				return $this->squad_render_icon();
			case 'image':
				return $this->squad_render_image();
			case 'button':
				return $this->squad_render_button();
			case 'divider':
				return $this->squad_render_divider();
			default:
				return '';
		}
	}

	/**
	 * Render the text content type.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	protected function squad_render_text(): string {
		$text = (string) $this->prop( 'text', '' );
		if ( '' === $text ) {
			return '';
		}

		$span = sprintf( '<span class="squad-inline__text">%s</span>', esc_html( $text ) );

		return $this->squad_maybe_wrap_link( $span );
	}

	/**
	 * Render the icon content type.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	protected function squad_render_icon(): string {
		$icon_raw = (string) $this->prop( 'icon', '' );
		if ( '' === $icon_raw ) {
			return '';
		}

		Divi::inject_fa_icons( $icon_raw );

		$icon_value = et_pb_get_extended_font_icon_value( $icon_raw, true );

		$html = sprintf(
			'<span class="squad-inline__icon"><span class="et-pb-icon">%s</span></span>',
			wp_kses_post( $icon_value )
		);

		return $this->squad_maybe_wrap_link( $html );
	}

	/**
	 * Render the image content type.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	protected function squad_render_image(): string {
		$src = (string) $this->prop( 'image', '' );
		if ( '' === $src ) {
			return '';
		}

		$alt = (string) $this->prop( 'image_alt', '' );

		$html = sprintf(
			'<img class="squad-inline__image" src="%s" alt="%s">',
			esc_url( $src ),
			esc_attr( $alt )
		);

		return $this->squad_maybe_wrap_link( $html );
	}

	/**
	 * Render the button content type.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	protected function squad_render_button(): string {
		$button_text = (string) $this->prop( 'button_text', '' );
		$button_url  = (string) $this->prop( 'button_url', '' );

		// Skip when both are empty.
		if ( '' === $button_text && '' === $button_url ) {
			return '';
		}

		$new_window = 'on' === (string) $this->prop( 'button_new_window', 'off' );
		$rel        = Inline_Helper::build_rel( $new_window );

		$link_attrs = sprintf( 'href="%s"', esc_url( $button_url ) );
		if ( $new_window ) {
			$link_attrs .= ' target="_blank"';
		}
		if ( '' !== $rel ) {
			$link_attrs .= sprintf( ' rel="%s"', esc_attr( $rel ) );
		}

		return sprintf(
			'<a class="squad-inline__button et_pb_button" %s>%s</a>',
			$link_attrs,
			esc_html( $button_text )
		);
	}

	/**
	 * Render the divider content type.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	protected function squad_render_divider(): string {
		$raw_style     = (string) $this->prop( 'divider_style', 'line' );
		$divider_style = in_array( $raw_style, array( 'line', 'dot' ), true ) ? $raw_style : 'line';

		return sprintf(
			'<span class="squad-inline__divider squad-inline__divider--%s"></span>',
			esc_attr( $divider_style )
		);
	}

	/**
	 * Optionally wrap the given inner markup in an anchor tag.
	 *
	 * @since 4.1.0
	 *
	 * @param string $inner Inner HTML to wrap.
	 *
	 * @return string Wrapped or unchanged HTML.
	 */
	protected function squad_maybe_wrap_link( string $inner ): string {
		if ( 'on' !== (string) $this->prop( 'use_link', 'off' ) ) {
			return $inner;
		}

		$link_url = (string) $this->prop( 'link_url', '' );
		if ( '' === $link_url ) {
			return $inner;
		}

		$new_window = 'on' === (string) $this->prop( 'link_new_window', 'off' );
		$rel        = Inline_Helper::build_rel( $new_window );

		$attrs = sprintf( 'href="%s"', esc_url( $link_url ) );
		if ( $new_window ) {
			$attrs .= ' target="_blank"';
		}
		if ( '' !== $rel ) {
			$attrs .= sprintf( ' rel="%s"', esc_attr( $rel ) );
		}

		return sprintf( '<a %s>%s</a>', $attrs, $inner );
	}
}
