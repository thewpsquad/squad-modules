<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Comparison List Module (Divi 5 / Block API).
 *
 * Native Divi 5 parent module. Accepts Comparison List Item child blocks and
 * wraps them in the same `.squad-comparison-list` shell emitted by the Divi 4
 * module, so output is identical across builders. Each child owns only its
 * `data-status` attribute, label, and nested content (see
 * `Comparison_List_Item`) — this parent owns the three status icons
 * (included / excluded / neutral) and their colors, and paints the correct
 * glyph onto every row via CSS attribute selectors keyed off `data-status`,
 * plus responsive column count, row gap, divider, and zebra-striping CSS.
 *
 * @since   4.4.0
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
use DiviSquad\Utils\Divi;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module as DiviModule;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations;
use Throwable;
use WP_Block;
use function absint;
use function esc_attr;
use function esc_html__;
use function et_pb_get_extended_font_icon_value;
use function in_array;
use function is_array;
use function max;
use function sprintf;

/**
 * Comparison List parent module class.
 *
 * @since 4.4.0
 */
class Comparison_List extends Module {

	/**
	 * Allowed row status values (must match `Comparison_List_Item::STATUSES`).
	 *
	 * @since 4.4.0
	 *
	 * @var array<int, string>
	 */
	private const STATUSES = array( 'included', 'excluded', 'neutral' );

	/**
	 * Allowed row divider line style tokens (first = fallback).
	 *
	 * @since 4.4.0
	 *
	 * @var array<int, string>
	 */
	private const DIVIDER_STYLES = array( 'solid', 'dashed', 'dotted' );

	/**
	 * Allowed icon position tokens (first = fallback).
	 *
	 * @since 4.4.0
	 *
	 * @var array<int, string>
	 */
	private const ICON_POSITIONS = array( 'left', 'right' );

	/**
	 * Default (ETModules extended-icon value) for the included status: a
	 * check mark.
	 *
	 * @since 4.4.0
	 *
	 * @var string
	 */
	private const DEFAULT_INCLUDED_ICON = '&#x4e;||divi||400';

	/**
	 * Default (ETModules extended-icon value) for the excluded status: a
	 * cross / times.
	 *
	 * @since 4.4.0
	 *
	 * @var string
	 */
	private const DEFAULT_EXCLUDED_ICON = '&#x4d;||divi||400';

	/**
	 * Default (ETModules extended-icon value) for the neutral status: a
	 * minus / dash.
	 *
	 * @since 4.4.0
	 *
	 * @var string
	 */
	private const DEFAULT_NEUTRAL_ICON = '&#x4b;||divi||400';

	/**
	 * Relative path to the generated module.json metadata folder.
	 *
	 * @since 4.4.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/comparison-list/';
	}

	/**
	 * Add the module classnames.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_comparison_list' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array( 'attrs' => $args['attrs']['module']['decoration'] ?? array() )
			)
		);
	}

	/**
	 * Assign the module's frontend script data.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $args Script data arguments.
	 *
	 * @return void
	 */
	public static function module_script_data( array $args ): void {
		$args['elements']->script_data( array( 'attrName' => 'module' ) );
	}

