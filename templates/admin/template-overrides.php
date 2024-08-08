<?php
/**
 * Template file
 *
 * @since           2.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

if ( ! ( defined( 'ABSPATH' ) && ! wp_doing_ajax() && isset( $args ) ) ) {
	die( 'Direct access forbidden.' );
}

?>

<main id="squad-generic-pages" class="squad-components">
	<?php echo $args; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</main>
