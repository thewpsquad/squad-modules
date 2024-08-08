<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Define asset loading helper class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Admin\Assets;

/**
 * Utils class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Utils {
	/**
	 * Validate the relative path.
	 *
	 * @param string $relative_path The path string for validation.
	 *
	 * @return string
	 */
	public static function validate_relative_path( $relative_path ) {
		if ( str_starts_with( $relative_path, './' ) ) {
			$relative_path = str_replace( './', '/', $relative_path );
		}

		if ( ! str_starts_with( $relative_path, '/' ) ) {
			$relative_path = sprintf( '/%1$s', $relative_path );
		}

		return $relative_path;
	}

	/**
	 * Resolve the resource root path.
	 *
	 * @return string
	 */
	public static function root_path() {
		return DISQ_DIR_PATH;
	}

	/**
	 * Resolve the resource root uri.
	 *
	 * @return string
	 */
	public static function root_path_uri() {
		return DISQ_DIR_URL;
	}

	/**
	 * Resolve the resource path.
	 *
	 * @param string $relative_path The current path string.
	 *
	 * @return string
	 */
	public static function resolve_file_path( $relative_path ) {
		return sprintf( '%1$s%2$s', self::root_path(), self::validate_relative_path( $relative_path ) );
	}

	/**
	 * Resolve the resource uri.
	 *
	 * @param string $relative_path The current path string.
	 *
	 * @return string
	 */
	public static function resolve_file_uri( $relative_path ) {
		return sprintf( '%1$s%2$s', self::root_path_uri(), self::validate_relative_path( $relative_path ) );
	}

	/**
	 * Process asset path and version data.
	 *
	 * @param string $path The asset relative path.
	 *
	 * @return array
	 */
	public static function process_asset_path_data( $path ) {
		$full_path     = '';
		$validate_path = self::validate_relative_path( $path );

		$version      = DISQ_VERSION;
		$dependencies = array();

		// search minified file when it is existed.
		foreach ( array( 'js', 'css' ) as $file_ext ) {
			$minified_file = str_replace( array( ".{$file_ext}" ), array( ".min.{$file_ext}" ), $validate_path );

			if ( file_exists( self::resolve_file_path( $minified_file ) ) ) {
				$validate_path = $minified_file;
			}

			// search version file when it is exists.
			$minified_version_file = str_replace( array( ".min.{$file_ext}" ), array( '.min.asset.php' ), $validate_path );
			if ( str_ends_with( $minified_version_file, '.asset.php' ) && file_exists( self::resolve_file_path( $minified_version_file ) ) ) {
				$minified_asset_data = include self::resolve_file_path( $minified_version_file );
				$version             = isset( $minified_asset_data['version'] ) ? $minified_asset_data['version'] : DISQ_VERSION;
				$dependencies        = isset( $minified_asset_data['dependencies'] ) ? $minified_asset_data['dependencies'] : array();
			}

			$main_version_file = str_replace( array( ".{$file_ext}" ), array( '.asset.php' ), $validate_path );
			if ( str_ends_with( $main_version_file, '.asset.php' ) && file_exists( self::resolve_file_path( $main_version_file ) ) ) {
				$main_asset_data = include self::resolve_file_path( $main_version_file );
				$version         = isset( $main_asset_data['version'] ) ? $main_asset_data['version'] : DISQ_VERSION;
				$dependencies    = isset( $main_asset_data['dependencies'] ) ? $main_asset_data['dependencies'] : array();
			}
		}

		$plugin_url_root = untrailingslashit( self::root_path_uri() );
		$full_path       = "{$plugin_url_root}{$validate_path}";

		return array(
			'path'         => $full_path,
			'version'      => $version,
			'dependencies' => $dependencies,
		);
	}

	/**
	 * Enqueue javascript.
	 *
	 * @param string $keyword The keyword name for an enqueue handle.
	 * @param string $path The asset relative path.
	 * @param array  $deps The dependencies for the current asset.
	 *
	 * @return void
	 */
	public static function asset_enqueue( $keyword, $path, array $deps = array() ) {
		$asset_data   = self::process_asset_path_data( $path );
		$handle       = sprintf( 'disq-%1$s', $keyword );
		$dependencies = array_merge( $asset_data['dependencies'], $deps );

		// Load script file.
		wp_enqueue_script( $handle, $asset_data['path'], $dependencies, DISQ_VERSION, true );
	}

	/**
	 * Enqueue styles.
	 *
	 * @param string $keyword The keyword name for an enqueue handle.
	 * @param string $path The asset relative path.
	 * @param array  $deps The dependencies for the current asset.
	 * @param string $media The media query for the current asset.
	 *
	 * @return void
	 */
	public static function style_enqueue( $keyword, $path, $deps = array(), $media = 'all' ) {
		$asset_data = self::process_asset_path_data( $path );
		$handle     = sprintf( 'disq-%1$s', $keyword );

		// Load stylesheet file.
		wp_enqueue_style( $handle, $asset_data['path'], $deps, DISQ_VERSION, $media );
	}
}
