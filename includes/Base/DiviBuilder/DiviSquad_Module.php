<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Base Class which help to the all module class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\DiviBuilder;

use ET_Builder_Module;

/**
 * Builder Utils class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
#[\AllowDynamicProperties]
abstract class DiviSquad_Module extends ET_Builder_Module {

	/**
	 * Utils credits.
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
	 * Utils folder name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $folder_name = 'et_pb_divi_squad_modules';

	/**
	 * The list of icon eligible element
	 *
	 * @var array
	 */
	protected $icon_not_eligible_elements;

	/**
	 * Stylesheet selector for tooltip container.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $tooltip_css_element = '';

	/**
	 * The default options for divider.
	 *
	 * @var array
	 */
	public $squad_divider_defaults = array(
		'divider_style'    => 'solid',
		'divider_position' => 'bottom',
		'divider_weight'   => '2px',
	);

	/**
	 * The show options for divider.
	 *
	 * @var array
	 */
	public $squad_divider_show_options = array(
		'off' => 'No',
		'on'  => 'Yes',
	);

	/**
	 * The instance of Utils class
	 *
	 * @var Utils
	 */
	public $squad_utils;
}
