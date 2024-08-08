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

use DiviSquad\Admin\Notices\WelcomeCampaign;
use DiviSquad\Utils\Asset;
use DiviSquad\Utils\WP;
use function admin_url;
use function apply_filters;
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
			// List of script dependencies.
			$admin_deps = array( 'lodash', 'react', 'react-dom', 'wp-api-fetch', 'wp-components', 'wp-element' );

			// Load all assets including scripts and stylesheets.
			Asset::style_enqueue( 'admin-components', Asset::admin_asset_path( 'admin-components', array( 'ext' => 'css' ) ) );
			Asset::asset_enqueue( 'admin', Asset::admin_asset_path( 'admin' ), $admin_deps );
			Asset::style_enqueue( 'admin', Asset::admin_asset_path( 'admin', array( 'ext' => 'css' ) ) );

			// Load localize data.
			add_filter( 'divi_squad_assets_backend_extra_data', array( $this, 'wp_localize_script_data' ) );

			// Load script translations.
			WP::set_script_translations( 'squad-admin', divi_squad()->get_name(), divi_squad()->get_localize_path() );
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
		$product_name = divi_squad()->get_name();

		// Collect factories.
		$rest_register = \DiviSquad\Base\Factories\RestRoute::get_instance();

		// Rest API routes for admin.
		$admin_rest_routes = array(
			'rest_api_wp' => array(
				'route'     => get_rest_url(),
				'namespace' => $rest_register->get_namespace( $product_name ),
				'routes'    => $rest_register->get_registered_routes( $product_name ),
			),
		);

		// Defaults data for common area.
		$defaults = array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'assets_url' => DIVI_SQUAD_ASSET_URL . 'assets/',
		);

		return array_merge( $defaults, $exists_data, $admin_rest_routes );
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
		$version_current = divi_squad()->get_version();
		$version_dot     = divi_squad()->get_version_dot();

		// Collect factories.
		$menu_register = \DiviSquad\Base\Factories\AdminMenu::get_instance();

		// Localize data for squad admin.
		$admin_localize = array(
			'version_wp_current' => $version_current,
			'version_wp_real'    => $version_dot,
			'site_url'           => home_url( '/' ),
			'admin_menus'        => $menu_register->get_registered_submenus(),
			'links'              => array(
				'dashboard'  => admin_url( 'admin.php?page=divi_squad_dashboard#/' ),
				'modules'    => admin_url( 'admin.php?page=divi_squad_dashboard#/modules' ),
				'extensions' => admin_url( 'admin.php?page=divi_squad_dashboard#/extensions' ),
				'whats_new'  => admin_url( 'admin.php?page=divi_squad_dashboard#/whats-new' ),
			),
			'l10n'               => array(
				'plugin_label'            => esc_html__( 'Divi Squad', 'squad-modules-for-divi' ),
				'dashboard'               => esc_html__( 'Dashboard', 'squad-modules-for-divi' ),
				'modules'                 => esc_html__( 'Modules', 'squad-modules-for-divi' ),
				'extensions'              => esc_html__( 'Extension', 'squad-modules-for-divi' ),
				'whats_new'               => esc_html__( 'What\'s New', 'squad-modules-for-divi' ),
				'view_all'                => esc_html__( 'View All', 'squad-modules-for-divi' ),
				'upgrade'                 => esc_html__( 'Upgrade to Pro', 'squad-modules-for-divi' ),
				'active'                  => esc_html__( 'Active License', 'squad-modules-for-divi' ),
				'activate_all'            => esc_html__( 'Active All', 'squad-modules-for-divi' ),
				'deactivate_all'          => esc_html__( 'Deactivate All', 'squad-modules-for-divi' ),
				'save_changes'            => esc_html__( 'Save Changes', 'squad-modules-for-divi' ),
				'badge_text_new'          => esc_html__( 'NEW', 'squad-modules-for-divi' ),
				'badge_text_updated'      => esc_html__( 'Updated', 'squad-modules-for-divi' ),
				'uncategories'            => esc_html__( 'Uncategories', 'squad-modules-for-divi' ),
				'content-modules'         => esc_html__( 'Content Modules', 'squad-modules-for-divi' ),
				'creative-modules'        => esc_html__( 'Creative Modules', 'squad-modules-for-divi' ),
				'dynamic-content-modules' => esc_html__( 'Dynamic Content Modules', 'squad-modules-for-divi' ),
				'form-styler-modules'     => esc_html__( 'Form Styler Modules', 'squad-modules-for-divi' ),
				'image-&-media-modules'   => esc_html__( 'Image & Media Modules', 'squad-modules-for-divi' ),
				'media-upload'            => esc_html__( 'Media Upload', 'squad-modules-for-divi' ),
				'enhancement'             => esc_html__( 'Enhancement', 'squad-modules-for-divi' ),
				'preparing'               => esc_html__( 'Preparing', 'squad-modules-for-divi' ),
				'modules_saved'           => esc_html__( 'Enabled modules saved!', 'squad-modules-for-divi' ),
				'modules_saving_failed'   => esc_html__( 'Whoops! Enabled modules not saved. Try again later.', 'squad-modules-for-divi' ),
				'not_found_title'         => esc_html__( 'Oops, Page not found!', 'squad-modules-for-divi' ),
				'not_found_message'       => esc_html__( 'The page you are looking for might have been removed had its name changed or is temporarily unavailable.', 'squad-modules-for-divi' ),
				'go_back'                 => esc_html__( 'Go Back', 'squad-modules-for-divi' ),
			),
			'plugins'            => WP::get_active_plugins(),
			'notices'            => array(
				'has_welcome' => ( new WelcomeCampaign() )->can_render_it(),
			),
		);

		return array_merge( $exists_data, $admin_localize );
	}
}