	/**
	 * Register the module style declarations.
	 *
	 * @since 4.4.0
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

		// Same source attribute group the former inline `<style>` builders read from.
		$group_attr = $attrs['comparisonList']['innerContent'] ?? array();

		// `columns` is the only field in this group flagged
		// `features.responsive` (see `module.json`), so it is handed the whole
		// attr group and Divi's own breakpoint iteration re-creates the
		// hand-written `max-width: 980px` / `max-width: 767px` rules natively.
		// Every other field is non-responsive and was read from
		// `…['desktop']['value']` only — printed once, outside any media query
		// — so those rules are handed just the desktop breakpoint to keep that
		// behaviour byte-for-byte.
		$desktop_attr = isset( $group_attr['desktop'] )
			? array( 'desktop' => $group_attr['desktop'] )
			: array();

		// Scoped to the real module order class via Divi's native style
		// pipeline (no inline `<style>`, no bespoke uid class). Selectors mirror
		// the Divi 4 parent's `%%order_class%%` output exactly, so both builders
		// feed the same `--squad-cl-*` custom properties to the shared
		// stylesheet.
		$advanced_styles = array(
			array(
				'componentName' => 'divi/common',
				'props'         => array(
					'selector'            => "{$order_class} .squad-comparison-list",
					'attr'                => $group_attr,
					'declarationFunction' => array( self::class, 'columns_style_declaration' ),
				),
			),
			array(
				'componentName' => 'divi/common',
				'props'         => array(
					'selector'            => "{$order_class} .squad-comparison-list",
					'attr'                => $desktop_attr,
					'declarationFunction' => array( self::class, 'layout_style_declaration' ),
				),
			),
			array(
				'componentName' => 'divi/common',
				'props'         => array(
					'selector'            => "{$order_class} .squad-comparison-list",
					'attr'                => $desktop_attr,
					'declarationFunction' => array( self::class, 'row_style_declaration' ),
				),
			),
		);

		// One glyph rule + one icon-colour rule per status, exactly as the
		// former `get_icon_css()` / `get_row_icon_css()` pair emitted them. The
		// status is taken from the `self::STATUSES` allow-list before it reaches
		// a selector string.
		$icon_declarations = array(
			'included' => array(
				'glyph' => array( self::class, 'included_icon_glyph_style_declaration' ),
				'color' => array( self::class, 'included_icon_color_style_declaration' ),
			),
			'excluded' => array(
				'glyph' => array( self::class, 'excluded_icon_glyph_style_declaration' ),
				'color' => array( self::class, 'excluded_icon_color_style_declaration' ),
			),
			'neutral'  => array(
				'glyph' => array( self::class, 'neutral_icon_glyph_style_declaration' ),
				'color' => array( self::class, 'neutral_icon_color_style_declaration' ),
			),
		);

		foreach ( self::STATUSES as $status ) {
			$advanced_styles[] = array(
				'componentName' => 'divi/common',
				'props'         => array(
					'selector'            => sprintf(
						'%1$s .squad-comparison-list__item[data-status="%2$s"] .squad-comparison-list__icon::before',
						$order_class,
						$status
					),
					'attr'                => $desktop_attr,
					'declarationFunction' => $icon_declarations[ $status ]['glyph'],
				),
			);

			$advanced_styles[] = array(
				'componentName' => 'divi/common',
				'props'         => array(
					'selector'            => sprintf(
						'%1$s .squad-comparison-list__item[data-status="%2$s"]',
						$order_class,
						$status
					),
					'attr'                => $desktop_attr,
					'declarationFunction' => $icon_declarations[ $status ]['color'],
				),
			);
		}

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
								'advancedStyles' => $advanced_styles,
							),
						)
					),
					CssStyle::style(
						array( 'selector' => $args['orderClass'], 'attr' => $attrs['css'] ?? array() )
					),
				),
			)
		);
	}

	/**
	 * Responsive column count custom property (`--squad-cl-columns`).
	 *
	 * Divi calls this once per breakpoint present in the attr group, so the
	 * tablet / phone media queries the former inline `<style>` hand-wrote are
	 * produced natively. The desktop rule was emitted unconditionally (default
	 * `1`); tablet / phone were emitted only when that breakpoint actually
	 * carried a `columns` value — hence the `isset()` guard below.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function columns_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$breakpoint = (string) ( $params['breakpoint'] ?? 'desktop' );
		if ( 'desktop' !== $breakpoint && ! isset( $value['columns'] ) ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );
		$declarations->add( '--squad-cl-columns', (string) max( 1, absint( $value['columns'] ?? 1 ) ) );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Row gap and icon size custom properties. These are consumed by the shared
	 * stylesheet as `var( --squad-cl-row-gap )` / `var( --squad-cl-icon-size )`
	 * rather than being hardcoded here, so the same variable names can be
	 * shared with the Divi 4 output.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function layout_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );

		$declarations->add(
			'--squad-cl-row-gap',
			self::sanitize_css_length( (string) ( $value['rowGap'] ?? '12px' ), '12px' )
		);
		$declarations->add(
			'--squad-cl-icon-size',
			self::sanitize_css_length( (string) ( $value['iconSize'] ?? '20px' ), '20px' )
		);

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Row background, zebra-stripe, and divider custom properties.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function row_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );

		$bg = self::sanitize_css_background( (string) ( $value['rowBackground'] ?? '' ) );
		if ( '' !== $bg ) {
			$declarations->add( '--squad-cl-row-bg', $bg );
		}

		$bg_alt = self::sanitize_css_background( (string) ( $value['rowBackgroundAlt'] ?? '' ) );
		if ( '' !== $bg_alt ) {
			$declarations->add( '--squad-cl-row-bg-alt', $bg_alt );
		}

		if ( 'off' !== (string) ( $value['rowDivider'] ?? 'on' ) ) {
			$divider_color = self::sanitize_css_background( (string) ( $value['rowDividerColor'] ?? '#e5e7eb' ) );
			if ( '' === $divider_color ) {
				$divider_color = '#e5e7eb';
			}
			$declarations->add( '--squad-cl-row-divider-color', $divider_color );

			$declarations->add(
				'--squad-cl-row-divider-width',
				self::sanitize_css_length( (string) ( $value['rowDividerWidth'] ?? '1px' ), '1px' )
			);

			$divider_style = (string) ( $value['rowDividerStyle'] ?? 'solid' );
			$divider_style = in_array( $divider_style, self::DIVIDER_STYLES, true ) ? $divider_style : 'solid';
			$declarations->add( '--squad-cl-row-divider-style', $divider_style );
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Included-row icon glyph (`content` + `font-family`).
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function included_icon_glyph_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();

		return is_array( $value )
			? self::build_icon_glyph_declaration( $value, 'includedIcon', self::DEFAULT_INCLUDED_ICON )
			: '';
	}

	/**
	 * Excluded-row icon glyph (`content` + `font-family`).
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function excluded_icon_glyph_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();

		return is_array( $value )
			? self::build_icon_glyph_declaration( $value, 'excludedIcon', self::DEFAULT_EXCLUDED_ICON )
			: '';
	}

	/**
	 * Neutral-row icon glyph (`content` + `font-family`).
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function neutral_icon_glyph_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();

		return is_array( $value )
			? self::build_icon_glyph_declaration( $value, 'neutralIcon', self::DEFAULT_NEUTRAL_ICON )
			: '';
	}

	/**
	 * Included-row icon colour custom property.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function included_icon_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();

		return is_array( $value )
			? self::build_icon_color_declaration( $value, 'includedIconColor', '#2ecc71' )
			: '';
	}

	/**
	 * Excluded-row icon colour custom property.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function excluded_icon_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();

		return is_array( $value )
			? self::build_icon_color_declaration( $value, 'excludedIconColor', '#e74c3c' )
			: '';
	}

	/**
	 * Neutral-row icon colour custom property.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function neutral_icon_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();

		return is_array( $value )
			? self::build_icon_color_declaration( $value, 'neutralIconColor', '#9aa0a6' )
			: '';
	}

	/**
	 * Build the `content` + `font-family` declaration for one comparison-list
	 * status icon.
	 *
	 * `Comparison_List_Item` deliberately renders an empty, `aria-hidden`
	 * icon span and tags its row with `data-status` (see that class's
	 * `render_callback()`) rather than printing the glyph itself — this
	 * parent owns the icon/color configuration for all three states and
	 * paints every row sharing a status identically via the
	 * `[data-status="…"]` attribute selector, so no HTML string-parsing of
	 * the already-rendered child content is required.
	 *
	 * The author picks each state's icon with the native Divi icon picker
	 * (`type => 'select_icon'`). The raw extended-icon value is resolved to a
	 * glyph exactly as the Divi 4 parent (and `Inline_Content_Item`) do —
	 * `Divi::inject_fa_icons()` (loads the relevant icon font) then
	 * `et_pb_get_extended_font_icon_value()`. Because the resolved glyph is
	 * interpolated into a CSS `content` string (not HTML text, so
	 * `esc_html()` can't apply) it is first run through
	 * `sanitize_css_background()` to strip any character that could break
	 * out of the declaration — the raw picker value is never written to CSS
	 * unvalidated.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $value        Packed `comparisonList.innerContent` desktop values.
	 * @param string               $icon_field   Attr key holding the extended-icon value.
	 * @param string               $default_icon Fallback extended-icon value.
	 *
	 * @return string
	 */
	protected static function build_icon_glyph_declaration( array $value, string $icon_field, string $default_icon ): string {
		$icon_raw = (string) ( $value[ $icon_field ] ?? $default_icon );
		if ( '' === $icon_raw ) {
			$icon_raw = $default_icon;
		}

		Divi::inject_fa_icons( $icon_raw );
		$icon_glyph = (string) et_pb_get_extended_font_icon_value( $icon_raw, true );

		// Strip any CSS-breakout characters before interpolating the
		// resolved glyph into a `content` declaration.
		$icon_glyph = self::sanitize_css_background( $icon_glyph );
		if ( '' === $icon_glyph ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );
		$declarations->add( 'content', '"' . $icon_glyph . '"' );
		$declarations->add( 'font-family', '"ETModules"' );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Build the `--squad-cl-icon-color` declaration for one comparison-list
	 * status.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $value         Packed `comparisonList.innerContent` desktop values.
	 * @param string               $color_field   Attr key holding the icon color.
	 * @param string               $default_color Fallback icon color.
	 *
	 * @return string
	 */
	protected static function build_icon_color_declaration( array $value, string $color_field, string $default_color ): string {
		$color = self::sanitize_css_background( (string) ( $value[ $color_field ] ?? $default_color ) );
		if ( '' === $color ) {
			$color = $default_color;
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );
		$declarations->add( '--squad-cl-icon-color', $color );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render callback for the Comparison List module.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $attrs                 Block attributes.
	 * @param string               $child_modules_content Rendered child HTML.
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
					esc_html__( 'Add at least one Comparison List Item.', 'squad-modules-for-divi' )
				);
			}

			// Parent settings are packed under the `comparisonList.innerContent`
			// group (same convention as Data Table's `dataTable.innerContent`).
			// The design CSS built from this group is emitted by
			// `module_styles()` through Divi's native style pipeline; only the
			// two markup-level tokens are read here.
			$inner = $attrs['comparisonList']['innerContent']['desktop']['value'] ?? array();

			$icon_position = (string) ( $inner['iconPosition'] ?? 'left' );
			$icon_position = in_array( $icon_position, self::ICON_POSITIONS, true ) ? $icon_position : 'left';

			$divider = 'off' === (string) ( $inner['rowDivider'] ?? 'on' ) ? 'off' : 'on';

			$comparison_list_html = sprintf(
				'<div class="squad-comparison-list squad-comparison-list--icon-%1$s" data-divider="%2$s">%3$s</div>',
				esc_attr( $icon_position ),
				esc_attr( $divider ),
				$child_modules_content
			);

			$style_components = $elements instanceof ModuleElements
				? (string) $elements->style_components( array( 'attrName' => 'module' ) )
				: '';

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
					'children'            => $style_components . $comparison_list_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Comparison List module' );

			return '';
		}
	}
}
