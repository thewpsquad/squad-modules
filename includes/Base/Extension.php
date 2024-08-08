<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The base class for Extension.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base;

/**
 * Extension class.
 *
 * @since       1.2.0
 * @package     squad-modules-for-divi
 */
abstract class Extension {

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
		$this->memory      = divi_squad()->memory;
		$this->inactivates = $this->memory->get( 'inactive_extensions', array() );
		$this->name_lists  = array_column( $this->inactivates, 'name' );

		// Verify the current extension, is in the allowed list.
		if ( ! in_array( $this->get_name(), $this->name_lists, true ) ) {
			$this->load();
		}
	}

	/**
	 * Get the plugin version number
	 *
	 * @return string
	 */
	protected function get_version() {
		return divi_squad()->get_version();
	}

	/**
	 * Get the extension name.
	 *
	 * @return string
	 */
	abstract protected function get_name();

	/**
	 * Load the extension.
	 *
	 * @return void
	 */
	abstract protected function load();
}
