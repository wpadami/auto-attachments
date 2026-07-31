<?php
/**
 * Front-end stylesheet and registered image sizes.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues the plugin's stylesheet (plus the show/hide overrides for the
 * mp3/video info headers) and registers the `aa_big`/`aa_thumb` image sizes
 * used by GalleryRenderer.
 */
class HeaderAssets {

	const HANDLE = 'auto-attachments';

	/**
	 * Plugin options (Settings::values()).
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @param array $options Plugin options (Settings::values()).
	 */
	public function __construct( array $options ) {
		$this->options = $options;
	}

	/**
	 * Hook style enqueueing and image size registration.
	 */
	public function register_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_head', array( $this, 'print_inline_overrides' ) );
		add_action( 'after_setup_theme', array( $this, 'register_image_sizes' ) );
	}

	/**
	 * Enqueue the plugin stylesheet.
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style(
			self::HANDLE,
			plugins_url( '/auto-attachments/a-a.css' ),
			array(),
			'1.0.0'
		);
	}

	/**
	 * Print the mp3/video info-header show/hide overrides.
	 */
	public function print_inline_overrides(): void {
		if ( isset( $this->options['showmp3info'] ) && 'no' === $this->options['showmp3info'] ) {
			echo '<style>div.mp3info {display:none;}</style>';
		}
		if ( isset( $this->options['showvideoinfo'] ) && 'no' === $this->options['showvideoinfo'] ) {
			echo '<style>div.videoinfo {display:none;}</style>';
		}
	}

	/**
	 * Register the gallery thumbnail/big image sizes.
	 */
	public function register_image_sizes(): void {
		$thumb_width  = isset( $this->options['thw'] ) ? (int) $this->options['thw'] : 100;
		$thumb_height = isset( $this->options['thh'] ) ? (int) $this->options['thh'] : 100;
		$big_width    = isset( $this->options['tbhw'] ) ? (int) $this->options['tbhw'] : 800;
		$big_height   = isset( $this->options['tbhh'] ) ? (int) $this->options['tbhh'] : 600;

		add_image_size( 'aa_big', $big_width, $big_height );
		add_image_size( 'aa_thumb', $thumb_width, $thumb_height, true );
	}
}
