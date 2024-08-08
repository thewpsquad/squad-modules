<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Utils\Helper;
use function DiviSquad\divi_squad;

/**
 * Extensions class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Extensions {

	/**
	 * Activate all extensions.
	 */
	public function activate_extensions() {
		$memory = divi_squad()->get_memory();
		$memory->set( 'extensions', $this->get_available_extensions() );
		$memory->set( 'default_active_extensions', $this->get_inactive_extensions() );
	}

	/**
	 * Get available extensions.
	 *
	 * @return array[]
	 */
	public function get_available_extensions() {
		$available_extensions = array(
			array(
				'name'               => 'JSON',
				'label'              => esc_html__( 'JSON Upload', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like allow JSON file through WordPress Media Uploader.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'media-upload',
			),
			array(
				'name'               => 'SVG',
				'label'              => esc_html__( 'SVG Upload', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like allow svg file through WordPress Media Uploader.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'media-upload',
			),
			array(
				'name'               => 'Font_Upload',
				'label'              => esc_html__( 'Font Upload', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like allow Font file through WordPress Media Uploader.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'category'           => 'media-upload',
			),
			array(
				'name'               => 'Divi_Library_Shortcode',
				'label'              => esc_html__( 'Divi Library Shortcode', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enable this feature only if you would like add Divi library shortcode feature.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'category'           => 'divi-enhancement',
			),
		);

		$sorts = Helper::array_sort( $available_extensions, 'name' );

		return array_values( $sorts );
	}

	/**
	 * Check current extension is an inactive extension.
	 *
	 * @param array $extension The current extension.
	 *
	 * @return array|null
	 */
	protected function is_inactive_extension( $extension ) {
		return ! $extension['is_default_active'] ? $extension : null;
	}

	/**
	 *  Check current extension is an active extension.
	 *
	 * @param array $extension The current extension.
	 *
	 * @return array|null
	 */
	protected function is_active_extension( $extension ) {
		return $extension['is_default_active'] ? $extension : null;
	}

	/**
	 * Get filtered extensions.
	 *
	 * @param callable $callback   The callback function for filter the current extension.
	 * @param array    $extensions The available extensions.
	 *
	 * @return array
	 */
	protected function get_filtered_extensions( $callback, $extensions ) {
		return array_values( array_filter( array_map( $callback, $extensions ) ) );
	}

	/**
	 * Get inactive extensions.
	 *
	 * @return array
	 */
	protected function get_inactive_extensions() {
		return $this->get_filtered_extensions( array( $this, 'is_inactive_extension' ), $this->get_available_extensions() );
	}

	/**
	 * Get default active extensions.
	 *
	 * @return array
	 */
	protected function get_default_active_extensions() {
		return $this->get_filtered_extensions( array( $this, 'is_active_extension' ), $this->get_available_extensions() );
	}

	/**
	 * Load enabled extensions
	 *
	 * @param string $path              The defined directory.
	 * @param array  $current_activates The activated extensions.
	 *
	 * @return void
	 */
	protected function load_extensions_files( $path, $current_activates ) {
		$active_extensions = $this->get_default_active_extensions();

		if ( is_array( $current_activates ) ) {
			$active_extensions = $current_activates;
		}

		foreach ( $active_extensions as $active_extension ) {
			$extension_name = is_string( $active_extension ) ? $active_extension : $active_extension['name'];
			$extension_path = sprintf( '%1$s/extensions/%2$s.php', $path, $extension_name );

			if ( file_exists( $extension_path ) ) {
				require_once $extension_path;
			}
		}
	}

	/**
	 * Load enabled extensions
	 *
	 * @param string $path The defined directory.
	 *
	 * @return void
	 */
	public function load_extensions( $path ) {
		if ( ! class_exists( \DiviSquad\Base\Extensions::class ) ) {
			return;
		}

		$memory = divi_squad()->get_memory();
		$this->load_extensions_files( $path, $memory->get( 'active_extensions' ) );
	}
}
