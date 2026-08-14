<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Advanced Tabs Module (Divi 5 / Block API).
 *
 * Parent module that turns its child Tab panels into an accessible tabbed
 * interface with horizontal / vertical layouts, an optional mobile accordion,
 * icon tabs and URL-hash deep-linking. The navigation is built on the frontend
 * from the rendered panels.
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
use function esc_attr;
use function esc_html__;
use function in_array;
use function is_array;
use function max;
use function trim;
use function wp_enqueue_script;

/**
 * Advanced Tabs parent module class.
 *
 * @since 4.2.0
 */
class Advanced_Tabs extends Module {

	/**
	 * Relative path to the generated Advanced Tabs module.json metadata folder.
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/advanced-tabs/';
	}

	/**
	 * Add module-specific classnames.
	 *
	 * @param array<string, mixed> $args Classnames arguments.
	 *
	 * @return void
	 */
	public static function module_classnames( array $args ): void {
		$args['classnamesInstance']->add( 'disq_advanced_tabs' );
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
		$tabs_attr   = $attrs['tabs']['innerContent'] ?? array();

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
								// Per-instance tab navigation colours, scoped to the module order
								// class through Divi's native style pipeline (no inline <style>,
								// no bespoke uid class). Mirrors the Divi 4 %%order_class%% output.
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-tabs .squad-tabs__nav-item",
											'attr'                => $tabs_attr,
											'declarationFunction' => array( self::class, 'nav_item_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-tabs .squad-tabs__nav-item.is-active",
											'attr'                => $tabs_attr,
											'declarationFunction' => array( self::class, 'active_nav_item_style_declaration' ),
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
	 * Inactive tab navigation item declaration (text colour).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function nav_item_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$tab_color = self::sanitize_css_background( (string) ( $value['tabTextColor'] ?? '' ) );
		if ( '' === $tab_color ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'color', $tab_color );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Active tab navigation item declaration (background, border colour, text colour).
	 *
	 * @since 4.2.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function active_nav_item_style_declaration( array $params ): string {
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

		$active_bg = self::sanitize_css_background( (string) ( $value['activeBgColor'] ?? '#5E2EFF' ) );
		if ( '' !== $active_bg ) {
			$declarations->add( 'background', $active_bg );
			$declarations->add( 'border-color', $active_bg );
		}

		$active_color = self::sanitize_css_background( (string) ( $value['activeTextColor'] ?? '#ffffff' ) );
		if ( '' !== $active_color ) {
			$declarations->add( 'color', $active_color );
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render the Advanced Tabs module on the frontend.
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
					esc_html__( 'Add at least one Tab.', 'squad-modules-for-divi' )
				);
			}

			wp_enqueue_script( 'squad-module-advanced-tabs' );

			$inner = $attrs['tabs']['innerContent']['desktop']['value'] ?? array();

			$layout = (string) ( $inner['layout'] ?? 'horizontal' );
			$layout = in_array( $layout, array( 'horizontal', 'vertical' ), true ) ? $layout : 'horizontal';

			$align = (string) ( $inner['tabAlignment'] ?? 'left' );
			$align = in_array( $align, array( 'left', 'center', 'right' ), true ) ? $align : 'left';

			$active      = max( 1, (int) ( $inner['activeTab'] ?? 1 ) );
			$accordion   = 'off' !== ( $inner['mobileAccordion'] ?? 'on' );
			$enable_hash = 'on' === ( $inner['enableHash'] ?? 'off' );

			$tabs_html = sprintf(
				'<div class="squad-tabs squad-tabs--%1$s squad-tabs--align-%2$s%3$s" data-active="%4$d" data-accordion="%5$s" data-hash="%6$s"><div class="squad-tabs__nav" role="tablist"></div><div class="squad-tabs__panels">%7$s</div></div>',
				esc_attr( $layout ),
				esc_attr( $align ),
				$accordion ? ' squad-tabs--mobile-accordion' : '',
				$active - 1,
				$accordion ? 'on' : 'off',
				$enable_hash ? 'on' : 'off',
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
					'children'            => $style_components . $tabs_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Advanced Tabs module' );

			return '';
		}
	}
}
