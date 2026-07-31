<?php
/**
 * Registers the [imageaa]/[filesaa]/[musicaa]/[videoaa] shortcodes.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Renders explicit attachment IDs given via shortcode attributes, using the
 * same Renderer classes as the automatic attachment listing and the
 * Gutenberg block - previously each shortcode callback built its own copy
 * of this markup. Rendering always uses 'shortcode_mode' (see the
 * Renderer implementations), matching each original shortcode's more
 * minimal output.
 */
class ShortcodeController {

	/**
	 * Renders the [filesaa] shortcode.
	 *
	 * @var Renderer
	 */
	private $file_renderer;

	/**
	 * Renders the [musicaa] shortcode.
	 *
	 * @var Renderer
	 */
	private $audio_renderer;

	/**
	 * Renders the [videoaa] shortcode.
	 *
	 * @var Renderer
	 */
	private $video_renderer;

	/**
	 * Renders the [imageaa] shortcode.
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
	 * @param Renderer $file_renderer    Renders the [filesaa] shortcode.
	 * @param Renderer $audio_renderer   Renders the [musicaa] shortcode.
	 * @param Renderer $video_renderer   Renders the [videoaa] shortcode.
	 * @param Renderer $gallery_renderer Renders the [imageaa] shortcode.
	 * @param array    $options          Plugin options (Settings::values()).
	 */
	public function __construct(
		Renderer $file_renderer,
		Renderer $audio_renderer,
		Renderer $video_renderer,
		Renderer $gallery_renderer,
		array $options
	) {
		$this->file_renderer    = $file_renderer;
		$this->audio_renderer   = $audio_renderer;
		$this->video_renderer   = $video_renderer;
		$this->gallery_renderer = $gallery_renderer;
		$this->options          = $options;
	}

	/**
	 * Register all four shortcodes.
	 */
	public function register_hooks(): void {
		add_shortcode( 'imageaa', array( $this, 'images' ) );
		add_shortcode( 'filesaa', array( $this, 'files' ) );
		add_shortcode( 'musicaa', array( $this, 'audio' ) );
		add_shortcode( 'videoaa', array( $this, 'video' ) );
	}

	/**
	 * [imageaa id="1,2,3"] - rendered as a lightbox-ready gallery.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function images( $atts ): string {
		$attachments = $this->attachments_from_atts( $atts );
		if ( empty( $attachments ) ) {
			return '';
		}
		return $this->gallery_renderer->render(
			$attachments,
			array_merge(
				$this->options,
				array( 'group' => 'aa-gallery-shortcode-' . get_the_ID() )
			)
		);
	}

	/**
	 * [filesaa id="1,2,3"] - always rendered as a grid, regardless of the
	 * "List View" option.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function files( $atts ): string {
		$attachments = $this->attachments_from_atts( $atts );
		if ( empty( $attachments ) ) {
			return '';
		}
		return $this->file_renderer->render( $attachments, $this->shortcode_options() );
	}

	/**
	 * [musicaa id="1,2,3"].
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function audio( $atts ): string {
		$attachments = $this->attachments_from_atts( $atts );
		if ( empty( $attachments ) ) {
			return '';
		}
		return $this->audio_renderer->render( $attachments, $this->shortcode_options() );
	}

	/**
	 * [videoaa id="1,2,3"].
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function video( $atts ): string {
		$attachments = $this->attachments_from_atts( $atts );
		if ( empty( $attachments ) ) {
			return '';
		}
		return $this->video_renderer->render( $attachments, $this->shortcode_options() );
	}

	/**
	 * Resolve a shortcode's `id="1,2,3"` attribute into WP_Post objects.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return \WP_Post[]
	 */
	private function attachments_from_atts( $atts ): array {
		$atts = shortcode_atts( array( 'id' => '' ), (array) $atts );
		if ( '' === $atts['id'] ) {
			return array();
		}
		$ids = array_map( 'absint', explode( ',', $atts['id'] ) );
		return array_filter( array_map( 'get_post', $ids ) );
	}

	/**
	 * This plugin's options, with shortcode_mode turned on.
	 */
	private function shortcode_options(): array {
		return array_merge( $this->options, array( 'shortcode_mode' => true ) );
	}
}
