<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Freemius connection class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integrations;

use DiviSquad\Utils\Asset;
use DiviSquad\Utils\Singleton;
use function add_action;
use function divi_squad_fs;
use function divi_squad_get_pro_basename;
use function divi_squad_is_pro_activated;
use function esc_html__;
use function fs_dynamic_init;
use function get_current_screen;
use function get_plugin_data;
use function load_template;

/**
 * Freemius SDK integration class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
final class Freemius {

	use Singleton;

	/**
	 * Store and retrieve the instance of Freemius SDK
	 *
	 * @var \Freemius The instance of Freemius SDK.
	 */
	private static $fs;

	/**
	 * Integration Constructor
	 *
	 * @throws \Freemius_Exception Thrown when an API call returns an exception.
	 */
	private function __construct() {
		if ( null === self::$fs && self::is_installed() ) {
			// Include Freemius SDK.
			require_once self::get_sdk_start_file_path();

			// Create Freemius SDK instance.
			self::$fs = fs_dynamic_init(
				array(
					'id'                  => '14784',
					'slug'                => 'squad-modules-for-divi',
					'premium_slug'        => 'squad-modules-pro-for-divi',
					'type'                => 'plugin',
					'public_key'          => 'pk_016b4bcadcf416ffec072540ef065',
					'is_premium'          => divi_squad_is_pro_activated(),
					'premium_suffix'      => 'Pro',
					'has_premium_version' => true,
					'has_addons'          => false,
					'has_paid_plans'      => true,
					'has_affiliation'     => 'selected',
					'menu'                => array(
						'slug'        => 'divi_squad_dashboard',
						'first-path'  => 'admin.php?page=divi_squad_dashboard',
						'affiliation' => true,
					),
					'permission'          => array(
						'enable_anonymous' => true,
						'anonymous_mode'   => true,
					),
				)
			);

			// Update some features.
			self::$fs->override_i18n(
				array(
					'hey'                         => esc_html__( 'Hey', 'squad-modules-for-divi' ),
					'yee-haw'                     => esc_html__( 'Hello Friend', 'squad-modules-for-divi' ),
					'opt-in-connect'              => esc_html__( "Yes - I'm in!", 'squad-modules-for-divi' ),
					'skip'                        => esc_html__( 'Not today', 'squad-modules-for-divi' ),
					/* translators: %s: Plan title */
					'activate-x-features'         => esc_html__( 'Activate %s Plugin', 'squad-modules-for-divi' ),
					/* translators: %s The plugin name, example: Squad Modules Lite */
					'plugin-x-activation-message' => esc_html__( '%s was successfully activated.', 'squad-modules-for-divi' ),
					/* translators: %s The module type */
					'premium-activated-message'   => esc_html__( 'Premium %s was successfully activated.', 'squad-modules-for-divi' ),
					/* translators: %1$s: Product title; %2$s: Plan title; %3$s: Activation link */
					'activate-premium-version'    => esc_html__( ' The paid plugin of %1$s is already installed. Please activate it to start benefiting the %2$s plugin. %3$s', 'squad-modules-for-divi' ),
				)
			);
			self::$fs->add_filter( 'hide_account_tabs', '__return_true' );
			self::$fs->add_filter( 'deactivate_on_activation', '__return_false' );
			self::$fs->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );
			self::$fs->add_filter( 'is_submenu_visible', array( $this, 'fs_hook_is_submenu_visible' ), 10, 2 );

			// Notice
			// Ref: (another step) vendor/freemius/wordpress-sdk/includes/class-freemius.php:7223
			// Ref: (download pro plugin) vendor/freemius/wordpress-sdk/includes/class-freemius.php:24788
			// Ref: vendor/freemius/wordpress-sdk/includes/managers/class-fs-admin-notice-manager.php:269
			self::$fs->add_filter( 'show_admin_notice', array( $this, 'fs_hook_show_admin_notice' ), 10, 2 );

			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:25632
			// Ref: vendor/freemius/wordpress-sdk/templates/plugin-icon.php:21
			// Ref: vendor/freemius/wordpress-sdk/templates/connect.php:162
			self::$fs->add_filter( 'plugin_icon', array( $this, 'fs_hook_plugin_icon' ) );

			// Plugin title
			// Ref: vendor/freemius/wordpress-sdk/templates/account.php:404
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:10514
			self::$fs->add_filter( 'plugin_title', array( $this, 'fs_hook_plugin_title' ) );

			// Plugin Version
			// Ref: vendor/freemius/wordpress-sdk/templates/account.php:448
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:10500
			self::$fs->add_filter( 'plugin_version', array( $this, 'fs_hook_plugin_version' ) );

			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:23479
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:23548
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:23525
			self::$fs->add_filter( 'templates/connect.php', array( $this, 'fs_hook_get_overrides_template' ) );
			self::$fs->add_filter( 'templates/account.php', array( $this, 'fs_hook_get_overrides_account_template' ) );
			self::$fs->add_filter( '/forms/affiliation.php', array( $this, 'fs_hook_get_overrides_account_template' ) );

			// Ref: vendor/freemius/wordpress-sdk/start.php:416
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:10145
			// $divi_squad_fs->add_action( 'after_uninstall', array( $self, 'fs_hook_uninstall_cleanup' ) );

			// Download Button
			// Ref: vendor/freemius/wordpress-sdk/templates/account.php:616

			// Active Pro plugin
			// Updated with new template.
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:5739
			// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:24766

			// Connect mesage on premium
			// Ref: vendor/freemius/wordpress-sdk/templates/connect.php:215
			// self::$fs->add_filter( 'connect-message_on-premium', array( $this, 'fs_hook_connect-message_on_premium' ) );
		}

		// Add hooks at the admin area.
		add_action( 'admin_head', array( $this, 'wp_hook_clean_admin_content_section' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_hook_enqueue_scripts' ) );
	}

	/**
	 * Get the Freemius start file path.
	 *
	 * @return string
	 */
	private static function get_sdk_start_file_path() {
		// Freemius sdk path.
		return realpath( DIVI_SQUAD_DIR_PATH . 'freemius/start.php' );
	}

	/**
	 * Retrieve the instance of Freemius SDK
	 *
	 * @return \Freemius The instance of Freemius SDK.
	 */
	public static function get_fs() {
		return self::$fs;
	}

	/**
	 * Get the Freemius' menu list.
	 *
	 * @return array
	 */
	public static function get_menu_lists() {
		return array(
			'plugins.php',
			'toplevel_page_divi_squad_dashboard',
			'divi-squad_page_divi_squad_dashboard-account',
			'divi-squad_page_divi_squad_dashboard-affiliation',
			'divi-squad_page_divi_squad_dashboard-contact',
		);
	}

	/**
	 * Get the status of Freemius sdk is installed or not.
	 *
	 * @return bool
	 */
	public static function is_installed() {
		return file_exists( self::get_sdk_start_file_path() );
	}

	/**
	 * Show the contact submenu item only when the user has a valid non-expired license.
	 *
	 * @param bool   $is_visible The filtered value. Whether the submenu item should be visible or not.
	 * @param string $menu_id    The ID of the submenu item.
	 *
	 * @return bool If true, the menu item should be visible.
	 */
	public function fs_hook_is_submenu_visible( $is_visible, $menu_id ) {
		if ( 'support' === $menu_id ) {
			return divi_squad_fs()->is_free_plan();
		}

		if ( 'contact' !== $menu_id ) {
			return $is_visible;
		}

		return false;
	}

	/**
	 * Update plugin icon url for opt-in screen,.
	 *
	 * @return string The src url of plugin icon.
	 */
	public function fs_hook_plugin_icon() {
		return DIVI_SQUAD_DIR_PATH . 'build/admin/images/divi-squad-default.png';
	}

	/**
	 * Get the account template path.
	 *
	 * @param string $content The template content.
	 *
	 * @return string
	 */
	public function fs_hook_get_overrides_account_template( $content ) {
		ob_start();

		// ref: vendor/freemius-wordpress-sdk/includes/class-freemius.php:23525
		// ref: vendor/freemius-wordpress-sdk/includes/class-freemius.php:23535
		$account_template_path = sprintf( '%1$s/admin/subscription.php', DIVI_SQUAD_TEMPLATES_PATH );
		load_template( $account_template_path, true, $content );

		return ob_get_clean();
	}

	/**
	 * Get the account template path.
	 *
	 * @param string $content The template content.
	 *
	 * @return string
	 */
	public function fs_hook_get_overrides_template( $content ) {
		ob_start();

		// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:13473
		// Ref: vendor/freemius/wordpress-sdk/includes/class-freemius.php:23479
		$account_template_path = sprintf( '%1$s/admin/template-overrides.php', DIVI_SQUAD_TEMPLATES_PATH );
		load_template( $account_template_path, true, $content );

		return ob_get_clean();
	}

	/**
	 * Control the visibility of admin notices.
	 *
	 * @param string $module_unique_affix Module's unique affix.
	 * @param mixed  $value               The value on which the filters hooked to `$tag` are applied on.
	 *
	 * @return bool The filtered value after all hooked functions are applied to it.
	 * @since  2.0.0
	 */
	public function fs_hook_show_admin_notice( $module_unique_affix, $value ) {
		$notice_type = ! empty( $value['type'] ) ? $value['type'] : '';
		$notice_id   = ! empty( $value['id'] ) ? $value['id'] : '';
		$manager_id  = ! empty( $value['manager_id'] ) ? $value['manager_id'] : '';

		return ! ( ( 'update-nag' === $notice_type && 'squad-modules-for-divi' === $manager_id ) || ( 'success' === $notice_type && 'plan_upgraded' === $notice_id ) );
	}

	/**
	 * Modify the plugin title based on free and pro plugin
	 *
	 * @param string $title The plugin title.
	 *
	 * @return string The activated plugin title between free and pro
	 * @since  2.0.0
	 */
	public function fs_hook_plugin_title( $title ) {
		if ( function_exists( 'divi_squad_fs' ) && divi_squad_fs()->can_use_premium_code() && divi_squad_is_pro_activated() ) {
			return esc_html__( 'Squad Modules Pro', 'squad-modules-for-divi' );
		}

		return $title;
	}

	/**
	 * Modify the plugin version based on free and pro plugin
	 *
	 * @param string $version The plugin version.
	 *
	 * @return string The activated plugin title between free and pro
	 * @since  2.0.0
	 */
	public function fs_hook_plugin_version( $version ) {
		if ( function_exists( 'divi_squad_fs' ) && divi_squad_is_pro_activated() && divi_squad_fs()->can_use_premium_code() ) {
			// Premium plugin basename.
			$pro_basename = divi_squad_get_pro_basename();

			// Retrieve plugin's metadata.
			$path_root   = realpath( dirname( DIVI_SQUAD_DIR_PATH ) );
			$plugin_data = get_plugin_data( "$path_root/$pro_basename" );

			return ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : $version;
		}

		return $version;
	}

	/**
	 * Remove all notices from the account page.
	 *
	 * @return void
	 */
	public function wp_hook_clean_admin_content_section() {
		$screen = get_current_screen();

		if ( $screen && in_array( $screen->id, self::get_menu_lists(), true ) ) {
			\Freemius::_clean_admin_content_section_hook();
		}
	}

	/**
	 * Enqueue the plugin's scripts and styles files in the WordPress admin area.
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function wp_hook_enqueue_scripts( $hook_suffix ) {
		// Load plugin asset in the all admin pages.
		Asset::style_enqueue( 'admin-freemius', Asset::admin_asset_path( 'admin-freemius', array( 'ext' => 'css' ) ) );

		// Load special styles for freemius pages.
		if ( in_array( $hook_suffix, self::get_menu_lists(), true ) ) {
			Asset::style_enqueue( 'admin-components', Asset::admin_asset_path( 'admin-components', array( 'ext' => 'css' ) ) );
			Asset::style_enqueue( 'admin', Asset::admin_asset_path( 'admin', array( 'ext' => 'css' ) ) );
		}

		// Page lists.
		// vendor/freemius-wordpress-sdk/templates/forms/affiliation.php:96
		// vendor/freemius-wordpress-sdk/templates/account.php:274
	}
}
