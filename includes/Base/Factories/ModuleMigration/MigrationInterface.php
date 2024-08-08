<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Interface for Squad Modules Migration.
 *
 * @package DiviSquad\Settings
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Base\Factories\ModuleMigration;

/**
 * Migration Interface
 *
 * @package DiviSquad\Base\Factories\ModuleMigration
 * @since   3.0.0
 */
interface MigrationInterface {

	/**
	 * Initialize migration.
	 */
	public static function init();

	/**
	 * Get all fields to need to be migrated.
	 *
	 * Contains an array with:
	 * - key as new field
	 * - value consists affected fields as old field and module location
	 *
	 * @return array New and old fields need to be migrated.
	 * @since 3.0.0
	 */
	public function get_fields();

	/**
	 * Get all modules affected.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function get_modules();

	/**
	 * Migrate from old value into new value.
	 *
	 * @param string $field_name        The field name.
	 * @param mixed  $current_value     The current value.
	 * @param string $module_slug       The module slug.
	 * @param mixed  $saved_value       The saved value.
	 * @param string $saved_field_name  The saved field name.
	 * @param array  $attrs             The attributes.
	 * @param mixed  $content           The content.
	 * @param string $module_address    The module address.
	 *
	 * @return mixed
	 * @since 3.0.0
	 */
	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address );

	/**
	 * Get all modules to need to be migrated.
	 *
	 * @return array
	 */
	public function get_content_migration_modules();

	/**
	 * This could have been written as abstract, but it's not as common to be expected to be implemented by every migration
	 *
	 * @param string $module_slug Internal system name for the module type.
	 * @param array  $attrs       Shortcode attributes.
	 * @param mixed  $content     Text/HTML content within the current module.
	 *
	 * @return mixed
	 */
	public function migrate_content( $module_slug, $attrs, $content );
}
