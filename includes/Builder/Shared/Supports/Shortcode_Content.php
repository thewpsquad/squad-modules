<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Shortcode content helper.
 *
 * Divi-free helper shared by the Divi 4 parent modules to render nested
 * child-module shortcodes contained in a parent's content. Because it has NO
 * Divi dependency, it boots cleanly under PHPUnit (unlike the module abstracts,
 * which hit the Patchwork esc_js DefinedTooEarly blocker).
 *
 * @since   4.4.2
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Shared\Supports;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use function do_shortcode;
use function has_shortcode;

/**
 * Shortcode content helper.
 *
 * @since 4.4.2
 */
final class Shortcode_Content {

	/**
	 * Render nested child-module shortcodes contained in a parent's content.
	 *
	 * Divi 4 renders child-module shortcodes into the `$content` passed to a
	 * parent module's render() (its own shortcode callback does_shortcode the
	 * inner content). Divi 5's Divi-4-shortcode compatibility layer
	 * (`ET\Builder\Packages\ShortcodeModule`) instead hands the parent its raw
	 * inner content, so nested child shortcodes (e.g. `[disq_timeline_item]`)
	 * would otherwise reach the front end as literal text.
	 *
	 * This renders them when a raw child shortcode is still present, and is a
	 * safe no-op on Divi 4, where `$content` arrives already rendered (no child
	 * shortcode remains, so `has_shortcode()` is false).
	 *
	 * @since 4.4.2
	 *
	 * @param string $content    Raw or already-rendered child-module content.
	 * @param string $child_slug The parent's child-module shortcode slug.
	 *
	 * @return string Content with any remaining child shortcodes rendered.
	 */
	public static function render_children( string $content, string $child_slug ): string {
		if ( '' === $content || '' === $child_slug ) {
			return $content;
		}

		return has_shortcode( $content, $child_slug ) ? do_shortcode( $content ) : $content;
	}
}
