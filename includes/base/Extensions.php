<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The base class for Extensions.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use function DiviSquad\divi_squad;

/**
 * Extensions class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 */
abstract class Extensions {

	/** The instance of memory.
	 *
	 * @var Memory
	 */
	protected $memory;
	/**
	 * The list of inactive extensions.
	 *
	 * @var array
	 */
	protected $inactivates;
	/**
	 * The name list of extensions.
	 *
	 * @var array
	 */
	protected $name_lists;

	/**
	 * The constructor class.
	 */
	public function __construct() {
		$this->memory      = divi_squad()->get_memory();
		$this->inactivates = $this->memory->get( 'inactive_extensions', array() );
		$this->name_lists  = array_column( $this->inactivates, 'name' );
	}

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	public function get_version() {
		return divi_squad()->get_version();
	}
}
