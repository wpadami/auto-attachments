<?php
/**
 * Auto-attaches media referenced in a post's content but left unattached.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * WordPress doesn't reliably set an attachment's post_parent on upload -
 * the block editor's Image/File/Audio/Video blocks frequently leave media
 * "unattached" (post_parent = 0) even though it's visibly embedded in the
 * post. Since AttachmentRepository (used by both the automatic listing and
 * the shortcode panel) finds a post's attachments by post_parent, those
 * uploads would otherwise never show up.
 *
 * On save, this scans the saved content for attachment references and
 * reparents any that are still unattached to this post. It never moves an
 * attachment that's already parented elsewhere - only orphaned media is
 * claimed.
 */
class AttachmentAttacher {

	/**
	 * Hook the save_post scan.
	 */
	public function register_hooks(): void {
		add_action( 'save_post', array( $this, 'attach_referenced_media' ) );
	}

	/**
	 * Scan a saved post's content and attach any unattached media it
	 * references.
	 *
	 * @param int $post_id Post being saved.
	 */
	public function attach_referenced_media( int $post_id ): void {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );
		if ( ! $post instanceof \WP_Post || '' === $post->post_content ) {
			return;
		}

		foreach ( $this->referenced_attachment_ids( $post->post_content ) as $attachment_id ) {
			$this->maybe_attach( $attachment_id, $post_id );
		}
	}

	/**
	 * Attach $attachment_id to $post_id, but only if it's currently
	 * unattached - never steal one that already belongs elsewhere.
	 *
	 * @param int $attachment_id Candidate attachment.
	 * @param int $post_id       Post to attach it to.
	 */
	private function maybe_attach( int $attachment_id, int $post_id ): void {
		$attachment = get_post( $attachment_id );
		if ( ! $attachment instanceof \WP_Post || 'attachment' !== $attachment->post_type ) {
			return;
		}
		if ( 0 !== (int) $attachment->post_parent ) {
			return;
		}

		wp_update_post(
			array(
				'ID'          => $attachment_id,
				'post_parent' => $post_id,
			)
		);
	}

	/**
	 * Resolve attachment IDs referenced in rendered content: the
	 * `wp-image-{ID}` class WordPress adds to every inserted `<img>`
	 * (block editor and classic editor alike), plus any `href`/`src` URL
	 * that resolves to an attachment (file/audio/video blocks).
	 *
	 * @param string $content Post content.
	 * @return int[]
	 */
	private function referenced_attachment_ids( string $content ): array {
		$ids = array();

		if ( preg_match_all( '/wp-image-(\d+)/', $content, $matches ) ) {
			$ids = array_map( 'absint', $matches[1] );
		}

		if ( preg_match_all( '/(?:href|src)=["\']([^"\']+)["\']/', $content, $matches ) ) {
			foreach ( $matches[1] as $url ) {
				$id = attachment_url_to_postid( $url );
				if ( $id ) {
					$ids[] = $id;
				}
			}
		}

		return array_values( array_unique( array_filter( $ids ) ) );
	}
}
