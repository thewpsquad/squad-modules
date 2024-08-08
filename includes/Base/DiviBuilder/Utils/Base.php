<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Utils Base Class
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   1.5.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils;

use DiviSquad\Base\DiviBuilder\DiviSquad_Module;
use DiviSquad\Base\DiviBuilder\Utils;

/**
 * Utils Base class
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */
abstract class Base {
	use Utils\Common;
	use Utils\Fields;
	use Utils\Fields\Compatibility;
	use Utils\Fields\Definition;
	use Utils\Fields\Processor;
	use Utils\Elements\Breadcrumbs;
	use Utils\Elements\Divider;
	use Utils\Elements\Forms;
	use Utils\Elements\MaskShape;

	/**
	 * The instance of Squad Module.
	 *
	 * @var DiviSquad_Module $element The instance of ET Builder Element (Squad Module).
	 *
	 * @since 1.5.0
	 */
	public $element;
}
