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
		);
	}

}
