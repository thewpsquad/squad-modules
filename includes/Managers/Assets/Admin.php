<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Admin class.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Managers\Assets;

use DiviSquad\Base\Factories\AdminMenu as AdminMenuFactory;
use DiviSquad\Base\Factories\PluginAsset\PluginAsset;
use DiviSquad\Base\Factories\RestRoute as RestRouteFactory;
use DiviSquad\Managers\Notices\Discount;
use DiviSquad\Utils\Asset;
use DiviSquad\Utils\Helper;
use DiviSquad\Utils\WP as WpUtils;
use function admin_url;
use function divi_squad;
use function esc_html__;
use function home_url;

/**
 * Admin class.
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Admin extends PluginAsset {

	/**
	 * Enqueue scripts, styles, and other assets in the WordPress frontend and admin area.
	 *
	 * @param string $type The type of the script. Default is 'frontend'.
	 * @param string $hook_suffix The hook suffix for the current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $type = 'frontend', $hook_suffix = '' ) {
		// Check if the type is not admin.
		if ( 'admin' !== $type ) {
			return;
		}

		$this->enqueue_admin_scripts( $hook_suffix );
	}

	/**
	 * Localize script data.
	 *
	 * @param string       $type The type of the localize data. Default is 'raw'. Accepts 'raw' or 'output'.
	 * @param string|array $data The data to localize.
	 *
	 * @return string|array
	 */
	public function get_localize_data( $type = 'raw', $data = array() ) {
		if ( 'raw' === $type ) {
			$data = $this->wp_common_localize_script_data( $data );
			$data = $this->wp_localize_script_data( $data );
		}

		return $data;
	}

	/**
	 * Enqueue the plugin's scripts and styles files in the WordPress admin area.
	 *
	 * @param string $hook_suffix Hook suffix for the current admin page.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {
		// Load plugin asset in the all admin pages.
		Asset::enqueue_script( 'admin-common', Asset::admin_asset_path( 'admin-common' ), array( 'jquery', 'wp-api-fetch' ) );
		Asset::enqueue_style( 'admin-common', Asset::admin_asset_path( 'admin-common', array( 'ext' => 'css' ) ) );

		// Load plugin asset in the allowed admin pages only.
		if ( Helper::is_squad_page( $hook_suffix ) ) {
			// List of script dependencies.
			$admin_deps = array( 'lodash', 'react', 'react-dom', 'react-jsx-runtime', 'wp-api-fetch', 'wp-components', 'wp-dom-ready', 'wp-element', 'wp-i18n' );

			// Load all assets including scripts and stylesheets.
			Asset::enqueue_style( 'admin-components', Asset::admin_asset_path( 'admin-components', array( 'ext' => 'css' ) ) );
			Asset::enqueue_script( 'admin', Asset::admin_asset_path( 'admin' ), $admin_deps );
			Asset::enqueue_style( 'admin', Asset::admin_asset_path( 'admin', array( 'ext' => 'css' ) ) );

			// Load script translations.
			WpUtils::set_script_translations( 'squad-admin', divi_squad()->get_name() );
		}
	}

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
		$rest_register = RestRouteFactory::get_instance();
		if ( ! $rest_register instanceof RestRouteFactory ) {
			$admin_rest_routes = array();
		} else {
			// Rest API routes for admin.
			$admin_rest_routes = array(
				'rest_api_wp' => array(
					'route'     => get_rest_url(),
					'namespace' => $rest_register->get_namespace( $product_name ),
					'routes'    => $rest_register->get_registered_routes( $product_name ),
				),
			);
		}

		// Defaults data for common area.
		$defaults = array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'assets_url' => divi_squad()->get_asset_url(),
		);

		return array_merge_recursive( $defaults, $exists_data, $admin_rest_routes );
	}

	/**
	 * Set localize data for admin area.
	 *
	 * @param array $exists_data Exists extra data.
	 *
	 * @return array
	 */
	public function wp_localize_script_data( $exists_data ) {
		if ( ! function_exists( '\get_current_screen' ) ) {
			return $exists_data;
		}

		$screen = \get_current_screen();
		// Check if the current screen is not a WP_Screen object.
		if ( ! $screen instanceof \WP_Screen ) {
			return $exists_data;
		}

		// Check if the current page is not a squad page.
		if ( ! Helper::is_squad_page( $screen->id ) ) {
			return $exists_data;
		}

		// Collect the plugin data.
		$version_current = divi_squad()->get_version();
		$version_dot     = divi_squad()->get_version_dot();

		// Collect factories.
		$menu_register = AdminMenuFactory::get_instance();
		if ( $menu_register instanceof AdminMenuFactory ) {
			$admin_menus = $menu_register->get_registered_submenus();
		} else {
			$admin_menus = array();
		}

		if ( ! function_exists( '\get_plugins' ) ) {
			require_once divi_squad()->get_wp_path() . 'wp-admin/includes/plugin.php';
		}

		// Get installed plugins.
		$is_pro_installed  = false;
		$pro_basename      = divi_squad()->get_pro_basename();
		$installed_plugins = \get_plugins();
		if ( ! empty( $installed_plugins ) ) {
			$installed_plugins = array_keys( $installed_plugins );
			$is_pro_installed  = in_array( $pro_basename, $installed_plugins, true );
		}

		// Localize data for squad admin.
		$admin_localize = array(
			'version_wp_current' => $version_current,
			'version_wp_real'    => $version_dot,
			'admin_menus'        => $admin_menus,
			'premium'            => array(
				'is_active'    => divi_squad_fs() instanceof \Freemius && divi_squad_fs()->can_use_premium_code(),
				'is_installed' => $is_pro_installed,
			),
			'links'              => array(
				'site_url'   => home_url( '/' ),
				'my_account' => divi_squad_fs() instanceof \Freemius ? divi_squad_fs()->get_account_url() : '',
				'plugins'    => admin_url( 'plugins.php' ),
				'dashboard'  => admin_url( 'admin.php?page=divi_squad_dashboard#/' ),
				'modules'    => admin_url( 'admin.php?page=divi_squad_dashboard#/modules' ),
				'extensions' => admin_url( 'admin.php?page=divi_squad_dashboard#/extensions' ),
				'whats_new'  => admin_url( 'admin.php?page=divi_squad_dashboard#/whats-new' ),
			),
			'l10n'               => array(
				'dashboard'  => esc_html__( 'Dashboard', 'squad-modules-for-divi' ),
				'modules'    => esc_html__( 'Modules', 'squad-modules-for-divi' ),
				'extensions' => esc_html__( 'Extension', 'squad-modules-for-divi' ),
				'whats_new'  => esc_html__( 'What\'s New', 'squad-modules-for-divi' ),
			),
			'plugins'            => WpUtils::get_active_plugins(),
			'notices'            => array(
				'has_welcome' => ( new Discount() )->can_render_it(),
			),
		);

		return array_merge_recursive( $exists_data, $admin_localize );
	}
}
