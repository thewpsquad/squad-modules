<?php
/**
 * Template file for the account.
 *
 * @since           2.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

global $submenu;

use DiviSquad\Utils\Polyfills\Str;

if ( ! ( defined( 'ABSPATH' ) && ! wp_doing_ajax() && isset( $args ) ) ) {
	die( 'Direct access forbidden.' );
}

// Check if the professional plugin is activated.
$divi_squad_is_pro_licensed = divi_squad_is_pro_activated() && divi_squad_fs()->can_use_premium_code();

// Verify current plugin version.
if ( $divi_squad_is_pro_licensed && function_exists( 'divi_squad_pro' ) ) {
	$divi_squad_version = divi_squad_pro()->get_version();
} else {
	$divi_squad_version = divi_squad()->get_version();
}

?>

<main id="squad-modules-app" class="squad-modules-app squad-components">
	<div class="app-wrapper">
		<div class="app-header">
			<div class="app-title">
				<div class="title-wrapper" data-badge-text="v<?php echo esc_attr( $divi_squad_version ); ?>">
					<svg width="400" height="400" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
						<circle cx="200" cy="200" r="200" fill="white"></circle>
						<path
							d="M146.428 305.69H146.118L103.028 296.7V99.54L103.338 99.85V99.54C112.225 96.6467 130.515 94.9933 158.208 94.58C185.901 94.1667 203.881 96.13 212.148 100.47C233.848 115.557 262.265 143.25 297.398 183.55C299.465 186.03 301.118 190.783 302.358 197.81C303.598 204.837 303.598 213.31 302.358 223.23C301.118 233.15 298.638 243.173 294.918 253.3C291.198 263.427 284.688 273.243 275.388 282.75C266.088 292.257 254.721 299.593 241.288 304.76C228.888 309.1 213.905 311.167 196.338 310.96C178.771 310.96 162.135 309.203 146.428 305.69ZM240.048 149.14C232.815 143.147 225.168 138.497 217.108 135.19C209.255 131.883 200.678 130.023 191.378 129.61C182.285 128.99 174.845 128.887 169.058 129.3C163.271 129.713 155.521 130.54 145.808 131.78L146.428 287.09C160.688 288.95 174.328 287.917 187.348 283.99C200.368 279.857 211.528 273.76 220.828 265.7C230.128 257.64 237.981 248.34 244.388 237.8C250.795 227.053 254.928 216.307 256.788 205.56C258.855 194.813 258.545 184.377 255.858 174.25C253.171 164.123 247.901 155.753 240.048 149.14Z"
							fill="#5E2EFF"></path>
					</svg>
					<h1 class="title">
						<?php if ( $divi_squad_is_pro_licensed ) : ?>
							<?php esc_html_e( 'Divi Squad Pro', 'squad-modules-for-divi' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Divi Squad', 'squad-modules-for-divi' ); ?>
						<?php endif; ?>
					</h1>
				</div>
			</div>
		</div>
		<div class="app-menu">
			<div class="app-menu-container">
				<div class="menu-list">
					<ul>
						<?php

						// Collect all registered menus from Menu Manager.
						$divi_squad_current_screent  = get_current_screen();
						$divi_squad_menu_register    = \DiviSquad\Base\Factories\AdminMenu::get_instance();
						$divi_squad_registered_menus = $divi_squad_menu_register->get_registered_submenus();

						?>
						<?php foreach ( $divi_squad_registered_menus as $divi_squad_registered_menu ) : ?>

							<?php list( $divi_squad_menu_name, , $divi_squad_menu_url ) = $divi_squad_registered_menu; ?>

							<?php if ( ! Str::contains( $divi_squad_menu_url, 'divi_squad_dashboard#' ) ) : ?>

								<?php $divi_squad_active_menu_class = ( "divi-squad_page_{$divi_squad_menu_url}" === $divi_squad_current_screent->id ) ? 'active' : ''; ?>
								<?php $divi_squad_menu_url = admin_url( "admin.php?page=$divi_squad_menu_url" ); ?>

								<li class="menu-item <?php echo esc_attr( $divi_squad_active_menu_class ); ?>">
									<a aria-current="page" class="<?php esc_attr( $divi_squad_active_menu_class ); ?>" href="<?php echo esc_url( $divi_squad_menu_url ); ?>">
										<?php echo wp_kses_post( $divi_squad_menu_name ); ?>
									</a>
								</li>

							<?php else : ?>

								<li class="menu-item">
									<a aria-current="page" href="<?php echo esc_url( $divi_squad_menu_url ); ?>">
										<?php echo esc_html( $divi_squad_menu_name ); ?>
									</a>
								</li>

							<?php endif; ?>

						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="wrapper-container">
			<div class="subscription-wrapper" style="display: none;">
				<?php echo $args; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</main>
