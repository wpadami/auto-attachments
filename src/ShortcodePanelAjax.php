<?php
/**
 * AJAX endpoints backing the classic-editor shortcode panel.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the two AJAX actions the shortcode panel (ShortcodePanel) uses:
 * saving which attachments a post's automatic listing should exclude (once
 * a shortcode has been inserted for them), and listing a post's
 * attachments of a given MIME type for the panel's selectboxes.
 */
class ShortcodePanelAjax {

	const NONCE_ACTION = 'ajax-nonce';

	/**
	 * Maps the panel's "durum" (kind) value to the post-meta key used to
	 * exclude those attachments from the automatic listing.
	 */
	const EXCLUDE_META_KEYS = array(
		'resim' => 'ex_rsm',
		'muzik' => 'ex_muz',
		'video' => 'ex_vid',
		'dosya' => 'ex_dosya',
	);

	/**
	 * Hook both AJAX actions.
	 */
	public function register_hooks(): void {
		add_action( 'wp_ajax_ex_aa', array( $this, 'save_exclude' ) );
		add_action( 'wp_ajax_get_imgs', array( $this, 'list_attachments' ) );
	}

	/**
	 * `wp_ajax_ex_aa` callback: persist which attachment IDs to exclude
	 * from a post's automatic listing, for one attachment kind.
	 */
	public function save_exclude(): void {
		$nonce     = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$post_meta = isset( $_POST['post_meta'] ) ? sanitize_text_field( wp_unslash( $_POST['post_meta'] ) ) : '';
		$durum     = isset( $_POST['durum'] ) ? sanitize_key( $_POST['durum'] ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'Busted!' );
		}

		if ( isset( self::EXCLUDE_META_KEYS[ $durum ] ) ) {
			update_post_meta( $post_id, self::EXCLUDE_META_KEYS[ $durum ], $post_meta );
		}

		wp_die();
	}

	/**
	 * `wp_ajax_get_imgs` callback: list a post's attachments of a given
	 * MIME type (or MIME type prefix, e.g. "application") as
	 * { id, post_name } pairs.
	 */
	public function list_attachments(): void {
		$nonce    = isset( $_GET['nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
		$post_id  = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
		$mimetype = isset( $_GET['postmim'] ) ? sanitize_text_field( wp_unslash( $_GET['postmim'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json( array() );
		}

		$attachments = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_parent'    => $post_id,
				'post_mime_type' => $mimetype,
				'numberposts'    => -1,
			)
		);

		if ( empty( $attachments ) ) {
			wp_send_json(
				array(
					array(
						'id'        => '-',
						'post_name' => 'Nope',
					),
				)
			);
		}

		wp_send_json(
			array_map(
				function ( $attachment ) {
					return array(
						'id'        => $attachment->ID,
						'post_name' => $attachment->post_name,
					);
				},
				$attachments
			)
		);
	}
}
