<?php
/**
 * Shared contract for attachment-type renderers.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Renders a set of attachments of one type (file, audio, video, image) into
 * HTML. Implementations receive the plugin options they need rather than
 * reading get_option() themselves, so they can be constructed and tested
 * independently of WordPress's options store.
 */
interface Renderer {

	/**
	 * Render the given attachments to HTML.
	 *
	 * @param \WP_Post[] $attachments Attachments to render.
	 * @param array      $options     Plugin options (Settings::values()), plus
	 *                                any renderer-specific context (e.g. the
	 *                                GalleryRenderer's lightbox 'group' key).
	 * @return string Rendered HTML, or '' if there's nothing to render.
	 */
	public function render( array $attachments, array $options ): string;
}
