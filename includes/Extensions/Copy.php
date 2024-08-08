<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * The Post Duplicator extension class for Divi Squad.
 *
 * @since       1.4.8
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace DiviSquad\Extensions;

use DiviSquad\Base\Extension;
use DiviSquad\Utils\Asset;
use DiviSquad\Utils\WP;
use function add_action;
use function add_filter;
use function check_ajax_referer;
use function current_user_can;
use function esc_attr;
use function esc_attr__;
use function esc_html__;
use function get_current_blog_id;
use function get_current_screen;
use function get_object_taxonomies;
use function get_post;
use function is_multisite;
use function load_template;
use function sanitize_post;
use function switch_to_blog;
use function wp_create_nonce;
use function wp_get_current_user;
use function wp_get_object_terms;
use function wp_insert_post;
use function wp_send_json;
use function wp_set_object_terms;

/**
 * The Post Duplicator class.
 *
 * @since       1.4.8
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Copy extends Extension {

	/**
	 * Get the extension name.
	 *
	 * @return string
	 */
	protected function get_name() {
		return 'Post_Duplicator';
	}

	/**
	 * Load the extension.
	 *
	 * @return void
	 */
	protected function load() {
		// Add CSS body class name for the available post or page.
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

		// Load the template at footer.
		add_action( 'admin_footer', array( $this, 'admin_footer_template' ) );

		// Enqueuing scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add a duplicate link in the array of row action links on the Posts list table.
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_filter( 'cuar/core/admin/content-list-table/row-actions', array( $this, 'row_actions' ), 10, 2 );

		// Add duplicate action in the bulk actions menu of the list table.
		add_filter( 'bulk_actions-edit-post', array( $this, 'bulk_actions' ) );
		add_filter( 'bulk_actions-edit-page', array( $this, 'bulk_actions' ) );
		add_filter( 'cuar/core/admin/container-list-table/bulk-actions?post_type=cuar_private_file', array( $this, 'bulk_actions' ) );
		add_filter( 'cuar/core/admin/container-list-table/bulk-actions?post_type=cuar_private_page', array( $this, 'bulk_actions' ) );
	}

	/**
	 * Verify allowed screen.
	 *
	 * @return bool
	 */
	public function is_ignored_admin_screen() {
		global $pagenow;

		$screen  = get_current_screen();
		$allowed = array( 'post', 'edit-post', 'edit-page', 'page' );

		return ( ! $screen || ! in_array( $screen->id, $allowed, true ) ) && ! ( 'edit.php' === $pagenow || 'post.php' === $pagenow );
	}

	/**
	 * Add CSS body class name for the available post or page.
	 *
	 * @param string $classes An array of body class names.
	 *
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		if ( $this->is_ignored_admin_screen() ) {
			return $classes;
		}

		// Update body classes.
		$classes .= ' divi-squad-ext-copy-this';

		return $classes;
	}

	/**
	 * Enqueuing scripts for all admin pages.
	 *
	 * @return void
	 */
	public function admin_footer_template() {
		if ( $this->is_ignored_admin_screen() ) {
			return;
		}

		// The template path.
		$template_path = sprintf( '%1$s/extensions/copy.php', DIVI_SQUAD_TEMPLATES_PATH );
		if ( current_user_can( 'edit_posts' ) && file_exists( $template_path ) ) {
			load_template( $template_path );
		}
	}

	/**
	 * Enqueuing scripts for all admin pages.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		if ( $this->is_ignored_admin_screen() ) {
			return;
		}

		if ( current_user_can( 'edit_posts' ) ) {
			Asset::register_script( 'vendor-tooltipster', Asset::vendor_asset_path( 'tooltipster.bundle' ) );
			Asset::register_style( 'vendor-tooltipster', Asset::vendor_asset_path( 'tooltipster.bundle', array( 'ext' => 'css' ) ) );
			Asset::register_script( 'vendor-toast', Asset::vendor_asset_path( 'jquery.toast' ), array( 'jquery' ) );
			Asset::register_style( 'vendor-toast', Asset::vendor_asset_path( 'jquery.toast', array( 'ext' => 'css' ) ) );

			Asset::asset_enqueue( 'ext-copy', Asset::extension_asset_path( 'copy' ), array( 'lodash', 'jquery', 'squad-vendor-tooltipster', 'squad-vendor-toast' ) );
			Asset::style_enqueue( 'ext-copy', Asset::extension_asset_path( 'copy', array( 'ext' => 'css' ) ), array( 'squad-vendor-tooltipster', 'squad-vendor-toast' ) );
			Asset::asset_enqueue( 'ext-copy-bulk', Asset::extension_asset_path( 'copy-bulk' ), array( 'lodash', 'jquery', 'squad-vendor-toast' ) );
			Asset::style_enqueue( 'ext-copy-bulk', Asset::extension_asset_path( 'copy-bulk', array( 'ext' => 'css' ) ), array( 'squad-vendor-toast' ) );

			// Load localize data.
			add_filter( 'divi_squad_assets_backend_extra_data', array( $this, 'wp_localize_script_data' ) );

			// Load script translations.
			WP::set_script_translations( 'squad-ext-copy', divi_squad()->get_name() );
		}
	}

	/**
	 * Set localize data for admin area.
	 *
	 * @param array $exists_data Exists extra data.
	 *
	 * @return array
	 */
	public function wp_localize_script_data( $exists_data ) {
		// Localize data.
		$admin_localize = array(
			'ajax_url'         => admin_url( 'admin-ajax.php' ),
			'ajax_nonce_copy'  => wp_create_nonce( 'divi_squad_ext_duplicate_post' ),
			'ajax_action_copy' => 'divi_squad_ext_duplicate_post',
			'l10n'             => array(
				'copy'             => esc_html__( 'Copy', 'squad-modules-for-divi' ),
				'copy_toast_title' => esc_html__( 'Squad Copy Extension', 'squad-modules-for-divi' ),
				'copy_empty_post'  => esc_html__( 'Kindly choose a minimum of one row for copying.', 'squad-modules-for-divi' ),
				'unknown_er'       => esc_html__( 'Something went wrong!', 'squad-modules-for-divi' ),
			),
		);

		return array_merge( $exists_data, $admin_localize );
	}

	/**
	 * Add the duplicate link to post, page, and custom actions
	 *
	 * @param string[] $actions An array of row action links. Defaults are 'Edit', 'Quick Edit', 'Restore', 'Trash', 'Delete Permanently', 'Preview', and 'View'.
	 * @param \WP_Post $post    The post-object.
	 *
	 * @return string[] An array of row action links.
	 */
	public function row_actions( $actions, $post ) {
		if ( current_user_can( 'edit_posts' ) && 'trash' !== $post->post_status ) {
			$actions['copy_this'] = sprintf(
				'<a class="copy-this-post-link" href="#squad_ext_copy" title="%1$s" data-id="%3$s" rel="permalink">%2$s</a>',
				esc_attr__( 'Copy this', 'squad-modules-for-divi' ),
				esc_html__( 'Copy', 'squad-modules-for-divi' ),
				esc_attr( $post->ID )
			);
		}

		return $actions;
	}

	/**
	 * Add duplicate action in the bulk actions menu of the list table.
	 *
	 * @param array $actions An array of the available bulk actions.
	 *
	 * @return string[] An array of bulk actions.
	 */
	public function bulk_actions( $actions ) {
		if ( current_user_can( 'edit_posts' ) ) {
			$actions['copy_selected'] = esc_html__( 'Copy ', 'squad-modules-for-divi' );
		}

		return $actions;
	}

	/**
	 * Duplicate the post.
	 *
	 * @return void
	 */
	public static function duplicate_the_post() {
		// Get access to the database.
		global $wpdb;

		// Nonce verification.
		check_ajax_referer( 'divi_squad_ext_duplicate_post', '_wpnonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json(
				array(
					'type'    => 'error',
					'message' => 'Permission denied.',
				)
			);
		}

		// Collect current post.
		$post_ids   = (array) $_POST['copyQueryOptions']['postID'];
		$post_count = intval( $_POST['copyQueryOptions']['postCount'] );
		$blog_id    = intval( $_POST['copyQueryOptions']['siteID'] );
		$current_id = get_current_blog_id();

		// Collect post-author info.
		$user = wp_get_current_user();

		if ( count( $post_ids ) !== 0 ) {
			$is_copy_done = false;

			foreach ( $post_ids as $post_id ) {
				$post = sanitize_post( get_post( $post_id ), 'db' );
				if ( isset( $post ) ) {

					// Switch to target blog site when multisite available.
					if ( is_multisite() && $blog_id !== $current_id ) {
						switch_to_blog( $blog_id );
					}

					for ( $interval = 1; $interval <= $post_count; $interval++ ) {
						$args = array(
							'post_status'    => 'draft',
							'comment_status' => $post->comment_status,
							'ping_status'    => $post->ping_status,
							'post_author'    => $user->ID,
							'post_content'   => $post->post_content,
							'post_excerpt'   => $post->post_excerpt,
							'post_name'      => $post->post_name,
							'post_parent'    => $post->post_parent,
							'post_password'  => $post->post_password,
							'post_title'     => 1 === $post_count ? $post->post_title : "$post->post_title #$interval",
							'post_type'      => $post->post_type,
							'to_ping'        => $post->to_ping,
							'menu_order'     => $post->menu_order,
						);

						$new_post_id = wp_insert_post( $args );

						// get all current post-terms ad set them to the new post-draft.
						$taxonomies = get_object_taxonomies( $post->post_type );
						foreach ( $taxonomies as $taxonomy ) {
							$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
							wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
						}

						// Duplicate all post meta just in two SQL queries.
						$post_meta_infos = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT meta_key, meta_value FROM `$wpdb->postmeta` WHERE `post_id` = %s",
								array( $post_id )
							)
						);
						if ( count( $post_meta_infos ) !== 0 ) {
							$sql_query_sel = array();
							$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
							foreach ( $post_meta_infos as $meta_info ) {
								$meta_key = $meta_info->meta_key;

								if ( '_wp_old_slug' === $meta_key ) {
									continue;
								}

								$meta_value      = $wpdb->prepare( '%s', $meta_info->meta_value );
								$sql_query_sel[] = $wpdb->prepare( 'SELECT %d, %s, %s', $new_post_id, $meta_key, $meta_value );
							}
							$sql_query .= implode( ' UNION ALL ', $sql_query_sel );
							$wpdb->query( $sql_query ); // phpcs:ignore
						}
					}

					// Rollback to current blog site when multisite available.
					if ( is_multisite() && $blog_id !== $current_id ) {
						switch_to_blog( $current_id );
					}

					$is_copy_done = true;
				} else {
					$is_copy_done = false;
					break;
				}
			}

			if ( $is_copy_done ) {
				wp_send_json(
					array(
						'type'    => 'success',
						'message' => 'Post duplication completed successfully. All duplicated posts are now saved as drafts for your review.',
					)
				);
			} else {
				wp_send_json(
					array(
						'type'    => 'error',
						'message' => 'Post creation encountered an error, and one or more posts could not be successfully duplicated.',
					)
				);
			}
		} else {
			wp_send_json(
				array(
					'type'    => 'error',
					'message' => esc_html__( 'Kindly choose a minimum of one post for copying.', 'squad-modules-for-divi' ),
				)
			);
		}
	}
}

new Copy();
