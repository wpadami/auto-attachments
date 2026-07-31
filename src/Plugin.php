<?php
/**
 * Plugin bootstrap and singleton container.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin bootstrap. AutoAttachments\ classes register their hooks here
 * instead of adding more bare functions to the global namespace.
 *
 * The init() method must be called exactly once, from the main plugin
 * file, before anything else in the plugin runs.
 */
class Plugin {

	const TEXT_DOMAIN = 'autoa';

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Absolute path to the main plugin file, set by init().
	 *
	 * @var string
	 */
	private $plugin_file = '';

	/**
	 * Get the shared Plugin instance, creating it on first call.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor - use instance() instead.
	 */
	private function __construct() {
	}

	/**
	 * Construct every subsystem and register its hooks.
	 *
	 * @param string $plugin_file Absolute path to the main plugin file
	 *                            (auto-attachments.php's __FILE__).
	 */
	public function init( string $plugin_file ): void {
		$this->plugin_file = $plugin_file;

		add_action( 'init', array( $this, 'load_textdomain' ) );

		( new Installer( $plugin_file ) )->register_hooks();

		$options    = Settings::values();
		$repository = new AttachmentRepository();

		$lightbox = new Lightbox( $options );
		$lightbox->register_hooks();

		$gallery_renderer = new GalleryRenderer( $lightbox );
		$file_renderer    = new FileRenderer();
		$audio_renderer   = new AudioRenderer();
		$video_renderer   = new VideoRenderer();

		$icons_renderer = new AttachmentIconsRenderer(
			$repository,
			$file_renderer,
			$audio_renderer,
			$video_renderer,
			$gallery_renderer,
			$options
		);
		( new ContentFilter( $icons_renderer, $options ) )->register_hooks();

		( new HeaderAssets( $options ) )->register_hooks();

		$shortcodes = new ShortcodeController( $file_renderer, $audio_renderer, $video_renderer, $gallery_renderer, $options );
		$shortcodes->register_hooks();

		( new ShortcodePanelAjax() )->register_hooks();
		( new ShortcodePanel( $repository ) )->register_hooks();
		( new PageMetaBox() )->register_hooks();
		( new AttachmentAttacher() )->register_hooks();

		( new SettingsRestController() )->register_hooks();
		( new SettingsPage() )->register_hooks();
		( new Block( $shortcodes ) )->register_hooks();
	}

	/**
	 * Load the plugin's translation files. Hooked to `init`, same timing
	 * as the original procedural implementation.
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			self::TEXT_DOMAIN,
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/languages'
		);
	}
}
