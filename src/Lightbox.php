<?php
/**
 * First-party lightbox enqueue and markup helper.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues the plugin's first-party vanilla-JS lightbox and exposes the
 * markup helper both the automatic gallery and the [imageaa] shortcode
 * use, so there's one place that knows how a lightbox link is built.
 *
 * Kept behind the `use_colorbox` option, same as the Slimbox2 integration
 * it replaces: some site owners rely on another plugin's lightbox and use
 * that option to opt out of bundling one here.
 */
class Lightbox {

	const HANDLE = 'auto-attachments-lightbox';

	/**
	 * Plugin options (`Settings::values()`).
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @param array $options Plugin options (`Settings::values()`).
	 */
	public function __construct( array $options ) {
		$this->options = $options;
	}

	/**
	 * Hook script/style enqueueing, if the lightbox is enabled.
	 */
	public function register_hooks(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Whether the `use_colorbox` option has the lightbox turned on.
	 */
	public function is_enabled(): bool {
		return isset( $this->options['use_colorbox'] ) && 'yes' === $this->options['use_colorbox'];
	}

	/**
	 * Enqueue the lightbox CSS/JS and its inline theme.
	 */
	public function enqueue(): void {
		$base_url = plugins_url( '/auto-attachments/includes' );

		wp_enqueue_style( self::HANDLE, $base_url . '/css/aa-lightbox.css', array(), '1.0.0' );
		wp_enqueue_script( self::HANDLE, $base_url . '/js/aa-lightbox.js', array(), '1.0.0', true );
		wp_add_inline_style( self::HANDLE, $this->theme_css() );
	}

	/**
	 * Light/dark overlay theme, driven by the existing `slimstyle` setting
	 * (same option the Slimbox2 integration this replaces used).
	 */
	private function theme_css(): string {
		$dark = isset( $this->options['slimstyle'] ) && 'dark' === $this->options['slimstyle'];

		if ( $dark ) {
			return '.aa-lightbox-overlay{background:rgba(0,0,0,.92);}.aa-lightbox-caption{color:#eee;}';
		}
		return '.aa-lightbox-overlay{background:rgba(255,255,255,.92);}.aa-lightbox-caption{color:#222;}';
	}

	/**
	 * HTML attribute that groups a set of anchors into one lightbox
	 * (prev/next only cycles within the same group).
	 *
	 * @param string $group Grouping key, e.g. the post ID.
	 */
	public function group_attr( string $group ): string {
		return sprintf( "data-aa-lightbox='%s'", esc_attr( $group ) );
	}
}
