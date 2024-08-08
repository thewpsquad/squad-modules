<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

namespace DiviSquad\Settings\Migration;

use DiviSquad\Settings\Migration;

/**
 * Migration process to migrate image into Featured Image of Post Element modules.
 *
 * @since 2.0.0
 */
class PostElement extends Migration {

	/**
	 * Migration Version
	 *
	 * @since 4.24
	 *
	 * @var string
	 */
	public $version = '4.24';

	/**
	 * Get all fields to need to be migrated.
	 *
	 * Contains an array with:
	 * - key as new field
	 * - value consists affected fields as old field and module location
	 *
	 * @return array New and old fields need to be migrated.
	 * @since 2.0.0
	 *
	 */
	public function get_fields() {
		return array(
			'element' => array(
				'affected_fields' => array(
					'element' => $this->get_modules(),
				),
			),
		);
	}

	/**
	 * Get all modules affected.
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function get_modules() {
		return array( 'disq_post_grid_child', 'disq_cpt_grid_child' );
	}

	/**
	 * Migrate from old value into new value.
	 *
	 * @param string $field_name
	 * @param mixed $current_value
	 * @param string $module_slug
	 * @param mixed $saved_value
	 * @param string $saved_field_name
	 * @param array $attrs
	 * @param mixed $content
	 * @param string $module_address
	 *
	 * @return mixed
	 */
	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		return ! empty( $saved_value ) && 'image' === $saved_value ? 'featured_image' : $saved_value;
	}
}
