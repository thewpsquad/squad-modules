<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Timeline Module (Divi 5 / Block API).
 *
 * Native Divi 5 parent module. Accepts Timeline Item child blocks and wraps
 * them in the same `.squad-timeline` track emitted by the Divi 4 module, so
 * output is identical across builders. Pure CSS layout + a tiny
 * IntersectionObserver (`timeline.ts`). No external carousel/lib dependency.
 *
 * @since   4.3.0
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

use DiviSquad\Builder\Shared\Modules\Content\Timeline\Timeline_Helper;
use DiviSquad\Builder\Version5\Abstracts\Module;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module as DiviModule;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations;
use Throwable;
use WP_Block;
use function absint;
use function esc_html__;
use function is_array;
use function max;
use function min;
use function wp_enqueue_script;

/**
 * Timeline parent module class.
 *
 * @since 4.3.0
 */
class Timeline extends Module {

	/**
	 * Relative path to the generated module.json metadata folder.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/timeline/';
	}

	/**
	 * Add the module classnames.
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_timeline' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array( 'attrs' => $args['attrs']['module']['decoration'] ?? array() )
			)
		);
	}

	/**
	 * Assign the module's frontend script data.
	 *
	 * @since 4.3.0
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
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $args Style arguments provided by Divi.
	 *
	 * @return void
	 */
	public static function module_styles( array $args ): void {
		$attrs         = $args['attrs'] ?? array();
		$elements      = $args['elements'];
		$settings      = $args['settings'] ?? array();
		$order_class   = (string) ( $args['orderClass'] ?? '' );
		$timeline_attr = $attrs['timeline']['innerContent'] ?? array();

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
								// Per-instance line/marker colours, scoped to the module order
								// class via Divi's native style pipeline (no inline <style>,
								// no bespoke uid class) — mirrors the Divi 4 module's
								// %%order_class%% colour output.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-timeline__line",
											'attr'                => $timeline_attr,
											'declarationFunction' => array( self::class, 'line_color_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-timeline__marker",
											'attr'                => $timeline_attr,
											'declarationFunction' => array( self::class, 'marker_color_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-timeline__dot, {$order_class} .squad-timeline__number, {$order_class} .squad-timeline__icon",
											'attr'                => $timeline_attr,
											'declarationFunction' => array( self::class, 'marker_glyph_color_style_declaration' ),
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
	 * Connecting-line declaration (line background colour).
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function line_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$line_color = self::sanitize_css_background( (string) ( $value['lineColor'] ?? '' ) );
		if ( '' === $line_color ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'background-color', $line_color );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Marker declaration (marker foreground + background colour).
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function marker_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$marker_color = self::sanitize_css_background( (string) ( $value['markerColor'] ?? '' ) );
		if ( '' === $marker_color ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'color', $marker_color );
		$declarations->add( 'background-color', $marker_color );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Marker glyph declaration (dot / number / icon colour).
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function marker_glyph_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$marker_color = self::sanitize_css_background( (string) ( $value['markerColor'] ?? '' ) );
		if ( '' === $marker_color ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'color', $marker_color );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render callback for the Timeline module.
	 *
	 * @since 4.3.0
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
					esc_html__( 'Add at least one Timeline Item.', 'squad-modules-for-divi' )
				);
			}

			wp_enqueue_script( 'squad-module-timeline' );

			// Parent settings are packed under the `timeline.innerContent` group
			// (same convention as Logo_Carousel's `carousel.innerContent`).
			$inner = $attrs['timeline']['innerContent']['desktop']['value'] ?? array();

			$config = array(
				'orientation' => (string) ( $inner['orientation'] ?? 'vertical' ),
				'layout'      => (string) ( $inner['layout'] ?? 'alternating' ),
				'reveal'      => 'off' === (string) ( $inner['revealOnScroll'] ?? 'on' ) ? 'off' : 'on',
				'stagger'     => max( 0, min( 600, absint( $inner['revealStagger'] ?? 120 ) ) ),
			);

			$timeline_html = Timeline_Helper::build_track( $config, $child_modules_content );

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
					'children'            => $style_components . $timeline_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Timeline module' );

			return '';
		}
	}
}
