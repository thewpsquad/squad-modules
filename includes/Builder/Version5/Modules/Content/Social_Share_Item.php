<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Social Share Item (child) Module (Divi 5 / Block API).
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

use DiviSquad\Builder\Shared\Modules\Content\Social_Share\Networks;
use DiviSquad\Builder\Version5\Abstracts\Module;
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
use function esc_html__;
use function esc_url;
use function is_array;
use function sanitize_text_field;
use function sprintf;

/**
 * Social Share Item (child) module class.
 *
 * @since 4.0.0
 */
class Social_Share_Item extends Module {

	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/social-share-item/';
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
		$args['classnamesInstance']->add( 'squad-social-share__item' );
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
		$item_attr   = $attrs['itemSettings']['innerContent'] ?? array();

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
								// Per-item brand/override colours on the share button, scoped to
								// the module order class via Divi's native style pipeline (no
								// inline <style>, no bespoke uid class) — mirrors the former
								// inline rule and the D4 %%order_class%% output.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-social-share__btn",
											'attr'                => $item_attr,
											'declarationFunction' => array( self::class, 'button_style_declaration' ),
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
	 * Share button colour declaration (brand background, optional custom overrides).
	 *
	 * @since 4.0.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function button_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$network = (string) ( $value['network'] ?? 'facebook' );
		$meta    = Networks::get_network( $network );
		if ( null === $meta ) {
			return '';
		}

		$use_custom = 'on' === ( $value['useCustomColors'] ?? 'off' );

		$bg = $meta['color']; // trusted hardcoded hex from Networks registry — no sanitize needed.
		if ( $use_custom ) {
			$override = self::sanitize_css_background( (string) ( $value['bgColorOverride'] ?? '' ) );
			if ( '' !== $override ) {
				$bg = $override;
			}
		}

		$declarations = new StyleDeclarations( array( 'returnType' => 'string', 'important' => false ) );

		if ( '' !== $bg ) {
			$declarations->add( 'background-color', $bg );
		}
		if ( $use_custom ) {
			$icon = self::sanitize_css_background( (string) ( $value['iconColorOverride'] ?? '' ) );
			if ( '' !== $icon ) {
				$declarations->add( 'color', $icon );
			}
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	public static function render_callback( array $attrs, string $content, WP_Block $block, $elements ): string {
		try {
			$item    = $attrs['itemSettings']['innerContent']['desktop']['value'] ?? array();
			$network = (string) ( $item['network'] ?? 'facebook' );

			if ( ! Networks::is_valid( $network ) ) {
				return '';
			}

			$meta = Networks::get_network( $network );
			if ( null === $meta ) {
				return '';
			}

			$target = Social_Share::$share_target;
			$ctx    = Social_Share::$button_context;

			$share_title = '' !== $target['title'] ? $target['title'] : $target['desc'];
			$href        = Networks::build_share_url( $network, $target['url'], $share_title );
			if ( '' === $href ) {
				return '';
			}

			$is_email = Networks::is_email( $network );
			$style    = 'icon_text' === ( $ctx['style'] ?? 'icon' ) ? 'icon_text' : 'icon';

			$label = sanitize_text_field( (string) ( $item['customLabel'] ?? '' ) );
			if ( '' === $label ) {
				$label = $meta['label'];
			}

			$link_attrs = sprintf( 'href="%s"', esc_url( $href ) );
			if ( ! $is_email ) {
				$link_attrs .= ' target="_blank" rel="noopener noreferrer nofollow"';
				if ( 'on' === ( $ctx['enable_popup'] ?? 'on' ) ) {
					$link_attrs .= ' data-squad-share="popup"';
				}
			}

			$style_components = $elements instanceof ModuleElements
				? (string) $elements->style_components( array( 'attrName' => 'module' ) )
				: '';

			$icon_html  = sprintf( '<span class="squad-social-share__icon squad-social-share__icon--%s" aria-hidden="true"></span>', esc_attr( $network ) );
			$label_html = 'icon_text' === $style
				? sprintf( '<span class="squad-social-share__label">%s</span>', esc_html( $label ) )
				: '';

			$btn_html = sprintf(
				'<a class="squad-social-share__btn squad-social-share__btn--%1$s" %2$s aria-label="%3$s">%4$s%5$s</a>',
				esc_attr( $network ),
				$link_attrs,
				/* translators: %s: network label */
				esc_attr( sprintf( esc_html__( 'Share on %s', 'squad-modules-for-divi' ), $meta['label'] ) ),
				$icon_html,
				$label_html
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
					'children'            => $style_components . $btn_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Social Share Item module' );

			return '';
		}
	}

}
