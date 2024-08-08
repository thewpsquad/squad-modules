<?php
/**
 * Template file to the beta campaign for pro-plugin notice.
 *
 * @since           2.0.0
 * @package         squad-modules-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}
?>

<div class="notice divi-squad-banner divi-squad-success-banner pro-activation-notice is-dismissible">
	<div class="divi-squad-banner-logo">
		<svg width="400" height="400" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
			<circle cx="200" cy="200" r="195" stroke="#5E2EFF" stroke-width="10"/>
			<path
				d="M147.156 299.7H146.856L105.156 291V100.2L105.456 100.5V100.2C114.056 97.4 131.756 95.8 158.556 95.4C185.356 95 202.756 96.9 210.756 101.1C231.756 115.7 259.256 142.5 293.256 181.5C295.256 183.9 296.856 188.5 298.056 195.3C299.256 202.1 299.256 210.3 298.056 219.9C296.856 229.5 294.456 239.2 290.856 249C287.256 258.8 280.956 268.3 271.956 277.5C262.956 286.7 251.956 293.8 238.956 298.8C226.956 303 212.456 305 195.456 304.8C178.456 304.8 162.356 303.1 147.156 299.7ZM237.756 148.2C230.756 142.4 223.356 137.9 215.556 134.7C207.956 131.5 199.656 129.7 190.656 129.3C181.856 128.7 174.656 128.6 169.056 129C163.456 129.4 155.956 130.2 146.556 131.4L147.156 281.7C160.956 283.5 174.156 282.5 186.756 278.7C199.356 274.7 210.156 268.8 219.156 261C228.156 253.2 235.756 244.2 241.956 234C248.156 223.6 252.156 213.2 253.956 202.8C255.956 192.4 255.656 182.3 253.056 172.5C250.456 162.7 245.356 154.6 237.756 148.2Z"
				fill="#5E2EFF"/>
		</svg>
	</div>
	<div class="divi-squad-banner-content">
		<p>
		<?php

			printf(
				/* translators: 1. Welcome Message, 2. Coupon Code */
				esc_html__( '%1$s Get a special discount and start building stunning websites today. Use code "%2$s" at checkout.' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'Unleash Your Divi Creativity with Squad Modules Pro!', 'squad-modules-for-divi' ) ),
				'<code>WELCOME60</code>'
			);

			?>
		</p>
	</div>
</div>
