<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Formidable Processor
 *
 * Handles the retrieval and processing of Formidable Forms.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.1.0
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Processors;

use DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Form;

/**
 * Formidable Forms Processor
 *
 * Handles the retrieval and processing of Formidable Forms.
 *
 * @package DiviSquad
 * @since   3.1.0
 */
class Formidable extends Form {

	/**
	 * Get Formidable Forms.
	 *
	 * @param string $collection The type of data to collect ('id' or 'title').
	 * @return array An array of Formidable Forms data.
	 */
	public function get_forms( $collection ) {
		if ( ! class_exists( 'FrmForm' ) ) {
			return array();
		}

		// Get all Formidable Forms.
		$forms = \FrmForm::get_published_forms( array(), 999 );

		return $this->process_form_data( $forms, $collection );
	}

	/**
	 * Get the ID of a Formidable Form.
	 *
	 * @param object $form The form object.
	 * @return int The form ID.
	 */
	protected function get_form_id( $form ) {
		return $form->id;
	}

	/**
	 * Get the title of a Formidable Form.
	 *
	 * @param object $form The form object.
	 * @return string The form title.
	 */
	protected function get_form_title( $form ) {
		return $form->name;
	}
}
