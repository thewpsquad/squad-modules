<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Social Share Module (Divi 5 / Block API).
 *
 * Parent module owning the share target, layout, header and global button style.
 *
 * @since   4.0.0
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Version5\Modules\Content;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

if ( ! class_exists( 'ET\Builder\Packages\Module\Module' ) ) {
	return;
}

use DiviSquad\Builder\Version5\Abstracts\Module;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module as DiviModule;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations;
use Throwable;
use WP_Block;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function esc_url_raw;
use function get_bloginfo;
use function get_permalink;
use function get_the_excerpt;
use function get_the_title;
use function home_url;
use function in_array;
use function is_array;
use function is_singular;
use function sanitize_text_field;
use function sanitize_textarea_field;
use function wp_enqueue_script;

/**
 * Social Share parent module class.
 *
 * @since 4.0.0
 */
class Social_Share extends Module {

	/**
	 * Resolved share target shared with child render passes.
	 *
	 * @var array{url: string, title: string, desc: string}
	 */
	public static $share_target = array(
		'url' => '',
		'title' => '',
		'desc' => '',
	);

	/**
	 * Resolved button context shared with child render passes.
	 *
	 * @var array<string, string>
	 */
	public static $button_context = array(
		'style' => 'icon',
		'enable_popup' => 'on',
	);

	/**
	 * Relative path to the generated Social Share module.json metadata folder.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/social-share/';
	}

	/**
	 * Add CSS classnames to the module wrapper.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $args Classnames arguments provided by Divi.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_social_share' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array( 'attrs' => $args['attrs']['module']['decoration'] ?? array() )
			)
		);
	}

	/**
	 * Register the module's script data.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $args Script data arguments provided by Divi.
	 *
	 * @return void
	 */
	public static function module_script_data( array $args ): void {
		$args['elements']->script_data( array( 'attrName' => 'module' ) );
	}

	/**
	 * Register the module's styles.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $args Style arguments provided by Divi.
	 *
	 * @return void
	 */
	public static function module_styles( array $args ): void {
		$attrs       = $args['attrs'] ?? array();
		$elements    = $args['elements'];
		$settings    = $args['settings'] ?? array();
		$order_class = (string) ( $args['orderClass'] ?? '' );
		$share_attr  = $attrs['shareSettings']['innerContent'] ?? array();

		Style::add(
			array(
				'id'            => $args['id'],
				'name'          => $args['name'],
				'orderIndex'    => $args['orderIndex'],
				'storeInstance' => $args['storeInstance'],
				'styles'        => array(
					$elements->style(
						array(
							'attrName'   => 'module',
							'styleProps' => array(
								'disabledOn'     => array(
									'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
								),
								// Per-instance share layout (columns, gap), icon size and button
								// colour/padding, scoped to the module order class through Divi's
								// native style pipeline. Mirrors the former inline <style> block
								// and the Divi 4 %%order_class%% output.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-social-share--inline .squad-social-share__list",
											'attr'                => $share_attr,
											'declarationFunction' => array( self::class, 'inline_list_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-social-share__list",
											'attr'                => $share_attr,
											'declarationFunction' => array( self::class, 'list_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-social-share__icon",
											'attr'                => $share_attr,
											'declarationFunction' => array( self::class, 'icon_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-social-share__btn",
											'attr'                => $share_attr,
											'declarationFunction' => array( self::class, 'button_style_declaration' ),
										),
									),
								),
							),
						)
					),
					CssStyle::style(
						array(
							'selector' => $args['orderClass'],
							'attr' => $attrs['css'] ?? array(),
						)
					),
				),
			)
		);
	}

