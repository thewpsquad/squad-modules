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
				'last_modified'      => array( '1.2.2', '1.2.3' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'DualButton',
				'label'              => esc_html__( 'Dual Button', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.1.0', '1.2.3' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'Lottie',
				'label'              => esc_html__( 'Lottie', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.0.1', '1.0.5', '1.2.3' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'PostGrid',
				'label'              => esc_html__( 'Post Grid', 'squad-modules-for-divi' ),
				'child_name'         => 'PostGridChild',
				'child_label'        => esc_html__( 'Post Element', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.0.2', '1.0.4', '1.1.0', '1.2.0', '1.2.2', '1.2.3' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'TypingText',
				'label'              => esc_html__( 'Typing Text', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.0.1', '1.0.5', '1.2.3' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'ImageMask',
				'label'              => esc_html__( 'Image Mask', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'FlipBox',
				'label'              => esc_html__( 'Flip Box', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'BusinessHours',
				'label'              => esc_html__( 'Business Hours', 'squad-modules-for-divi' ),
				'child_name'         => 'BusinessHoursChild',
				'child_label'        => esc_html__( 'Business Day', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.2.0', '1.2.3' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'BeforeAfterImageSlider',
				'label'              => esc_html__( 'Before After Image Slider', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'ImageGallery',
				'label'              => esc_html__( 'Image Gallery', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => array( '1.2.2', '1.2.3' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'Accordion',
				'label'              => esc_html__( 'Advanced Accordion', 'squad-modules-for-divi' ),
				'child_name'         => 'AccordionChild',
				'child_label'        => esc_html__( 'Advanced Accordion', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.2',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'FormStylerContactForm7',
				'label'              => esc_html__( 'Contact Form 7', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'contact-form-7/wp-contact-form-7.php' ),
			),
			array(
				'name'               => 'FormStylerWPForms',
				'label'              => esc_html__( 'WP Forms', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'wpforms-lite/wpforms.php|wpforms/wpforms.php' ),
			),
			array(
				'name'               => 'FormStylerGravityForms',
				'label'              => esc_html__( 'Gravity Forms', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'gravityforms/gravityforms.php' ),
			),
			array(
				'name'               => 'PostReadingTime',
				'label'              => esc_html__( 'Post Reading Time', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.2',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
			array(
				'name'               => 'GlitchText',
				'label'              => esc_html__( 'Glitch Text', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
			),
		);

		return array_values( Helper::array_sort( $available_modules, 'name' ) );
	}

	/**
	 * Check the current module is an inactive module.
	 *
	 * @param array  $module The array of current module.
	 * @param string $type   The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 *
	 * @return array|null
	 */
	protected function is_inactive_module( $module, $type = 'D4' ) {
		$single        = ! empty( is_string( $module['type'] ) ) && is_string( $module['type'] ) && $type === $module['type'];
		$compatibility = ! empty( is_string( $module['type'] ) ) && is_array( $module['type'] ) && in_array( $type, $module['type'], true );

		return ( $single || $compatibility ) && ! $module['is_default_active'] ? $module : null;
	}

	/**
	 *  Check the current module is an active module.
	 *
	 * @param array  $module The array of current module.
	 * @param string $type   The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 *
	 * @return array|null
	 */
	protected function is_active_module( $module, $type = 'D4' ) {
		$single        = ! empty( is_string( $module['type'] ) ) && is_string( $module['type'] ) && $type === $module['type'];
		$compatibility = ! empty( is_string( $module['type'] ) ) && is_array( $module['type'] ) && in_array( $type, $module['type'], true );

		return ( $single || $compatibility ) && $module['is_default_active'] ? $module : null;
	}

	/**
	 * Get filtered modules.
	 *
	 * @param callable $callback The callback function for filter the current module.
	 * @param array    $modules  The available modules.
	 * @param string   $type     The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 *
	 * @return array
	 */
	protected function get_filtered_modules( $callback, $modules, $type = 'D4' ) {
		return array_values(
			array_filter(
				array_map(
					function ( $module ) use ( $callback, $type ) {
						return call_user_func_array( $callback, array( $module, $type ) );
					},
					$modules
				)
			)
		);
	}

	/**
	 *  Get inactive modules.
	 *
	 * @param string $type The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 *
	 * @return array
	 */
	protected function get_inactive_modules( $type = 'D4' ) {
		return $this->get_filtered_modules( array( $this, 'is_inactive_module' ), $this->get_available_modules(), $type );
	}

	/**
	 *  Get default active modules.
	 *
	 * @param string $type The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 *
	 * @return array
	 */
	protected function get_default_active_modules( $type = 'D4' ) {
		return $this->get_filtered_modules( array( $this, 'is_active_module' ), $this->get_available_modules(), $type );
	}

	/**
	 * Load the module class.
	 *
	 * @param string $path   The module class path.
	 * @param string $module The module name.
	 * @param string $type   The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 * @param object|null $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 *
	 * @return void
	 */
	protected function require_module_path( $path, $module, $type = 'D4', $dependency_tree = null ) {
		if ( 'D5' === $type ) {
			$module_path = sprintf( '%1$s/%2$s/%2$s.php', $path, $module );
			if ( file_exists( $module_path ) && is_object( $dependency_tree ) && method_exists( $dependency_tree, 'add_dependency' ) ) {
				$module_instance = require_once $module_path;
				$dependency_tree->add_dependency( $module_instance );
			}
		} else {
			$module_path = sprintf( '%1$s/modules/%2$s/%2$s.php', $path, $module );
			if ( file_exists( $module_path ) ) {
				require_once $module_path;
			}
		}
	}

	/**
	 * Load the module class.
	 *
	 * @param string $path            The module class path.
	 * @param mixed  $modules         The available modules list.
	 * @param string $type            The type of Divi Builder module, default is: D4. Available opinions are: D4, D5
	 * @param object|null $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 *
	 * @return void
	 */
	protected function load_module_files( $path, $modules, $type = 'D4', $dependency_tree = null ) {
		// Collect all active modules.
		$active_modules = $this->get_default_active_modules( $type );

		if ( is_array( $modules ) ) {
			$active_modules = $modules;
		}

		foreach ( $active_modules as $active_module ) {
			$divi4_module_path = sprintf( '%1$s/modules/%2$s/%2$s.php', $path, $active_module['name'] );
			$divi5_module_path = sprintf( '%1$s/%2$s/%2$s.php', $path, $active_module['name'] );
			$module_path       = 'D5' === $type ? $divi5_module_path : $divi4_module_path;

			if ( file_exists( $module_path ) ) {
				$this->require_module_path( $path, $active_module['name'], $type, $dependency_tree );

				if ( isset( $active_module['child_name'] ) ) {
					$this->require_module_path( $path, $active_module['child_name'], $type, $dependency_tree );
				}

				if ( isset( $active_module['full_width_name'] ) ) {
					$this->require_module_path( $path, $active_module['full_width_name'], $type, $dependency_tree );

					if ( isset( $active_module['full_width_child_name'] ) ) {
						$this->require_module_path( $path, $active_module['full_width_child_name'], $type, $dependency_tree );
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
	public function load_divi4_modules( $path ) {
		if ( ! class_exists( \ET_Builder_Element::class ) ) {
			return;
		}

		// Load enabled modules.
		$memory = divi_squad()->get_memory();
		$this->load_module_files( $path, $memory->get( 'active_modules' ) );
	}
}
