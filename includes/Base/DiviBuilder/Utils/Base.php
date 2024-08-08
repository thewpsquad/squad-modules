<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Utils Base Class
 *
 * @since       1.5.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */


namespace DiviSquad\Base\DiviBuilder\Utils;

use DiviSquad\Base\DiviBuilder\DiviSquad_Module;
use DiviSquad\Base\DiviBuilder\Utils;

/**
 * Utils Base class
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
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
