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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Utils\Asset;
use DiviSquad\Utils\WP;
use function admin_url;
use function apply_filters;
use function DiviSquad\divi_squad;
use function esc_html__;
use function get_rest_url;

/**
 * Assets class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Assets {

	/**
	 * The plugin options.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @param array $options The options.
	 */
	public function __construct( $options ) {
		$this->options = $options;
	}

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
	 * Enqueue the plugin's scripts and styles files in the WordPress admin area.
	 *
	 * @param string $hook_suffix Hook suffix for the current admin page.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_plugin_admin_asset( $hook_suffix ) {
		// Load plugin asset in the all admin pages.
		Asset::asset_enqueue( 'admin-common', Asset::admin_asset_path( 'admin-common' ), array( 'jquery', 'wp-api-fetch' ) );
		Asset::style_enqueue( 'admin-common', Asset::admin_asset_path( 'admin-common', array( 'ext' => 'css' ) ) );

		// Load localize data.
		add_filter( 'divi_squad_assets_backend_extra_data', array( $this, 'wp_common_localize_script_data' ) );

		// Load plugin asset in the allowed admin pages only.
		if ( in_array( $hook_suffix, self::get_plugin_asset_allowed_pages(), true ) ) {
			// Load all assets including scripts and stylesheets.
			Asset::asset_enqueue( 'admin', Asset::admin_asset_path( 'admin' ), array( 'lodash', 'react', 'react-dom', 'wp-api-fetch', 'wp-element' ) );
			Asset::style_enqueue( 'admin', Asset::admin_asset_path( 'admin', array( 'ext' => 'css' ) ) );

			// Load localize data.
			add_filter( 'divi_squad_assets_backend_extra_data', array( $this, 'wp_localize_script_data' ) );

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
	 * @param array $exists_data Exists extra data.
	 *
	 * @return array
	 */
	public function wp_common_localize_script_data( $exists_data ) {
		// Collect the plugin name.
		$namespace = divi_squad()->get_name();

		// For review notice.
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

		// Rest API routes for admin.
		$admin_rest_routes = array(
			'rest_api_review' => array(
				'route'     => get_rest_url(),
				'namespace' => "/$namespace/v1",
				'routes'    => array_merge( $review_routes, array() ),
			),
		);

		return array_merge( $exists_data, $admin_rest_routes );
	}

	/**
	 * Set localize data for admin area.
	 *
	 * @param array $exists_data Exists extra data.
	 *
	 * @return array
	 */
	public function wp_localize_script_data( $exists_data ) {
		// Collect the plugin data.
		$namespace       = $this->options['Name'];
		$version_dot     = $this->options['Version'];
		$version_current = divi_squad()->get_version();

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

		// Localize data for squad admin.
		$admin_localize = array(
			'version_wp_current' => $version_current,
			'version_wp_real'    => $version_dot,
			'rest_api_wp'        => array(
				'route'     => get_rest_url(),
				'namespace' => "/$namespace/v1",
				'routes'    => array_merge( $module_routes, $extension_routes, $whats_new_routes ),
			),
			'admin_menus'        => $admin_menu->get_admin_sub_menu(),
			'links'              => array(
				'dashboard'  => admin_url( 'admin.php?page=divi_squad_dashboard' ),
				'modules'    => admin_url( 'admin.php?page=divi_squad_dashboard#/modules' ),
				'extensions' => admin_url( 'admin.php?page=divi_squad_dashboard#/extensions' ),
				'whatsNew'   => admin_url( 'admin.php?page=divi_squad_dashboard#/whats-new' ),
				'premium'    => admin_url( 'admin.php?page=divi_squad_dashboard#/go-premium' ),
			),
			'l10n'               => array(
				'divi_squad'                  => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
				'menu_dashboard'              => esc_html__( 'Dashboard', 'squad-modules-for-divi' ),
				'menu_modules'                => esc_html__( 'Modules', 'squad-modules-for-divi' ),
				'menu_extensions'             => esc_html__( 'Extensions', 'squad-modules-for-divi' ),
				'menu_whats_new'              => esc_html__( 'What\'s New', 'squad-modules-for-divi' ),
				'view_all'                    => esc_html__( 'View All', 'squad-modules-for-divi' ),
				'upgrade'                     => esc_html__( 'Upgrade to Pro', 'squad-modules-for-divi' ),
				'active'                      => esc_html__( 'Active License', 'squad-modules-for-divi' ),
				'docs_title'                  => esc_html__( 'View Documentation', 'squad-modules-for-divi' ),
				'docs_desc'                   => esc_html__( 'Get started by spending some time with the documentation to get familiar with Squad Modules.', 'squad-modules-for-divi' ),
				'rating_title'                => esc_html__( 'Show Your Love', 'squad-modules-for-divi' ),
				'rating_desc'                 => esc_html__( 'Take your 2 minutes to review the plugin and spread the love to encourage us to keep it going.', 'squad-modules-for-divi' ),
				'help_title'                  => esc_html__( 'Need Help?', 'squad-modules-for-divi' ),
				'help_desc'                   => esc_html__( 'Stuck with something? Get help from live chat or submit a support ticket.', 'squad-modules-for-divi' ),
				'group_title'                 => esc_html__( 'Join the Community', 'squad-modules-for-divi' ),
				'group_desc'                  => esc_html__( 'Join the user community and discuss with fellow developers & users.', 'squad-modules-for-divi' ),
				'changelog'                   => esc_html__( 'Changelog', 'squad-modules-for-divi' ),
				'changelog_desc'              => esc_html__( 'Dive into the Changelog and discover all the magic behind the scenes!', 'squad-modules-for-divi' ),
				'changelog_view'              => esc_html__( 'View Changelog', 'squad-modules-for-divi' ),
				'update_title_desc'           => esc_html__( 'Supercharge your experience with our game-changing new updates!', 'squad-modules-for-divi' ),
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
				'module_manage_desc'          => esc_html__( 'Enjoy seamless module control at your fingertips. The Toggle Button makes it a breeze to activate or deactivate elements, keeping your workflow smooth and efficient.', 'squad-modules-for-divi' ),
				'module_defaults'             => esc_html__( 'Default', 'squad-modules-for-divi' ),
				'activate_all'                => esc_html__( 'Active All', 'squad-modules-for-divi' ),
				'deactivate_all'              => esc_html__( 'Deactivate All', 'squad-modules-for-divi' ),
				'save_changes'                => esc_html__( 'Save Changes', 'squad-modules-for-divi' ),
				'extensions_manage_title'     => esc_html__( 'Manage Extensions', 'squad-modules-for-divi' ),
				'extensions_manage_desc'      => esc_html__( "Take charge of your workspace with the Toggle Button. It's your ultimate tool for managing extensions seamlessly, keeping your focus where it matters.", 'squad-modules-for-divi' ),
				'badge_text_new'              => esc_html__( 'NEW', 'squad-modules-for-divi' ),
				'badge_text_updated'          => esc_html__( 'Updated', 'squad-modules-for-divi' ),
				'uncategories'                => esc_html__( 'Uncategories', 'squad-modules-for-divi' ),
				'content-modules'             => esc_html__( 'Content Modules', 'squad-modules-for-divi' ),
				'creative-modules'            => esc_html__( 'Creative Modules', 'squad-modules-for-divi' ),
				'dynamic-content-modules'     => esc_html__( 'Dynamic Content Modules', 'squad-modules-for-divi' ),
				'form-styler-modules'         => esc_html__( 'Form Styler Modules', 'squad-modules-for-divi' ),
				'image-&-media-modules'       => esc_html__( 'Image & Media Modules', 'squad-modules-for-divi' ),
				'media-upload'                => esc_html__( 'Media Upload', 'squad-modules-for-divi' ),
				'enhancement'                 => esc_html__( 'Enhancement', 'squad-modules-for-divi' ),
				'preparing'                   => esc_html__( 'Preparing', 'squad-modules-for-divi' ),
				'modules_saved'               => esc_html__( 'Enabled modules saved!', 'squad-modules-for-divi' ),
				'modules_saving_failed'       => esc_html__( 'Whoops! Enabled modules not saved. Try again later.', 'squad-modules-for-divi' ),
				'extensions_saved'            => esc_html__( 'Enabled extensions saved!', 'squad-modules-for-divi' ),
				'extensions_saving_failed'    => esc_html__( 'Whoops! Enabled extensions not saved. Try again later.', 'squad-modules-for-divi' ),
				'support'                     => array(
					'popup_title'     => esc_html__( 'We are here to assist you with any queries you may have. Feel free to ask us anything!', 'squad-modules-for-divi' ),
					'message_default' => esc_html__( 'Hi, how can I help?', 'squad-modules-for-divi' ),
				),
				'not_found_title'             => esc_html__( 'Oops, Page not found!', 'squad-modules-for-divi' ),
				'not_found_message'           => esc_html__( 'The page you are looking for might have been removed had its name changed or is temporarily unavailable.', 'squad-modules-for-divi' ),
				'go_back'                     => esc_html__( 'Go Back', 'squad-modules-for-divi' ),
			),
			'plugins'            => WP::get_active_plugins(),
		);

		return array_merge( $exists_data, $admin_localize );
	}
}
