<?php
/**
 * Renders the audio attachment list.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Renders audio attachments using WordPress core's HTML5 player
 * (wp_audio_shortcode()).
 *
 * Pass `'shortcode_mode' => true` in $options when rendering for the
 * [musicaa] shortcode: it never shows the "mp3info" label header or the
 * per-item title/description, matching the original getmusic_aa()
 * shortcode callback.
 */
class AudioRenderer implements Renderer {

	/**
	 * Render audio attachments to HTML.
	 *
	 * @param \WP_Post[] $attachments Audio attachments to render.
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
		$label          = isset( $options['mp3_listen'] ) ? $options['mp3_listen'] : '';

		$html = $shortcode_mode
			? "<div class='dIW'><ul>"
			: "<div class='dIW'><div class='mp3info'>" . esc_html( $label ) . '</div><ul>';

		foreach ( $attachments as $mp3 ) {
			$html .= '<li>';
			$html .= wp_audio_shortcode(
				array(
					'src'   => wp_get_attachment_url( $mp3->ID ),
					'width' => $width,
				)
			);
			if ( ! $shortcode_mode ) {
				$html .= "<span class='mp3title'>" . esc_html( $mp3->post_title . ' - ' . $mp3->post_content ) . '</span>';
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
