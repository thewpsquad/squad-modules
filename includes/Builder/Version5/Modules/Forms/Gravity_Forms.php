<?php // phpcs:ignore WordPress.Files.FileName
declare( strict_types=1 );

/**
 * Gravity Forms Styler Module (Divi 5 / Block API).
 *
 * Native Divi 5 form-styler for Gravity Forms. Embeds the selected Gravity form via the
 * `gravity_form()` function and lets Divi style the form, fields, labels, placeholder,
 * checkbox/radio, submit button and messages through the declarative style groups in
 * `module.json`.
 *
 * @since   3.4.0
 * @package DiviSquad
 * @author  The WP Squad <support@squadmodules.com>
 */

namespace DiviSquad\Builder\Version5\Modules\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

if ( ! class_exists( 'ET\Builder\Packages\Module\Module' ) ) {
	return;
}

use DiviSquad\Builder\Version5\Abstracts\Form_Styler;
use function gravity_form;

/**
 * Gravity Forms Styler module class.
 *
 * @since 3.4.0
 */
class Gravity_Forms extends Form_Styler {

	/**
	 * Relative path to the generated module.json metadata folder.
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	protected static function get_metadata_folder_path(): string {
		return '/build/divi-builder-5/modules-json/gravity-forms/';
	}

	/**
	 * Root CSS classname applied to the Gravity Forms styler wrapper.
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	protected static function get_root_classname(): string {
		return 'disq_form_styler_gravity_forms';
	}

	/**
	 * The Squad forms-element type key.
	 *
	 * @since 3.4.0
	 *
	 * @return string
	 */
	protected static function get_form_type(): string {
		return 'gravity_forms';
	}

	/**
	 * Whether Gravity Forms is active.
	 *
	 * @since 3.4.0
	 *
	 * @return bool
	 */
	protected static function is_form_plugin_active(): bool {
		return function_exists( 'gravity_form' );
	}

	/**
	 * Render the Gravity Forms form HTML for the given form id.
	 *
	 * Gravity Forms is embedded through its `gravity_form()` function rather than a
	 * shortcode. Output is buffered so the returned markup can be wrapped by the module.
	 *
	 * @since 3.4.0
	 *
	 * @param string               $form_id Raw Gravity Forms form id.
	 * @param array<string, mixed> $inner   Form-group inner-content values.
	 *
	 * @return string
	 */
	protected static function get_form_html( string $form_id, array $inner ): string {
		if ( ! function_exists( 'gravity_form' ) ) {
			return '';
		}

		$display_title       = 'on' === ( $inner['formTitle'] ?? 'off' );
		$display_description = 'on' === ( $inner['formDescription'] ?? 'off' );
		$use_ajax            = 'on' === ( $inner['formAjax'] ?? 'off' );

		/*
		 * The eighth argument is `$echo`. Passing false makes gravity_form() return the
		 * markup through GFForms::get_form() instead of printing it, so no output buffer
		 * is needed. This module previously printed the form and captured it with
		 * ob_start()/ob_get_clean(), which is the only place any form styler buffered
		 * output, and it broke the Visual Builder.
		 *
		 * Divi 5 renders modules inside a REST request. If gravity_form() threw — a
		 * missing or corrupt form, a fatal in an add-on's field rendering — the buffer
		 * opened by ob_start() was never closed, because the throw unwound past
		 * ob_get_clean() into the catch in Form_Styler::render_callback(). That catch
		 * logged the error and returned an empty string, so the module looked handled
		 * while an unbalanced buffer was still open. Whatever Gravity Forms had already
		 * printed then leaked into the REST response body, the builder could not parse
		 * the JSON, and it showed "Oops! An Error Has Occurred. This content could not
		 * be displayed."
		 *
		 * Returning the markup removes the buffer, so a throw inside Gravity Forms is
		 * now caught and logged without corrupting the response. The Divi 4 module has
		 * always passed false here; this brings Divi 5 in line with it.
		 *
		 * @see gravityforms.php gravity_form() — `if ( ! $echo ) { return GFForms::get_form( … ); }`
		 */
		return (string) gravity_form( $form_id, $display_title, $display_description, false, null, $use_ajax, 0, false );
	}
}
