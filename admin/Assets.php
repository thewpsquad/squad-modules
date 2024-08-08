<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The admin asset management class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

use DiviSquad\Admin\Assets\Utils;
use function DiviSquad\divi_squad;

/**
 * Assets class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Assets {

	/** The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the instance of self-class
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();

			add_action( 'admin_enqueue_scripts', array( self::$instance, 'wp_hook_enqueue_admin_asset' ) );
		}

		return self::$instance;
	}

	/**
	 * Enqueue scripts and styles files in the WordPress admin area.
	 */
	public function wp_hook_enqueue_admin_asset() {
		Utils::asset_enqueue( 'admin-free', 'build/admin/scripts/admin-script.js' );
		Utils::style_enqueue( 'admin-free', 'build/admin/styles/admin-style.css' );

		$localize_data = self::wp_localize_script_data();
		wp_localize_script( 'disq-admin-free', 'DISQAdminBackend', $localize_data );
	}

	/**
	 * Set localize data for admin area.
	 *
	 * @return array
	 */
	public static function wp_localize_script_data() {
		// Collect the plugin name.
		$namespace = divi_squad()->get_name();

		return array(
			'rest_api' => array(
				'route'     => get_rest_url(),
				'namespace' => "/{$namespace}/v1",
				'routes'    => array(
					'getModules'    => array(
						'root'    => 'modules',
						'methods' => array( 'get' ),
					),
					'activeModules' => array(
						'root'    => 'active_modules',
						'methods' => array( 'get', 'post' ),
					),
				),
			),
			'links'    => array(
				'dashboard' => admin_url( 'admin.php?page=divi_squad_dashboard' ),
				'modules'   => admin_url( 'admin.php?page=divi_squad_modules' ),
				'premium'   => admin_url( 'admin.php?page=divi_squad_go_premium' ),
			),
			'l10n'     => array(
				'divi_squad'               => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
				'upgrade'                  => esc_html__( 'Upgrade to Pro', 'squad-modules-for-divi' ),
				'active'                   => esc_html__( 'Active License', 'squad-modules-for-divi' ),
				'docs_title'               => esc_html__( 'View Documentation', 'squad-modules-for-divi' ),
				'docs_desc'                => esc_html__( 'Get started by spending some time with the documentation to get familiar with Divi Squad Modules.', 'squad-modules-for-divi' ),
				'rating_title'             => esc_html__( 'Show Your Love', 'squad-modules-for-divi' ),
				'rating_desc'              => esc_html__( 'Take your 2 minutes to review the plugin and spread the love to encourage us to keep it going.', 'squad-modules-for-divi' ),
				'help_title'               => esc_html__( 'Need Help?', 'squad-modules-for-divi' ),
				'help_desc'                => esc_html__( 'Stuck with something? Get help from live chat or submit a support ticket.', 'squad-modules-for-divi' ),
				'group_title'              => esc_html__( 'Join the Community', 'squad-modules-for-divi' ),
				'group_desc'               => esc_html__( 'Join the user community and discuss with fellow developers & users.', 'squad-modules-for-divi' ),
				'changelog_title'          => esc_html__( 'View Changelog', 'squad-modules-for-divi' ),
				'changelog_desc'           => esc_html__( 'Check out the changes & features we have added with our new updates', 'squad-modules-for-divi' ),
				'unlock_pro'               => esc_html__( 'Unlock 20+ Advanced PRO Modules to Enhance Your Divi Site Building Experience', 'squad-modules-for-divi' ),
				'unlock_pro_title'         => esc_html__( 'Automatic Updates & Priority Support', 'squad-modules-for-divi' ),
				'unlock_pro_desc'          => esc_html__( 'Get access to automatic updates & keep your website up-to-date with constantly developing features.Having any trouble? Don’t worry as you can reach out to our expert Support team any time through live chat or support tickets.', 'squad-modules-for-divi' ),
				'module_load_server_issue' => esc_html__( 'Unable to load module list. It may be server issue.', 'squad-modules-for-divi' ),
				'module_loading'           => esc_html__( 'Loading available modules...', 'squad-modules-for-divi' ),
				'module_manage_title'      => esc_html__( 'Manage Modules', 'squad-modules-for-divi' ),
				'module_manage_desc'       => esc_html__( 'Check out the changes & features we have added with our new updates', 'squad-modules-for-divi' ),
				'module_defaults'          => esc_html__( 'Default', 'squad-modules-for-divi' ),
				'module_actives'           => esc_html__( 'Active All', 'squad-modules-for-divi' ),
				'module_deactivates'       => esc_html__( 'Deactivate All', 'squad-modules-for-divi' ),
				'module_save'              => esc_html__( 'Save Changes', 'squad-modules-for-divi' ),
			),
		);
	}

}
