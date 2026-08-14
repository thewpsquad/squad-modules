<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Skill Bar Item (child) Module (Divi 5 / Block API).
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
use function absint;
use function esc_html;
use function is_array;

/**
 * Skill Bar Item (child) module class.
 *
 * @since 4.0.0
 */
class Skill_Bar_Item extends Module {

	/**
	 * Relative path to the generated Skill Bar Item module.json metadata folder.
	 *
	 * @since 4.0.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/skill-bar-item/';
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
		$args['classnamesInstance']->add( 'squad-skill-bar__item' );
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
		$item_attr   = $attrs['slideItem']['innerContent'] ?? array();

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
								// Per-instance bar geometry/colours, scoped to the module
								// order class via Divi's native style pipeline (no inline
								// <style>, no bespoke uid class).
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-skill-bar__wrapper",
											'attr'                => $item_attr,
											'declarationFunction' => array( self::class, 'bar_wrapper_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-skill-bar__fill",
											'attr'                => $item_attr,
											'declarationFunction' => array( self::class, 'bar_fill_style_declaration' ),
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
	 * Fill-track wrapper declaration (height, radius, track background).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function bar_wrapper_style_declaration( array $params ): string {
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

		$height = self::sanitize_css_length( (string) ( $value['barHeight'] ?? '30px' ) );
		$radius = self::sanitize_css_length( (string) ( $value['barRadius'] ?? '40px' ) );

		$track = self::sanitize_css_background( (string) ( $value['trackGradient'] ?? '' ) );
		if ( '' === $track ) {
			$track = self::sanitize_css_background( (string) ( $value['trackColor'] ?? '#dddddd' ) );
		}

		if ( '' !== $height ) {
			$declarations->add( 'height', $height );
		}
		if ( '' !== $radius ) {
			$declarations->add( 'border-radius', $radius );
		}
		if ( '' !== $track ) {
			$declarations->add( 'background', $track );
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Fill (progress) declaration (fill background).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function bar_fill_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$fill = self::sanitize_css_background( (string) ( $value['fillGradient'] ?? '' ) );
		if ( '' === $fill ) {
			$fill = self::sanitize_css_background( (string) ( $value['fillColor'] ?? '#5E2EFF' ) );
		}
		if ( '' === $fill ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'background', $fill );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render a single skill bar with its clamped level and optional name/percentage text.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $attrs    Block attributes.
	 * @param string               $content  Inner block content (unused).
	 * @param WP_Block             $block    Parsed block instance.
	 * @param ModuleElements       $elements ModuleElements instance.
	 *
	 * @return string Rendered HTML.
	 */
	public static function render_callback( array $attrs, string $content, WP_Block $block, $elements ): string {
		try {
			$item       = $attrs['slideItem']['innerContent']['desktop']['value'] ?? array();
			$level      = max( 0, min( 100, absint( $item['level'] ?? 70 ) ) );
			$use_name   = 'off' !== ( $item['useName'] ?? 'on' );
			$hide_level = 'on' === ( $item['hideLevel'] ?? 'off' );
			$placement  = 'outside' === ( $item['textPlacement'] ?? 'inside' ) ? 'outside' : 'inside';

			$name_html  = $use_name ? sprintf( '<span class="squad-skill-bar__name">%s</span>', esc_html( $item['name'] ?? '' ) ) : '';
			$level_html = ! $hide_level ? sprintf( '<span class="squad-skill-bar__level">%d%%</span>', $level ) : '';
			$text_html  = ( '' !== $name_html || '' !== $level_html )
				? sprintf( '<div class="squad-skill-bar__text squad-skill-bar__text--%1$s">%2$s%3$s</div>', $placement, $name_html, $level_html )
				: '';

			$style_components = $elements instanceof ModuleElements
				? (string) $elements->style_components( array( 'attrName' => 'module' ) )
				: '';

			$bar_html = sprintf(
				'<div class="squad-skill-bar__wrapper"><div class="squad-skill-bar__fill" style="--squad-sb-level:%1$d%%">%2$s</div></div>',
				$level,
				$text_html
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
					'children'            => $style_components . $bar_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Skill Bar Item module' );

			return '';
		}
	}
}
