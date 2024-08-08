<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Manager;

use DiviSquad\Base\Memory;
use DiviSquad\Utils\Helper;
use function DiviSquad\divi_squad;

/**
 * Modules class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @license     GPL-3.0-only
 */
class Modules {

	/** The instance of the current class.
	 *
	 * @var self
	 */
	private static $instance;

	/** The instance of the memory class.
	 *
	 * @var Memory
	 */
	private static $memory;

	/**
	 * Get the instance of the current class.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$memory   = divi_squad()->get_memory();

			// update database.
			self::$memory->set( 'modules', self::$instance->get_available_modules() );
			self::$memory->set( 'default_active_modules', self::$instance->get_default_active_modules() );
		}

		return self::$instance;
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
				'label'              => 'Advanced Divider',
				'release_version'    => '1.0.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'DualButton',
				'label'              => 'Dual Button',
				'release_version'    => '1.0.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'Lottie',
				'label'              => 'Lottie',
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'PostGrid',
				'label'              => 'Post Grid',
				'child_name'         => 'PostGridChild',
				'child_label'        => 'Post Element',
				'release_version'    => '1.0.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'TypingText',
				'label'              => 'Typing Text',
				'release_version'    => '1.0.1',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'ImageMask',
				'label'              => 'Image Mask',
				'release_version'    => '1.0.2',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'FlipBox',
				'label'              => 'Flip Box',
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'BusinessHours',
				'label'              => 'Business Hours',
				'child_name'         => 'BusinessHoursChild',
				'child_label'        => 'Business Day',
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			),
			array(
				'name'               => 'BeforeAfterImageSlider',
				'label'              => 'Before After Image Slider',
				'release_version'    => '1.0.0',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => '4',
			)
		);

		$sorts = Helper::array_sort( $available_modules, 'name' );
		return array_values( $sorts );
	}

	/**
	 *  Get inactive modules.
	 *
	 * @return array
	 */
	private function get_inactive_modules() {
		$inactive_modules_fn = static function ( $module ) {
			return ! $module['is_default_active'] ? $module : null;
		};

		return array_values(
			array_filter(
				array_map(
					$inactive_modules_fn,
					self::$instance->get_available_modules()
				)
			)
		);
	}

	/**
	 *  Get default active modules.
	 *
	 * @return array
	 */
	private function get_default_active_modules() {
		$active_modules_fn = static function ( $module ) {
			return $module['is_default_active'] ? $module : null;
		};

		return array_values(
			array_filter(
				array_map(
					$active_modules_fn,
					self::$instance->get_available_modules()
				)
			)
		);
	}

	/**
	 * Load the module class.
	 *
	 * @param string $path   The module class path.
	 * @param string $module The module name.
	 *
	 * @return void
	 */
	private function require_module_path( $path, $module ) {
		$module_path = sprintf( '%1$s/modules/%2$s/%2$s.php', $path, $module );
		if ( file_exists( $module_path ) ) {
			require_once $module_path;
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

		$current_activates = self::$memory->get( 'active_modules', null );
		$active_modules    = $this->get_default_active_modules();

		if ( is_array( $current_activates ) ) {
			$active_modules = $current_activates;
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
}
