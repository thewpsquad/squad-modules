<?php
/**
 * Template file to the plugin review notice.
 *
 * @since           1.2.3
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}
?>

<div class="ext-copy-content" id="squad_ext_copy_content" style="display: none;">
	<fieldset class="options-container">
		<legend><?php echo esc_html__( 'Copy Options', 'squad-modules-for-divi' ); ?></legend>
		<div class="fieldset-container">
			<div class="align-left">
				<label>
					<input value="1" placeholder="1" min="1" minlength="1" maxlength="5" max="10000" type="number" name="copied-post-count">
				</label>
			</div>
			<div class="align-left">&nbsp;<?php echo esc_html__( 'time(s)', 'squad-modules-for-divi' ); ?></div>
			<div class="align-left">&nbsp;<?php echo esc_html__( 'to', 'squad-modules-for-divi' ); ?>&nbsp;</div>
			<div class="align-left">
				<label>

					<?php $divi_squad_current_site = get_current_blog_id(); ?>
					<?php $divi_squad_site_is_multi = is_multisite() !== true ? ' disabled' : ''; ?>

					<select name="copied-post-target-site" <?php echo esc_attr( $divi_squad_site_is_multi ); ?>>

						<option value="<?php echo esc_attr( $divi_squad_current_site ); ?>" data-type="dynamic">
							<?php esc_html_e( 'this site', 'squad-modules-for-divi' ); ?>
						</option>

						<?php if ( function_exists( 'is_multisite' ) && is_multisite() ) : ?>

							<?php $divi_squad_current_sites = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs ORDER BY blog_id" ); ?>

							<?php foreach ( $divi_squad_current_sites as $divi_squad_site ) : ?>
								<?php if ( $divi_squad_current_site !== (int) $divi_squad_site->blog_id ) : ?>

									<option value="<?php echo esc_attr( $divi_squad_site->blog_id ); ?>" data-type="dynamic">
										<?php echo esc_html( get_blog_option( $divi_squad_site->blog_id, 'blogname' ) ); ?>
									</option>

								<?php endif; ?>
							<?php endforeach; ?>

						<?php endif; ?>

					</select>
				</label>
			</div>
		</div>
	</fieldset>

	<button class="squad-admin-button fill-button" data-btn="copy-quick">
		<?php echo esc_html__( 'Copy now!', 'squad-modules-for-divi' ); ?>
	</button>
</div>

<div class="ext-copy-loader-overlay" style="opacity: 0">
	<div class="ext-copy-text-overlay">
		<h1><?php echo esc_html__( 'Please wait, copying in progress...', 'squad-modules-for-divi' ); ?></h1>
		<p><?php echo esc_html__( 'If you’re making a lot of copies, it can take a while <br>(up to 5 minutes if you’re on a slow server).', 'squad-modules-for-divi' ); ?></p>
		<span><?php echo esc_html__( 'Average time is 8 copies per second.', 'squad-modules-for-divi' ); ?></span>
	</div>
	<div class="ext-copy-spinner"></div>
</div>
