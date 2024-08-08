<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Module Helper Class which help to the all module class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\BuilderModule;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET_Builder_Module;

/**
 * Builder Module class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
#[\AllowDynamicProperties]
abstract class Squad_Divi_Builder_Module extends ET_Builder_Module {

	use Traits\Field_Compatibility;
	use Traits\Field_Definition;
	use Traits\Field_Processor;
	use Traits\Fields;
	use Traits\Elements\Breadcrumbs;
	use Traits\Elements\Divider;
	use Traits\Elements\Mask_Shape;

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
	 * Module folder name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $folder_name = 'et_pb_divi_squad_modules';

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
		$order_classes = array();
		foreach ( $this->classname as $key => $classname ) {
			if ( 0 !== strpos( $classname, "{$this->slug}_" ) ) {
				$order_classes[ $key ] = $classname;
			}
		}

		return $order_classes;
	}
}
