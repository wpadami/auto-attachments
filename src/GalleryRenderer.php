<?php
namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Renders an image attachment gallery with lightbox-ready markup. Shared
 * by the automatic attachment listing (get_attachment_icons()) and the
 * [imageaa] shortcode (getimages_aa()), which previously each built their
 * own copy of this HTML.
 */
class GalleryRenderer {

	/** @var Lightbox */
	private $lightbox;

	public function __construct( Lightbox $lightbox ) {
		$this->lightbox = $lightbox;
	}

	/**
	 * @param \WP_Post[] $attachments Image attachments to render.
	 * @param string     $group       Lightbox grouping key (e.g. the post ID);
	 *                                keep automatic and shortcode galleries in
	 *                                separate groups so they don't cycle together.
	 * @param string     $style       'light' or 'dark' gallery style.
	 */
	public function render( array $attachments, string $group, string $style ): string {
		$attachments = array_filter( $attachments, function ( $attachment ) {
			return $attachment instanceof \WP_Post;
		} );

		if ( empty( $attachments ) ) {
			return '';
		}

		$style = ( 'dark' === $style ) ? 'dark' : 'light';
		$html  = "<div class='dIW1'><div class='galeri-{$style}'>";

		foreach ( $attachments as $attachment ) {
			$thumb = wp_get_attachment_image_src( $attachment->ID, 'aa_thumb' );
			$big   = wp_get_attachment_image_src( $attachment->ID, 'aa_big' );

			if ( ! $thumb || ! $big ) {
				continue;
			}

			$html .= sprintf(
				"<a href='%s' %s><img src='%s' alt='%s' /></a>",
				esc_url( $big[0] ),
				$this->lightbox->group_attr( $group ),
				esc_url( $thumb[0] ),
				esc_attr( $attachment->post_title )
			);
		}

		$html .= '</div></div><div style="clear:both;"></div>';

		return $html;
	}
}
