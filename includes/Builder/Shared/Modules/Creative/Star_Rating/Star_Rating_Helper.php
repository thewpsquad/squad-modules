<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Star Rating helper.
 *
 * Divi-free markup builder shared by the Divi 4 and Divi 5 Star Rating render
 * paths, so the stars output is byte-identical across builders. Previously the
 * logic was duplicated in each module and had drifted (the Divi 5 copy had lost
 * `aria-hidden` on the star glyphs and schema-markup support in the number
 * output). Because it has NO Divi dependency it boots cleanly under PHPUnit.
 *
 * @since   4.4.2
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Shared\Modules\Creative\Star_Rating;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use function absint;
use function esc_attr;
use function esc_html;
use function number_format;
use function sprintf;
use function wp_parse_args;

/**
 * Star Rating helper.
 *
 * @since 4.4.2
 */
final class Star_Rating_Helper {

	/**
	 * Build the star glyph markup (and optional numeric readout) for a rating.
	 *
	 * @since 4.4.2
	 *
	 * @param array<string, mixed> $args {
	 *     Rating arguments.
	 *
	 *     @type int        $rating_scale        Total number of stars (e.g. 5 or 10).
	 *     @type int|float  $rating              The rating value.
	 *     @type string     $show_number         'on' to append the numeric readout.
	 *     @type string     $stars_schema_markup 'on' to emit schema.org itemprops.
	 * }
	 *
	 * @return string
	 */
	public static function get_star_rating( array $args = array() ): string {
		$defaults = array(
			'rating_scale'        => 5,
			'rating'              => 5.0,
			'show_number'         => 'off',
			'stars_schema_markup' => 'off',
		);

		$args = wp_parse_args( $args, $defaults );

		$scale      = absint( $args['rating_scale'] );
		$int_rating = absint( $args['rating'] );
		$precision  = ( (float) $args['rating'] ) - (float) $int_rating;
		$output     = '';

		for ( $stars = 1; $stars <= $scale; $stars++ ) {
			if ( $stars <= $int_rating ) {
				$output .= '<i class="star-full" aria-hidden="true">☆</i>';
			} elseif ( $int_rating + 1 === $stars && $precision > 0 ) {
				// Partial star with precision using a CSS custom property.
				$decimal = number_format( $precision * 100, 0, '', '' );
				$output .= sprintf(
					'<i class="star-precision" aria-hidden="true" style="--squad-star-rating-precision: %1$s">☆</i>',
					esc_attr( $decimal )
				);
			} else {
				$output .= '<i class="star-empty" aria-hidden="true">☆</i>';
			}
		}

		if ( 'on' === $args['show_number'] ) {
			$rating_html = esc_html( (string) $args['rating'] );
			$scale_html  = esc_html( (string) $args['rating_scale'] );

			if ( 'on' === $args['stars_schema_markup'] ) {
				$stars_number_html = '<meta itemprop="worstRating" content="1">(<span itemprop="ratingValue">' . $rating_html . '</span>/<span itemprop="bestRating">' . $scale_html . '</span>)';
			} else {
				$stars_number_html = '(<span>' . $rating_html . '</span>/<span>' . $scale_html . '</span>)';
			}

			$output .= ' <span class="star-rating-text">' . $stars_number_html . '</span>';
		}

		return $output;
	}
}
