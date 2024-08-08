<?php
/**
 * Template file for the account.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   2.0.0
 *
 * @var array|string $args Arguments passed to the template.
 */

if ( ! ( defined( 'ABSPATH' ) && is_string( $args ) ) ) {
	die( 'Direct access forbidden.' );
}

if ( wp_doing_ajax() ) {
	die( 'Access forbidden from AJAX request.' );
}

global $submenu;

use DiviSquad\Utils\Media\Image;
use DiviSquad\Utils\Polyfills\Str;

// Load the image class.
$divi_squad_image = new Image( divi_squad()->get_path( '/build/admin/images/logos' ) );

// Check if image is validated.
if ( is_wp_error( $divi_squad_image->is_path_validated() ) ) {
	return;
}

// Verify current plugin version.
if ( ( divi_squad()->is_pro_activated() && divi_squad()->publisher()->can_use_premium_code() ) && false !== strpos( divi_squad_pro()->get_version(), '.' ) ) {
	$divi_squad_pro_version = divi_squad_pro()->get_version();
	$divi_squad_version     = "v$divi_squad_pro_version";
} elseif ( ! divi_squad()->is_pro_activated() && ( false !== strpos( divi_squad()->get_version(), '.' ) ) ) {
	$divi_squad_core_version = divi_squad()->get_version();
		$divi_squad_version  = "v$divi_squad_core_version";
} else {
	$divi_squad_version = esc_html__( 'Nightly', 'squad-modules-for-divi' );
}

?>

<main id="squad-modules-app" class="squad-modules-app squad-components">
	<div class="app-wrapper">
		<div class="app-header">
			<div class="app-title">
				<div class="title-wrapper" data-badge-text="<?php echo esc_attr( $divi_squad_version ); ?>">

					<?php $divi_squad_subscription_logo = $divi_squad_image->get_image( 'divi-squad-default.png', 'png' ); ?>
					<?php if ( ! is_wp_error( $divi_squad_subscription_logo ) ) : ?>
						<img class='logo' alt='Divi Squad' src="<?php echo esc_url( $divi_squad_subscription_logo, array( 'data' ) ); ?>"/>
					<?php endif; ?>

					<h1 class="title">
						<?php if ( divi_squad()->is_pro_activated() && divi_squad()->publisher()->can_use_premium_code() ) : ?>
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
