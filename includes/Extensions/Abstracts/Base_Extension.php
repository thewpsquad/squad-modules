<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * The base class for Extension.
 *
 * @since   1.2.0
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Extensions\Abstracts;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Core\Memory;
use DiviSquad\Extensions\Contracts\Extension_Interface;

/**
 * Extension class.
 *
 * @since   1.2.0
 * @package DiviSquad
 */
abstract class Base_Extension implements Extension_Interface {
	/** The instance of memory.
	 *
	 * @var Memory
	 */
	protected Memory $memory;

	/**
	 * The list of inactive extensions.
	 *
	 * @var array<string, string>
	 */
	protected array $inactivates;

	/**
	 * The name list of extensions.
	 *
	 * @var array<string>
	 */
	protected array $name_lists;

	/**
	 * The constructor class.
	 */
	public function __construct() {
		$this->memory      = divi_squad()->memory;
		// inactive_extensions is persisted as a flat list of extension-name strings
		// (see Core\Extensions), so use it directly — array_column() on a string
		// list returns [], which silently disabled this self-guard (fail-open).
		$this->inactivates = (array) $this->memory->get( 'inactive_extensions', array() );
		$this->name_lists  = $this->inactivates;

		// Verify the current extension, is in the allowed list.
		if ( ! in_array( $this->get_name(), $this->name_lists, true ) ) {
			$this->load();
		}
	}
}
