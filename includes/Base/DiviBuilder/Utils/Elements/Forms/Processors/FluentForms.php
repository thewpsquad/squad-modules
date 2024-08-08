<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Fluent Forms Processor
 *
 * Handles the retrieval and processing of Fluent Forms.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.1
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Processors;

use DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Form;

/**
 * Fluent Forms Processor
 *
 * Handles the retrieval and processing of Fluent Forms.
 *
 * @package DiviSquad\Base\DiviBuilder\Utils\Elements
 * @since   3.0.1
 */
class FluentForms extends Form {

	/**
	 * Get Fluent Forms.
	 *
	 * @param string $collection The type of data to collect ('id' or 'title').
	 * @return array An array of Fluent Forms data.
	 */
	public function get_forms( $collection ) {
		if ( ! function_exists( 'wpFluentForm' ) ) {
			return array();
		}

		$forms = \wpFluent()->table( 'fluentform_forms' )
							->select( array( 'id', 'title' ) )
							->orderBy( 'id', 'DESC' )
							->get();

		return $this->process_form_data( $forms, $collection );
	}

	/**
	 * Get the ID of a Fluent Form.
	 *
	 * @param object $form The form object.
	 * @return int The form ID.
	 */
	protected function get_form_id( $form ) {
		return $form->id;
	}

	/**
	 * Get the title of a Fluent Form.
	 *
	 * @param object $form The form object.
	 * @return string The form title.
	 */
	protected function get_form_title( $form ) {
		return $form->title;
	}
}
