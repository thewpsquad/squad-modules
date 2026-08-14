<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Collection Filter trait for filtering arrays with callbacks.
 *
 * @since   3.4.5
 * @package DiviSquad
 */

namespace DiviSquad\Core\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use Throwable;

/**
 * Collection Filter trait.
 *
 * Provides functionality to filter collections (modules, extensions, etc.)
 * using callback functions. This trait centralizes the duplicate filter logic
 * that was previously present in both Modules and Extensions classes.
 *
 * @since   3.4.5
 * @package DiviSquad
 */
trait Collection_Filter {

	/**
	 * Filter items in a collection based on callback
	 *
	 * This method provides a safe wrapper around array_filter that includes
	 * error handling and allows filtering with custom callback functions.
	 *
	 * @since 3.4.5
	 *
	 * @param array<string, mixed> $collection The collection to filter.
	 * @param callable             $callback   Function to filter items.
	 *
	 * @return array<string, mixed> Filtered collection.
	 */
	protected function filter_collection( array $collection, callable $callback ): array {
		$filtered = array();

		// Catch PER ITEM, not around the whole array_filter: a single throwing callback
		// must skip only that item, never collapse the entire collection to [] (which
		// would silently disable every module/extension the caller was filtering).
		foreach ( $collection as $key => $item ) {
			try {
				if ( $callback( $item, $key ) ) {
					$filtered[ $key ] = $item;
				}
			} catch ( Throwable $e ) {
				if ( function_exists( 'divi_squad' ) ) {
					divi_squad()->log_error( $e, 'Failed to filter collection item', false );
				}
			}
		}

		return $filtered;
	}
}
