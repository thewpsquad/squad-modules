<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Pro Plugin
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin\Notices;

use DiviSquad\Base\Factories\AdminNotice\NoticeCore;
use function divi_squad;
use function divi_squad_fs;
use function divi_squad_is_pro_activated;

/**
 * Pro Plugin Class
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 */
class ProActivation extends NoticeCore {

	/**
	 * Check if we can render notice.
	 */
	public function can_render_it() {
		static $can_render;

		if ( ! isset( $can_render ) ) {
			$can_use_premium_code = function_exists( 'divi_squad_fs' ) && divi_squad_fs()->can_use_premium_code();
			$is_pro_notice_closed = divi_squad()->memory->get( 'pro_activation_notice_close', false );

			if ( ! divi_squad_is_pro_activated() && $can_use_premium_code && ! $is_pro_notice_closed ) {
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				// Collect basename of all installed and the pro plugin.
				$installed_plugins = array_keys( get_plugins() );
				$pro_basename      = divi_squad_get_pro_basename();

				if ( in_array( $pro_basename, $installed_plugins, true ) ) {
					$can_render = true;
				}
			}

			$can_render = false;
		}

		return $can_render;
	}

	/**
	 * Filters the CSS classes for the body tag in the admin.
	 *
	 * @return string
	 * @since 1.2.5
	 */
	public function get_body_classes() {
		return ' divi-squad-notice';
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function get_template() {
		return sprintf( '%1$s/notices/pro-activation.php', DIVI_SQUAD_TEMPLATES_PATH );
	}
}
