<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Gallery Image value object.
 *
 * Extracted from Image_Gallery so each file declares a single class, which is
 * what PSR-4 autoloading and the coding standard both expect. Behaviour is
 * unchanged; the class was previously declared inline above the module.
 *
 * Both builders rendered the same gallery data through their own identical copy
 * of this class, one under Version4\Modules\Media and one under
 * Version5\Modules\Media. Neither was a Divi module, so neither belonged in a
 * Modules directory; they are consolidated here as a single shared value object,
 * alongside the other code both builders share.
 *
 * @since   4.5.1
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Shared\Modules\Media\Image_Gallery;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Gallery attachment value object.
 *
 * Lightweight typed container for the per-image data the gallery renderer needs.
 * Divi 4 mutated dynamic properties directly onto the {@see WP_Post} objects
 * returned by {@see get_posts()}; both renderers now collect the same values
 * into this strongly-typed object so the markup is byte-identical while the
 * property accesses remain statically analysable.
 *
 * @since 3.4.0
 */
class Gallery_Image {

	/**
	 * The attachment ID.
	 *
	 * @since 3.4.0
	 *
	 * @var int
	 */
	public int $ID = 0;

	/**
	 * The attachment excerpt (used as caption/pinterest/tweet text).
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $post_excerpt = '';

	/**
	 * The attachment title.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_title = '';

	/**
	 * The attachment caption.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_caption = '';

	/**
	 * The attachment description.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_description = '';

	/**
	 * The attachment permalink.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_href = '';

	/**
	 * The full-size image URL.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_src_full = '';

	/**
	 * The thumbnail image URL.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_src_thumb = '';

	/**
	 * The image alt text.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $image_alt_text = '';

	/**
	 * The large (lightGallery) image size string.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public string $lg_size = '';
}
