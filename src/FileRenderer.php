<?php
/**
 * Renders the document/file attachment list.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the "before title" header plus the document/file list (list or
 * grid view). These stay together because the original markup always
 * emits the wrapping `<div class="dIW2">` and optional title, even when
 * there happen to be no file attachments for this post - only the audio,
 * video, or gallery sections applied.
 *
 * Pass `'shortcode_mode' => true` in $options when rendering for the
 * [filesaa] shortcode: it always renders grid view and never shows the
 * "before title" header, regardless of the listview/show_b_title options -
 * matching the original getfiles_aa() shortcode callback, which never
 * shared that chrome with the automatic listing.
 */
class FileRenderer implements Renderer {

	/**
	 * Render the "before title" header plus the file/document list.
	 *
	 * @param \WP_Post[] $attachments File attachments to render.
	 * @param array      $options     Plugin options (Settings::values()).
	 */
	public function render( array $attachments, array $options ): string {
		$attachments = array_filter(
			$attachments,
			function ( $attachment ) {
				return $attachment instanceof \WP_Post;
			}
		);

		$shortcode_mode = ! empty( $options['shortcode_mode'] );
		$listview       = ! $shortcode_mode && isset( $options['listview'] ) && 'yes' === $options['listview'];
		$newwindow      = isset( $options['newwindow'] ) && 'yes' === $options['newwindow'];
		$show_title     = ! $shortcode_mode && isset( $options['show_b_title'] ) && 'yes' === $options['show_b_title'];
		$before_title   = isset( $options['before_title'] ) ? $options['before_title'] : '';
		$icon_width     = isset( $options['fhw'] ) ? $options['fhw'] : '48';
		$icon_height    = isset( $options['fhh'] ) ? $options['fhh'] : '48';
		$icons_url      = plugins_url( '/auto-attachments/includes' );

		$html = "<div class='dIW2'>";
		if ( $show_title ) {
			// Sanitized with wp_kses_post() when saved (Settings::sanitize_field()) - "Basic HTML allowed" is by design.
			$html .= wp_kses_post( $before_title ) . '<br />';
		}
		if ( $listview ) {
			$html .= '<ul>';
		}

		foreach ( $attachments as $file ) {
			$html .= $this->render_item( $file, $listview, $newwindow, $icons_url, $icon_width, $icon_height );
		}

		if ( $listview ) {
			$html .= '</ul>';
		}
		$html .= "</div><div style='clear:both;'></div>";

		return $html;
	}

	/**
	 * Render a single file's list-item or grid-item markup.
	 *
	 * @param \WP_Post $file        Attachment to render.
	 * @param bool     $listview    Whether to render a list item vs. a grid icon.
	 * @param bool     $newwindow   Whether the link should open in a new window.
	 * @param string   $icons_url   Base URL for the MIME-type icon images.
	 * @param string   $icon_width  Icon width, grid view only.
	 * @param string   $icon_height Icon height, grid view only.
	 */
	private function render_item( \WP_Post $file, bool $listview, bool $newwindow, string $icons_url, string $icon_width, string $icon_height ): string {
		$file_link = wp_get_attachment_url( $file->ID );
		$target    = $newwindow ? ' target="_blank"' : '';

		if ( $listview ) {
			return sprintf(
				"<li id='%d'><a style='font-weight:bold;text-decoration:none;' href='%s'%s><span class='ikon kaydet'></span>%s</a></li>",
				$file->ID,
				esc_url( $file_link ),
				$target,
				esc_html( $file->post_title )
			);
		}

		$mime_slug = str_replace( '/', '-', $file->post_mime_type );

		return sprintf(
			"<div class='dI' id='%d'><a href='%s'%s><img src='%s/images/mime/%s.png' width='%s' height='%s'/></a><a class='dItitle' href='%s'>%s</a></div>",
			$file->ID,
			esc_url( $file_link ),
			$target,
			esc_url( $icons_url ),
			esc_attr( $mime_slug ),
			esc_attr( $icon_width ),
			esc_attr( $icon_height ),
			esc_url( $file_link ),
			esc_html( $file->post_title )
		);
	}
}
