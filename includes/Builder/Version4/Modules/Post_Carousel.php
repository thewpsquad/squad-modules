<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * The Post Carousel Module Class which extend the Post Grid Module Class.
 *
 * Classic (Divi 4) counterpart of the native Divi 5 Post Carousel block. It reuses the
 * {@see Post_Grid} query + post-element rendering engine — each queried post becomes a
 * `swiper-slide` instead of a grid `<li>` — so it inherits every Post Element type, icon,
 * separator and query option for free, and renders the same Swiper markup the shared
 * `squad-module-post-carousel` frontend script expects.
 *
 * @since   3.4.0
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Version4\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Core\Supports\Polyfills\Str;
use Throwable;
use WP_Post;
use WP_Query;
use function absint;
use function esc_attr;
use function esc_html__;
use function get_post;
use function sanitize_html_class;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_json_encode;
use function wp_parse_args;
use function wp_reset_postdata;

/**
 * The Post-Carousel Module Class.
 *
 * @since   3.4.0
 * @package DiviSquad
 */
class Post_Carousel extends Post_Grid {

	/**
	 * Transition effects the carousel accepts, used as the allowlist for the `carousel_effect` prop.
	 *
	 * @since 3.4.0
	 *
	 * @var array<int, string>
	 */
	protected static array $squad_carousel_effects = array( 'slide', 'fade', 'coverflow' );

	/**
	 * Initiate Module.
	 *
	 * Builds on the Post Grid definition, then re-points it at the carousel: new identity,
	 * carousel-only toggles, and no pagination / load-more UI.
	 *
	 * @since 3.4.0
	 *
	 * @return void
	 */
	public function init(): void {
		parent::init();

		// The Post Grid root selector every inherited advanced/custom-css selector was built from.
		$grid_css_element = $this->main_css_element;

		$this->name      = esc_html__( 'Post Carousel', 'squad-modules-for-divi' );
		$this->plural    = esc_html__( 'Post Carousels', 'squad-modules-for-divi' );
		$this->icon_path = divi_squad()->get_icon_path( 'post-carousel.svg' );

		$this->slug             = 'disq_post_carousel';
		$this->child_slug       = 'disq_post_grid_child';
		$this->vb_support       = 'on';
		$this->main_css_element = "%%order_class%%.$this->slug";

		// The inherited selectors still point at `%%order_class%%.disq_post_grid`, which never
		// matches a carousel instance. Re-point them at the carousel root class.
		$this->advanced_fields   = $this->squad_retarget_selectors( $this->advanced_fields, $grid_css_element );
		$this->custom_css_fields = $this->squad_retarget_selectors( $this->custom_css_fields, $grid_css_element );

		$this->squad_remove_grid_only_settings();

		// Declare the carousel toggle beside the inherited Post/Layout option toggles.
		$this->settings_modal_toggles['general']['toggles']['carousel_settings'] = esc_html__( 'Carousel Settings', 'squad-modules-for-divi' );
	}

	/**
	 * Declare general fields for the module.
	 *
	 * Everything the Post Grid declares, minus the pagination and load-more groups, plus the
	 * carousel controls. The field names mirror the Divi 5 `query.innerContent` sub-names.
	 *
	 * @since 3.4.0
	 *
	 * @return array<string, array<string, array<int|string, string>|bool|string>>
	 */
	public function get_fields(): array {
		$fields = parent::get_fields();

		// Pagination and load-more page through a grid; a carousel pages through slides instead.
		foreach ( array_keys( $fields ) as $field_key ) {
			if ( ! is_string( $field_key ) ) {
				continue;
			}

			if ( Str::starts_with( $field_key, 'pagination' ) || Str::starts_with( $field_key, 'active_pagination' ) || Str::starts_with( $field_key, 'load_more' ) ) {
				unset( $fields[ $field_key ] );
			}
		}

		return array_merge( $fields, $this->squad_get_carousel_fields() );
	}

