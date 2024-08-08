<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Form Interface
 *
 * Interface for form processors.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.1
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\Forms;

/**
 * Form Interface
 *
 * Interface for form processors.
 *
 * @package DiviSquad
 * @since   3.0.1
 */
interface FormInterface {

	/**
	 * Get forms of a specific type.
	 *
	 * @param string $collection Either 'id' or 'title'.
	 * @return array Associative array of form IDs or titles
	 */
	public function get_forms( $collection );
}
