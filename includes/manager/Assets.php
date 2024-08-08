<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use DiviSquad\Integration\Divi;
use DiviSquad\Utils\Helper;

/**
 * Assets Class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Assets {

	/** The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Get the instance of the current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();

			add_action( 'wp_enqueue_scripts', array( self::$instance, 'enqueue_scripts' ) );

			if (isset($_GET['et_fb']) && '1' === $_GET['et_fb']) { // phpcs:ignore
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'enqueue_scripts_vb' ) );
			}
		}

		return self::$instance;
	}

	/**
	 * Set the asset path.
	 *
	 * @param string $file_name The file name without an extension.
	 * @param string $ext       The file extension, default is js.
	 * @param string $type      The file type, default is module.
	 *
	 * @return string
	 */
	private function asset_path( $file_name, $ext = 'js', $type = 'module' ) {
		$asset_url  = DISQ_ASSET_URL;
		$asset_path = 'module' === $type ? 'shortcode/scripts/modules' : $type;

		return sprintf( '%1$s%2$s/%3$s.%4$s', $asset_url, $asset_path, $file_name, $ext );
	}

	/**
	 * Register scripts for frontend and builder.
	 *
	 * @param string $handle The handle name.
	 * @param string $path   The script path url.
	 * @param array  $deps   The script dependencies.
	 *
	 * @return void
	 */
	private function register_scripts( $handle, $path, $deps = array() ) {
		wp_register_script( "disq-$handle", $path, $deps, DISQ_VERSION, true );
	}

	/**
	 * Enqueue scripts for frontend.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$core_asset_deps = array( 'jquery' );

		// All vendor scripts.
		$this->register_scripts( 'vendor-lottie', $this->asset_path( 'lottie', 'min.js', 'vendor' ) );
		$this->register_scripts( 'vendor-typed', $this->asset_path( 'typed', 'umd.js', 'vendor' ) );

		$lottie_asset_deps       = array_merge( $core_asset_deps, array( 'disq-vendor-lottie' ) );
		$typing_text_module_deps = array_merge( $core_asset_deps, array( 'disq-vendor-typed' ) );

		// All module js.
		$this->register_scripts( 'module-divider', $this->asset_path( 'divider-bundle' ), $core_asset_deps );
		$this->register_scripts( 'module-lottie', $this->asset_path( 'lottie-bundle' ), $lottie_asset_deps );
		$this->register_scripts( 'module-typing-text', $this->asset_path( 'typing-text-bundle' ), $typing_text_module_deps );
		$this->register_scripts( 'module-bais', $this->asset_path( 'bai-slider-bundle' ), $core_asset_deps );
		$this->register_scripts( 'module-accordion', $this->asset_path( 'accordion-bundle' ), $core_asset_deps );
	}

	/**
	 * Enqueue scripts for builder.
	 *
	 * @return void
	 */
	public function enqueue_scripts_vb() {
		wp_enqueue_script( 'disq-vendor-typed' );
	}

}
