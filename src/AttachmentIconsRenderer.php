<?php
/**
 * Orchestrates the full auto-appended attachment list for a post.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the complete attachment listing for a post: files, audio, video,
 * and (if enabled) an image gallery. Replaces the original monolithic
 * get_attachment_icons() function - each section's markup now lives in its
 * own Renderer, this class only decides which attachments go where.
 */
class AttachmentIconsRenderer {

	/**
	 * Attachment query service.
	 *
	 * @var AttachmentRepository
	 */
	private $repository;

	/**
	 * Renders the document/file list.
	 *
	 * @var Renderer
	 */
	private $file_renderer;

	/**
	 * Renders audio attachments.
	 *
	 * @var Renderer
	 */
	private $audio_renderer;

	/**
	 * Renders video attachments.
	 *
	 * @var Renderer
	 */
	private $video_renderer;

	/**
	 * Renders the image gallery.
	 *
	 * @var Renderer
	 */
	private $gallery_renderer;

	/**
	 * Plugin options (Settings::values()).
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @param AttachmentRepository $repository       Attachment query service.
	 * @param Renderer             $file_renderer    Renders the document/file list.
	 * @param Renderer             $audio_renderer   Renders audio attachments.
	 * @param Renderer             $video_renderer   Renders video attachments.
	 * @param Renderer             $gallery_renderer Renders the image gallery.
	 * @param array                $options          Plugin options (Settings::values()).
	 */
	public function __construct(
		AttachmentRepository $repository,
		Renderer $file_renderer,
		Renderer $audio_renderer,
		Renderer $video_renderer,
		Renderer $gallery_renderer,
		array $options
	) {
		$this->repository       = $repository;
		$this->file_renderer    = $file_renderer;
		$this->audio_renderer   = $audio_renderer;
		$this->video_renderer   = $video_renderer;
		$this->gallery_renderer = $gallery_renderer;
		$this->options          = $options;
	}

	/**
	 * Render the attachment list for a post, or '' if it has no
	 * attachments of any kind.
	 *
	 * @param int $post_id Post to render attachments for.
	 */
	public function render( int $post_id ): string {
		if ( ! $this->repository->has_any( $post_id ) ) {
			return '';
		}

		$ex_dosya = get_post_meta( $post_id, 'ex_dosya', true );
		$html     = $this->file_renderer->render( $this->repository->files( $post_id, $ex_dosya ), $this->options );

		$ex_muz = get_post_meta( $post_id, 'ex_muz', true );
		$html  .= $this->audio_renderer->render( $this->repository->audio( $post_id, $ex_muz ), $this->options );

		$ex_vid = get_post_meta( $post_id, 'ex_vid', true );
		$html  .= $this->video_renderer->render( $this->repository->video( $post_id, $ex_vid ), $this->options );

		if ( isset( $this->options['galeri'] ) && 'yes' === $this->options['galeri'] ) {
			$thumb_id = get_post_thumbnail_id( $post_id );
			$ex_rsm   = get_post_meta( $post_id, 'ex_rsm', true );
			$html    .= $this->gallery_renderer->render(
				$this->repository->images( $post_id, $thumb_id . ',' . $ex_rsm ),
				array_merge( $this->options, array( 'group' => 'aa-gallery-' . $post_id ) )
			);
		}

		$html .= "<div style='clear:both;'></div>";

		return $html;
	}
}
