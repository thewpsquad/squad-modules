<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * The publisher connection class
 *
 * @since   1.0.0
 * @author  The WP Squad <support@squadmodules.com>
 * @package DiviSquad
 */

namespace DiviSquad\Integrations\Freemius;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Core\Assets;
use DiviSquad\Core\Contracts\Hookable;
use DiviSquad\Core\Supports\Polyfills\Constant;
use Freemius;
use Throwable;
use function add_action;
use function esc_html__;
use function fs_dynamic_init;
use function load_template;

/**
 * Distribution SDK integration class.
 *
 * @since   1.0.0
 * @package DiviSquad
 */
class Distribution implements Hookable {

	/**
	 * Store and retrieve the instance of publisher SDK
	 *
	 * @var Freemius|null
	 */
	private ?Freemius $fs = null;

	/**
	 * Whether the Distribution is initialized.
	 *
	 * @var bool
	 */
	private bool $is_initialized = false;

	/**
	 * Integration Constructor
	 */
	public function __construct() {
		$this->initialize();
	}

	/**
	 * Initialize the Freemius SDK
	 *
	 * @return void Whether initialization was successful
	 */
	public function initialize(): void {
		try {
			// Include publisher SDK.
			$sdk_path = $this->get_sdk_start_file_path();
			if ( ! file_exists( $sdk_path ) ) {
				return;
			}

			require_once $sdk_path;

			$fs_init_args = array(
				'id'                  => '14784',
				'slug'                => 'squad-modules-for-divi',
				'premium_slug'        => 'squad-modules-pro-for-divi',
				'type'                => 'plugin',
				'public_key'          => 'pk_016b4bcadcf416ffec072540ef065',
				'is_premium'          => false,
				'premium_suffix'      => 'Pro',
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'is_org_compliant'    => true,
				'has_affiliation'     => 'selected',
				'enable_anonymous'    => true,
				'menu'                => array(
					'slug'       => 'divi_squad',
					'first-path' => 'admin.php?page=divi_squad',
					'contact'    => false,
				),
				'parallel_activation' => array(
					'enabled'                  => true,
					'premium_version_basename' => divi_squad()->get_pro_basename(),
				),
			);

			// Create publisher SDK instance.
			$this->fs = fs_dynamic_init( $fs_init_args );

			// Set initialized flag.
			$this->is_initialized = true;

			// Set global reference for backward compatibility.
			global $divi_squad_fs;
			if ( ! isset( $divi_squad_fs ) ) {
				$divi_squad_fs = $this->fs;
			}

			// Initialize hooks and filters.
			$this->register_hooks();

			return;
		} catch ( Throwable $e ) {
			// Log error but don't throw - we'll handle initialization failure gracefully.
			divi_squad()->log_error( $e, 'Distribution initialization failed', false );

			return;
		}
	}

	/**
	 * Initialize hooks and filters
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		if ( ! $this->is_initialized || null === $this->fs ) {
			return;
		}

		// Work in anonymous mode automatically, but ONLY for sites that are neither
		// registered nor licensed. Calling skip_connection() unconditionally pins a
		// registered/licensed site to the anonymous state, and Freemius only adds the
		// Account (and other) submenu items when is_registered() is true
		// (class-freemius.php:18913) — so an unconditional skip makes the Account menu
		// vanish for paying users. Guarding it keeps free installs quiet while letting
		// licensed/registered sites resolve their account menu.
		if ( ! $this->fs->is_registered() && ! $this->fs->has_active_valid_license() ) {
			$this->fs->skip_connection();
		}

		// Freemius behaviour tweaks.
		$this->fs->add_filter( 'enable_cpt_advanced_menu_logic', '__return_true' );
		$this->fs->add_filter( 'hide_account_tabs', '__return_true' );
		$this->fs->add_filter( 'deactivate_on_activation', '__return_false' );
		$this->fs->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );
		$this->fs->add_filter( 'is_submenu_visible', array( $this, 'fs_hook_is_submenu_visible' ), 10, 2 );
		$this->fs->add_filter( 'show_admin_notice', array( $this, 'fs_hook_show_admin_notice' ), 10, 2 );
		$this->fs->add_filter( 'plugin_icon', array( $this, 'fs_hook_plugin_icon' ) );

		// Override the default Freemius screen templates with the plugin's own.
		$this->fs->add_filter( '/forms/affiliation.php', array( $this, 'fs_hook_get_account_template' ) );
		$this->fs->add_filter( 'templates/account.php', array( $this, 'fs_hook_get_account_template' ) );
		$this->fs->add_filter( 'templates/connect.php', array( $this, 'fs_hook_get_default_template' ) );
		$this->fs->add_filter( 'templates/checkout.php', array( $this, 'fs_hook_get_default_template' ) );
		$this->fs->add_filter( 'templates/pricing.php', array( $this, 'fs_hook_get_default_template' ) );

		// Register/enqueue the publisher stylesheet in the admin.
		add_action( 'divi_squad_register_admin_assets', array( $this, 'register_scripts' ) );
		add_action( 'divi_squad_enqueue_admin_assets', array( $this, 'enqueue_scripts' ) );

		// Relabel the Freemius submenu items under the Divi Squad menu.
		add_action( 'admin_menu', array( $this, 'wp_hook_update_admin_menu_title' ), Constant::PHP_INT_MAX );
	}

	/**
	 * Retrieve the instance of Freemius SDK
	 *
	 * @return Freemius|null The instance of Freemius SDK or null if not initialized.
	 */
	public function get_fs(): ?Freemius {
		if ( ! $this->is_initialized() ) {
			$this->initialize();
		}

		return $this->fs;
	}

