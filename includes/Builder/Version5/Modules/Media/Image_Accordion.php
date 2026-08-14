<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Image Accordion Module (Divi 5 / Block API).
 *
 * Parent module: a horizontal or vertical row of image panels. One panel is
 * "active" (expanded) at a time; the rest collapse to a thin strip showing a
 * persistent label. Activating a panel (click or hover) reveals its nested
 * Divi module content overlaid on the panel's image with a scrim behind it.
 *
 * @since   4.4.0
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Version5\Modules\Media;

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
use function absint;
use function esc_attr;
use function esc_html__;
use function in_array;
use function is_array;
use function is_string;
use function max;
use function min;
use function preg_quote;
use function preg_replace;
use function preg_replace_callback;
use function sprintf;
use function substr_count;
use function trim;
use function wp_enqueue_script;

/**
 * Image Accordion parent module class.
 *
 * @since 4.4.0
 */
class Image_Accordion extends Module {

	/**
	 * @since 4.4.0
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/image-accordion/';
	}

	/**
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_image_accordion' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array( 'attrs' => $args['attrs']['module']['decoration'] ?? array() )
			)
		);
	}

	/**
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
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $args Style arguments.
	 *
	 * @return void
	 */
	public static function module_styles( array $args ): void {
		$attrs          = $args['attrs'] ?? array();
		$elements       = $args['elements'];
		$settings       = $args['settings'] ?? array();
		$order_class    = (string) ( $args['orderClass'] ?? '' );
		$accordion_attr = $attrs['accordion']['innerContent'] ?? array();

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
								// Per-instance panel-sizing / overlay custom properties on the
								// accordion wrapper, scoped to the module order class via Divi's
								// native style pipeline (mirrors the former inline <style> block
								// and the Divi 4 %%order_class%% output).
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-image-accordion",
											'attr'                => $accordion_attr,
											'declarationFunction' => array( self::class, 'panel_style_declaration' ),
										),
									),
								),
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
	 * Panel-sizing / overlay custom properties for the accordion wrapper.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function panel_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );

		$collapsed = self::sanitize_css_length( (string) ( $value['collapsedSize'] ?? '60px' ), '60px' );
		$declarations->add( '--squad-ia-collapsed', $collapsed );

		$overlay_bg = self::sanitize_css_background( (string) ( $value['overlayBackground'] ?? 'rgba(0,0,0,0.6)' ) );
		if ( '' !== $overlay_bg ) {
			$declarations->add( '--squad-ia-overlay-bg', $overlay_bg );
		}

		$opacity_pct = max( 0, min( 100, absint( $value['overlayOpacity'] ?? 60 ) ) );
		$declarations->add( '--squad-ia-overlay-opacity', (string) ( $opacity_pct / 100 ) );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Image Accordion module on the frontend.
	 *
	 * @since 4.4.0
	 *
	 * @param array<string, mixed> $attrs                 Block attributes.
	 * @param string               $child_modules_content Rendered (concatenated) child panel HTML.
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
					esc_html__( 'Add at least one Image Accordion Item.', 'squad-modules-for-divi' )
				);
			}

			wp_enqueue_script( 'squad-module-image-accordion' );

			$inner = $attrs['accordion']['innerContent']['desktop']['value'] ?? array();

			$orientation = (string) ( $inner['orientation'] ?? 'horizontal' );
			$orientation = in_array( $orientation, array( 'horizontal', 'vertical' ), true ) ? $orientation : 'horizontal';

			$trigger = (string) ( $inner['expandTrigger'] ?? 'click' );
			$trigger = in_array( $trigger, array( 'click', 'hover' ), true ) ? $trigger : 'click';

			list( $child_modules_content, $active_index ) = self::mark_active_panel( $child_modules_content, $inner );

			$accordion_html = sprintf(
				'<div class="squad-image-accordion squad-image-accordion--%1$s" data-trigger="%2$s" data-active-index="%3$d">%4$s</div>',
				esc_attr( $orientation ),
				esc_attr( $trigger ),
				$active_index,
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
					'children'            => $style_components . $accordion_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Image Accordion module' );

			return '';
		}
	}

	/**
	 * Number the rendered child panels sequentially (0-indexed) and mark the
	 * default-active one with `is-active` / `aria-expanded="true"`.
	 *
	 * Static equivalent of {@see \DiviSquad\Builder\Version4\Modules\Media\Image_Accordion::mark_active_panel()} —
	 * see that method's docblock for the full rationale.
	 *
	 * @since 4.4.0
	 *
	 * @param string                $content Rendered (concatenated) child panel HTML.
	 * @param array<string, mixed>  $inner   Accordion innerContent values (for `defaultActive`).
	 *
	 * @return array{0: string, 1: int} [$processed_content, $active_index (0-indexed)].
	 */
	protected static function mark_active_panel( string $content, array $inner ): array {
		$panel_count = substr_count( $content, Image_Accordion_Item::INDEX_TOKEN );

		if ( 0 === $panel_count ) {
			return array( $content, 0 );
		}

		$counter = -1;
		$content = (string) preg_replace_callback(
			'/' . preg_quote( Image_Accordion_Item::INDEX_TOKEN, '/' ) . '/',
			static function () use ( &$counter ): string {
				++$counter;

				return (string) $counter;
			},
			$content
		);

		$requested    = max( 1, absint( $inner['defaultActive'] ?? 1 ) );
		$active_index = min( $panel_count, $requested ) - 1;

		$pattern = sprintf(
			'/(<div class="squad-image-accordion__panel)(" data-index="%d" style="[^"]*" tabindex="0" role="button" aria-expanded=")false(")/',
			$active_index
		);

		$content = (string) preg_replace( $pattern, '$1 is-active$2true$3', $content, 1 );

		return array( $content, $active_index );
	}
}
