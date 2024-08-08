<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * The DiviBackend integration helper for Divi Builder
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Base\DiviBuilder;

use function _x;

/**
 * Builder DiviBackend Placeholder class.
 *
 * @since       1.0.0
 * @package     squad-modules-for-divi
 */
abstract class Placeholder {

	/**
	 *  Get The defaults data for module.
	 *
	 * @return array
	 */
	public function get_modules_defaults() {
		// Elegant themes icons : https://www.elegantthemes.com/blog/resources/elegant-icon-font.
		return array(
			'title'           => _x( 'Your Title Goes Here', 'Modules dummy content', 'squad-modules-for-divi' ),
			'subtitle'        => _x( 'Subtitle goes Here', 'Modules dummy content', 'squad-modules-for-divi' ),
			'body'            => _x(
				'<p>Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings -- Need to have p tag.
				'et_builder',
				'squad-modules-for-divi'
			),
			'number'          => 50,
			'button'          => _x( 'Click Here', 'Modules dummy content', 'squad-modules-for-divi' ),
			'button_two'      => _x( 'Learn More', 'Modules dummy content', 'squad-modules-for-divi' ),
			'custom_text'     => _x( 'Custom Text Here', 'Modules dummy content', 'squad-modules-for-divi' ),
			// SEO ISSUE: https://developer.chrome.com/docs/lighthouse/seo/link-text/.
			'read_more'       => _x( 'Read More', 'Modules dummy content', 'squad-modules-for-divi' ),
			'comments_before' => _x( 'Comments: ', 'Modules dummy content', 'squad-modules-for-divi' ),
			'icon'            => array(
				'check' => '&#x4e;||divi||400',
				'arrow' => '&#x24;||divi||400',
			),
			'image'           => array(
				'download_button' => "data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%237EBEC5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' %3e%3cpolyline points='8 17 12 21 16 17'%3e%3c/polyline%3e%3cline x1='12' y1='12' x2='12' y2='21'%3e%3c/line%3e%3cpath d='M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29'%3e%3c/path%3e%3c/svg%3e",
				'landscape'       => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==',
				'portrait'        => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDUwMCA1MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxwYXRoIGZpbGw9IiNFQkVCRUIiIGQ9Ik0wIDBoNTAwdjUwMEgweiIvPgogICAgICAgIDxyZWN0IGZpbGwtb3BhY2l0eT0iLjEiIGZpbGw9IiMwMDAiIHg9IjY4IiB5PSIzMDUiIHdpZHRoPSIzNjQiIGhlaWdodD0iNTY4IiByeD0iMTgyIi8+CiAgICAgICAgPGNpcmNsZSBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBjeD0iMjQ5IiBjeT0iMTcyIiByPSIxMDAiLz4KICAgIDwvZz4KPC9zdmc+Cg==',
				'vertical'        => 'data:image/svg+xml;base64,PHN2ZyBpZD0ibXlTdmdFbGVtZW50IiB3aWR0aD0iNTQwIiBoZWlnaHQ9IjU0MCIgdmlld0JveD0iMCAwIDU0MCA1NDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+Cgk8Zz4KCQk8cGF0aCBkPSJNMCwwaDU0MHY1NDBIMFYweiIgZmlsbD0iI0VCRUJFQiIvPgoJCTxwYXRoIGQ9Ik00NDUuNiw1NDBoLTk5bC0yMDItMjAyTDAsNDgyLjZ2LTk5bDExNi40LTExNi40YzE1LjYtMTUuNiw0MC45LTE1LjYsNTYuNiwwTDQ0NS42LDU0MEw0NDUuNiw1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KCQk8Y2lyY2xlIGN4PSIzMzEiIGN5PSIxNDgiIHI9IjcwIiBmaWxsLW9wYWNpdHk9Ii4wNSIgZmlsbD0iIzAwMCIvPgoJCTxwb2x5Z29uIHBvaW50cz0iNTQwLDIxNS4yIDIxNS4yLDU0MCAzMjguMyw1NDAgNTQwLDMyOC4zIiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KCTwvZz4KPC9zdmc+',
			),
			'video'           => 'https://www.youtube.com/watch?v=FkQuawiGWUw',
		);
	}

	/**
	 * Filters backend data passed to the Visual Builder.
	 * This function is used to add static helpers whose content rarely changes.
	 * eg: google fonts, module default, and so on.
	 *
	 * @param array $exists Exists definitions.
	 *
	 * @return array
	 */
	abstract public function static_asset_definitions( $exists = array() );

	/**
	 * Used to update the content of the cached definitions js file.
	 *
	 * @param string $content content.
	 *
	 * @return string
	 */
	abstract public function asset_definitions( $content );
}
