<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The Ajax actions handler class.
 *
 * This class handles all http ajax requests and provides a response to the client.
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Managers;

use DiviSquad\Extensions;
use DiviSquad\Modules;

/**
 * The Ajax actions handler class.
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Ajax {

	/**
	 * Load all ajax action.
	 *
	 * @return void
	 */
	public static function load() {
		// Load ajax action for the copy extension.
		$duplicate_post_action   = 'divi_squad_ext_duplicate_post';
		$duplicate_post_callback = array( Extensions\Copy::class, 'duplicate_the_post' );
		add_action( "wp_ajax_{$duplicate_post_action}", $duplicate_post_callback );

		// Registers all hook for processing post elements.
		$post_grid_load_more_query_action   = 'divi_squad_post_query_load_more';
		$post_grid_load_more_query_callback = array( Modules\PostGrid\PostGrid::class, 'wp_hook_squad_post_load_more_query' );
		add_action( "wp_ajax_{$post_grid_load_more_query_action}", $post_grid_load_more_query_callback );
		add_action( "wp_ajax_nopriv_{$post_grid_load_more_query_action}", $post_grid_load_more_query_callback );
	}
}
