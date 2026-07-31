<?php
/**
 * Appends the attachment list to `the_content` where configured.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks `the_content` and appends the attachment list on single posts
 * (always) and on the homepage/category archives/pages when their
 * respective options are enabled.
 */
class ContentFilter {

	/**
	 * Builds the attachment list HTML.
	 *
	 * @var AttachmentIconsRenderer
	 */
	private $renderer;

	/**
	 * Plugin options (Settings::values()).
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @param AttachmentIconsRenderer $renderer Builds the attachment list HTML.
	 * @param array                   $options  Plugin options (Settings::values()).
	 */
	public function __construct( AttachmentIconsRenderer $renderer, array $options ) {
		$this->renderer = $renderer;
		$this->options  = $options;
	}

	/**
	 * Hook all `the_content` callbacks.
	 */
	public function register_hooks(): void {
		add_filter( 'the_content', array( $this, 'append_for_single' ) );
		add_filter( 'the_content', array( $this, 'append_for_home' ) );
		add_filter( 'the_content', array( $this, 'append_for_category' ) );
		add_filter( 'the_content', array( $this, 'append_for_page' ) );
	}

	/**
	 * Append the attachment list on single post views.
	 *
	 * @param string $content Post content.
	 */
	public function append_for_single( string $content ): string {
		if ( is_single() ) {
			$content .= $this->renderer->render( get_the_ID() );
		}
		return $content;
	}

	/**
	 * Append the attachment list on the homepage, if enabled.
	 *
	 * @param string $content Post content.
	 */
	public function append_for_home( string $content ): string {
		if ( is_home() && $this->option_enabled( 'homepage_ok' ) ) {
			$content .= $this->renderer->render( get_the_ID() );
		}
		return $content;
	}

	/**
	 * Append the attachment list on category archives, if enabled.
	 *
	 * @param string $content Post content.
	 */
	public function append_for_category( string $content ): string {
		if ( is_category() && is_archive() && $this->option_enabled( 'category_ok' ) ) {
			$content .= $this->renderer->render( get_the_ID() );
		}
		return $content;
	}

	/**
	 * Append the attachment list on pages whose per-page meta box opted in.
	 *
	 * @param string $content Post content.
	 */
	public function append_for_page( string $content ): string {
		if ( ! is_page() ) {
			return $content;
		}

		$post_id = get_the_ID();
		$meta    = get_post_meta( $post_id, 'aa_page_meta', true );

		if ( is_array( $meta ) && isset( $meta['show'] ) && 'yes' === $meta['show'] ) {
			$content .= $this->renderer->render( $post_id );
		}

		return $content;
	}

	/**
	 * Whether a 'yes'/'no' option is set to 'yes'.
	 *
	 * @param string $key Option key to check.
	 */
	private function option_enabled( string $key ): bool {
		return isset( $this->options[ $key ] ) && 'yes' === $this->options[ $key ];
	}
}
