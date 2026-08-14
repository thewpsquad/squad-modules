<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Testimonial Module (Divi 5 / Block API).
 *
 * Parent module that lays out a responsive grid of child Testimonial cards.
 *
 * @since   4.2.0
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
use function esc_html__;
use function is_array;
use function max;
use function min;
use function trim;

/**
 * Testimonial parent module class.
 *
 * @since 4.2.0
 */
class Testimonial extends Module {

	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/testimonial/';
	}

	/**
	 * Add module-specific classnames.
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_testimonial' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array( 'attrs' => $args['attrs']['module']['decoration'] ?? array() )
			)
		);
	}

	/**
	 * Set module script data.
	 *
	 * @param array<string, mixed> $args Script data arguments.
	 *
	 * @return void
	 */
	public static function module_script_data( array $args ): void {
		$args['elements']->script_data( array( 'attrName' => 'module' ) );
	}

	/**
	 * Add module styles.
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
		$grid_attr     = $attrs['testimonials']['innerContent'] ?? array();
		$grid_selector = "{$order_class} .squad-testimonials.squad-testimonials--grid";

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
								// Per-instance grid geometry for the testimonials wrapper, scoped
								// to the module order class via Divi's native style pipeline (no
								// inline <style>, no bespoke uid class). Mirrors the D4
								// %%order_class%% output. The two collapse rules keep the hardcoded
								// breakpoints (980px / 767px) of the former inline CSS verbatim;
								// they are not derived from a responsive attribute.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => $grid_selector,
											'attr'                => $grid_attr,
											'declarationFunction' => array( self::class, 'grid_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => $grid_selector,
											'attr'                => $grid_attr,
											'atRules'             => '@media only screen and (max-width: 980px)',
											'declarationFunction' => array( self::class, 'grid_tablet_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => $grid_selector,
											'attr'                => $grid_attr,
											'atRules'             => '@media only screen and (max-width: 767px)',
											'declarationFunction' => array( self::class, 'grid_phone_style_declaration' ),
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
	 * Testimonials grid wrapper declaration (display, column count, gaps).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$columns = max( 1, min( 4, (int) ( $value['columns'] ?? 3 ) ) );
		$col_gap = self::sanitize_css_length( (string) ( $value['columnGap'] ?? '30px' ) );
		$row_gap = self::sanitize_css_length( (string) ( $value['rowGap'] ?? '30px' ) );

		$col_value = '' !== $col_gap ? $col_gap : '30px';
		$row_value = '' !== $row_gap ? $row_gap : '30px';

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );
		$declarations->add( 'display', 'grid' );
		$declarations->add( 'grid-template-columns', "repeat({$columns},minmax(0,1fr))" );
		$declarations->add( 'gap', "{$row_value} {$col_value}" );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Testimonials grid tablet collapse declaration (hardcoded two columns).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_tablet_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );
		$declarations->add( 'grid-template-columns', 'repeat(2,minmax(0,1fr))' );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Testimonials grid phone collapse declaration (hardcoded single column).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_phone_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );
		$declarations->add( 'grid-template-columns', '1fr' );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Testimonial module on the frontend.
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
					esc_html__( 'Add at least one Testimonial.', 'squad-modules-for-divi' )
				);
			}

			$grid_html = sprintf(
				'<div class="squad-testimonials squad-testimonials--grid">%s</div>',
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
					'children'            => $elements->style_components( array( 'attrName' => 'module' ) ) . $grid_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Testimonial module' );

			return '';
		}
	}
}
