<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Logo Grid Module (Divi 5 / Block API).
 *
 * Static CSS Grid of logos with hover effects and optional per-logo links.
 * No JavaScript dependency.
 *
 * @since   4.0.0
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
use function esc_html__;
use function is_array;

/**
 * Logo Grid parent module class.
 *
 * @since 4.0.0
 */
class Logo_Grid extends Module {

	/**
	 * Relative path to the generated module.json metadata folder.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/logo-grid/';
	}

	/**
	 * Add the module classnames.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_logo_grid' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array(
					'attrs' => $args['attrs']['module']['decoration'] ?? array(),
				)
			)
		);
	}

	/**
	 * Assign the module's frontend script data.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $args Script data arguments.
	 *
	 * @return void
	 */
	public static function module_script_data( array $args ): void {
		$args['elements']->script_data(
			array(
				'attrName' => 'module',
			)
		);
	}

	/**
	 * Register the module style declarations, including the grid geometry, logo
	 * sizing and hover effects scoped to the module order class.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $args Style arguments.
	 *
	 * @return void
	 */
	public static function module_styles( array $args ): void {
		$attrs       = $args['attrs'] ?? array();
		$elements    = $args['elements'];
		$settings    = $args['settings'] ?? array();
		$order_class = (string) ( $args['orderClass'] ?? '' );
		$grid_attr   = $attrs['grid']['innerContent'] ?? array();

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
								// Grid geometry, logo sizing and hover effects, scoped to the
								// module order class via Divi's native style pipeline (no inline
								// <style>, no bespoke uid class). Mirrors the Divi 4
								// `%%order_class%% .squad-logo-grid` output, which Divi 5 does
								// not substitute automatically.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-logo-grid",
											'attr'                => $grid_attr,
											'declarationFunction' => array( self::class, 'grid_style_declaration' ),
										),
									),
									// Tablet/phone column counts come from dedicated non-responsive
									// sub-attributes (`columnsTablet`/`columnsPhone`) rather than
									// Divi breakpoint values, so the hardcoded breakpoints of the
									// Divi 4 module (`max_width_980` / `max_width_767`) are carried
									// over verbatim through `atRules`.
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-logo-grid",
											'attr'                => $grid_attr,
											'atRules'             => '@media only screen and (max-width: 980px)',
											'declarationFunction' => array( self::class, 'grid_columns_tablet_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-logo-grid",
											'attr'                => $grid_attr,
											'atRules'             => '@media only screen and (max-width: 767px)',
											'declarationFunction' => array( self::class, 'grid_columns_phone_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-logo-grid__logo",
											'attr'                => $grid_attr,
											'declarationFunction' => array( self::class, 'logo_sizing_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-logo-grid__logo",
											'attr'                => $grid_attr,
											'declarationFunction' => array( self::class, 'logo_hover_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-logo-grid__item:hover .squad-logo-grid__logo",
											'attr'                => $grid_attr,
											'declarationFunction' => array( self::class, 'logo_hover_state_style_declaration' ),
										),
									),
								),
							),
						)
					),
					CssStyle::style(
						array(
							'selector' => $args['orderClass'],
							'attr'     => $attrs['css'] ?? array(),
						)
					),
				),
			)
		);
	}

	/**
	 * Grid layout declaration (display, column count, gap).
	 *
	 * @since 4.0.0
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

		$columns = max( 1, absint( $value['columns'] ?? 4 ) );
		$gap     = max( 0, absint( $value['gap'] ?? 30 ) );

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important'  => false,
			)
		);
		$declarations->add( 'display', 'grid' );
		$declarations->add( 'grid-template-columns', "repeat({$columns},1fr)" );
		$declarations->add( 'gap', "{$gap}px" );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Tablet column count declaration (emitted inside the 980px at-rule).
	 *
	 * Falls back to the desktop column count when no tablet count is set.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_columns_tablet_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) || 'desktop' !== ( $params['breakpoint'] ?? 'desktop' ) ) {
			return '';
		}

		$columns_desktop = max( 1, absint( $value['columns'] ?? 4 ) );
		$columns         = max( 1, absint( $value['columnsTablet'] ?? $columns_desktop ) );

		return self::grid_columns_declaration( $columns );
	}

	/**
	 * Phone column count declaration (emitted inside the 767px at-rule).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function grid_columns_phone_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) || 'desktop' !== ( $params['breakpoint'] ?? 'desktop' ) ) {
			return '';
		}

		$columns = max( 1, absint( $value['columnsPhone'] ?? 2 ) );

		return self::grid_columns_declaration( $columns );
	}

	/**
	 * Build a `grid-template-columns` declaration for a column count.
	 *
	 * @since 4.0.0
	 *
	 * @param int $columns Number of columns.
	 *
	 * @return string
	 */
	protected static function grid_columns_declaration( int $columns ): string {
		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important'  => false,
			)
		);
		$declarations->add( 'grid-template-columns', "repeat({$columns},1fr)" );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Logo sizing declaration (max-width, max-height).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function logo_sizing_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$max_width  = self::sanitize_css_length( (string) ( $value['logoMaxWidth'] ?? '160px' ) );
		$max_height = self::sanitize_css_length( (string) ( $value['logoMaxHeight'] ?? '80px' ) );

		if ( '' === $max_width && '' === $max_height ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important'  => false,
			)
		);
		if ( '' !== $max_width ) {
			$declarations->add( 'max-width', $max_width );
		}
		if ( '' !== $max_height ) {
			$declarations->add( 'max-height', $max_height );
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Resting hover-effect declaration for the logo image.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function logo_hover_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important'  => false,
			)
		);

		switch ( (string) ( $value['hoverEffect'] ?? 'grayscale' ) ) {
			case 'grayscale':
				$declarations->add( 'filter', 'grayscale(100%)' );
				$declarations->add( 'transition', 'filter .3s ease' );
				break;

			case 'opacity':
				$opacity = max( 0.0, min( 1.0, (float) ( $value['hoverOpacity'] ?? '0.5' ) ) );
				$declarations->add( 'opacity', (string) $opacity );
				$declarations->add( 'transition', 'opacity .3s ease' );
				break;

			case 'zoom':
				$declarations->add( 'transition', 'transform .3s ease' );
				break;

			default:
				return '';
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Hovered-state declaration for the logo image.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function logo_hover_state_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important'  => false,
			)
		);

		switch ( (string) ( $value['hoverEffect'] ?? 'grayscale' ) ) {
			case 'grayscale':
				$declarations->add( 'filter', 'grayscale(0%)' );
				break;

			case 'opacity':
				$declarations->add( 'opacity', '1' );
				break;

			case 'zoom':
				$declarations->add( 'transform', 'scale(1.1)' );
				break;

			default:
				return '';
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render callback for the Logo Grid module.
	 *
	 * @since 4.0.0
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
					esc_html__( 'Add at least one Logo Grid Item.', 'squad-modules-for-divi' )
				);
			}

			$grid_html = sprintf(
				'<div class="squad-logo-grid">%s</div>',
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
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Logo Grid module' );

			return '';
		}
	}
}
