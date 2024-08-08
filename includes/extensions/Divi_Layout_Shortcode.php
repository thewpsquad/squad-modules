<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Divi Library Shortcode extension class for Divi Squad.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\Extensions;

/**
 * The Divi Library Shortcode class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Divi_Layout_Shortcode extends Extensions {

	/**
	 * The constructor class.
	 */
	public function __construct() {
		parent::__construct();

		// Allow divi layout shortcode in the current installation.
		if ( ! in_array( 'Divi_Library_Shortcode', $this->name_lists, true ) ) {
			// Create New Column in the Divi Library.
			add_filter( 'manage_et_pb_layout_posts_columns', array( $this, 'create_shortcode_column' ), 5 );
			add_action( 'manage_et_pb_layout_posts_custom_column', array( $this, 'shortcode_column_content' ), 5, 2 );

			// Register New Divi Shortcodes.
			if ( ! shortcode_exists( 'disq_divi_library_layout' ) ) {
				add_shortcode( 'disq_divi_library_layout', array( $this, 'shortcode_callback' ) );
			}

			// Add another shortcode if they are not exists.
			if ( ! shortcode_exists( 'divi_library_layout' ) ) {
				add_shortcode( 'divi_library_layout', array( $this, 'shortcode_callback' ) );
			}
		}
	}

	/**
	 * Create New Admin Column
	 *
	 * @param array $columns Exists columns array data.
	 *
	 * @return array
	 */
	public function create_shortcode_column( $columns ) {
		$columns['disq_shortcode_column'] = esc_html__( 'Shortcode', 'squad-modules-for-divi' );

		return $columns;
	}

	/**
	 * Display Shortcode
	 *
	 * @param string $column The current column name.
	 * @param int    $id     The current post id.
	 *
	 * @return void
	 */
	public function shortcode_column_content( $column, $id ) {
		if ( 'disq_shortcode_column' === $column ) {
			printf( '<p>[disq_divi_library_layout id="%s"]</p>', esc_attr( $id ) );
		}
	}

	/**
	 * Create New Shortcode
	 *
	 * @param array|string|mixed $atts The attributes of the current shortcode.
	 *
	 * @return string
	 */
	public function shortcode_callback( $atts ) {
		$attributes    = shortcode_atts( array( 'id' => '*' ), $atts );
		$is_vb_preview = function_exists( 'is_et_pb_preview' ) && is_et_pb_preview();

		if ( $is_vb_preview ) {
			add_filter( 'pre_do_shortcode_tag', array( $this, 'shortcode_set_ajax_module_index' ) );
		}

		$layout_content = do_shortcode( '[et_pb_section global_module="' . esc_attr( (int) $attributes['id'] ) . ' "][/et_pb_section]' );

		if ( $is_vb_preview ) {
			global $et_pb_predefined_module_index, $disq_pbe_module_index_before;
			if ( isset( $disq_pbe_module_index_before ) ) {
				$et_pb_predefined_module_index = $disq_pbe_module_index_before; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
				unset( $disq_pbe_module_index_before );
			} else {
				unset( $et_pb_predefined_module_index );
			}
			remove_filter( 'pre_do_shortcode_tag', array( $this, 'shortcode_set_ajax_module_index' ) );
		}

		return $layout_content;
	}

	/**
	 * Set a random high module index when rendering in the visual builder to avoid conflicts with other modules on the same page
	 *
	 * @param mixed $value The content of current shortcode.
	 *
	 * @return mixed
	 */
	public function shortcode_set_ajax_module_index( $value ) {
		global $et_pb_predefined_module_index, $disq_pbe_module_index, $disq_pbe_module_index_before;
		if ( ! isset( $disq_pbe_module_index ) ) {
			$disq_pbe_module_index = wp_rand( 999, 999999 );
			if ( isset( $et_pb_predefined_module_index ) ) {
				$disq_pbe_module_index_before = $et_pb_predefined_module_index;
			}
		}
		$et_pb_predefined_module_index = ++$disq_pbe_module_index;  // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

		return $value;
	}
}

new Divi_Layout_Shortcode();
