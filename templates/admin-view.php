<?php
/**
 * Template file to manage layouts.
 *
 * @since           1.0.0
 * @package         squad-modules-pro-for-divi
 * @author          WP Squad <support@thewpsquad.com>
 * @license         GPL-3.0-only
 */

defined( 'ABSPATH' ) || die();
?>

<style>
	#squad-modules-app {
		z-index: -1;
	}
	/**===== Squad Modules App (Preloader) =====*/
	#squad-modules-app-loader.square {
		display: block;
		position: absolute;
		top: 50%;
		left: 50%;
		height: 50px;
		width: 50px;
		margin: -25px 0 0 -25px;
	}

	#squad-modules-app-loader.square span {
		width: 16px;
		height: 16px;
		background-color: #5E2EFF;
		display: inline-block;
		-webkit-animation: app-loader-square 1.7s infinite ease-in-out both;
		animation: app-loader-square 1.7s infinite ease-in-out both;
	}

	#squad-modules-app-loader.square span:nth-child(1) {
		left: 0;
		-webkit-animation-delay: 0.1s;
		animation-delay: 0.1s;
	}

	#squad-modules-app-loader.square span:nth-child(2) {
		left: 15px;
		-webkit-animation-delay: 0.6s;
		animation-delay: 0.6s;
	}

	#squad-modules-app-loader.square span:nth-child(3) {
		left: 30px;
		-webkit-animation-delay: 1.1s;
		animation-delay: 1.1s;
	}

	#squad-modules-app-loader.square span:nth-child(4) {
		left: 45px;
		-webkit-animation-delay: 1.5s;
		animation-delay: 1.5s;
	}

	@keyframes app-loader-square {
		0% {
			-webkit-transform: scale(0);
			transform: scale(0);
			opacity: 0;
		}
		50% {
			-webkit-transform: scale(1);
			transform: scale(1);
			opacity: 1;
		}
		100% {
			-webkit-transform: rotate(60deg);
			transform: rotate(60deg);
			opacity: .5;
		}
	}
	@-webkit-keyframes app-loader-square {
		0% {
			-webkit-transform: scale(0);
			transform: scale(0);
			opacity: 0;
		}
		50% {
			-webkit-transform: scale(1);
			transform: scale(1);
			opacity: 1;
		}
		100% {
			-webkit-transform: rotate(60deg);
			transform: rotate(60deg);
			opacity: .5;
		}
	}
	/** END of square */
</style>

<div id="squad-modules-app-loader" class="square"> <span></span> <span></span> <span></span> <span></span> </div>
<section id="squad-modules-app"></section>
