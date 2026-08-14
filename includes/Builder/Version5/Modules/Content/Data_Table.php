<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Data Table Module (Divi 5 / Block API).
 *
 * Native Divi 5 parent module. Accepts Data Table Row child blocks and wraps
 * them in the same `.squad-data-table` shell emitted by the Divi 4 module, so
 * output is identical across builders. Pure CSS layout + a tiny progressive
 * enhancement script (`data-table.ts`) for responsive labels, per-cell
 * highlighting and optional column sorting. No external lib dependency.
 *
 * @since   4.3.0
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

use DiviSquad\Builder\Shared\Modules\Content\Data_Table\Data_Table_Helper;
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
use function wp_enqueue_script;

/**
 * Data Table parent module class.
 *
 * @since 4.3.0
 */
class Data_Table extends Module {

	/**
	 * Relative path to the generated module.json metadata folder.
	 *
	 * @since 4.3.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/data-table/';
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
		$args['classnamesInstance']->add( 'disq_data_table' );
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
		$attrs       = $args['attrs'] ?? array();
		$elements    = $args['elements'];
		$settings    = $args['settings'] ?? array();
		$order_class = (string) ( $args['orderClass'] ?? '' );
		$table_attr  = $attrs['dataTable']['innerContent'] ?? array();

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
								// Per-instance header / cell colours, scoped to the module
								// order class via Divi's native style pipeline — mirrors the
								// Divi 4 `%%order_class%% .squad-data-table__th|__td` output
								// (no inline <style>, no bespoke uid class).
								'advancedStyles' => array(
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-data-table__th",
											'attr'                => $table_attr,
											'declarationFunction' => array( self::class, 'header_cell_style_declaration' ),
										),
									),
									array(
										'componentName' => 'divi/common',
										'props'         => array(
											'selector'            => "{$order_class} .squad-data-table__td",
											'attr'                => $table_attr,
											'declarationFunction' => array( self::class, 'body_cell_style_declaration' ),
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
	 * Header cell declaration (header background colour + header text colour).
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function header_cell_style_declaration( array $params ): string {
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

		$header_bg = self::sanitize_css_background( (string) ( $value['headerBgColor'] ?? '' ) );
		if ( '' !== $header_bg ) {
			$declarations->add( 'background-color', $header_bg );
		}

		$header_text = self::sanitize_css_background( (string) ( $value['headerTextColor'] ?? '' ) );
		if ( '' !== $header_text ) {
			$declarations->add( 'color', $header_text );
		}

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Body cell declaration (cell text colour).
	 *
	 * @since 4.3.0
	 *
	 * @param array<string, mixed> $params Declaration params supplied by Divi.
	 *
	 * @return string
	 */
	public static function body_cell_style_declaration( array $params ): string {
		$value = $params['attrValue'] ?? array();
		if ( ! is_array( $value ) ) {
			return '';
		}

		$cell_text = self::sanitize_css_background( (string) ( $value['cellTextColor'] ?? '' ) );
		if ( '' === $cell_text ) {
			return '';
		}

		$declarations = new StyleDeclarations(
			array(
				'returnType' => 'string',
				'important' => false,
			)
		);
		$declarations->add( 'color', $cell_text );

		$out = $declarations->value();

		return is_string( $out ) ? $out : '';
	}

	/**
	 * Render callback for the Data Table module.
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
			if ( '' === trim( $child_modules_content ) ) {
				return sprintf(
					'<div class="squad-notice">%s</div>',
					esc_html__( 'Add at least one Data Table Row.', 'squad-modules-for-divi' )
				);
			}

			wp_enqueue_script( 'squad-module-data-table' );

			// Parent settings are packed under the `dataTable.innerContent` group
			// (same convention as Timeline's `timeline.innerContent`).
			$inner = $attrs['dataTable']['innerContent']['desktop']['value'] ?? array();

			$highlight_ui = absint( $inner['highlightColumn'] ?? 0 );

			$config = array(
				'headers'         => Data_Table_Helper::split_lines( (string) ( $inner['columns'] ?? '' ) ),
				'responsive'      => (string) ( $inner['responsiveMode'] ?? 'stack' ),
				'highlightColumn' => $highlight_ui > 0 ? $highlight_ui - 1 : -1,
				'sticky'          => 'on' === (string) ( $inner['stickyHeader'] ?? 'off' ) ? 'on' : 'off',
				'striped'         => 'on' === (string) ( $inner['stripedRows'] ?? 'on' ) ? 'on' : 'off',
				'sortable'        => 'on' === (string) ( $inner['sortable'] ?? 'off' ) ? 'on' : 'off',
				'ribbon'          => (string) ( $inner['ribbonText'] ?? '' ),
			);

			$data_table_html = Data_Table_Helper::build_table( $config, $child_modules_content );

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
					'children'            => $style_components . $data_table_html,
				)
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to render Divi 5 Data Table module' );

			return '';
		}
	}
}
