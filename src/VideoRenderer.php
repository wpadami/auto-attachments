<?php
/**
 * Renders the video attachment list.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Renders video attachments using WordPress core's HTML5 player
 * (wp_video_shortcode()).
 *
 * Pass `'shortcode_mode' => true` in $options when rendering for the
 * [videoaa] shortcode: it never shows the "videoinfo" label header or the
 * per-item title/description, matching the original getvideo_aa()
 * shortcode callback.
 */
class VideoRenderer implements Renderer {

	/**
	 * Render video attachments to HTML.
	 *
	 * @param \WP_Post[] $attachments Video attachments to render.
	 * @param array      $options     Plugin options (Settings::values()).
	 */
	public function render( array $attachments, array $options ): string {
		$attachments = array_filter(
			$attachments,
			function ( $attachment ) {
				return $attachment instanceof \WP_Post;
			}
		);

		if ( empty( $attachments ) ) {
			return '';
		}

		$shortcode_mode = ! empty( $options['shortcode_mode'] );
		$width          = isset( $options['jhw'] ) ? $options['jhw'] : '';
		$height         = isset( $options['jhh'] ) ? $options['jhh'] : '';
		$label          = isset( $options['video_watch'] ) ? $options['video_watch'] : '';

		$html = $shortcode_mode
			? "<div class='dIW'><ul>"
			: "<div class='dIW'><div class='videoinfo'>" . esc_html( $label ) . '</div><ul>';

		foreach ( $attachments as $video ) {
			$html .= '<li>';
			$html .= wp_video_shortcode(
				array(
					'src'    => wp_get_attachment_url( $video->ID ),
					'width'  => $width,
					'height' => $height,
				)
			);
			if ( ! $shortcode_mode ) {
				$html .= "<span class='mp3title'>" . esc_html( $video->post_title . ' - ' . $video->post_content ) . '</span>';
			}
			$html .= '</li>';
		}

		$html .= '</ul></div>';
		if ( $shortcode_mode ) {
			$html .= "<div style='clear:both;'></div>";
		}

		return $html;
	}
}
