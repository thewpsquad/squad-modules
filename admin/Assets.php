<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The admin asset management class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin;

use DiviSquad\Utils\Asset;
use DiviSquad\Utils\WP;
use function DiviSquad\divi_squad;
use function get_rest_url;

/**
 * Assets class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Assets {

	/**
	 * Get the lis of admin asset allowed page for the plugin.
	 *
	 * @return array
	 * @since 1.2.0
	 */
	protected static function get_plugin_asset_allowed_pages() {
		return apply_filters( 'divi_squad_admin_asset_allowed_pages', array( 'toplevel_page_divi_squad_dashboard' ) );
	}

	/**
	 * Get the lis of admin extra asset allowed page for the plugin.
	 *
	 * @return array
	 * @since 1.2.0
	 */
	protected static function get_plugin_extra_asset_allowed_pages() {
		return apply_filters( 'divi_squad_admin_extra_asset_allowed_pages', array() );
	}

	/**
	 * Enqueue the plugin's scripts and styles files in the WordPress admin area.
	 *
	 * @param string $hook_suffix Hook suffix for the current admin page.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_plugin_admin_asset( $hook_suffix ) {
		// Load plugin asset in the all admin pages.
		Asset::asset_enqueue( 'admin-common', Asset::asset_path( 'admin-common', array( 'path' => 'admin/scripts', 'ext' => 'js' ) ) ); // phpcs:ignore
		Asset::style_enqueue( 'admin-common', Asset::asset_path( 'admin-common', array( 'path' => 'admin/styles', 'ext' => 'css' ) ) ); // phpcs:ignore

		// Load localize scripts.
		$admin_localize_scripts = $this->wp_common_localize_script_data();
		WP::localize_script( 'disq-admin-common', 'DISQAdminCommonBackend', $admin_localize_scripts );

		// Load plugin asset in the allowed admin pages only.
		if ( in_array( $hook_suffix, self::get_plugin_asset_allowed_pages(), true ) ) {
			// Load all assets including scripts and stylesheets.
			Asset::asset_enqueue( 'admin', Asset::asset_path( 'admin', array( 'path' => 'admin/scripts', 'ext' => 'js' ) ) ); // phpcs:ignore
			Asset::style_enqueue( 'admin', Asset::asset_path( 'admin', array( 'path' => 'admin/styles', 'ext' => 'css' ) ) ); // phpcs:ignore

			// Load localize scripts.
			$admin_localize_scripts = $this->wp_localize_script_data();
			WP::localize_script( 'disq-admin', 'DISQAdminBackend', $admin_localize_scripts );

			// Load script translations.
			$localize_path = divi_squad()->get_localize_path();
			WP::set_script_translations( 'admin', divi_squad()->get_name(), "$localize_path/languages" );
		}
	}

	/**
	 * Enqueue extra scripts and styles files in the WordPress admin area.
	 *
	 * @param string $hook_suffix Hook suffix for the current admin page.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_extra_admin_asset( $hook_suffix ) {}

	/**
	 * Set localize data for admin area.
	 *
	 * @return array
	 */
	public function wp_common_localize_script_data() {
		// Collect the plugin name.
		$namespace = divi_squad()->get_name();

		// for review notice.
		$review_routes = array(
			'reviewNextWeek'         => array(
				'root'    => 'review/next_week',
				'methods' => array( 'post' ),
			),
			'reviewDone'             => array(
				'root'    => 'review/done',
				'methods' => array( 'post' ),
			),
			'reviewNoticeCloseCount' => array(
				'root'    => 'review/notice_close_count',
				'methods' => array( 'post' ),
			),
			'reviewAskSupportCount'  => array(
				'root'    => 'review/ask_support',
				'methods' => array( 'post' ),
			),
		);

		return array(
			'rest_api' => array(
				'route'     => get_rest_url(),
				'namespace' => "/$namespace/v1",
				'routes'    => array_merge( $review_routes, array() ),
			),
		);
	}

	/**
	 * Set localize data for admin area.
	 *
	 * @return array
	 */
	public function wp_localize_script_data() {
		// Collect the plugin name.
		$namespace = divi_squad()->get_name();

		// Collect admin menus.
		$admin_menu = new \DiviSquad\Admin\Menu();

		// for modules.
		$module_routes = array(
			'getModules'    => array(
				'root'    => 'modules',
				'methods' => array( 'get' ),
			),
			'activeModules' => array(
				'root'    => 'active_modules',
				'methods' => array( 'get', 'post' ),
			),
		);

		// for extensions.
		$extension_routes = array(
			'getExtensions'    => array(
				'root'    => 'extensions',
				'methods' => array( 'get' ),
			),
			'activeExtensions' => array(
				'root'    => 'active_extensions',
				'methods' => array( 'get', 'post' ),
			),
		);

		// for what's new.
		$whats_new_routes = array(
			'getWhatsNew' => array(
				'root'    => 'whats-new',
				'methods' => array( 'get' ),
			),
		);

		return array(
			'version'      => divi_squad()->get_version(),
			'version_real' => DISQ_VERSION,
			'rest_api'     => array(
				'route'     => get_rest_url(),
				'namespace' => "/$namespace/v1",
				'routes'    => array_merge( $module_routes, $extension_routes, $whats_new_routes ),
			),
			'admin_menus'  => $admin_menu->get_admin_sub_menu(),
			'links'        => array(
				'dashboard'  => admin_url( 'admin.php?page=divi_squad_dashboard' ),
				'modules'    => admin_url( 'admin.php?page=divi_squad_dashboard#/modules' ),
				'extensions' => admin_url( 'admin.php?page=divi_squad_dashboard#/extensions' ),
				'whatsNew'   => admin_url( 'admin.php?page=divi_squad_dashboard#/whats-new' ),
				'premium'    => admin_url( 'admin.php?page=divi_squad_dashboard#/go-premium' ),
			),
			'l10n'         => array(
				'divi_squad'                  => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
				'upgrade'                     => esc_html__( 'Upgrade to Pro', 'squad-modules-for-divi' ),
				'active'                      => esc_html__( 'Active License', 'squad-modules-for-divi' ),
				'docs_title'                  => esc_html__( 'View Documentation', 'squad-modules-for-divi' ),
				'docs_desc'                   => esc_html__( 'Get started by spending some time with the documentation to get familiar with Divi Squad Modules.', 'squad-modules-for-divi' ),
				'rating_title'                => esc_html__( 'Show Your Love', 'squad-modules-for-divi' ),
				'rating_desc'                 => esc_html__( 'Take your 2 minutes to review the plugin and spread the love to encourage us to keep it going.', 'squad-modules-for-divi' ),
				'help_title'                  => esc_html__( 'Need Help?', 'squad-modules-for-divi' ),
				'help_desc'                   => esc_html__( 'Stuck with something? Get help from live chat or submit a support ticket.', 'squad-modules-for-divi' ),
				'group_title'                 => esc_html__( 'Join the Community', 'squad-modules-for-divi' ),
				'group_desc'                  => esc_html__( 'Join the user community and discuss with fellow developers & users.', 'squad-modules-for-divi' ),
				'changelog'                   => esc_html__( 'Changelog', 'squad-modules-for-divi' ),
				'changelog_view'              => esc_html__( 'View Changelog', 'squad-modules-for-divi' ),
				'update_title_desc'           => esc_html__( 'Check out the changes & features we have added with our new updates', 'squad-modules-for-divi' ),
				'unlock_pro'                  => esc_html__( 'Unlock 20+ Advanced PRO Modules to Enhance Your Divi Site Building Experience', 'squad-modules-for-divi' ),
				'unlock_pro_title'            => esc_html__( 'Automatic Updates & Priority Support', 'squad-modules-for-divi' ),
				'unlock_pro_desc'             => esc_html__( 'Get access to automatic updates & keep your website up-to-date with constantly developing features.Having any trouble? Don’t worry as you can reach out to our expert Support team any time through live chat or support tickets.', 'squad-modules-for-divi' ),
				'module_load_server_issue'    => esc_html__( 'Unable to load module list.', 'squad-modules-for-divi' ),
				'module_loading'              => esc_html__( 'Loading available modules...', 'squad-modules-for-divi' ),
				'extension_load_server_issue' => esc_html__( 'Unable to load extension list.', 'squad-modules-for-divi' ),
				'extension_loading'           => esc_html__( 'Loading available extensions...', 'squad-modules-for-divi' ),
				'whats_new_load_server_issue' => esc_html__( 'Unable to load new changes.', 'squad-modules-for-divi' ),
				'whats_new_loading'           => esc_html__( 'Loading available what\'s new changes...', 'squad-modules-for-divi' ),
				'module_manage_title'         => esc_html__( 'Manage Modules', 'squad-modules-for-divi' ),
				'module_manage_desc'          => esc_html__( 'Check out the changes & features we have added with our new updates', 'squad-modules-for-divi' ),
				'module_defaults'             => esc_html__( 'Default', 'squad-modules-for-divi' ),
				'activate_all'                => esc_html__( 'Active All', 'squad-modules-for-divi' ),
				'deactivate_all'              => esc_html__( 'Deactivate All', 'squad-modules-for-divi' ),
				'save_changes'                => esc_html__( 'Save Changes', 'squad-modules-for-divi' ),
				'extensions_manage_title'     => esc_html__( 'Manage Extensions', 'squad-modules-for-divi' ),
				'badge_text_new'              => esc_html__( 'NEW', 'squad-modules-for-divi' ),
				'badge_text_updated'          => esc_html__( 'Updated', 'squad-modules-for-divi' ),
				'support'                     => array(
					'popup_title'     => esc_html__( 'We are here to assist you with any queries you may have. Feel free to ask us anything!', 'squad-modules-for-divi' ),
					'message_default' => esc_html__( 'Hi, how can I help?', 'squad-modules-for-divi' ),
				),
			),
			'plugins'      => WP::get_active_plugins(),
		);
	}
}
