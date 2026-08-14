<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Pricing Table Module (Divi 5 / Block API).
 *
 * Parent module that lays out a responsive grid of child pricing-plan cards.
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
 * Pricing Table parent module class.
 *
 * @since 4.2.0
 */
class Pricing_Table extends Module {

	/**
	 * Relative path to the generated Pricing Table module.json metadata folder.
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/pricing-table/';
	}

	/**
	 * Add module-specific classnames.
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_pricing_table' );
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
		$attrs       = $args['attrs'] ?? array();
		$elements    = $args['elements'];
		$settings    = $args['settings'] ?? array();
		$order_class = (string) ( $args['orderClass'] ?? '' );
		$plans_attr  = $attrs['plans']['innerContent'] ?? array();
		$grid        = "{$order_class} .squad-pricing-tables--grid";

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
								// Per-instance plans grid (columns + gaps) scoped to the module
								// order class through Divi's native style pipeline, mirroring the
								// Divi 4 `%%order_class%% .squad-pricing-tables--grid` rules that
								// replaced the former inline <style> block and its bespoke uid class.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => $grid,
											'attr'                => $plans_attr,
											'declarationFunction' => array( self::class, 'grid_style_declaration' ),
										),
									),
									// The tablet/phone column counts are fixed (not attribute
									// driven) in Divi 5, so they are emitted through explicit
									// at-rules rather than the attribute's own breakpoints. The
									// breakpoint values match the former hand-written media
									// queries and the Divi 4 `max_width_980` / `max_width_767`
									// media queries.
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => $grid,
											'attr'                => $plans_attr,
											'atRules'             => '@media only screen and (max-width: 980px)',
											'declarationFunction' => array( self::class, 'grid_tablet_columns_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => $grid,
											'attr'                => $plans_attr,
											'atRules'             => '@media only screen and (max-width: 767px)',
											'declarationFunction' => array( self::class, 'grid_phone_columns_style_declaration' ),
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
	 * Plans grid declaration (grid display, desktop column count and gaps).
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
		$col_gap = self::sanitize_css_length( (string) ( $value['columnGap'] ?? '24px' ) );
		$row_gap = self::sanitize_css_length( (string) ( $value['rowGap'] ?? '24px' ) );

		$col_value = '' !== $col_gap ? $col_gap : '24px';
		$row_value = '' !== $row_gap ? $row_gap : '24px';

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'display', 'grid' );
		$declarations->add( 'grid-template-columns', sprintf( 'repeat(%d,minmax(0,1fr))', $columns ) );
		$declarations->add( 'gap', "{$row_value} {$col_value}" );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Plans grid tablet column override (fixed two columns).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_tablet_columns_style_declaration( array $params ): string {
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
		$declarations->add( 'grid-template-columns', 'repeat(2,minmax(0,1fr))' );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Plans grid phone column override (fixed single column).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_phone_columns_style_declaration( array $params ): string {
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
		$declarations->add( 'grid-template-columns', '1fr' );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Pricing Table module on the frontend.
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
					esc_html__( 'Add at least one Pricing Plan.', 'squad-modules-for-divi' )
				);
			}

			$grid_html = sprintf(
				'<div class="squad-pricing-tables squad-pricing-tables--grid">%s</div>',
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
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Pricing Table module' );

			return '';
		}
	}
}
