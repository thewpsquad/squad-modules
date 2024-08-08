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

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use DiviSquad\Base\Extensions;
use DiviSquad\Utils\Asset;
use DiviSquad\Utils\WP;
use function check_ajax_referer;
use function esc_html__;
use function get_blog_option;
use function get_current_blog_id;
use function get_post;
use function is_multisite;
use function wp_get_current_user;
use function wp_insert_post;

/**
 * The Post Duplicator class.
 *
 * @since       1.4.8
 * @package     squad-modules-for-divi
 * @author      WP Squad <support@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
class Copy extends Extensions {

	/**
	 * The constructor class.
	 */
	public function __construct() {
		parent::__construct();

		// Allow extra mime type file upload in the current installation.
		if ( ! in_array( 'Post_Duplicator', $this->name_lists, true ) ) {
			// Add CSS body class name for the available post or page.
			add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

			// Load the template at footer.
			add_action( 'admin_footer', array( $this, 'admin_footer_template' ) );

			// Enqueuing scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Add a duplicate link  in the array of row action links on the Posts list table.
			add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
			add_filter( 'page_row_actions', array( $this, 'row_actions' ), 10, 2 );
			add_filter( 'cuar/core/admin/content-list-table/row-actions', array( $this, 'row_actions' ), 10, 2 );

			// Add duplicate action in the bulk actions menu of the list table.
			add_filter( 'bulk_actions-edit-post', array( $this, 'bulk_actions' ) );
			add_filter( 'bulk_actions-edit-page', array( $this, 'bulk_actions' ) );
			add_filter( 'cuar/core/admin/container-list-table/bulk-actions?post_type=cuar_private_file', array( $this, 'bulk_actions' ) );
			add_filter( 'cuar/core/admin/container-list-table/bulk-actions?post_type=cuar_private_page', array( $this, 'bulk_actions' ) );

			add_action( 'wp_ajax_disq_ext_duplicate_post', array( $this, 'duplicate_the_post' ) );
		}
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
		$template_path = sprintf( '%1$s/templates/extension-copy-view.php', DISQ_DIR_PATH );
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
		global $wpdb;

		if ( $this->is_ignored_admin_screen() ) {
			return;
		}

		if ( current_user_can( 'edit_posts' ) ) {
			Asset::register_script( 'vendor-tooltipster', Asset::vendor_asset_path( 'tooltipster.bundle' ) );
			Asset::register_style( 'vendor-tooltipster', Asset::vendor_asset_path( 'tooltipster.bundle', array( 'ext' => 'css' ) ) );
			Asset::register_script( 'vendor-toast', Asset::vendor_asset_path( 'jquery.toast' ), array( 'jquery' ) );
			Asset::register_style( 'vendor-toast', Asset::vendor_asset_path( 'jquery.toast', array( 'ext' => 'css' ) ) );

			Asset::asset_enqueue( 'ext-copy', Asset::extension_asset_path( 'copy' ), array( 'disq-vendor-tooltipster', 'disq-vendor-toast' ) );
			Asset::style_enqueue( 'ext-copy', Asset::extension_asset_path( 'copy', array( 'ext' => 'css' ) ), array( 'disq-vendor-tooltipster', 'disq-vendor-toast' ) );
			Asset::asset_enqueue( 'ext-copy-bulk', Asset::extension_asset_path( 'copy-bulk' ), array( 'disq-vendor-toast' ) );
			Asset::style_enqueue( 'ext-copy-bulk', Asset::extension_asset_path( 'copy-bulk', array( 'ext' => 'css' ) ), array( 'disq-vendor-toast' ) );

			$select_options = array();
			if ( is_multisite() ) :
				$sites = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs ORDER BY blog_id" );
				foreach ( $sites as $site ) :
					$select_options[ $site->blog_id ] = get_blog_option( $site->blog_id, 'blogname' );
				endforeach;
			endif;

			// Load localize scripts.
			WP::localize_script(
				'disq-ext-copy',
				'DiviSquadExtCopy',
				array(
					'isMultisite'   => is_multisite(),
					'currentSiteID' => get_current_blog_id(),
					'selectOptions' => $select_options,
					'ajaxURL'       => admin_url( 'admin-ajax.php' ),
					'ajaxNonce'     => wp_create_nonce( '_wpnonce' ),
					'ajaxAction'    => 'disq_ext_duplicate_post',
					'l10n'          => array(
						'copy'        => esc_html__( 'Copy', 'squad-modules-for-divi' ),
						'toast_title' => esc_html__( 'Squad Copy Extension', 'squad-modules-for-divi' ),
						'empty_post'  => esc_html__( 'Kindly choose a minimum of one row for copying.', 'squad-modules-for-divi' ),
						'unknown_er'  => esc_html__( 'Something went wrong!', 'squad-modules-for-divi' ),
					),
				)
			);
		}
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
	public function duplicate_the_post() {
		// Get access to the database.
		global $wpdb;

		// Nonce verification.
		check_ajax_referer( '_wpnonce' );

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
						$taxonomies = \get_object_taxonomies( $post->post_type );
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
