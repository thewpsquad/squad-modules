<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Gallery Image value object.
 *
 * Extracted from Image_Gallery so each file declares a single class, which is
 * what PSR-4 autoloading and the coding standard both expect. Behaviour is
 * unchanged; the class was previously declared inline above the module.
 *
 * @since   4.5.1
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Version4\Modules\Media;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Gallery attachment value object.
 *
 * Lightweight typed container for the per-image data the gallery renderer needs.
 * Divi 4 previously mutated dynamic properties directly onto the {@see WP_Post}
 * objects returned by {@see get_posts()}; collecting the same values into this
 * strongly-typed object keeps the rendered markup and the computed-callback JSON
 * byte-identical while the property accesses remain statically analysable.
 *
 * @since   1.2.0
 * @package DiviSquad
 */
class Gallery_Image {
	/**
	 * The attachment ID.
	 *
	 * @var int
	 */
	public int $ID = 0;

	/**
	 * The attachment excerpt (used as caption/pinterest/tweet text).
	 *
	 * @var string
	 */
	public string $post_excerpt = '';

	/**
	 * The attachment title.
	 *
	 * @var string
	 */
	public string $image_title = '';

	/**
	 * The attachment caption.
	 *
	 * @var string
	 */
	public string $image_caption = '';

	/**
	 * The attachment description.
	 *
	 * @var string
	 */
	public string $image_description = '';

	/**
	 * The attachment permalink.
	 *
	 * @var string
	 */
	public string $image_href = '';

	/**
	 * The full-size image URL.
	 *
	 * @var string
	 */
	public string $image_src_full = '';

	/**
	 * The thumbnail image URL.
	 *
	 * @var string
	 */
	public string $image_src_thumb = '';

	/**
	 * The image alt text.
	 *
	 * @var string
	 */
	public string $image_alt_text = '';

	/**
	 * The large (lightGallery) image size string.
	 *
	 * @var string
	 */
	public string $lg_size = '';
}
