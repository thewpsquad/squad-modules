<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Pro Plugin Activation Notice Class for the plugin.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 */

namespace DiviSquad\Managers\Notices;

use DiviSquad\Base\Factories\AdminNotice\Notice;
use function divi_squad;
use function esc_html__;
use function wp_nonce_url;

/**
 * Pro Plugin Class
 *
 * @package DiviSquad
 * @since   2.0.0
 */
class ProActivation extends Notice {

	/**
	 * The notice id for the notice.
	 *
	 * @var string
	 */
	protected $notice_id = 'pro-activation';

	/**
	 * Check if we can render notice.
	 */
	public function can_render_it() {
		static $can_render;

		if ( ! isset( $can_render ) ) {
			$can_use_premium_code = divi_squad()->publisher() instanceof \Freemius && divi_squad()->publisher()->can_use_premium_code();
			$is_pro_notice_closed = divi_squad()->memory->get( 'pro_activation_notice_close', false );

			if ( ! $is_pro_notice_closed && $can_use_premium_code && ! divi_squad()->is_pro_activated() ) {
				if ( ! function_exists( '\get_plugins' ) ) {
					require_once divi_squad()->get_wp_path() . 'wp-admin/includes/plugin.php';
				}

				// Collect basename of all installed and the pro plugin.
				$installed_plugins = array_keys( \get_plugins() );
				$pro_basename      = divi_squad()->get_pro_basename();

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
		return 'divi-squad-notice';
	}

	/**
	 * Get the template arguments
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function get_template_args() {
		return array(
			'wrapper_classes' => 'divi-squad-success-banner pro-activation-notice',
			'logo'            => 'logos/divi-squad-d-default.svg',
			'title'           => esc_html__( 'Approaching closer to unlocking the benefits of the Pro plugin.', 'squad-modules-for-divi' ),
			'content'         => sprintf(
				/* translators: %1$s: Product title; */
				esc_html__( ' The paid plugin of %1$s is already installed. Please activate it to start benefiting the Pro features.', 'squad-modules-for-divi' ),
				sprintf( '<em>%s</em>', esc_html__( 'Squad Modules Lite', 'squad-modules-for-divi' ) )
			),
			'action-buttons'  => array(
				'left' => array(
					array(
						'link'    => wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . divi_squad()->get_pro_basename(), 'activate-plugin_' . divi_squad()->get_pro_basename() ),
						'text'    => esc_html__( 'Active Pro Plugin', 'squad-modules-for-divi' ),
						'classes' => 'button-primary divi-squad-notice-action-button',
						'icon'    => 'dashicons-external',
					),
				),
			),
			'is_dismissible'  => true,
		);
	}
}