	/**
	 * Render module output.
	 *
	 * @since 3.4.0
	 *
	 * @param array<string, mixed> $attrs       List of unprocessed attributes.
	 * @param string               $content     Content being processed.
	 * @param string               $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string module's rendered output.
	 */
	public function render( $attrs, $content, $render_slug ): string {
		// This render does not delegate to Post_Grid::render(), so it has to claim the
		// rendering slot itself; the inherited post-element handlers step aside for any
		// instance that is not the one rendering. See Post_Grid::squad_is_rendering_module().
		$previous_rendering_instance     = self::$squad_rendering_instance;
		self::$squad_rendering_instance = $this;

		try {
			// Show a notice message in the frontend if the list item is empty.
			if ( '' === $content ) {
				return sprintf(
					'<div class="squad-notice">%s</div>',
					esc_html__( 'No elements found. Please add one or more elements to display in the post carousel.', 'squad-modules-for-divi' )
				);
			}

			$props      = wp_parse_args( $attrs, $this->props );
			$post_query = new WP_Query( static::squad_build_post_query_args( $props, $this->content ) );

			if ( ! $post_query->have_posts() ) {
				wp_reset_postdata();

				return sprintf(
					'<div class="squad-notice">%s</div>',
					esc_html__( 'No posts found that match the specified criteria.', 'squad-modules-for-divi' )
				);
			}

			$slides      = $this->squad_render_carousel_slides( $post_query, $props );
			$found_posts = (int) $post_query->found_posts;

			/* Restore original Post Data */
			wp_reset_postdata();

			if ( '' === trim( $slides ) ) {
				return sprintf(
					'<div class="squad-notice">%s</div>',
					esc_html__( 'No posts found that match the specified criteria.', 'squad-modules-for-divi' )
				);
			}

			$this->squad_generate_all_styles( $attrs );

			wp_enqueue_style( 'squad-vendor-swiper' );
			wp_enqueue_script( 'squad-module-post-carousel' );

			$uid = $this->squad_get_instance_uid( $render_slug );

			/*
			 * Structure mirrors the Divi 5 render callback so the shared frontend script
			 * (`src/divi-builder/divi-5/modules/frontend/post-carousel.ts`) drives both builders.
			 * The extra `squad-post-container` class is Divi 4 only: it keeps every inherited
			 * Post Grid design selector (`… .squad-post-container .post …`) alive on this markup.
			 * It sits on the block-level `.swiper` root, where the grid-only `grid-template-columns`
			 * and `gap` declarations are inert.
			 */
			return sprintf(
				'<div class="swiper squad-post-carousel squad-post-container" data-swiper-options=\'%1$s\'><div class="swiper-wrapper">%2$s</div>%3$s</div>',
				esc_attr( (string) wp_json_encode( $this->squad_build_swiper_options( $uid, $found_posts ) ) ),
				$slides,
				$this->squad_render_carousel_controls( $uid )
			);
		} catch ( Throwable $e ) {
			divi_squad()->log_error( $e, 'Error in Squad Post Carousel module render method' );

			return '';
		} finally {
			self::$squad_rendering_instance = $previous_rendering_instance;
		}
	}

	/**
	 * Render every queried post as a Swiper slide.
	 *
	 * Divi 5 does the same thing through `Post_Grid::render_post_items( …, 'div', 'swiper-slide' )`;
	 * here the parameterised Post Grid item renderer swaps the grid `<li>` for a slide `<div>`.
	 *
	 * @since 3.4.0
	 *
	 * @param WP_Query             $post_query The resolved post query.
	 * @param array<string, mixed> $props      The merged module properties.
	 *
	 * @return string
	 */
	protected function squad_render_carousel_slides( WP_Query $post_query, array $props ): string {
		ob_start();

		try {
			while ( $post_query->have_posts() ) {
				$post_query->the_post();
				$post = get_post();
				if ( ! $post instanceof WP_Post ) {
					continue;
				}

				static::squad_render_current_post( $post, $props, $this->content, 'div', 'swiper-slide' );
			}

			return (string) ob_get_clean();
		} catch ( Throwable $e ) {
			ob_get_clean();
			divi_squad()->log_error( $e, 'Error while rendering Squad Post Carousel slides' );

			return '';
		}
	}

