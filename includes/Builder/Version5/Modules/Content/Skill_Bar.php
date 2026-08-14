<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Skill Bar Module (Divi 5 / Block API).
 *
 * Parent module holding an optional title and animated child skill bars.
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
use function esc_html;
use function esc_html__;
use function in_array;
use function is_array;
use function wp_enqueue_script;

/**
 * Skill Bar parent module class.
 *
 * @since 4.0.0
 */
class Skill_Bar extends Module {

	/**
	 * Locate the generated module.json metadata folder for the Skill Bar module.
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/skill-bar/';
	}

	/**
	 * Add module-specific classnames.
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_skill_bar' );
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
		$bars_attr   = $attrs['bars']['innerContent'] ?? array();

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
								// Per-instance bar/title spacing, scoped to the module order
								// class via Divi's native style pipeline (no inline <style>,
								// no bespoke uid class) — mirrors the Divi 4 module's
								// %%order_class%% spacing output.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-skill-bar .squad-skill-bar__item",
											'attr'                => $bars_attr,
											'declarationFunction' => array( self::class, 'bar_spacing_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-skill-bar__title",
											'attr'                => $bars_attr,
											'declarationFunction' => array( self::class, 'title_spacing_style_declaration' ),
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
	 * Skill bar item spacing declaration (gap between bars).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function bar_spacing_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$bar_gap = self::sanitize_css_length( (string) ( $value['barSpacing'] ?? '20px' ) );
		if ( '' === $bar_gap ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'margin-bottom', $bar_gap );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Title spacing declaration (gap below the module title).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function title_spacing_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$title_gap = self::sanitize_css_length( (string) ( $value['titleSpacing'] ?? '10px' ) );
		if ( '' === $title_gap ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'margin-bottom', $title_gap );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Skill Bar module on the frontend.
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
					esc_html__( 'Add at least one Skill Bar Item.', 'squad-modules-for-divi' )
				);
			}

			wp_enqueue_script( 'squad-module-skill-bar' );

			$inner = $attrs['bars']['innerContent']['desktop']['value'] ?? array();

			$title      = (string) ( $inner['title'] ?? '' );
			$title_html = '';
			if ( '' !== $title ) {
				$level      = (string) ( $inner['titleLevel'] ?? 'h3' );
				$level      = in_array( $level, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ), true ) ? $level : 'h3';
				$title_html = sprintf( '<%1$s class="squad-skill-bar__title">%2$s</%1$s>', $level, esc_html( $title ) );
			}

			$grid_html = sprintf(
				'%1$s<div class="squad-skill-bar">%2$s</div>',
				$title_html,
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
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Skill Bar module' );

			return '';
		}
	}
}
