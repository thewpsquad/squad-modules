<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Pricing Table Item (child) Module (Divi 5 / Block API).
 *
 * A single pricing-plan card with schema.org Product markup.
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
use DiviSquad\Core\Supports\Polyfills\Str;
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
use function esc_url;
use function explode;
use function is_array;
use function ltrim;
use function trim;

/**
 * Pricing Table Item (child) module class.
 *
 * @since 4.2.0
 */
class Pricing_Table_Item extends Module {

	/**
	 * Relative path to the generated Pricing Table Item module.json metadata folder.
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/pricing-table-item/';
	}

	/**
	 * Add CSS classnames to the module wrapper.
	 *
	 * @param array<string, mixed> $args Classnames arguments provided by Divi.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'squad-pricing-table-item' );
		$args['classnamesInstance']->add(
			ElementClassnames::classnames(
				array( 'attrs' => $args['attrs']['module']['decoration'] ?? array() )
			)
		);
	}

	/**
	 * Register the module's script data.
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
	 * @param array<string, mixed> $args Style arguments provided by Divi.
	 *
	 * @return void
	 */
	public static function module_styles( array $args ): void {
		$attrs       = $args['attrs'] ?? array();
		$elements    = $args['elements'];
		$settings    = $args['settings'] ?? array();
		$order_class = (string) ( $args['orderClass'] ?? '' );
		$plan_attr   = $attrs['plan']['innerContent'] ?? array();

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
								// Per-instance accent colour (button + ribbon background and the
								// featured-card border), scoped to the module order class via
								// Divi's native style pipeline (no inline <style>, no bespoke uid
								// class) — mirrors the Divi 4 %%order_class%% output.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-pricing .squad-pricing__button",
											'attr'                => $plan_attr,
											'declarationFunction' => array( self::class, 'button_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-pricing .squad-pricing__ribbon",
											'attr'                => $plan_attr,
											'declarationFunction' => array( self::class, 'ribbon_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-pricing.is-featured",
											'attr'                => $plan_attr,
											'declarationFunction' => array( self::class, 'featured_card_style_declaration' ),
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
	 * Resolve the plan accent colour from a declaration params payload.
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string Sanitized accent colour, or empty string.
	 */
	protected static function get_accent_color( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		return self::sanitize_css_background( (string) ( $value['accentColor'] ?? '#5E2EFF' ) );
	}

	/**
	 * Call-to-action button declaration (accent background).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function button_style_declaration( array $params ): string {
		$accent = self::get_accent_color( $params );
		if ( '' === $accent ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'background', $accent );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Ribbon declaration (accent background).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function ribbon_style_declaration( array $params ): string {
		$accent = self::get_accent_color( $params );
		if ( '' === $accent ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'background', $accent );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Featured card declaration (accent border colour).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function featured_card_style_declaration( array $params ): string {
		$accent = self::get_accent_color( $params );
		if ( '' === $accent ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'border-color', $accent );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Pricing Plan card on the frontend.
	 *
	 * @param array<string, mixed> $attrs    Block attributes.
	 * @param string               $content  Inner content (unused).
	 * @param WP_Block             $block    Parsed block instance.
	 * @param ModuleElements       $elements ModuleElements instance.
	 *
	 * @return string Rendered HTML.
	 */
	public static function render_callback( array $attrs, string $content, WP_Block $block, $elements ): string {
		try {
			$item = $attrs['plan']['innerContent']['desktop']['value'] ?? array();

			$title       = (string) ( $item['title'] ?? '' );
			$price       = (string) ( $item['price'] ?? '' );
			$period      = (string) ( $item['period'] ?? '' );
			$description = (string) ( $item['description'] ?? '' );
			$ribbon      = (string) ( $item['ribbon'] ?? '' );
			$is_featured = 'on' === ( $item['isFeatured'] ?? 'off' );
			$features    = (string) ( $item['features'] ?? '' );
			$button_text = (string) ( $item['buttonText'] ?? '' );
			$button_url  = (string) ( $item['buttonUrl'] ?? '#' );

			$ribbon_html = '' !== $ribbon ? sprintf( '<div class="squad-pricing__ribbon">%s</div>', esc_html( $ribbon ) ) : '';
			$title_html  = '' !== $title ? sprintf( '<h3 class="squad-pricing__title" itemprop="name">%s</h3>', esc_html( $title ) ) : '';

			$price_html = '';
			if ( '' !== $price ) {
				$period_html = '' !== $period ? sprintf( '<span class="squad-pricing__period">%s</span>', esc_html( $period ) ) : '';
				$price_html  = sprintf( '<div class="squad-pricing__price">%s%s</div>', esc_html( $price ), $period_html );
			}

			$desc_html     = '' !== $description ? sprintf( '<div class="squad-pricing__desc" itemprop="description">%s</div>', esc_html( $description ) ) : '';
			$features_html = self::render_features( $features );

			$button_html = '';
			if ( '' !== $button_text ) {
				$button_text_color = self::sanitize_css_background( (string) ( $item['buttonTextColor'] ?? '#ffffff' ) );
				$button_text_color = '' !== $button_text_color ? $button_text_color : '#ffffff';

				$button_html = sprintf(
					'<a class="squad-pricing__button" href="%s" style="color:%s;">%s</a>',
					esc_url( '' !== $button_url ? $button_url : '#' ),
					esc_attr( $button_text_color ),
					esc_html( $button_text )
				);
			}

			$style_components = $elements instanceof ModuleElements
				? (string) $elements->style_components( array( 'attrName' => 'module' ) )
				: '';

			$card_html = sprintf(
				'<div class="squad-pricing%1$s" itemscope itemtype="https://schema.org/Product">%2$s<div class="squad-pricing__head">%3$s%4$s%5$s</div>%6$s%7$s</div>',
				$is_featured ? ' is-featured' : '',
				$ribbon_html,
				$title_html,
				$price_html,
				$desc_html,
				$features_html,
				$button_html
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
					'children'            => $style_components . $card_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Pricing Table Item module' );

			return '';
		}
	}

	/**
	 * Build the feature list. Lines starting with "-" render as not-included.
	 *
	 * @since 4.2.0
	 *
	 * @param string $features Raw newline-separated feature list.
	 *
	 * @return string
	 */
	protected static function render_features( string $features ): string {
		if ( '' === trim( $features ) ) {
			return '';
		}

		$items = '';
		foreach ( explode( "\n", $features ) as $line ) {
			$line = trim( $line );
			if ( '' === $line ) {
				continue;
			}

			$unavailable = Str::starts_with( $line, '-' );
			$text        = $unavailable ? trim( ltrim( $line, '-' ) ) : $line;

			$items .= sprintf(
				'<li class="squad-pricing__feature %1$s"><span class="squad-pricing__icon" aria-hidden="true">%2$s</span><span class="squad-pricing__feature-text">%3$s</span></li>',
				$unavailable ? 'is-unavailable' : 'is-available',
				$unavailable ? '✕' : '✓',
				esc_html( $text )
			);
		}

		return '' !== $items ? sprintf( '<ul class="squad-pricing__features">%s</ul>', $items ) : '';
	}
}