	/**
	 * Check if the Distribution is properly initialized.
	 *
	 * @return bool Whether the Distribution is initialized.
	 */
	public function is_initialized(): bool {
		return $this->is_initialized;
	}

	/**
	 * Get the publisher start file path.
	 *
	 * @return string
	 */
	private function get_sdk_start_file_path(): string {
		return divi_squad()->get_path( '/freemius/start.php' );
	}

	/**
	 * Control Freemius submenu visibility.
	 *
	 * The Support item is shown only on the free plan; every Freemius submenu item is
	 * hidden while the plugin's requirements are unmet.
	 *
	 * @param bool   $is_visible Whether the submenu item should be visible.
	 * @param string $menu_id    The ID of the submenu item.
	 *
	 * @return bool If true, the menu item should be visible.
	 */
	public function fs_hook_is_submenu_visible( bool $is_visible, string $menu_id ): bool {
		try {
			if ( ! $this->is_initialized || null === $this->fs ) {
				return $is_visible;
			}

			if ( 'support' === $menu_id ) {
				$is_visible = $this->fs->is_free_plan();
			}

			return $is_visible && divi_squad()->requirements->is_fulfilled();
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, sprintf( 'Failed to determine submenu visibility for menu ID: %s', $menu_id ) );

			return false; // Default to not showing when error occurs.
		}
	}

	/**
	 * Plugin icon url for the opt-in screen.
	 *
	 * @return string The src url of plugin icon.
	 */
	public function fs_hook_plugin_icon(): string {
		return divi_squad()->get_path( '/build/admin/images/logos/divi-squad-default.png' );
	}

	/**
	 * Get the account template for Freemius screens.
	 *
	 * @param string $content The template content.
	 *
	 * @return string|false
	 */
	public function fs_hook_get_account_template( string $content ) {
		try {
			ob_start();

			load_template(
				divi_squad()->get_template_path( 'admin/publisher/account.php' ),
				true,
				array( 'content' => $content )
			);

			return ob_get_clean();
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to load account template content' );

			return $content; // Return original content as fallback.
		}
	}

	/**
	 * Get the default template for Freemius screens.
	 *
	 * @param string $content The template content.
	 *
	 * @return string|false
	 */
	public function fs_hook_get_default_template( string $content ) {
		try {
			ob_start();

			load_template(
				divi_squad()->get_template_path( 'admin/publisher/default.php' ),
				true,
				array( 'content' => $content )
			);

			return ob_get_clean();
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to load default template content' );

			return $content; // Return original content as fallback.
		}
	}

	/**
	 * Control the visibility of admin notices.
	 *
	 * @since  2.0.0
	 *
	 * @param bool  $show   Whether to show the notice.
	 * @param array<string, mixed> $notice Notice data — keys: type, id, manager_id, message, etc.
	 *
	 * @return bool
	 */
	public function fs_hook_show_admin_notice( bool $show, array $notice ): bool {
		if ( ! $show ) {
			return false;
		}

		$notice_type = $notice['type'] ?? '';
		$notice_id   = $notice['id'] ?? '';
		$manager_id  = $notice['manager_id'] ?? '';
		$plugin_id   = divi_squad()->get_name();

		return ! ( ( 'update-nag' === $notice_type && $plugin_id === $manager_id ) || ( 'success' === $notice_type && 'plan_upgraded' === $notice_id ) );
	}

	/**
	 * Register the plugin's scripts and styles files in the WordPress admin area.
	 *
	 * @param Assets $assets The assets manager instance.
	 *
	 * @return void
	 */
	public function register_scripts( Assets $assets ): void {
		$assets->register_style(
			'publisher',
			array(
				'file' => 'publisher',
				'path' => 'admin',
				'deps' => array(),
			)
		);
	}

	/**
	 * Enqueue the plugin's scripts and styles files in the WordPress admin area.
	 *
	 * @param Assets $assets The assets manager instance.
	 *
	 * @return void
	 */
	public function enqueue_scripts( Assets $assets ): void {
		$assets->add_body_class( 'publisher' );
		$assets->enqueue_style( 'publisher' );
	}

	/**
	 * Relabel the Freemius submenu items under the Divi Squad menu.
	 *
	 * @since 3.3.0
	 *
	 * @return void
	 */
	public function wp_hook_update_admin_menu_title(): void {
		try {
			global $submenu;

			if ( ! isset( $submenu['divi_squad'] ) || ! is_array( $submenu['divi_squad'] ) ) {
				return;
			}

			$titles = array(
				'divi_squad-affiliation'      => esc_html__( 'Affiliation', 'squad-modules-for-divi' ),
				'divi_squad-account'          => esc_html__( 'Account', 'squad-modules-for-divi' ),
				'divi_squad-wp-support-forum' => esc_html__( 'Support Forum', 'squad-modules-for-divi' ),
				'divi_squad-pricing'          => esc_html__( 'Pricing', 'squad-modules-for-divi' ),
			);
			$prefix = esc_html__( 'Divi Squad', 'squad-modules-for-divi' );

			foreach ( $submenu['divi_squad'] as $index => $data ) {
				if ( isset( $data[2], $data[3], $titles[ $data[2] ] ) ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$submenu['divi_squad'][ $index ][3] = sprintf( '%s ‹ %s', $prefix, $titles[ $data[2] ] );
				}
			}
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Failed to update admin menu titles' );
		}
	}
}
