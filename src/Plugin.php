<?php
/**
 * Plugin bootstrap and singleton container.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin bootstrap. New AutoAttachments\ classes register their hooks here
 * instead of adding more bare functions to the global namespace. Legacy
 * procedural code (admin/*.php) keeps loading separately from
 * auto-attachments.php until each piece is migrated onto this structure.
 */
class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Shared gallery renderer instance.
	 *
	 * @var GalleryRenderer|null
	 */
	private $gallery_renderer;

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
	 * Register hooks for OOP-based subsystems as they're added.
	 */
	public function init(): void {
		$lightbox = new Lightbox( Settings::values() );
		$lightbox->register_hooks();

		$this->gallery_renderer = new GalleryRenderer( $lightbox );

		( new SettingsRestController() )->register_hooks();
		( new SettingsPage() )->register_hooks();
		( new Block() )->register_hooks();
	}

	/**
	 * Shared image-gallery renderer, used by both the automatic attachment
	 * listing and the [imageaa] shortcode until those are fully migrated
	 * off the procedural code that currently calls this.
	 */
	public function gallery_renderer(): GalleryRenderer {
		if ( null === $this->gallery_renderer ) {
			$this->init();
		}
		return $this->gallery_renderer;
	}
}
