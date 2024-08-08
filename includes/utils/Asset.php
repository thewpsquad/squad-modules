<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Asset loading helper.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Utils;

use function DiviSquad\divi_squad;

/**
 * Utils class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
class Asset {

	/**
	 * Get the version
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_the_version() {
		return divi_squad()->get_version();
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
	 * Resolve the resource path.
	 *
	 * @param string $relative_path The current path string.
	 *
	 * @return string
	 */
	public static function resolve_file_path( $relative_path ) {
		return sprintf( '%1$s%2$s', static::root_path(), static::validate_relative_path( $relative_path ) );
	}

	/**
	 * Resolve the resource uri.
	 *
	 * @param string $relative_path The current path string.
	 *
	 * @return string
	 */
	public static function resolve_file_uri( $relative_path ) {
		return sprintf( '%1$s%2$s', static::root_path_uri(), static::validate_relative_path( $relative_path ) );
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
		$validate_path = static::validate_relative_path( $path );

		$version      = static::get_the_version();
		$dependencies = array();

		// search minified file when it is existed.
		foreach ( array( 'js', 'css' ) as $file_ext ) {
			// Check for the minified version in the server on production mode.
			$minified_asset_file = str_replace( array( ".$file_ext" ), array( ".min.$file_ext" ), $validate_path );
			if ( str_ends_with( $validate_path, ".$file_ext" ) && ! str_ends_with( $validate_path, ".min.$file_ext" ) && file_exists( static::resolve_file_path( $minified_asset_file ) ) ) {
				$validate_path = $minified_asset_file;
			}

			// Verify that the current file is a minified and located in the current physical device.
			if ( str_ends_with( $validate_path, ".min.$file_ext" ) && file_exists( static::resolve_file_path( $validate_path ) ) ) {
				$minified_version_file = str_replace( array( ".min.$file_ext" ), array( '.min.asset.php' ), $validate_path );
				if ( file_exists( static::resolve_file_path( $minified_version_file ) ) ) {
					$minified_asset_data = include static::resolve_file_path( $minified_version_file );
					$version             = ! empty( $minified_asset_data['version'] ) ? $minified_asset_data['version'] : $version;
					$dependencies        = ! empty( $minified_asset_data['dependencies'] ) ? $minified_asset_data['dependencies'] : $dependencies;
				}
			}

			// Verify that the current file is non-minified and located in the current physical device.
			if ( str_ends_with( $validate_path, ".$file_ext" ) && file_exists( static::resolve_file_path( $validate_path ) ) ) {
				$main_version_file = str_replace( array( ".$file_ext" ), array( '.asset.php' ), $validate_path );
				if ( str_ends_with( $main_version_file, '.asset.php' ) && file_exists( static::resolve_file_path( $main_version_file ) ) ) {
					$main_asset_data = include static::resolve_file_path( $main_version_file );
					$version         = ! empty( $main_asset_data['version'] ) ? $main_asset_data['version'] : $version;
					$dependencies    = ! empty( $main_asset_data['dependencies'] ) ? $main_asset_data['dependencies'] : $dependencies;
				}
			}
		}

		// Collect actual path for the current asset file.
		$plugin_url_root = untrailingslashit( static::root_path_uri() );
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
	 * @param string $keyword Name of the javascript. Should be unique.
	 * @param string $path    Relative path of the javascript, or path of the stylesheet relative to the WordPress root directory. Default empty.
	 * @param array  $deps    Optional. An array of registered javascript handles this stylesheet depends on. Default empty array.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function asset_enqueue( $keyword, $path, array $deps = array() ) {
		$asset_data   = self::process_asset_path_data( $path );
		$handle       = sprintf( 'disq-%1$s', $keyword );
		$dependencies = array_merge( $asset_data['dependencies'], $deps );

		// Load script file.
		wp_enqueue_script( $handle, $asset_data['path'], $dependencies, static::get_the_version(), true );
	}

	/**
	 * Enqueue styles.
	 *
	 * @param string $keyword Name of the stylesheet. Should be unique.
	 * @param string $path    Relative path of the stylesheet, or path of the stylesheet relative to the WordPress root directory. Default empty.
	 * @param array  $deps    Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media   Optional. The media for which this stylesheet has been defined. Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function style_enqueue( $keyword, $path, $deps = array(), $media = 'all' ) {
		$asset_data = static::process_asset_path_data( $path );
		$handle     = sprintf( 'disq-%1$s', $keyword );

		// Load stylesheet file.
		wp_enqueue_style( $handle, $asset_data['path'], $deps, static::get_the_version(), $media );
	}
}
