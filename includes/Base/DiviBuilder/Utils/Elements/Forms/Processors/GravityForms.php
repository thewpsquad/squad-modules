<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Gravity Forms Processor
 *
 * Handles the retrieval and processing of Gravity Forms.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since 3.0.1
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Processors;

use DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Form;

/**
 * Gravity Forms Processor
 *
 * Handles the retrieval and processing of Gravity Forms.
 *
 * @package DiviSquad
 * @since   3.0.1
 */
class GravityForms extends Form {

	/**
	 * Get Gravity Forms.
	 *
	 * @param string $collection The type of data to collect ('id' or 'title').
	 * @return array An array of Gravity Forms data.
	 */
	public function get_forms( $collection ) {
		if ( ! class_exists( '\RGFormsModel' ) ) {
			return array();
		}

		// Get all Gravity Forms.
		$forms = \RGFormsModel::get_forms( null, 'title' );

		return $this->process_form_data( $forms, $collection );
	}

	/**
	 * Get the ID of a Gravity Form.
	 *
	 * @param object $form The form object.
	 * @return int The form ID.
	 */
	protected function get_form_id( $form ) {
		return $form->id;
	}

	/**
	 * Get the title of a Gravity Form.
	 *
	 * @param object $form The form object.
	 * @return string The form title.
	 */
	protected function get_form_title( $form ) {
		return $form->title;
	}
}
