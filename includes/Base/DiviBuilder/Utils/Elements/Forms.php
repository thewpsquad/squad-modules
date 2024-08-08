<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Builder Form Utils Helper Class
 *
 * @since       1.5.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\DiviBuilder\Utils\Elements;

use WP_Post;
use function esc_html__;
use function get_posts;

trait Forms {

	/**
	 * Get default value for form id.
	 *
	 * @var string
	 */
	public static $default_form_id = 'cfcd208495d565ef66e7dff9f98764da';

	/**
	 * Store all forms and remove redundancy.
	 *
	 * @var array The collection of forms.
	 */
	public static $form_collections = array(
		'cf7'          => array(
			'id'    => array(),
			'title' => array(),
		),
		'wpforms'      => array(
			'id'    => array(),
			'title' => array(),
		),
		'fluetforms'   => array(
			'id'    => array(),
			'title' => array(),
		),
		'ninjaforms'   => array(
			'id'    => array(),
			'title' => array(),
		),
		'gravityforms' => array(
			'id'    => array(),
			'title' => array(),
		),
	);

	/**
	 * Declare allowed fields for the module.
	 *
	 * @return array
	 */
	public static function form_get_allowed_fields() {
		return array( 'input[type=email]', 'input[type=text]', 'input[type=url]', 'input[type=tel]', 'input[type=number]', 'input[type=date]', 'input[type=file]', 'select', 'textarea' );
	}

	/**
	 * Declare all prefixes for custom spacing fields for the module.
	 *
	 * @return array
	 */
	public static function form_get_custom_spacing_prefixes() {
		return array(
			'wrapper'         => array( 'label' => esc_html__( 'Wrapper', 'squad-modules-for-divi' ) ),
			'field'           => array( 'label' => esc_html__( 'Field', 'squad-modules-for-divi' ) ),
			'message_error'   => array( 'label' => esc_html__( 'Message', 'squad-modules-for-divi' ) ),
			'message_success' => array( 'label' => esc_html__( 'Message', 'squad-modules-for-divi' ) ),
		);
	}

	/**
	 * Collect all contact form from the database.
	 *
	 * @param string $form_type       The form type, available are cf7, fluetforms, ninjaforms, gravityforms, wpforms.
	 * @param string $collection The collection type of form data.
	 *
	 * @return array
	 */
	public static function form_get_all_items( $form_type, $collection = 'title' ) {
		$forms = array(
			self::$default_form_id => esc_html__( 'Select one', 'squad-modules-for-divi' ),
		);

		// Collect all forms from contact form 7.
		if ( 'cf7' === $form_type && 0 === count( self::$form_collections[ $form_type ][ $collection ] ) && class_exists( 'WPCF7' ) ) {
			$args = array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => - 1,
			);

			// Collect available contact form from the database.
			$forms = get_posts( $args );
			if ( count( $forms ) ) {
				/**
				 * Collect form iad and title based on conditions.
				 *
				 * @var WP_Post[] $forms
				 * @var WP_Post   $form
				 */
				foreach ( $forms as $form ) {
					self::$form_collections[ $form_type ][ $collection ][ md5( $form->ID ) ] = 'title' === $collection ? $form->post_title : $form->ID;
				}
			}
		}

		// Collect all forms from fluet forms.
		if ( 'fluetforms' === $form_type && count( self::$form_collections[ $form_type ][ $collection ] ) === 0 && function_exists( 'wpFluentForm' ) ) {
			// Collect available contact form from the database.
			$forms_table = \wpFluent()->table( 'fluentform_forms' );
			$collections = $forms_table->select( array( 'id', 'title' ) )->orderBy( 'id', 'DESC' )->get();

			/**
			 * Collect form iad and title based on conditions.
			 *
			 * @var array  $collections
			 * @var object $form
			 */
			foreach ( $collections as $form ) {
				self::$form_collections[ $form_type ][ $collection ][ md5( $form->id ) ] = 'title' === $collection ? $form->title : $form->id;
			}
		}

		// Collect all forms from gravity forms.
		if ( 'gravityforms' === $form_type && count( self::$form_collections[ $form_type ][ $collection ] ) === 0 && class_exists( '\GFCommon' ) && class_exists( '\RGFormsModel' ) ) {
			// Collect available contact form from the database.
			$forms = \RGFormsModel::get_forms( null, 'title' );
			if ( count( $forms ) ) {
				foreach ( $forms as $form ) {
					self::$form_collections[ $form_type ][ $collection ][ md5( $form->id ) ] = 'title' === $collection ? $form->title : $form->id;
				}
			}
		}

		// Collect all forms from ninja forms.
		if ( 'ninjaforms' === $form_type && count( self::$form_collections[ $form_type ][ $collection ] ) === 0 && function_exists( '\Ninja_Forms' ) ) {
			// Collect available wp form from a database.
			$ninja_forms = \Ninja_Forms()->form()->get_forms();
			if ( is_array( $ninja_forms ) && count( $ninja_forms ) ) {
				/**
				 * Collect form iad and title based on conditions.
				 *
				 * @var \NF_Abstracts_Model[] $ninja_forms
				 * @var \NF_Abstracts_Model   $form
				 */
				foreach ( $ninja_forms as $form ) {
					self::$form_collections[ $form_type ][ $collection ][ md5( $form->get_id() ) ] = 'title' === $collection ? $form->get_setting( 'title' ) : $form->get_id();
				}
			}
		}
		// Collect all forms from wp forms.
		if ( 'wpforms' === $form_type && count( self::$form_collections[ $form_type ][ $collection ] ) === 0 && function_exists( 'wpforms' ) ) {
			// Collect available wp form from a database.
			$args = array(
				'post_type'      => 'wpforms',
				'posts_per_page' => - 1,
			);

			// Collect available wp form from a database.
			$forms = get_posts( $args );
			if ( count( $forms ) ) {
				/**
				 * Collect form iad and title based on conditions.
				 *
				 * @var WP_Post[] $forms
				 * @var WP_Post   $form
				 */
				foreach ( $forms as $form ) {
					self::$form_collections[ $form_type ][ $collection ][ md5( $form->ID ) ] = 'title' === $collection ? $form->post_title : $form->ID;
				}
			}
		}

		return array_merge( $forms, self::$form_collections[ $form_type ][ $collection ] );
	}
}
