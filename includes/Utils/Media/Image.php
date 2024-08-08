<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Image class.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Utils\Media;

use DiviSquad\Utils\Polyfills\Str;
use RuntimeException;
use WP_Filesystem_Base;
use function apply_filters;
use function esc_html;
use function esc_html__;
use function is_wp_error;
use function wp_kses_allowed_html;

/**
 * The Image class.
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Image extends Filesystem {

	/**
	 * The images array.
	 *
	 * @var array
	 */
	private static $images = array();

	/**
	 * The kses defaults array.
	 *
	 * @var array
	 */
	private static $kses_defaults = array();

	/**
	 * The WP_Filesystem_Base instance.
	 *
	 * @var WP_Filesystem_Base
	 */
	private $wp_fs;

	/**
	 * The image path.
	 *
	 * @var string
	 */
	private $path;

	/**
	 * The valid types array.
	 *
	 * @var array
	 */
	private $valid_types = array( 'png', 'jpg', 'jpeg', 'gif', 'svg' );

	/**
	 * The validated flag.
	 *
	 * @var bool|\WP_Error
	 */
	private $path_validated;

	/**
	 * The constructor class.
	 *
	 * @param string $path The image path.
	 *
	 * @throws RuntimeException If image path is not a directory or not readable.
	 */
	public function __construct( $path ) {
		$this->path  = $path;
		$this->wp_fs = $this->get_wp_filesystem();

		// Validate path.
		$this->path_validated = $this->validate_path();
	}

	/**
	 * Validate path.
	 *
	 * @return bool|\WP_Error
	 * @throws RuntimeException If image path is not a directory or not readable.
	 */
	private function validate_path() {
		if ( ! $this->wp_fs->is_dir( $this->path ) ) {
			return new \WP_Error(
				'divi_squad_image_path_not_directory',
				sprintf(
					/* translators: The image file path */
					esc_html__( 'Image path (%s) is not a directory.', 'squad-modules-for-divi' ),
					esc_html( $this->path )
				)
			);
		}

		if ( ! $this->wp_fs->is_readable( $this->path ) ) {
			return new \WP_Error(
				'divi_squad_image_path_not_readable',
				sprintf(
					/* translators: The image file path */
					esc_html__( 'Image path (%s) is not readable.', 'squad-modules-for-divi' ),
					esc_html( $this->path )
				)
			);
		}

		if ( Str::ends_with( $this->path, '/' ) ) {
			$this->path = rtrim( $this->path, '/' );
		}

		return true;
	}

	/**
	 * Check if the image is validated.
	 *
	 * @return bool|\WP_Error
	 */
	public function is_path_validated() {
		return $this->path_validated;
	}

	/**
	 * Load image.
	 *
	 * @param string $image The image path.
	 * @param string $type  The image type.
	 *
	 * @return string|\WP_Error
	 * @throws RuntimeException If image file does not exist.
	 */
	public function get_image( $image, $type ) {
		$image_data_key = "{$type}_{$image}";

		// Check if image is already loaded.
		if ( isset( self::$images[ $image_data_key ] ) ) {
			return self::$images[ $image_data_key ];
		}

		/**
		 * Filters the supported image types.
		 *
		 * @param array<string> $supported_types The supported image types.
		 *
		 * @return array
		 */
		$supported_types = apply_filters( 'divi_squad_image_supported_types', $this->valid_types );

		if ( ! in_array( $type, $supported_types, true ) ) {
			return new \WP_Error(
				'divi_squad_image_type_not_supported',
				sprintf(
					/* translators: The image file path */
					esc_html__( 'Image type (%s) is not supported.', 'squad-modules-for-divi' ),
					esc_html( $type )
				)
			);
		}

		// Convert svg to svg+xml.
		if ( 'svg' === $type ) {
			$type = 'svg+xml';
		}

		// Get the image.
		$squad_image = $this->get_image_raw( $image );
		if ( is_wp_error( $squad_image ) ) {
			return new \WP_Error(
				'divi_squad_image_not_generated',
				sprintf(
					/* translators: The image file path */
					esc_html__( 'Image file (%s) could not be generated.', 'squad-modules-for-divi' ),
					esc_html( $image )
				)
			);
		}

		// Get the base64 image.
		$base64_image = 'data:image/' . $type . ';base64,' . base64_encode( $squad_image ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

		/**
		 * Filters the base64 image data.
		 *
		 * @param string $base64Image   The base64 image data to load the image.
		 * @param string $image         The image path to load the image.
		 * @param string $type          The image type to load the image.
		 *
		 * @return string
		 */
		$base64_image = apply_filters( 'divi_squad_image_base64', $base64_image, $image, $type );

		// Store the image in the array.
		self::$images[ $image_data_key ] = $base64_image;

		return $base64_image;
	}

	/**
	 * Load image.
	 *
	 * @param string $image The image path.
	 *
	 * @return string|\WP_Error
	 * @throws RuntimeException If image file does not exist.
	 */
	public function get_image_raw( $image ) {
		// Validate image path.
		$image_path = $this->path . '/' . $image;

		if ( $this->wp_fs->exists( $image_path ) ) {
			return $this->wp_fs->get_contents( $image_path );

		}

		return new \WP_Error(
			'divi_squad_image_not_exist',
			sprintf(
				/* translators: The image file path */
				esc_html__( 'Image file (%s) does not exist.', 'squad-modules-for-divi' ),
				esc_html( $image_path )
			)
		);
	}

	/**
	 * Set allowed html for image.
	 *
	 * @param array $allowed_html The allowed html.
	 *
	 * @return void
	 */
	public function set_image_allowed_html( $allowed_html ) {
		self::$kses_defaults = $allowed_html;
	}

	/**
	 * Get allowed html for image.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_kses_allowed_html/
	 * @link https://wordpress.stackexchange.com/a/316943
	 *
	 * @return array
	 */
	public function get_image_allowed_html() {
		if ( ! empty( self::$kses_defaults ) ) {
			return self::$kses_defaults;
		}

		// Default allowed html.
		$kses_defaults = wp_kses_allowed_html( 'post' );

		// SVG allowed html.
		$svg_args = array(
			'data'           => array(),
			'svg'            => array(
				'class'               => true,
				'aria-hidden'         => true,
				'aria-labelledby'     => true,
				'role'                => true,
				'xmlns'               => true,
				'width'               => true,
				'height'              => true,
				'preserveaspectratio' => true,
				'fill'                => true,
				'viewbox'             => true,
			),
			'g'              => array( 'fill' => true ),
			'title'          => array( 'title' => true ),
			'path'           => array(
				'd'    => true,
				'fill' => true,
			),
			'defs'           => array(),
			'stop'           => array(
				'offset'       => true,
				'stop-color'   => true,
				'stop-opacity' => true,
			),
			'lineargradient' => array(
				'id'            => true,
				'x1'            => true,
				'y1'            => true,
				'x2'            => true,
				'y2'            => true,
				'gradientUnits' => true,
			),
		);

		// Store the allowed html in the array.
		self::$kses_defaults = array_merge( $kses_defaults, $svg_args );

		return self::$kses_defaults;
	}
}