	/**
	 * Declare the carousel control fields.
	 *
	 * Labels, ranges and defaults mirror the Divi 5 `contentCarousel` group in
	 * `src/divi-builder/divi-5/modules/builder/post-carousel/module.json-source.ts`.
	 *
	 * @since 3.4.0
	 *
	 * @return array<string, array<string, array<int|string, string>|bool|string>>
	 */
	protected function squad_get_carousel_fields(): array {
		return array(
			'carousel_slides_desktop' => divi_squad()->d4_module_helper->add_range_field(
				esc_html__( 'Slides (Desktop)', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Number of slides shown on desktop.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '8',
						'max'       => '8',
						'step'      => '1',
					),
					'default'        => '3',
					'unitless'       => true,
					'fixed_range'    => true,
					'mobile_options' => false,
					'sticky'         => false,
					'hover'          => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'carousel_settings',
				)
			),
			'carousel_slides_tablet'  => divi_squad()->d4_module_helper->add_range_field(
				esc_html__( 'Slides (Tablet)', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Number of slides shown on tablet.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '8',
						'max'       => '8',
						'step'      => '1',
					),
					'default'        => '2',
					'unitless'       => true,
					'fixed_range'    => true,
					'mobile_options' => false,
					'sticky'         => false,
					'hover'          => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'carousel_settings',
				)
			),
			'carousel_slides_phone'   => divi_squad()->d4_module_helper->add_range_field(
				esc_html__( 'Slides (Phone)', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Number of slides shown on phone.', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '1',
						'min'       => '1',
						'max_limit' => '6',
						'max'       => '6',
						'step'      => '1',
					),
					'default'        => '1',
					'unitless'       => true,
					'fixed_range'    => true,
					'mobile_options' => false,
					'sticky'         => false,
					'hover'          => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'carousel_settings',
				)
			),
			'carousel_gap'            => divi_squad()->d4_module_helper->add_range_field(
				esc_html__( 'Slide Gap', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Space between slides (px).', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '0',
						'min'       => '0',
						'max_limit' => '200',
						'max'       => '200',
						'step'      => '1',
					),
					'default'        => '30',
					'unitless'       => true,
					'fixed_range'    => true,
					'mobile_options' => false,
					'sticky'         => false,
					'hover'          => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'carousel_settings',
				)
			),
			'carousel_speed'          => divi_squad()->d4_module_helper->add_range_field(
				esc_html__( 'Transition Speed', 'squad-modules-for-divi' ),
				array(
					'description'    => esc_html__( 'Slide transition duration (ms).', 'squad-modules-for-divi' ),
					'range_settings' => array(
						'min_limit' => '100',
						'min'       => '100',
						'max_limit' => '5000',
						'max'       => '5000',
						'step'      => '50',
					),
					'default'        => '500',
					'unitless'       => true,
					'fixed_range'    => true,
					'mobile_options' => false,
					'sticky'         => false,
					'hover'          => false,
					'tab_slug'       => 'general',
					'toggle_slug'    => 'carousel_settings',
				)
			),
			'carousel_effect'         => divi_squad()->d4_module_helper->add_select_box_field(
				esc_html__( 'Effect', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Transition effect between slides.', 'squad-modules-for-divi' ),
					'options'          => array(
						'slide'     => esc_html__( 'Slide', 'squad-modules-for-divi' ),
						'fade'      => esc_html__( 'Fade', 'squad-modules-for-divi' ),
						'coverflow' => esc_html__( 'Coverflow', 'squad-modules-for-divi' ),
					),
					'default'          => 'slide',
					'default_on_front' => 'slide',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'carousel_settings',
				)
			),
			'carousel_centered'       => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Centered Slides', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Center the active slide.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'tab_slug'    => 'general',
					'toggle_slug' => 'carousel_settings',
				)
			),
			'carousel_arrows'         => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Show Arrows', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Show previous/next navigation arrows.', 'squad-modules-for-divi' ),
					'default'          => 'on',
					'default_on_front' => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'carousel_settings',
				)
			),
			'carousel_dots'           => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Show Dots', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Show clickable pagination dots.', 'squad-modules-for-divi' ),
					'default'          => 'on',
					'default_on_front' => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'carousel_settings',
				)
			),
			'carousel_loop'           => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Loop', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Continuously loop the slides.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'tab_slug'    => 'general',
					'toggle_slug' => 'carousel_settings',
				)
			),
			'carousel_autoplay'       => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Autoplay', 'squad-modules-for-divi' ),
				array(
					'description' => esc_html__( 'Automatically advance slides.', 'squad-modules-for-divi' ),
					'default'     => 'off',
					'affects'     => array(
						'carousel_autoplay_delay',
						'carousel_pause_on_hover',
					),
					'tab_slug'    => 'general',
					'toggle_slug' => 'carousel_settings',
				)
			),
			'carousel_autoplay_delay' => divi_squad()->d4_module_helper->add_range_field(
				esc_html__( 'Autoplay Delay', 'squad-modules-for-divi' ),
				array(
					'description'     => esc_html__( 'Delay between auto transitions (ms).', 'squad-modules-for-divi' ),
					'range_settings'  => array(
						'min_limit' => '500',
						'min'       => '500',
						'max_limit' => '15000',
						'max'       => '15000',
						'step'      => '100',
					),
					'default'         => '3000',
					'unitless'        => true,
					'fixed_range'     => true,
					'mobile_options'  => false,
					'sticky'          => false,
					'hover'           => false,
					'depends_show_if' => 'on',
					'tab_slug'        => 'general',
					'toggle_slug'     => 'carousel_settings',
				)
			),
			'carousel_pause_on_hover' => divi_squad()->d4_module_helper->add_yes_no_field(
				esc_html__( 'Pause On Hover', 'squad-modules-for-divi' ),
				array(
					'description'      => esc_html__( 'Pause autoplay while hovering the carousel.', 'squad-modules-for-divi' ),
					'default'          => 'on',
					'default_on_front' => 'on',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'general',
					'toggle_slug'      => 'carousel_settings',
				)
			),
		);
	}

	/**
	 * Build the Swiper option object from the module props.
	 *
	 * Mirrors the Divi 5 `Post_Carousel::build_swiper_options()` shape one-for-one so both
	 * builders hand the same configuration to Swiper. Every numeric value is bounded and the
	 * effect is allowlisted before it reaches the markup.
	 *
	 * @since 3.4.0
	 *
	 * @param string $uid        Per-instance identifier used to scope the navigation selectors.
	 * @param int    $post_count Number of posts in the query (for loop guarding).
	 *
	 * @return array<string, mixed>
	 */
	protected function squad_build_swiper_options( string $uid, int $post_count = 0 ): array {
		$slides_desktop = max( 1, min( 8, absint( $this->prop( 'carousel_slides_desktop', '3' ) ) ) );
		$slides_tablet  = max( 1, min( 8, absint( $this->prop( 'carousel_slides_tablet', '2' ) ) ) );
		$slides_phone   = max( 1, min( 6, absint( $this->prop( 'carousel_slides_phone', '1' ) ) ) );
		$gap            = max( 0, min( 200, absint( $this->prop( 'carousel_gap', '30' ) ) ) );
		$speed          = max( 100, min( 5000, absint( $this->prop( 'carousel_speed', '500' ) ) ) );

		$effect = (string) $this->prop( 'carousel_effect', 'slide' );
		$effect = in_array( $effect, static::$squad_carousel_effects, true ) ? $effect : 'slide';

		// Swiper's fade effect only supports a single slide per view — clamp every breakpoint.
		if ( 'fade' === $effect ) {
			$slides_desktop = 1;
			$slides_tablet  = 1;
			$slides_phone   = 1;
		}

		// Swiper loop needs more slides than are shown; disable it when too few posts exist.
		$max_slides = max( $slides_desktop, $slides_tablet, $slides_phone );
		$loop       = 'on' === $this->prop( 'carousel_loop', 'off' );
		if ( $loop && $post_count > 0 && $post_count <= $max_slides ) {
			$loop = false;
		}

		$options = array(
			'slidesPerView'  => $slides_phone,
			'spaceBetween'   => $gap,
			'speed'          => $speed,
			'effect'         => $effect,
			'centeredSlides' => 'on' === $this->prop( 'carousel_centered', 'off' ),
			'loop'           => $loop,
			'breakpoints'    => array(
				'768'  => array(
					'slidesPerView' => $slides_tablet,
					'spaceBetween'  => $gap,
				),
				'1024' => array(
					'slidesPerView' => $slides_desktop,
					'spaceBetween'  => $gap,
				),
			),
		);

		if ( 'on' === $this->prop( 'carousel_arrows', 'on' ) ) {
			$options['navigation'] = array(
				'nextEl' => ".{$uid}-next",
				'prevEl' => ".{$uid}-prev",
			);
		}

		if ( 'on' === $this->prop( 'carousel_dots', 'on' ) ) {
			$options['pagination'] = array(
				'el'             => ".{$uid}-pagination",
				'clickable'      => true,
				'dynamicBullets' => true,
			);
		}

		if ( 'on' === $this->prop( 'carousel_autoplay', 'off' ) ) {
			$options['autoplay'] = array(
				'delay'                => max( 500, min( 15000, absint( $this->prop( 'carousel_autoplay_delay', '3000' ) ) ) ),
				'pauseOnMouseEnter'    => 'on' === $this->prop( 'carousel_pause_on_hover', 'on' ),
				'disableOnInteraction' => false,
			);
		}

		return $options;
	}

	/**
	 * Render the arrow + pagination control markup.
	 *
	 * Identical markup to the Divi 5 `Post_Carousel::render_controls()`.
	 *
	 * @since 3.4.0
	 *
	 * @param string $uid Per-instance identifier.
	 *
	 * @return string
	 */
	protected function squad_render_carousel_controls( string $uid ): string {
		$html = '';

		if ( 'on' === $this->prop( 'carousel_arrows', 'on' ) ) {
			$html .= sprintf(
				'<div class="swiper-button-prev %1$s-prev"></div><div class="swiper-button-next %1$s-next"></div>',
				esc_attr( $uid )
			);
		}

		if ( 'on' === $this->prop( 'carousel_dots', 'on' ) ) {
			$html .= sprintf( '<div class="swiper-pagination %1$s-pagination"></div>', esc_attr( $uid ) );
		}

		return $html;
	}

	/**
	 * Build a stable per-instance identifier for scoping the Swiper navigation selectors.
	 *
	 * @since 3.4.0
	 *
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	protected function squad_get_instance_uid( string $render_slug ): string {
		$order_class = sanitize_html_class( (string) self::get_module_order_class( $render_slug ) );

		return '' !== $order_class ? "squad-pc-{$order_class}" : 'squad-pc-' . $this->slug;
	}

	/**
	 * Re-point inherited Post Grid selectors at the carousel root class.
	 *
	 * The parent builds every advanced/custom-css selector from its own `main_css_element`, which
	 * is baked in before this module can change the slug. This walks the resulting definition and
	 * swaps the grid root for the carousel root, leaving every other part of the selector intact.
	 *
	 * @since 3.4.0
	 *
	 * @param array<mixed> $definition The inherited field definition.
	 * @param string       $search     The Post Grid root selector to replace.
	 *
	 * @return array<mixed>
	 */
	protected function squad_retarget_selectors( array $definition, string $search ): array {
		if ( '' === $search ) {
			return $definition;
		}

		foreach ( $definition as $key => $value ) {
			if ( is_array( $value ) ) {
				$definition[ $key ] = $this->squad_retarget_selectors( $value, $search );
			} elseif ( is_string( $value ) ) {
				$definition[ $key ] = str_replace( $search, $this->main_css_element, $value );
			}
		}

		return $definition;
	}

	/**
	 * Drop the inherited pagination and load-more option groups.
	 *
	 * Their fields are removed in {@see self::get_fields()}; this clears the matching toggles,
	 * advanced-field groups and custom-css entries so the settings modal has no empty sections.
	 *
	 * @since 3.4.0
	 *
	 * @return void
	 */
	protected function squad_remove_grid_only_settings(): void {
		foreach ( array( 'pagination', 'load_more_button' ) as $general_toggle ) {
			unset( $this->settings_modal_toggles['general']['toggles'][ $general_toggle ] );
		}

		$advanced_toggles = array(
			'load_more_button',
			'load_more_button_text',
			'pagination_wrapper',
			'pagination',
			'pagination_text',
			'active_pagination',
			'active_pagination_text',
		);
		foreach ( $advanced_toggles as $advanced_toggle ) {
			unset( $this->settings_modal_toggles['advanced']['toggles'][ $advanced_toggle ] );
		}

		foreach ( array( 'load_more_button_text', 'pagination_text', 'active_pagination_text' ) as $font_group ) {
			unset( $this->advanced_fields['fonts'][ $font_group ] );
		}

		$style_groups = array( 'load_more_button', 'pagination', 'pagination_wrapper', 'active_pagination' );
		foreach ( $style_groups as $style_group ) {
			unset( $this->advanced_fields['borders'][ $style_group ], $this->advanced_fields['box_shadow'][ $style_group ] );
		}

		$custom_css_groups = array( 'load_more_button', 'pagination_wrapper', 'pagination_numbers', 'pagination_active_number' );
		foreach ( $custom_css_groups as $custom_css_group ) {
			unset( $this->custom_css_fields[ $custom_css_group ] );
		}
	}
}
