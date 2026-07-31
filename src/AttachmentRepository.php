<?php
/**
 * Queries a post's attachments, grouped by the categories the plugin cares about.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Wraps get_children() lookups for a post's attachments so renderers and
 * the content filter don't each build their own WP_Query args.
 */
class AttachmentRepository {

	/**
	 * MIME types treated as "files" (documents/archives), matching the
	 * original get_attachment_icons() list.
	 */
	private const FILE_MIME_TYPES = array(
		'application/pdf',
		'application/rar',
		'application/msword',
		'application/vnd.ms-powerpoint',
		'application/vnd.ms-excel',
		'application/zip',
		'application/x-rar-compressed',
		'application/x-tar',
		'application/x-gzip',
		'application/vnd.oasis.opendocument.spreadsheet',
		'application/vnd.oasis.opendocument.formula',
		'text/plain',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/x-compress',
		'application/mathcad',
		'application/postscript',
	);

	/**
	 * Document/archive attachments (see FILE_MIME_TYPES).
	 *
	 * @param int    $post_id Post to query.
	 * @param string $exclude Comma-separated attachment IDs to exclude.
	 * @return \WP_Post[]
	 */
	public function files( int $post_id, string $exclude = '' ): array {
		return $this->query( $post_id, self::FILE_MIME_TYPES, $exclude );
	}

	/**
	 * Audio attachments.
	 *
	 * @param int    $post_id Post to query.
	 * @param string $exclude Comma-separated attachment IDs to exclude.
	 * @return \WP_Post[]
	 */
	public function audio( int $post_id, string $exclude = '' ): array {
		return $this->query( $post_id, 'audio', $exclude );
	}

	/**
	 * Video attachments.
	 *
	 * @param int    $post_id Post to query.
	 * @param string $exclude Comma-separated attachment IDs to exclude.
	 * @return \WP_Post[]
	 */
	public function video( int $post_id, string $exclude = '' ): array {
		return $this->query( $post_id, 'video', $exclude );
	}

	/**
	 * Image attachments.
	 *
	 * @param int    $post_id Post to query.
	 * @param string $exclude Comma-separated attachment IDs to exclude.
	 * @return \WP_Post[]
	 */
	public function images( int $post_id, string $exclude = '' ): array {
		return $this->query( $post_id, 'image', $exclude );
	}

	/**
	 * Whether the post has any attachments at all, regardless of type.
	 *
	 * @param int $post_id Post to check.
	 */
	public function has_any( int $post_id ): bool {
		return ! empty( $this->query( $post_id, '', '' ) );
	}

	/**
	 * Run the underlying get_children() query.
	 *
	 * @param int          $post_id    Post to query.
	 * @param string|array $mime_types A mime type, list of mime types, or ''
	 *                                 for no mime type restriction.
	 * @param string       $exclude    Comma-separated attachment IDs to exclude.
	 * @return \WP_Post[]
	 */
	private function query( int $post_id, $mime_types, string $exclude ): array {
		$args = array(
			'post_parent' => $post_id,
			'post_type'   => 'attachment',
			'numberposts' => -1,
		);
		if ( '' !== $mime_types ) {
			$args['post_mime_type'] = $mime_types;
		}
		if ( '' !== $exclude ) {
			$args['exclude'] = $exclude;
		}
		return get_children( $args );
	}
}
