<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Managers;

use DiviSquad\Base\Factories\SquadFeatures as ManagerBase;
use DiviSquad\Base\Memory;
use DiviSquad\Utils\WP;
use function divi_squad;

/**
 * Module Mangaer class
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Modules extends ManagerBase {

	/**
	 *  Get available modules.
	 *
	 * @return array[]
	 */
	public function get_registered_list() {
		return array(
			array(
				'name'               => 'Divider',
				'label'              => esc_html__( 'Advanced Divider', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Create visually appealing dividers with various styles, shapes, and customization options.', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.2.2', '1.2.3', '1.2.6', '1.4.1' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'DualButton',
				'label'              => esc_html__( 'Dual Button', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'It allows you to display two buttons side by side with customizable styles and text.', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.1.0', '1.2.3' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'Lottie',
				'label'              => esc_html__( 'Lottie Image', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly add animated elements for a more engaging website experience', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.0.1', '1.0.5', '1.2.3', '1.4.5' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'image-&-media-modules',
			),
			array(
				'name'               => 'PostGrid',
				'label'              => esc_html__( 'Post Grid', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Display your blog posts in a stylish and organized grid layout.', 'squad-modules-for-divi' ),
				'child_name'         => 'PostGridChild',
				'child_label'        => esc_html__( 'Post Element', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.0.2', '1.0.4', '1.1.0', '1.2.0', '1.2.2', '1.2.3', '1.4.4', '1.4.8', '1.4.10', '1.4.11' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'dynamic-content-modules',
			),
			array(
				'name'               => 'TypingText',
				'label'              => esc_html__( 'Typing Text', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Create eye-catching animated title or heading text that simulates a typing effect.', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.0.1', '1.0.5', '1.2.3', '1.4.6' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'ImageMask',
				'label'              => esc_html__( 'Image Mask', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Apply stunning masks to your images, adding creativity and visual appeal to your website.', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'image-&-media-modules',
			),
			array(
				'name'               => 'FlipBox',
				'label'              => esc_html__( 'Flip Box', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Display content on one side, then on hover, flip to reveal more info or a different design.', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => '1.2.3',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'content-modules',
			),
			array(
				'name'               => 'BusinessHours',
				'label'              => esc_html__( 'Business Hours', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Display your business hours in a clear and organized manner.', 'squad-modules-for-divi' ),
				'child_name'         => 'BusinessHoursChild',
				'child_label'        => esc_html__( 'Business Day', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.2.0', '1.2.3', '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'content-modules',
			),
			array(
				'name'               => 'BeforeAfterImageSlider',
				'label'              => esc_html__( 'Before After Image Slider', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Engage your visitors with interactive image comparisons.', 'squad-modules-for-divi' ),
				'release_version'    => '1.0.0',
				'last_modified'      => array( '1.2.3', '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'image-&-media-modules',
			),
			array(
				'name'               => 'ImageGallery',
				'label'              => esc_html__( 'Image Gallery', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly create stunning galleries to engage and captivate your audience.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => array( '1.2.2', '1.2.3', '1.3.0', '1.4.5', '1.4.8', '1.4.9' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'image-&-media-modules',
			),
			array(
				'name'               => 'FormStylerContactForm7',
				'label'              => esc_html__( 'Contact Form 7', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly customize Contact Form 7 design. Adjust colors, fonts, spacing, and add CSS for your desired look.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => array( '1.2.3', '1.4.7', '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'contact-form-7/wp-contact-form-7.php' ),
				'category'           => 'form-styler-modules',
			),
			array(
				'name'               => 'FormStylerWPForms',
				'label'              => esc_html__( 'WP Forms', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly customize WP Forms design. Adjust colors, fonts, spacing, and add CSS for your desired look.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => array( '1.2.3', '1.4.7', '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'wpforms-lite/wpforms.php|wpforms/wpforms.php' ),
				'category'           => 'form-styler-modules',
			),
			array(
				'name'               => 'FormStylerGravityForms',
				'label'              => esc_html__( 'Gravity Forms', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly customize Gravity Forms design. Adjust colors, fonts, spacing, and add CSS for your desired look.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.0',
				'last_modified'      => array( '1.2.3', '1.4.7', '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'gravityforms/gravityforms.php' ),
				'category'           => 'form-styler-modules',
			),
			array(
				'name'               => 'PostReadingTime',
				'label'              => esc_html__( 'Post Reading Time', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Show how long it takes to read your blog posts. Useful for readers planning their time.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.2',
				'last_modified'      => array( '1.2.3', '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'dynamic-content-modules',
			),
			array(
				'name'               => 'GlitchText',
				'label'              => esc_html__( 'Glitch Text', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Create eye-catching headlines and captions with a mesmerizing glitch effect.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.3',
				'last_modified'      => array( '1.3.0' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'GradientText',
				'label'              => esc_html__( 'Gradient Text', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Create eye-catching headlines, captions, and more with this versatile and dynamic module.', 'squad-modules-for-divi' ),
				'release_version'    => '1.2.6',
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'ScrollingText',
				'label'              => esc_html__( 'Scrolling Text', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Add dynamic, attention-grabbing text animations to your Divi-powered website.', 'squad-modules-for-divi' ),
				'release_version'    => '1.3.0',
				'last_modified'      => array( '1.4.8' ),
				'is_default_active'  => false,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'StarRating',
				'label'              => esc_html__( 'Star Rating', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Add stylish star ratings to your content for user feedback and ratings.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.0',
				'last_modified'      => array( '1.4.5', '1.4.6' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'Breadcrumbs',
				'label'              => esc_html__( 'Breadcrumbs', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Enhance navigation with a clear path for users to trace their steps through your website.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.0',
				'last_modified'      => array( '1.4.1', '1.4.2', '1.4.6', '1.4.8' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'DropCap',
				'label'              => esc_html__( 'Drop Cap Text', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Create visually appealing drop caps to add emphasis and style to your text content.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'name'               => 'VideoPopup',
				'label'              => esc_html__( 'Video Popup', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Engage visitors with customizable video popups for YouTube and Vimeo.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.1',
				'last_modified'      => array( '1.4.4' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'image-&-media-modules',
			),
			array(
				'name'               => 'GoogleMap',
				'label'              => esc_html__( 'Google Embed Map', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Right into your Divi\'s site easily without having to worry about anything else.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.7',
				'last_modified'      => array( '1.4.8' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'content-modules',
			),
			array(
				'name'               => 'FormStylerNinjaForms',
				'label'              => esc_html__( 'Ninja Forms', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly customize Ninja Forms design. Adjust colors, fonts, spacing, and add CSS for your desired look.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.7',
				'last_modified'      => array( '1.4.8' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'ninja-forms/ninja-forms.php' ),
				'category'           => 'form-styler-modules',
			),
			array(
				'name'               => 'FormStylerFluentForms',
				'label'              => esc_html__( 'Fluent Forms', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Effortlessly customize Fluent Forms design. Adjust colors, fonts, spacing, and add CSS for your desired look.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.7',
				'last_modified'      => array( '1.4.8' ),
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'required'           => array( 'plugin' => 'fluentform/fluentform.php' ),
				'category'           => 'form-styler-modules',
			),
		);
	}

	/**
	 * Get all modules including extra modules.
	 *
	 * @return array[]
	 */
	public function get_modules_with_extra() {
		// List of core modules.
		$core_modules = $this->get_registered_list();

		// List of pro-modules.
		$pro_modules = array(
			array(
				'name'               => 'AdvancedList',
				'label'              => esc_html__( 'Advanced List', 'squad-modules-pro-for-divi' ),
				'description'        => esc_html__( 'Elevate your content presentation providing versatile and stylish list formats for a captivating user experience.', 'squad-modules-pro-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'Blurb',
				'label'              => esc_html__( 'Advanced Blurb', 'squad-modules-pro-for-divi' ),
				'description'        => esc_html__( 'Craft engaging and informative content with advanced styling and layout options for a standout user experience.', 'squad-modules-pro-for-divi' ),
				'is_premium_feature' => true,
				'type'               => array( 'D4', 'D5' ),
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'UserList',
				'label'              => esc_html__( 'User List', 'squad-modules-pro-for-divi' ),
				'description'        => esc_html__( 'Showcase your users allowing you to display user profiles in a sleek and customizable list format.', 'squad-modules-pro-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'Heading',
				'label'              => esc_html__( 'Advanced Heading', 'squad-modules-pro-for-divi' ),
				'description'        => esc_html__( 'Make a bold statement offering enhanced customization and design options for impactful and visually stunning headings.', 'squad-modules-pro-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'TaxonomyList',
				'label'              => esc_html__( 'Taxonomy List', 'squad-modules-pro-for-divi' ),
				'description'        => esc_html__( 'Easily organize and display your taxonomy enhancing user experience.', 'squad-modules-pro-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'CPTGrid',
				'label'              => esc_html__( 'CPT Grid', 'squad-modules-pro-for-divi' ),
				'description'        => esc_html__( 'Showcase your Custom Post Types creating a visually appealing grid layout.', 'squad-modules-pro-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
		);

		if ( ! divi_squad_is_pro_activated() ) {
			return array_merge( $core_modules, $pro_modules );
		}

		return $core_modules;
	}

	/**
	 * Check the current module type.
	 *
	 * @param array $module The array of current module.
	 *
	 * @return bool
	 */
	protected function verify_module_type( $module ) {
		$single        = isset( $module['type'] ) && is_string( $module['type'] ) && $this->builder_type === $module['type'];
		$compatibility = isset( $module['type'] ) && is_array( $module['type'] ) && in_array( $this->builder_type, $module['type'], true );

		return ( $single || $compatibility );
	}

	/**
	 *  Get default active modules.
	 *
	 * @return array
	 */
	public function get_default_registries() {
		return $this->get_filtered_registries(
			$this->get_registered_list(),
			function ( $module ) {
				return $this->verify_module_type( $module ) && $module['is_default_active'];
			}
		);
	}

	/**
	 *  Get inactive modules.
	 *
	 * @return array
	 */
	public function get_inactive_registries() {
		return $this->get_filtered_registries(
			$this->get_registered_list(),
			function ( $module ) {
				return $this->verify_module_type( $module ) && ! $module['is_default_active'];
			}
		);
	}

	/**
	 * Load the module class.
	 *
	 * @param string      $path            The module class path.
	 * @param string      $module          The module name.
	 * @param object|null $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 *
	 * @return void
	 */
	protected function require_module_path( $path, $module, $dependency_tree = null ) {
		if ( 'D5' === $this->builder_type ) {
			$module_path = sprintf( '%1$s/%2$s/%2$s.php', $path, $module );
			if ( file_exists( $module_path ) && is_object( $dependency_tree ) && method_exists( $dependency_tree, 'add_dependency' ) ) {
				$module_instance = require_once $module_path;
				if ( 'object' === gettype( $module_instance ) ) {
					$dependency_tree->add_dependency( $module_instance );
				}
			}
		} else {
			$module_path = sprintf( '%1$s/Modules/%2$s/%2$s.php', $path, $module );
			if ( file_exists( $module_path ) ) {
				require_once $module_path;
			}
		}
	}

	/**
	 * Load enabled modules for Divi Builder from defined directory.
	 *
	 * @param string $path The defined directory.
	 *
	 * @return void
	 */
	public function load_divi_builder_4_modules( $path ) {
		if ( ! class_exists( \ET_Builder_Element::class ) ) {
			return;
		}

		// Load enabled modules.
		$this->builder_type = 'D4';
		$this->load_module_files( $path, divi_squad()->memory );
	}

	/**
	 * Load the module class.
	 *
	 * @param string      $path            The module class path.
	 * @param Memory      $memory          The instance of Memory class.
	 * @param object|null $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 *
	 * @return void
	 */
	protected function load_module_files( $path, $memory, $dependency_tree = null ) {
		// Load enabled modules.
		$activated  = $memory->get( 'active_modules', array() );
		$registered = $this->get_filtered_registries( $this->get_registered_list(), array( $this, 'verify_module_type' ) );
		$defaults   = $this->get_default_registries();

		// Get verified active modules.
		$activated_modules = $this->get_verified_registries( $registered, $defaults, $activated );

		// Collect all active plugins from the current installation.
		$active_plugins = array_column( WP::get_active_plugins(), 'slug' );

		foreach ( $activated_modules as $activated_module ) {
			$divi_builder_4_module_path = sprintf( '%1$s/Modules/%2$s/%2$s.php', $path, $activated_module['name'] );
			$divi_builder_5_module_path = sprintf( '%1$s/%2$s/%2$s.php', $path, $activated_module['name'] );
			$module_path                = 'D5' === $this->builder_type ? $divi_builder_5_module_path : $divi_builder_4_module_path;

			if ( $this->verify_requirements( $activated_module, $active_plugins ) && file_exists( $module_path ) ) {
				$this->require_module_path( $path, $activated_module['name'], $dependency_tree );

				if ( isset( $activated_module['child_name'] ) ) {
					$this->require_module_path( $path, $activated_module['child_name'], $dependency_tree );
				}

				if ( isset( $activated_module['full_width_name'] ) ) {
					$this->require_module_path( $path, $activated_module['full_width_name'], $dependency_tree );

					if ( isset( $activated_module['full_width_child_name'] ) ) {
						$this->require_module_path( $path, $activated_module['full_width_child_name'], $dependency_tree );
					}
				}
			}
		}
	}
}
