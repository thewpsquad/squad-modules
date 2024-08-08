<?php
/**
 * Template file to the plugin review notice.
 *
 * @since           1.2.3
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

defined( 'ABSPATH' ) || die();
?>

<div class="notice divi-squad-banner is-dismissible">
	<div class="divi-squad-banner-logo">
		<svg width="400" height="400" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
			<circle cx="200" cy="200" r="195" stroke="#5E2EFF" stroke-width="10"/>
			<path
				d="M147.156 299.7H146.856L105.156 291V100.2L105.456 100.5V100.2C114.056 97.4 131.756 95.8 158.556 95.4C185.356 95 202.756 96.9 210.756 101.1C231.756 115.7 259.256 142.5 293.256 181.5C295.256 183.9 296.856 188.5 298.056 195.3C299.256 202.1 299.256 210.3 298.056 219.9C296.856 229.5 294.456 239.2 290.856 249C287.256 258.8 280.956 268.3 271.956 277.5C262.956 286.7 251.956 293.8 238.956 298.8C226.956 303 212.456 305 195.456 304.8C178.456 304.8 162.356 303.1 147.156 299.7ZM237.756 148.2C230.756 142.4 223.356 137.9 215.556 134.7C207.956 131.5 199.656 129.7 190.656 129.3C181.856 128.7 174.656 128.6 169.056 129C163.456 129.4 155.956 130.2 146.556 131.4L147.156 281.7C160.956 283.5 174.156 282.5 186.756 278.7C199.356 274.7 210.156 268.8 219.156 261C228.156 253.2 235.756 244.2 241.956 234C248.156 223.6 252.156 213.2 253.956 202.8C255.956 192.4 255.656 182.3 253.056 172.5C250.456 162.7 245.356 154.6 237.756 148.2Z"
				fill="#5E2EFF"/>
		</svg>
	</div>
	<div class="divi-squad-banner-content">
		<h2><?php esc_html_e( 'Ready to take Squad Modules Lite to the next level?', 'squad-modules-for-divi' ); ?></h2>
		<p><?php esc_html_e( "Hey there! Long time no Squad Modules Lite, eh? We hope it's been making your website development journey a breeze! If you have a spare minute, your feedback would be like gold dust to us. Leave a rating and let us know what you think! ✨", 'squad-modules-for-divi' ); ?></p>
		<div class="divi-squad-notice-action">
			<div class="divi-squad-notice-action-left">
				<a href="https://wordpress.org/support/plugin/squad-modules-for-divi/reviews/?rate=5#new-post" target="_blank" class="divi-squad-notice-action-button">
					<?php esc_html_e( 'Shine Bright ★★★★★', 'squad-modules-for-divi' ); ?>
				</a>
				<a href="#" class="divi-squad-notice-close"><?php esc_html_e( '5-Star Reminder!', 'squad-modules-for-divi' ); ?></a>
				<a href="#" class="divi-squad-notice-already"><?php esc_html_e( 'No, Thanks', 'squad-modules-for-divi' ); ?></a>
			</div>
			<div class="divi-squad-notice-action-right">
				<a href='https://wordpress.org/support/plugin/squad-modules-for-divi/' target="_blank" class="support">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
						<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
						<path
							d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
					</svg>
					<?php esc_html_e( 'Stuck here - any advice?', 'squad-modules-for-divi' ); ?>
				</a>
			</div>
		</div>
	</div>
</div>
