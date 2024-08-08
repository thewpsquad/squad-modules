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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\Divider\Divider::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\DualButton\DualButton::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\Lottie\Lottie::class ),
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
				'classes'            => array(
					'root_class'  => \DiviSquad\Modules\PostGrid\PostGrid::class,
					'child_class' => \DiviSquad\Modules\PostGridChild\PostGridChild::class,
				),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\TypingText\TypingText::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\ImageMask\ImageMask::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\FlipBox\FlipBox::class ),
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
				'classes'            => array(
					'root_class'  => \DiviSquad\Modules\BusinessHours\BusinessHours::class,
					'child_class' => \DiviSquad\Modules\BusinessHoursChild\BusinessHoursChild::class,
				),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\BeforeAfterImageSlider\BeforeAfterImageSlider::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\ImageGallery\ImageGallery::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\FormStylerContactForm7\ContactForm7::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\FormStylerWPForms\WPForms::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\FormStylerGravityForms\GravityForms::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\PostReadingTime\PostReadingTime::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\GlitchText\GlitchText::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\GradientText\GradientText::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\ScrollingText\ScrollingText::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\StarRating\StarRating::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\Breadcrumbs\Breadcrumbs::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\DropCapText\DropCapText::class ),
				'name'               => 'DropCapText',
				'label'              => esc_html__( 'Drop Cap Text', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Create visually appealing drop caps to add emphasis and style to your text content.', 'squad-modules-for-divi' ),
				'release_version'    => '1.4.0',
				'is_default_active'  => true,
				'is_premium_feature' => false,
				'type'               => 'D4',
				'category'           => 'creative-modules',
			),
			array(
				'classes'            => array( 'root_class' => \DiviSquad\Modules\VideoPopup\VideoPopup::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\GoogleMap\GoogleMap::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\FormStylerNinjaForms\NinjaForms::class ),
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
				'classes'            => array( 'root_class' => \DiviSquad\Modules\FormStylerFluentForms\FluentForms::class ),
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
				'label'              => esc_html__( 'Advanced List', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Elevate your content presentation providing versatile and stylish list formats for a captivating user experience.', 'squad-modules-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'Blurb',
				'label'              => esc_html__( 'Advanced Blurb', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Craft engaging and informative content with advanced styling and layout options for a standout user experience.', 'squad-modules-for-divi' ),
				'is_premium_feature' => true,
				'type'               => array( 'D4', 'D5' ),
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'UserList',
				'label'              => esc_html__( 'User List', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Showcase your users allowing you to display user profiles in a sleek and customizable list format.', 'squad-modules-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'Heading',
				'label'              => esc_html__( 'Advanced Heading', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Make a bold statement offering enhanced customization and design options for impactful and visually stunning headings.', 'squad-modules-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'TaxonomyList',
				'label'              => esc_html__( 'Taxonomy List', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Easily organize and display your taxonomy enhancing user experience.', 'squad-modules-for-divi' ),
				'is_premium_feature' => true,
				'type'               => 'D4',
				'category'           => 'pro-modules',
			),
			array(
				'name'               => 'CPTGrid',
				'label'              => esc_html__( 'CPT Grid', 'squad-modules-for-divi' ),
				'description'        => esc_html__( 'Showcase your Custom Post Types creating a visually appealing grid layout.', 'squad-modules-for-divi' ),
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
	 * Load the module class from path.
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
	 * Load the module class.
	 *
	 * @param string      $module_key      The module specification key.
	 * @param array       $module          The module.
	 * @param \ET\Builder\Framework\DependencyManagement\DependencyTree|null $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 *
	 * @return void
	 * @since 2.1.2
	 */
	protected function require_module_class( $module_key = 'name', $module = array(), $dependency_tree = null ) {
		// Replace `name` from the module key string if include underscore or not
		$module_key   = str_replace( array( '_', 'name' ), '', $module_key );
		$module_class = empty( $module_key ) ? 'root' : $module_key;

		// Load the module class for divi builder 5.
		if ( isset( $module['classes'][ "{$module_class}_block_class" ] ) && class_exists( $module['classes'][ "{$module_class}_block_class" ] ) ) {
			if ( is_object( $dependency_tree ) && method_exists( $dependency_tree, 'add_dependency' ) ) {
				$block_module_class = $module['classes'][ "{$module_class}_block_class" ];
				$class_interfaces   = class_implements( $block_module_class );
				$core_interface     = 'ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface';
				if ( $class_interfaces && in_array( $core_interface, $class_interfaces, true ) ) {
					$dependency_tree->add_dependency( new $block_module_class() );
				}
			}
		}

		// Load the module class for divi builder 4.
		if ( isset( $module['classes'][ "{$module_class}_class" ] ) && class_exists( $module['classes'][ "{$module_class}_class" ] ) ) {
			$squad_module = new $module['classes'][ "{$module_class}_class" ]();

			// Initialize custom hooks.
			if ( method_exists( $squad_module, 'squad_init_custom_hooks' ) ) {
				$squad_module->squad_init_custom_hooks();
			}
		}
	}

	/**
	 * Verify the requirements of the module.
	 *
	 * @param array $activated_module       The module.
	 * @param object|null $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 * @param string $module_key            The module name key.
	 *
	 * @return void
	 */
	private function load_module_if_exists( $activated_module, $dependency_tree, $module_key ) {
		if ( ! empty( $activated_module[ $module_key ] ) ) {
			$this->require_module_class( $module_key, $activated_module, $dependency_tree );
		}
	}

	/**
	 * Load enabled modules for Divi Builder from defined directory.
	 *
	 * @param string $path            The defined directory.
	 * @param object $dependency_tree `DependencyTree` class is used as a utility to manage loading classes in a meaningful manner.
	 *
	 * @return void
	 */
	public function load_modules( $path, $dependency_tree = null ) {
		// Validate the divi builder element base when the dependency tree is null.
		if ( is_null( $dependency_tree ) && ! class_exists( \ET_Builder_Element::class ) ) {
			return;
		}

		// Load enabled modules.
		$this->builder_type = is_null( $dependency_tree ) ? 'D4' : 'D5';
		$this->load_module_files( $path, divi_squad()->memory, $dependency_tree );
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
			/**
			 * Load modules from the class path.
			 * @since 2.1.2
			 */
			if ( isset( $activated_module['classes']['root_class'] ) && class_exists( $activated_module['classes']['root_class'] ) ) {
				if ( $this->verify_requirements( $activated_module, $active_plugins ) ) {
					$this->load_module_if_exists( $activated_module, $dependency_tree, 'name' );
					$this->load_module_if_exists( $activated_module, $dependency_tree, 'child_name' );
					$this->load_module_if_exists( $activated_module, $dependency_tree, 'full_width_name' );
					$this->load_module_if_exists( $activated_module, $dependency_tree, 'full_width_child_name' );
				}
			} else {
				$module_path_root = 'D5' === $this->builder_type ? 'Block' : '';
				$module_path_full = sprintf( '%1$s/%2$sModules/%3$s/%3$s.php', $path, $module_path_root, $activated_module['name'] );

				if ( $this->verify_requirements( $activated_module, $active_plugins ) && file_exists( $module_path_full ) ) {
					$module_names = array_filter(
						array(
							isset( $activated_module['name'] ) ? $activated_module['name'] : null,
							isset( $activated_module['child_name'] ) ? $activated_module['child_name'] : null,
							isset( $activated_module['full_width_name'] ) ? $activated_module['full_width_name'] : null,
							isset( $activated_module['full_width_child_name'] ) ? $activated_module['full_width_child_name'] : null,
						)
					);

					foreach ( $module_names as $module_name ) {
						$this->require_module_path( $path, $module_name, $dependency_tree );
					}
				}
			}
		}
	}
}
