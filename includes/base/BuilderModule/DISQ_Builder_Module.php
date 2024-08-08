<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Module Helper Class which help to the all module class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\BuilderModule;

use ET_Builder_Module;

/**
 * Builder Module class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */
abstract class DISQ_Builder_Module extends ET_Builder_Module {

	use Traits\Field_Compatibility;
	use Traits\Field_Definition;
	use Traits\Field_Processor;
	use Traits\Fields;
	use Traits\Elements\Mask_Shape;
	use Traits\Elements\Divider;

	/**
	 * Module credits.
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $module_credits = array(
		'module_uri' => '',
		'author'     => 'Divi Squad',
		'author_uri' => 'https://squadmodules.com/?utm_campaign=wporg&utm_source=module_modal&utm_medium=module_author_link',
	);

	/**
	 * The icon for module.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $icon = '';

	/**
	 * The icon path for module.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $icon_path = '';

	/**
	 * Stylesheet selector for tooltip container.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $tooltip_css_element = '';

	/**
	 * Module folder name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $folder_name = 'et_pb_divi_squad_modules';

	/**
	 * Collect all modules from Divi Builder.
	 *
	 * @param array $allowed_prefix The allowed prefix list.
	 *
	 * @return array
	 */
	protected function disq_get_all_modules( $allowed_prefix = array() ) {
		// Initiate default data.
		$all_modules            = self::get_modules_array();
		$default_allowed_prefix = array( 'difl', 'df', 'dfadh' );
		$clean_modules          = array(
			'none'   => esc_html__( 'Select Module', 'squad-modules-for-divi' ),
			'custom' => esc_html__( 'Custom', 'squad-modules-for-divi' ),
		);

		// Merge new data with default prefix.
		$all_prefix = array_merge( $default_allowed_prefix, $allowed_prefix );

		foreach ( $all_modules as $module ) {
			if ( strpos( $module['label'], '_' ) ) {
				$module_explode = explode( '_', $module['label'] );

				if ( in_array( $module_explode[0], $all_prefix, true ) ) {
					$clean_modules[ $module['label'] ] = $module['title'];
				}
			}
		}

		return $clean_modules;
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	protected function disq_collect_raw_props( $content ) {
		return wp_strip_all_tags( $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return string
	 */
	protected function disq_json_format_raw_props( $content ) {
		return sprintf( '[%s]', $content );
	}

	/**
	 * Collect actual props from child module with escaping raw html.
	 *
	 * @param string $content The raw content form child element.
	 *
	 * @return array
	 */
	protected function disq_collect_child_json_props( $content ) {
		$raw_props   = $this->disq_json_format_raw_props( $content );
		$clean_props = str_replace( '},]', '}]', $raw_props );
		$child_props = json_decode( $clean_props, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				sprintf(
					/* translators: 1: Error message. */
					esc_html__( __( 'Error when decoding child props: %1$s', 'squad-modules-for-divi' ), 'squad-modules-for-divi' ), // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
					json_last_error_msg() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				)
			);

			return array();
		}

		return $child_props;
	}

	/**
	 * Get default selectors for main and hover in divi module.
	 *
	 * @return array[]
	 */
	protected function disq_get_module_default_selectors() {
		return array(
			'css' => array(
				'main'  => $this->main_css_element,
				'hover' => "$this->main_css_element:hover",
			),
		);
	}

	/**
	 * Clean order class name from the class list for current module.
	 *
	 * @return string[]
	 */
	public function disq_clean_order_class() {
		return array_filter(
			$this->classname,
			function ( $classname ) {
				return 0 !== strpos( $classname, "{$this->slug}_" );
			}
		);
	}

}
