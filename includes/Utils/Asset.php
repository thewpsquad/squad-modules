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

use DiviSquad\Utils\Polyfills\Str;
use function divi_squad;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_parse_args;

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
		return (string) divi_squad()->get_version();
	}

	/**
	 * Get current mode is production or not
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function is_production_mode() {
		return strpos( static::get_the_version(), '.' );
	}

	/**
	 * Resolve the resource root path.
	 *
	 * @return string
	 */
	public static function root_path() {
		return DIVI_SQUAD_DIR_PATH;
	}

	/**
	 * Resolve the resource root uri.
	 *
	 * @return string
	 */
	public static function root_path_uri() {
		return DIVI_SQUAD_DIR_URL;
	}

	/**
	 * Validate the relative path.
	 *
	 * @param string $relative_path The path string for validation.
	 *
	 * @return string
	 */
	public static function validate_relative_path( $relative_path ) {
		if ( Str::starts_with( $relative_path, './' ) ) {
			$relative_path = str_replace( './', '/', $relative_path );
		}

		if ( ! Str::starts_with( $relative_path, '/' ) ) {
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
	 * @param array $path The asset relative path with options.
	 *
	 * @return array
	 */
	public static function process_asset_path_data( $path ) {
		$full_path   = '';
		$pattern     = ! empty( $path['pattern'] ) ? $path['pattern'] : 'build/[path_prefix]/[file].[ext]';
		$path_prefix = ! empty( $path['path'] ) ? $path['path'] : 'divi-builder-4';
		$extension   = ! empty( $path['ext'] ) ? $path['ext'] : 'js';

		// Update path.
		$path_prefix .= 'js' === $extension ? '/scripts' : '/styles';

		if ( empty( $path['file'] ) ) {
			return array(
				'path'         => '',
				'version'      => '',
				'dependencies' => '',
			);
		}

		// Default file for development and production.
		$the_file = $path['file'];

		// Load alternative production file when found.
		if ( static::is_production_mode() && ! empty( $path['prod_file'] ) ) {
			$the_file = $path['prod_file'];
		}

		// Load alternative development file when found.
		if ( ! static::is_production_mode() && ! empty( $path['dev_file'] ) ) {
			$the_file = $path['dev_file'];
		}

		// The validated path of default file.
		$path_clean    = str_replace( array( '[path_prefix]', '[file]', '[ext]' ), array( $path_prefix, $the_file, $extension ), $pattern );
		$path_validate = static::validate_relative_path( $path_clean );

		$version      = static::get_the_version();
		$dependencies = array();

		if ( in_array( $extension, array( 'js', 'css' ), true ) ) {
			// Check for the minified version in the server on production mode.
			$minified_asset_file     = str_replace( array( ".$extension" ), array( ".min.$extension" ), $path_validate );
			$is_minified_asset_file  = Str::ends_with( $path_validate, ".min.$extension" );
			$is_minified_asset_found = file_exists( static::resolve_file_path( $minified_asset_file ) );
			if ( Str::ends_with( $path_validate, ".$extension" ) && ! $is_minified_asset_file && $is_minified_asset_found ) {
				$path_validate = $minified_asset_file;
			}

			// Load the version and dependencies data for javascript file.
			if ( 'js' === $extension ) {
				// Verify that the current file is a minified and located in the current physical device.
				if ( Str::ends_with( $path_validate, ".min.$extension" ) && file_exists( static::resolve_file_path( $path_validate ) ) ) {
					$minified_version_file = str_replace( array( ".min.$extension" ), array( '.min.asset.php' ), $path_validate );
					if ( file_exists( static::resolve_file_path( $minified_version_file ) ) ) {
						$minified_asset = include static::resolve_file_path( $minified_version_file );
						$version        = ! empty( $minified_asset['version'] ) ? $minified_asset['version'] : $version;
						$dependencies   = ! empty( $minified_asset['dependencies'] ) ? $minified_asset['dependencies'] : $dependencies;
					}
				}

				// Verify that the current file is non-minified and located in the current physical device.
				if ( Str::ends_with( $path_validate, ".$extension" ) && file_exists( static::resolve_file_path( $path_validate ) ) ) {
					$main_version_file = str_replace( array( ".$extension" ), array( '.asset.php' ), $path_validate );
					if ( Str::ends_with( $main_version_file, '.asset.php' ) && file_exists( static::resolve_file_path( $main_version_file ) ) ) {
						$main_asset   = include static::resolve_file_path( $main_version_file );
						$version      = ! empty( $main_asset['version'] ) ? $main_asset['version'] : $version;
						$dependencies = ! empty( $main_asset['dependencies'] ) ? $main_asset['dependencies'] : $dependencies;
					}
				}
			}
		}

		// Collect actual path for the current asset file.
		$plugin_url_root = untrailingslashit( static::root_path_uri() );
		$full_path       = "{$plugin_url_root}{$path_validate}";

		return array(
			'path'         => $full_path,
			'version'      => $version,
			'dependencies' => $dependencies,
		);
	}

	/**
	 * Get the admin asset path.
	 *
	 * @param string $file    The file name.
	 * @param array  $options The options for current asset file.
	 *
	 * @return array
	 */
	public static function admin_asset_path( $file, $options = array() ) {
		$defaults = array(
			'path' => 'admin',
		);

		return self::asset_path( $file, wp_parse_args( $options, $defaults ) );
	}

	/**
	 * Get the module asset path.
	 *
	 * @param string $file    The file name.
	 * @param array  $options The options for current asset file.
	 *
	 * @return array
	 */
	public static function module_asset_path( $file, $options = array() ) {
		$defaults = array(
			'path' => 'divi-builder-4',
		);

		return self::asset_path( $file, wp_parse_args( $options, $defaults ) );
	}

	/**
	 * Get the extensions asset path.
	 *
	 * @param string $file    The file name.
	 * @param array  $options The options for current asset file.
	 *
	 * @return array
	 */
	public static function extension_asset_path( $file, $options = array() ) {
		$defaults = array(
			'path' => 'extensions',
		);

		return self::asset_path( $file, wp_parse_args( $options, $defaults ) );
	}

	/**
	 * Get the vendor asset path.
	 *
	 * @param string $file    The file name.
	 * @param array  $options The options for current asset file.
	 *
	 * @return array
	 */
	public static function vendor_asset_path( $file, $options = array() ) {
		$defaults = array(
			'path' => 'vendor',
		);

		return self::asset_path( $file, wp_parse_args( $options, $defaults ) );
	}

	/**
	 * Set the asset path.
	 *
	 * @param string $file    The file name.
	 * @param array  $options The options for current asset file.
	 *
	 * @return array
	 */
	public static function asset_path( $file, $options = array() ) {
		$defaults = array(
			'pattern'   => 'build/[path_prefix]/[file].[ext]',
			'file'      => $file,
			'dev_file'  => '',
			'prod_file' => '',
			'ext'       => 'js',
			'path'      => '',
		);

		return wp_parse_args( $options, $defaults );
	}

	/**
	 * Enqueue javascript.
	 *
	 * @param string $keyword   Name of the javascript. Should be unique.
	 * @param array  $path      Relative path of the javascript with options for the WordPress root directory.
	 * @param array  $deps      Optional. An array of registered javascript handles this stylesheet depends on. Default empty array.
	 * @param bool   $no_prefix Optional. Set the plugin prefix with asset handle name is or not.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function asset_enqueue( $keyword, $path, array $deps = array(), $no_prefix = false ) {
		$asset_data   = self::process_asset_path_data( $path );
		$dependencies = array_merge( $asset_data['dependencies'], $deps );
		$handle       = $no_prefix ? $keyword : sprintf( 'squad-%1$s', $keyword );
		$version      = ! empty( $asset_data['version'] ) ? $asset_data['version'] : static::get_the_version();

		// Load script file.
		wp_enqueue_script( $handle, $asset_data['path'], $dependencies, $version, self::footer_arguments( true ) );
	}

	/**
	 * Enqueue styles.
	 *
	 * @param string $keyword   Name of the stylesheet. Should be unique.
	 * @param array  $path      Relative path of the stylesheet with options for the WordPress root directory.
	 * @param array  $deps      Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media     Optional. The media for which this stylesheet has been defined. Default 'all'.
	 * @param bool   $no_prefix Optional. Set the plugin prefix with asset handle name is or not.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function style_enqueue( $keyword, $path, $deps = array(), $media = 'all', $no_prefix = false ) {
		$asset_data = static::process_asset_path_data( $path );
		$handle     = $no_prefix ? $keyword : sprintf( 'squad-%1$s', $keyword );
		$version    = ! empty( $asset_data['version'] ) ? $asset_data['version'] : static::get_the_version();

		// Load stylesheet file.
		wp_enqueue_style( $handle, $asset_data['path'], $deps, $version, $media );
	}

	/**
	 * Register scripts for frontend and builder.
	 *
	 * @param string $handle The handle name.
	 * @param array  $path   The script path url with options.
	 * @param array  $deps   The script dependencies.
	 *
	 * @return void
	 */
	public static function register_script( $handle, $path, $deps = array() ) {
		$asset_data   = self::process_asset_path_data( $path );
		$handle       = sprintf( 'squad-%1$s', $handle );
		$dependencies = array_merge( $asset_data['dependencies'], $deps );
		$version      = ! empty( $asset_data['version'] ) ? $asset_data['version'] : static::get_the_version();

		wp_register_script( $handle, $asset_data['path'], $dependencies, $version, self::footer_arguments( true ) );
	}

	/**
	 * Enqueue styles.
	 *
	 * @param string $keyword Name of the stylesheet. Should be unique.
	 * @param array  $path    Relative path of the stylesheet with options for the WordPress root directory.
	 * @param array  $deps    Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
	 * @param string $media   Optional. The media for which this stylesheet has been defined. Default 'all'.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register_style( $keyword, $path, $deps = array(), $media = 'all' ) {
		$asset_data = static::process_asset_path_data( $path );
		$handle     = sprintf( 'squad-%1$s', $keyword );
		$version    = ! empty( $asset_data['version'] ) ? $asset_data['version'] : static::get_the_version();

		// Load stylesheet file.
		wp_register_style( $handle, $asset_data['path'], $deps, $version, $media );
	}

	/**
	 * Get available script enqueue footer arguments.
	 *
	 * @param bool $add_strategy Optional. If provided, may be either 'defer' or 'async'.
	 *
	 * @return array
	 * @since 1.4.8
	 */
	public static function footer_arguments( $strategy = false, $priority = false ) {
		$footer_arguments = array(
			'in_footer' => true,
		);

		if ( $strategy ) {
			$footer_arguments['strategy'] = 'defer';
		}

		if ( $priority ) {
			$footer_arguments['fetchpriority'] = 'high';
		}

		return $footer_arguments;
	}
}
