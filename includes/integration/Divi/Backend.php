<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Backend integration helper for Divi Builder
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Integration\Divi;

use DiviSquad\Base\BuilderBackendPlaceholder;
use DiviSquad\Utils\Helper;
use function et_fb_process_shortcode;

/**
 * Define integration helper functionalities for this plugin.
 *
 * @since      1.0.0
 * @package    squad-modules-for-divi
 */
class Backend extends BuilderBackendPlaceholder {
	/**
	 * Filters backend data passed to the Visual Builder.
	 * This function is used to add static helpers whose content rarely changes.
	 * eg: google fonts, module defaults, and so on.
	 *
	 * @param array $exists The existed definitions.
	 *
	 * @return array
	 */
	public function static_asset_definitions( $exists = array() ) {
		// Defaults data for modules.
		$defaults = $this->get_modules_defaults();

		// generate shortcode for business day child module.
		$business_day_1 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Sun Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( '10AM - 5PM', 'Modules dummy content', 'squad-modules-for-divi' )
		);
		$business_day_2 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Mon Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( '10AM - 5PM', 'Modules dummy content', 'squad-modules-for-divi' )
		);
		$business_day_3 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Tue Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( '10AM - 5PM', 'Modules dummy content', 'squad-modules-for-divi' )
		);
		$business_day_4 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Wed Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( '10AM - 5PM', 'Modules dummy content', 'squad-modules-for-divi' )
		);
		$business_day_5 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Thu Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( '10AM - 5PM', 'Modules dummy content', 'squad-modules-for-divi' )
		);
		$business_day_6 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Fri Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( 'Closed', 'Modules dummy content', 'squad-modules-for-divi' )
		);
		$business_day_7 = sprintf(
			'[disq_business_day day="%1$s" time="%2$s"][/disq_business_day]',
			_x( 'Sat Day', 'Modules dummy content', 'squad-modules-for-divi' ),
			_x( 'Closed', 'Modules dummy content', 'squad-modules-for-divi' )
		);

		// child module default data.
		$post_grid_child_defaults = array(
			'element_image_fullwidth__enable' => 'off',
			'element_excerpt__enable'         => 'on',
			'element_ex_con_length__enable'   => 'on',
			'element_ex_con_length'           => '20',
			'element_author_name_type'        => 'nickname',
			'element_read_more_text'          => $defaults['read_more'],
			'element_comments_before'         => $defaults['comments_before'],
			'element_categories_sepa'         => ',',
			'element_tags_sepa'               => ',',
			'element_custom_text'             => $defaults['custom_text'],
		);
		$accordion_child_defaults = array(
			'title'                  => $defaults['title'],
			'content_button__enable' => 'on',
			'button_text'            => _x( 'Learn More', 'Modules dummy content', 'squad-modules-for-divi' ),
		);

		// generate shortcode for post-grid child module.
		$post_grid_child1 = sprintf(
			'[disq_post_grid_child %s][/disq_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array( 'element' => 'image' ),
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child2 = sprintf(
			'[disq_post_grid_child %s][/disq_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array(
						'element'           => 'title',
						'element_title_tag' => 'h2',
					),
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child3 = sprintf(
			'[disq_post_grid_child %s][/disq_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array( 'element' => 'content' ),
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child4 = sprintf(
			'[disq_post_grid_child %s][/disq_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array(
						'element'           => 'date',
						'element_date_type' => 'modified',
					),
					$post_grid_child_defaults
				)
			)
		);
		$post_grid_child5 = sprintf(
			'[disq_post_grid_child %s][/disq_post_grid_child]',
			Helper::implode_assoc_array(
				array_merge(
					array( 'element' => 'read_more' ),
					$post_grid_child_defaults
				)
			)
		);

		// generate shortcode for accordion-grid child module.
		$accordion_child = sprintf(
			'[disq_accordion_child %1$s]%2$s[/disq_accordion_child]',
			Helper::implode_assoc_array( $accordion_child_defaults ),
			$defaults['body']
		);

		$business_day_child_shortcodes = implode(
			'',
			array(
				$business_day_1,
				$business_day_2,
				$business_day_3,
				$business_day_4,
				$business_day_5,
				$business_day_6,
				$business_day_7,
			)
		);
		$post_grid_child_shortcodes    = implode(
			'',
			array(
				$post_grid_child1,
				$post_grid_child2,
				$post_grid_child3,
				$post_grid_child4,
				$post_grid_child5,
			)
		);
		$accordion_child_shortcodes    = implode(
			'',
			array(
				$accordion_child,
				$accordion_child,
				$accordion_child,
			)
		);

		// Default texts for option list field.
		$typing_text_default_text   = array(
			array(
				'value'   => _x( 'Typing Text', 'Modules dummy content', 'squad-modules-for-divi' ),
				'checked' => 0,
				'dragID'  => - 1,
			),
		);
		$gradient_text_default_text = array(
			array(
				'value'   => _x( 'Your Gradient Text Here', 'Modules dummy content', 'squad-modules-for-divi' ),
				'checked' => 0,
				'dragID'  => - 1,
			),
		);

		$definitions = array(
			'defaults' => array(
				'disq_accordion'                 => array(
					'accordion_open_icon'  => '&#x4c;||divi||400',
					'accordion_close_icon' => '&#x4b;||divi||400',
					'content'              => et_fb_process_shortcode( $accordion_child_shortcodes ),
				),
				'disq_accordion_child'           => array_merge(
					$accordion_child_defaults,
					array(
						'content' => $defaults['body'],
					)
				),
				'disq_divider'                   => array(
					'divider_icon' => $defaults['icon']['check'],
				),
				'disq_dual_button'               => array(
					'left_button_text'  => $defaults['button'],
					'left_button_icon'  => $defaults['icon']['check'],
					'right_button_text' => $defaults['button_two'],
					'right_button_icon' => $defaults['icon']['arrow'],
				),
				'disq_typing_text'               => array(
					'prefix_text' => _x( 'Your', 'Modules dummy content', 'squad-modules-for-divi' ),
					'typing_text' => wp_json_encode( $typing_text_default_text ),
					'suffix_text' => _x( 'Goes Here', 'Modules dummy content', 'squad-modules-for-divi' ),
				),
				'disq_image_mask'                => array(
					'image' => $defaults['image']['landscape'],
				),
				'disq_post_grid'                 => array(
					'content'                            => et_fb_process_shortcode( $post_grid_child_shortcodes ),
					'list_number_of_columns_last_edited' => 'on|desktop',
					'list_number_of_columns'             => '3',
					'list_number_of_columns_tablet'      => '2',
					'list_number_of_columns_phone'       => '1',
					'list_item_gap'                      => '20px',
					'pagination__enable'                 => 'on',
					'pagination_numbers__enable'         => 'on',
					'pagination_old_entries_text'        => _x( 'Older', 'Modules dummy content', 'squad-modules-for-divi' ),
					'pagination_next_entries_text'       => _x( 'Next', 'Modules dummy content', 'squad-modules-for-divi' ),
					'load_more_button_text'              => _x( 'Load More', 'Modules dummy content', 'squad-modules-for-divi' ),
				),
				'disq_post_grid_child'           => $post_grid_child_defaults,
				'disq_flip_box'                  => array(
					'front_title'                => _x( 'Flip Right', 'Modules dummy content', 'squad-modules-for-divi' ),
					'front_content'              => $defaults['body'],
					'back_button__enable'        => 'on',
					'back_button_text'           => _x( 'View More', 'Modules dummy content', 'squad-modules-for-divi' ),
					'back_button_icon_type'      => 'icon',
					'back_button_icon'           => '&#x35;||divi||400',
					'back_button_url'            => esc_url( 'https://squadmodules.com/module/flip-box' ),
					'back_button_url_new_window' => 'on',
				),
				'disq_business_hours'            => array(
					'content'          => et_fb_process_shortcode( $business_day_child_shortcodes ),
					'title'            => _x( 'Business Hours', 'Modules dummy content', 'squad-modules-for-divi' ),
					'day_elements_gap' => '20px',
				),
				'disq_business_day'              => array(
					'day'  => _x( 'Sun Day', 'Modules dummy content', 'squad-modules-for-divi' ),
					'time' => _x( '10AM - 5PM', 'Modules dummy content', 'squad-modules-for-divi' ),
				),
				'disq_bai_slider'                => array(
					'before_image'                      => $defaults['image']['landscape'],
					'after_image'                       => $defaults['image']['landscape'],
					'before_label__enable'              => 'on',
					'after_label__enable'               => 'on',
					'before_label'                      => _x( 'Before', 'Modules dummy content', 'squad-modules-for-divi' ),
					'after_label'                       => _x( 'After', 'Modules dummy content', 'squad-modules-for-divi' ),
					'slide_control_start_point'         => 25,
					'slide_control_shadow__enable'      => 'on',
					'slide_control_circle__enable'      => 'on',
					'slide_control_circle_blur__enable' => 'on',
					'slide_control_smoothing__enable'   => 'on',
					'slide_control_smoothing_amount'    => 100,
				),
				'disq_form_styler_cf7'           => array(),
				'disq_form_styler_wpforms'       => array(),
				'disq_form_styler_gravity_forms' => array(),
				'disq_post_reading_time'         => array(
					'time_prefix_text'          => _x( 'Reading Time', 'Modules dummy content', 'squad-modules-for-divi' ),
					'time_suffix_text'          => _x( 'minutes', 'Modules dummy content', 'squad-modules-for-divi' ),
					'time_suffix_text_singular' => _x( 'minute', 'Modules dummy content', 'squad-modules-for-divi' ),
				),
				'disq_glitch_text'               => array(
					'glitch_text' => _x( 'Your Glitch Text Here', 'Modules dummy content', 'squad-modules-for-divi' ),
				),
				'disq_gradient_text'             => array(
					'gradient_text'                      => wp_json_encode( $gradient_text_default_text ),
					'text_gradient_use_color_gradient'   => 'on',
					'text_gradient_color_gradient_stops' => '#1f7016 0%|#29c4a9 100%',
				),
				'disq_scrolling_text'            => array(
					'scrolling_text' => _x( 'Your Scrolling Text Here', 'Modules dummy content', 'squad-modules-for-divi' ),
				),
			),
		);

		return array_merge_recursive( $exists, $definitions );
	}

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @param string $content content.
	 *
	 * @return string
	 */
	public function asset_definitions( $content ) {
		return $content . sprintf(
				';window.DISQBuilderBackend=%1$s; jQuery.extend(true, window.ETBuilderBackend, %1$s);',
				et_fb_remove_site_url_protocol( wp_json_encode( $this->static_asset_definitions() ) )
			);
	}
}
