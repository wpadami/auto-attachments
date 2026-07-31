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
	 * @param int $post_id Post being saved.
	 */
	public function save( int $post_id ): void {
		$nonce = isset( $_POST[ self::NONCE_FIELD ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! isset( $_POST['post_type'] ) || 'page' !== $_POST['post_type'] ) {
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
