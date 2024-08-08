<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Factory for creating migration instances.
 *
 * @package DiviSquad\Settings
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.1
 */

namespace DiviSquad\Base\Factories;

use DiviSquad\Settings\Migration as AbstractMigration;
use InvalidArgumentException;

/**
 * Factory for creating migration instances.
 *
 * @package DiviSquad\Settings
 * @since   3.0.1
 */
class Migration {

	/**
	 * Creates a migration instance based on the migration name.
	 *
	 * @param string $class_name The name of the migration class to instantiate.
	 * @return AbstractMigration The migration instance.
	 * @throws InvalidArgumentException If the class does not exist or is not an instance of AbstractMigration.
	 */
	public function create( $class_name ) {
		if ( ! class_exists( $class_name ) ) {
				throw new InvalidArgumentException(
					sprintf(
						// translators: %s: class name.
						esc_html__( "Class '%s' does not exist.", 'squad-modules-for-divi' ),
						esc_html( $class_name )
					)
				);
		}
		$instance = new $class_name();
		if ( ! $instance instanceof AbstractMigration ) {
				throw new InvalidArgumentException(
					sprintf(
						// translators: %s: class name.
						esc_html__( "Class '%s' is not an instance of AbstractMigration.", 'squad-modules-for-divi' ),
						esc_html( $class_name )
					)
				);
		}

		return $instance;
	}
}