	/**
	 * Inline (horizontal) list declaration (grid column count).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function inline_list_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$columns = max( 1, min( 8, (int) ( $value['columns'] ?? 4 ) ) );

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'grid-template-columns', sprintf( 'repeat(%d,minmax(0,max-content))', $columns ) );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Button list declaration (gap between buttons).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function list_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$gap = self::sanitize_css_length( (string) ( $value['itemGap'] ?? '10px' ) );
		if ( '' === $gap ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'gap', $gap );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Icon declaration (icon size).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function icon_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$icon_size = self::sanitize_css_length( (string) ( $value['iconSize'] ?? '18px' ) );
		if ( '' === $icon_size ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'font-size', $icon_size );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Share button declaration (icon colour, background, padding).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function button_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);

		$icon_color = self::sanitize_css_background( (string) ( $value['iconColor'] ?? '#ffffff' ) );
		if ( '' !== $icon_color ) {
			$declarations->add( 'color', $icon_color );
		}

		// Only when non-empty; the child brand colour is the default background.
		$button_bg = self::sanitize_css_background( (string) ( $value['buttonBg'] ?? '' ) );
		if ( '' !== $button_bg ) {
			$declarations->add( 'background-color', $button_bg );
		}

		$button_padding = self::sanitize_css_length( (string) ( $value['buttonPadding'] ?? '12px' ) );
		if ( '' !== $button_padding ) {
			$declarations->add( 'padding', $button_padding );
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Social Share wrapper around its child share buttons.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $attrs                 Block attributes.
	 * @param string               $child_modules_content Inner (child) block content.
	 * @param WP_Block             $block                 Parsed block instance.
	 * @param ModuleElements       $elements              ModuleElements instance.
	 *
	 * @return string Rendered HTML.
	 */
	public static function render_callback( array $attrs, string $child_modules_content, WP_Block $block, $elements ): string {
		try {
			if ( '' === trim( $child_modules_content ) ) {
				return sprintf(
					'<div class="squad-notice">%s</div>',
					esc_html__( 'Add at least one Social Share Item.', 'squad-modules-for-divi' )
				);
			}

			$inner = $attrs['shareSettings']['innerContent']['desktop']['value'] ?? array();

			$enable_popup = 'off' === ( $inner['enablePopup'] ?? 'on' ) ? 'off' : 'on';
			$style        = 'icon_text' === ( $inner['buttonStyle'] ?? 'icon' ) ? 'icon_text' : 'icon';

			if ( 'on' === $enable_popup ) {
				wp_enqueue_script( 'squad-module-social-share' );
			}

			self::$share_target   = self::resolve_share_target( $inner );
			self::$button_context = array(
				'style' => $style,
				'enable_popup' => $enable_popup,
			);

			$orientation = 'stacked' === ( $inner['orientation'] ?? 'inline' ) ? 'stacked' : 'inline';
			$shape       = (string) ( $inner['buttonShape'] ?? 'rounded' );
			$shape       = in_array( $shape, array( 'square', 'rounded', 'circle' ), true ) ? $shape : 'rounded';
			$hover       = (string) ( $inner['hoverEffect'] ?? 'fill' );
			$hover       = in_array( $hover, array( 'none', 'fill', 'lift', 'scale' ), true ) ? $hover : 'fill';

			$header_html = '';
			if ( 'on' === ( $inner['headerShow'] ?? 'off' ) ) {
				$title    = sanitize_text_field( (string) ( $inner['headerTitle'] ?? '' ) );
				$subtitle = sanitize_text_field( (string) ( $inner['headerSubtitle'] ?? '' ) );
				$head     = '';
				if ( '' !== $title ) {
					$head .= sprintf( '<h3 class="squad-social-share__header-title">%s</h3>', esc_html( $title ) );
				}
				if ( '' !== $subtitle ) {
					$head .= sprintf( '<p class="squad-social-share__header-subtitle">%s</p>', esc_html( $subtitle ) );
				}
				if ( '' !== $head ) {
					$header_html = sprintf( '<div class="squad-social-share__header">%s</div>', $head );
				}
			}

			$style_components = $elements instanceof ModuleElements
				? (string) $elements->style_components( array( 'attrName' => 'module' ) )
				: '';

			$wrapper_html = sprintf(
				'<div class="squad-social-share squad-social-share--%1$s squad-social-share--shape-%2$s squad-social-share--hover-%3$s squad-social-share--style-%4$s">%5$s<div class="squad-social-share__list">%6$s</div></div>',
				esc_attr( $orientation ),
				esc_attr( $shape ),
				esc_attr( $hover ),
				esc_attr( $style ),
				$header_html,
				$child_modules_content
			);

			return DiviModule::render(
				array(
					'orderIndex'          => $block->parsed_block['orderIndex'],
					'storeInstance'       => $block->parsed_block['storeInstance'],
					'attrs'               => $attrs,
					'elements'            => $elements,
					'id'                  => $block->parsed_block['id'],
					'name'                => $block->block_type->name,
					'moduleCategory'      => $block->block_type->category,
					'classnamesFunction'  => array( static::class, 'module_classnames' ),
					'stylesComponent'     => array( static::class, 'module_styles' ),
					'scriptDataComponent' => array( static::class, 'module_script_data' ),
					'children'            => $style_components . $wrapper_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Social Share module' );

			return '';
		}
	}

	/**
	 * Resolve the share target from the parent inner-content attrs.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $inner Parent inner content.
	 *
	 * @return array{url: string, title: string, desc: string}
	 */
	public static function resolve_share_target( array $inner ): array {
		$source = 'custom' === ( $inner['shareSource'] ?? 'current' ) ? 'custom' : 'current';

		$current_url   = is_singular() ? (string) get_permalink() : home_url( '/' );
		$current_title = is_singular() ? (string) get_the_title() : (string) get_bloginfo( 'name' );
		$current_desc  = is_singular() ? (string) get_the_excerpt() : '';

		if ( 'custom' === $source ) {
			$url   = esc_url_raw( (string) ( $inner['customUrl'] ?? '' ) );
			$title = sanitize_text_field( (string) ( $inner['customTitle'] ?? '' ) );
			$desc  = sanitize_textarea_field( (string) ( $inner['customDesc'] ?? '' ) );

			if ( '' === $url ) {
				$url = esc_url_raw( $current_url );
			}
			if ( '' === $title ) {
				$title = $current_title;
			}
			if ( '' === $desc ) {
				$desc = $current_desc;
			}

			return array(
				'url' => $url,
				'title' => $title,
				'desc' => $desc,
			);
		}

		return array(
			'url'   => esc_url_raw( $current_url ),
			'title' => $current_title,
			'desc'  => $current_desc,
		);
	}
}
