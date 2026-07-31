<?php
/**
 * Per-page "show Auto Attachments here?" meta box.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a checkbox meta box to the Page editor that opts a single page into
 * the attachment listing (read by ContentFilter::append_for_page()).
 */
class PageMetaBox {

	const META_KEY     = 'aa_page_meta';
	const NONCE_FIELD  = 'auto_attachments_page_meta_nonce';
	const NONCE_ACTION = 'auto_attachments_page_meta';

	/**
	 * Hook meta box registration.
	 */
	public function register_hooks(): void {
		add_action( 'admin_init', array( $this, 'register_metabox' ) );
	}

	/**
	 * Add the meta box and hook its save handler.
	 */
	public function register_metabox(): void {
		add_meta_box( 'all_aa_meta', __( 'Show Auto Attachments?', 'autoa' ), array( $this, 'render' ), 'page', 'side', 'high' );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Render the meta box.
	 *
	 * @param \WP_Post $post Page being edited.
	 */
	public function render( \WP_Post $post ): void {
		$meta    = get_post_meta( $post->ID, self::META_KEY, true );
		$checked = is_array( $meta ) && isset( $meta['show'] ) && 'yes' === $meta['show'];

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		?>
		<p><?php esc_html_e( 'If you want to show Auto Attachments on this page, check this box.', 'autoa' ); ?></p>
		<input type="checkbox" id="aa_page_meta" name="<?php echo esc_attr( self::META_KEY ); ?>[show]" value="yes" <?php checked( $checked ); ?> />
		<label for="aa_page_meta"><?php esc_html_e( 'I want to show it', 'autoa' ); ?></label>
		<?php
	}

	/**
	 * Save handler for `save_post`.
	 *
	 * Uses get_post_type() rather than trusting $_POST['post_type'] to
	 * identify pages: the block editor's meta-box-compat save request
	 * (used for any classic meta box, like this one, on a post type
	 * that uses the block editor) doesn't reliably include that field,
	 * which would otherwise cause this handler to bail before ever
	 * reaching the update/delete logic below - freezing whatever the
	 * checkbox's state happened to be, regardless of what's submitted.
	 *
	 * @param int $post_id Post being saved.
	 */
	public function save( int $post_id ): void {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( 'page' !== get_post_type( $post_id ) ) {
			return;
		}

		$nonce = isset( $_POST[ self::NONCE_FIELD ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

		$field = isset( $_POST[ self::META_KEY ] ) ? (array) wp_unslash( $_POST[ self::META_KEY ] ) : array();
		$show  = isset( $field['show'] ) && 'yes' === $field['show'];

		if ( $show ) {
			update_post_meta( $post_id, self::META_KEY, array( 'show' => 'yes' ) );
		} else {
			delete_post_meta( $post_id, self::META_KEY );
		}
	}
}
