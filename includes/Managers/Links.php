<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The plugin link management class for the plugin dashboard at admin area.
 *
 * @package DiviSquad
 * @author  WP Squad <support@squadmodules.com>
 * @since   3.0.0
 */

namespace DiviSquad\Managers;

/**
 * Link Class
 *
 * @package DiviSquad
 * @since   3.0.0
 */
class Links {
	/**
	 * The plugin home URL.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const HOME_URL = 'https://squadmodules.com/';

	/**
	 * The plugin support URL.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const PRICING_URL = 'https://squadmodules.com/pricing/';

	/**
	 * The plugin issues URL.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const ISSUES_URL = 'https://github.com/thewpsquad/squad-modules/issues';

	/**
	 * The plugin URL from WP.org.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const WP_ORG_URL = 'http://wordpress.org/plugins/squad-modules-for-divi/';

	/**
	 * The plugin support URL.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const SUPPORT_URL = 'https://wordpress.org/support/plugin/squad-modules-for-divi/#postform';

	/**
	 * The plugin ratting URL.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const RATTING_URL = 'https://wordpress.org/support/plugin/squad-modules-for-divi/reviews/?rate=5#new-post';

	/**
	 * The plugin translate URL.
	 *
	 * @var string
	 * @since 3.0.0
	 */
	const TRANSLATE_URL = 'https://translate.wordpress.org/projects/wp-plugins/squad-modules-for-divi';
}
