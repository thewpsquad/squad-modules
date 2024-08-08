<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers;

use DiviSquad\Base\Factories\SquadFeatures as ManagerBase;
use DiviSquad\Base\Memory;
use DiviSquad\Utils\Polyfills\Arr;
use function divi_squad;

/**
 * Extension Manager class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Extensions extends ManagerBase {

	/**
	 * Get available extensions.
	 *
	 * @return array[]
	 */
	public function get_registered_list() {
		$available_extensions = array(
			array(
				'name'               => 'JSON',
				'label'              => esc_html__( 'JSON File Upload Support', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like allow JSON file through WordPress Media Uploader.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'media-upload',
			),
			array(
				'name'               => 'SVG',
				'label'              => esc_html__( 'SVG Image Upload Support', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like allow svg file through WordPress Media Uploader.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'media-upload',
			),
			array(
				'name'               => 'Font_Upload',
				'label'              => esc_html__( 'Custom Fonts Upload Support', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like allow Font file through WordPress Media Uploader.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'category'           => 'media-upload',
			),
			array(
				'name'               => 'Divi_Layout_Shortcode',
				'label'              => esc_html__( 'Divi Library Shortcode', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like add Divi library shortcode feature.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'enhancement',
			),
			array(
				'name'               => 'Copy',
				'label'              => esc_html__( 'Copy Post or Page', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like add Post or Page coping feature.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.8',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'enhancement',
			),
		);

		return Arr::sort( $available_extensions, 'name' );
	}

	/**
	 * Load enabled extensions
	 *
	 * @param string $path The defined directory.
	 *
	 * @return void
	 */
	public function load_extensions( $path ) {
		if ( ! class_exists( \DiviSquad\Base\Extension::class ) ) {
			return;
		}

		$this->load_extensions_files( $path, divi_squad()->memory );
	}

	/**
	 * Load enabled extensions
	 *
	 * @param string $path   The defined directory.
	 * @param Memory $memory The instance of Memory class.
	 *
	 * @return void
	 */
	protected function load_extensions_files( $path, $memory ) {
		// Load enabled extensions.
		$activated  = $memory->get( 'active_extensions', array() );
		$registered = $this->get_registered_list();
		$defaults   = $this->get_default_registries();

		// Get verified active modules.
		$activated_extensions = $this->get_verified_registries( $registered, $defaults, $activated );

		foreach ( $activated_extensions as $activated_extension ) {
			$extension_name = $activated_extension['name'];
			$extension_file = sprintf( '%1$s/Extensions/%2$s.php', $path, $extension_name );

			if ( file_exists( $extension_file ) ) {
				require_once $extension_file;
			}
		}
	}

	/**
	 * Get inactive extensions.
	 *
	 * @return array
	 */
	public function get_inactive_registries() {
		return $this->get_filtered_registries(
			$this->get_registered_list(),
			function ( $module ) {
				return ! $module['is_default_active'];
			}
		);
	}

	/**
	 * Get default active extensions.
	 *
	 * @return array
	 */
	public function get_default_registries() {
		return $this->get_filtered_registries(
			$this->get_registered_list(),
			function ( $module ) {
				return $module['is_default_active'];
			}
		);
	}
}
