<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use DiviSquad\Utils\Helper;
use function DiviSquad\divi_squad;

/**
 * Modules class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Modules {

	/**
	 * Activate all modules.
	 */
	public function active_modules() {
		$memory = divi_squad()->get_memory();
		$memory->set( 'modules', $this->get_available_modules() );
		$memory->set( 'default_active_modules', $this->get_default_active_modules() );
	}

	/**
	 *  Get available modules.
	 *
	 * @return array[]
	 */
	public function get_available_modules() {
		$available_modules = array(
			array(
				'name'               => 'Divider',
				'label'              => esc_html__( 'Advanced Divider', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.2',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'DualButton',
				'label'              => esc_html__( 'Dual Button', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'Lottie',
				'label'              => esc_html__( 'Lottie', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'PostGrid',
				'label'              => esc_html__( 'Post Grid', 'squad-modules-for-divi' ),
				'child_name'         => 'PostGridChild',
				'child_label'        => esc_html__( 'Post Element', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.2',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'TypingText',
				'label'              => esc_html__( 'Typing Text', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'ImageMask',
				'label'              => esc_html__( 'Image Mask', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'FlipBox',
				'label'              => esc_html__( 'Flip Box', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'BusinessHours',
				'label'              => esc_html__( 'Business Hours', 'squad-modules-for-divi' ),
				'child_name'         => 'BusinessHoursChild',
				'child_label'        => esc_html__( 'Business Day', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'BeforeAfterImageSlider',
				'label'              => esc_html__( 'Before After Image Slider', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'ImageGallery',
				'label'              => esc_html__( 'Image Gallery', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => '1.2.2',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'FormStylerContactForm7',
				'label'              => esc_html__( 'Contact Form 7', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
				'required'           => array( 'plugin' => 'contact-form-7' ),
			),
			array(
				'name'               => 'FormStylerWPForms',
				'label'              => esc_html__( 'WP Forms', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
				'required'           => array( 'plugin' => 'wpforms-lite|wpforms' ),
			),
			array(
				'name'               => 'FormStylerGravityForms',
				'label'              => esc_html__( 'Gravity Forms', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
				'required'           => array( 'plugin' => 'gravityforms' ),
			),
			array(
				'name'               => 'PostReadingTime',
				'label'              => esc_html__( 'Post Reading Time', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.2',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
		);

		return array_values( Helper::array_sort( $available_modules, 'name' ) );
	}

	/**
	 * Check the current module is an inactive module.
	 *
	 * @param array $module The array of current module.
	 *
	 * @return array|null
	 */
	protected function is_inactive_module( $module ) {
		return ! $module['is_default_active'] ? $module : null;
	}

	/**
	 *  Check the current module is an active module.
	 *
	 * @param array $module The array of current module.
	 *
	 * @return array|null
	 */
	protected function is_active_module( $module ) {
		return $module['is_default_active'] ? $module : null;
	}

	/**
	 * Get filtered modules.
	 *
	 * @param callable $callback The callback function for filter the current module.
	 * @param array    $modules  The available modules.
	 *
	 * @return array
	 */
	protected function get_filtered_modules( $callback, $modules ) {
		return array_values( array_filter( array_map( $callback, $modules ) ) );
	}

	/**
	 *  Get inactive modules.
	 *
	 * @return array
	 */
	protected function get_inactive_modules() {
		return $this->get_filtered_modules( array( $this, 'is_inactive_module' ), $this->get_available_modules() );
	}

	/**
	 *  Get default active modules.
	 *
	 * @return array
	 */
	protected function get_default_active_modules() {
		return $this->get_filtered_modules( array( $this, 'is_active_module' ), $this->get_available_modules() );
	}

	/**
	 * Load the module class.
	 *
	 * @param string $path   The module class path.
	 * @param string $module The module name.
	 *
	 * @return void
	 */
	protected function require_module_path( $path, $module ) {
		$module_path = sprintf( '%1$s/modules/%2$s/%2$s.php', $path, $module );
		if ( file_exists( $module_path ) ) {
			require_once $module_path;
		}
	}

	/**
	 * Load the module class.
	 *
	 * @param string $path    The module class path.
	 * @param mixed  $modules The available modules list.
	 *
	 * @return void
	 */
	protected function load_module_files( $path, $modules ) {
		// Collect all active modules.
		$active_modules = $this->get_default_active_modules();

		if ( is_array( $modules ) ) {
			$active_modules = $modules;
		}

		foreach ( $active_modules as $active_module ) {
			if ( file_exists( sprintf( '%1$s/modules/%2$s/%2$s.php', $path, $active_module['name'] ) ) ) {
				$this->require_module_path( $path, $active_module['name'] );

				if ( isset( $active_module['child_name'] ) ) {
					$this->require_module_path( $path, $active_module['child_name'] );
				}

				if ( isset( $active_module['full_width_name'] ) ) {
					$this->require_module_path( $path, $active_module['full_width_name'] );

					if ( isset( $active_module['full_width_child_name'] ) ) {
						$this->require_module_path( $path, $active_module['full_width_child_name'] );
					}
				}
			}
		}
	}

	/**
	 * Load enabled modules for Divi Builder from defined directory
	 *
	 * @param string $path The defined directory.
	 *
	 * @return void
	 */
	public function load_modules( $path ) {
		if ( ! class_exists( \ET_Builder_Element::class ) ) {
			return;
		}

		// Load enabled modules.
		$memory = divi_squad()->get_memory();
		$this->load_module_files( $path, $memory->get( 'active_modules' ) );
	}
}
