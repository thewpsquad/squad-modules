<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Beta Campaign
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
use function divi_squad_pro;

/**
 * Beta Campaign Class
 *
 * @since       2.0.0
 * @package     squad-modules-for-divi
 */
class WelcomeCampaign extends NoticeCore {

	/**
	 * Check if we can render notice.
	 */
	public function can_render_it() {
		static $can_render;

		if ( ! isset( $can_render ) ) {
			$current_version_dot  = function_exists( 'divi_squad_pro' ) ? divi_squad_pro()->get_version_dot() : '';
			$can_use_premium_code = function_exists( 'divi_squad_fs' ) && divi_squad_fs()->can_use_premium_code();
			$is_pro_notice_closed = divi_squad()->memory->get( 'beta_campaign_notice_close', false );

			$can_render = '1.0.0' === $current_version_dot && ! $can_use_premium_code && ! $is_pro_notice_closed;
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
		return sprintf( '%1$s/notices/welcome-campaign.php', DIVI_SQUAD_TEMPLATES_PATH );
	}
}
