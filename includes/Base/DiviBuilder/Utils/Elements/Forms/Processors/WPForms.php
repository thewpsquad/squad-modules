<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * WPForms Processor
 *
 * Handles the retrieval and processing of WPForms.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.1
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Processors;

use DiviSquad\Base\DiviBuilder\Utils\Elements\Forms\Form;

/**
 * WPForms Processor
 *
 * Handles the retrieval and processing of WPForms.
 *
 * @package DiviSquad
 * @since   3.0.1
 */
class WPForms extends Form {

	/**
	 * Get WPForms.
	 *
	 * @param string $collection The type of data to collect ('id' or 'title').
	 * @return array An array of WPForms data.
	 */
	public function get_forms( $collection ) {
		if ( ! function_exists( 'wpforms' ) ) {
			return array();
		}

		// Get all WPForms.
		$forms = get_posts(
			array(
				'post_type'      => 'wpforms',
				'posts_per_page' => -1,
			)
		);

		return $this->process_form_data( $forms, $collection );
	}

	/**
	 * Get the ID of a WPForm.
	 *
	 * @param \WP_Post $form The form post object.
	 * @return int The form ID.
	 */
	protected function get_form_id( $form ) {
		return $form->ID;
	}

	/**
	 * Get the title of a WPForm.
	 *
	 * @param \WP_Post $form The form post object.
	 * @return string The form title.
	 */
	protected function get_form_title( $form ) {
		return $form->post_title;
	}
}
