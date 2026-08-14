<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Image Hotspots Module (Divi 5 / Block API).
 *
 * Native Divi 5 parent module. Accepts Image Hotspot Pin child blocks and wraps
 * them in the same `.squad-hotspots` canvas emitted by the Divi 4 module, so
 * output is identical across builders. Pure CSS layout + a tiny vanilla frontend
 * engine (`image-hotspots.ts`). No external lib dependency.
 *
 * @since   4.3.0
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

use DiviSquad\Builder\Shared\Modules\Media\Image_Hotspots\Image_Hotspots_Helper;
use DiviSquad\Builder\Version5\Abstracts\Module;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module as DiviModule;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\StyleLibrary\Utils\StyleDeclarations;
use Throwable;
use WP_Block;
use function is_array;
use function wp_enqueue_script;

/**
 * Image Hotspots parent module class.
 *
 * @since 4.3.0
 */
class Image_Hotspots extends Module {

	/**
	 * Relative path to the generated module.json metadata folder.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/image-hotspots/';
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
		$args['classnamesInstance']->add( 'disq_image_hotspots' );
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
		$hotspots_attr = $attrs['imageHotspots']['innerContent'] ?? array();

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
								// Per-instance pin (marker) colour, scoped to the module order
								// class via Divi's native style pipeline (no inline <style>, no
								// bespoke uid class). Mirrors the Divi 4 module's
								// `%%order_class%% .squad-hotspots__marker { background-color }`.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-hotspots__marker",
											'attr'                => $hotspots_attr,
											'declarationFunction' => array( self::class, 'pin_color_style_declaration' ),
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
	 * Pin (marker) colour declaration.
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function pin_color_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$pin_color = self::sanitize_css_background( (string) ( $value['pinColor'] ?? '' ) );
		if ( '' === $pin_color ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'background-color', $pin_color );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render callback for the Image Hotspots module.
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
			wp_enqueue_script( 'squad-module-image-hotspots' );

			// Parent settings are packed under the `imageHotspots.innerContent` group
			// (same convention as Logo_Carousel's `carousel.innerContent`).
			$inner = $attrs['imageHotspots']['innerContent']['desktop']['value'] ?? array();

			$config = array(
				'image'    => self::resolve_upload_url( $inner['image'] ?? '' ),
				'imageAlt' => (string) ( $inner['imageAlt'] ?? '' ),
				'trigger'  => (string) ( $inner['trigger'] ?? 'hover' ),
			);

			$canvas_html = Image_Hotspots_Helper::build_canvas( $config, $child_modules_content );

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
					'children'            => $style_components . $canvas_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Image Hotspots module' );

			return '';
		}
	}
}
