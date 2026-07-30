<?php
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
	 * @var self|null
	 */
	private static $instance = null;

	/** @var GalleryRenderer|null */
	private $gallery_renderer;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
	}

	/**
	 * Register hooks for OOP-based subsystems as they're added.
	 */
	public function init(): void {
		$options = get_option( 'auto_attachments_options' );
		$options = is_array( $options ) ? $options : array();

		$lightbox = new Lightbox( $options );
		$lightbox->register_hooks();

		$this->gallery_renderer = new GalleryRenderer( $lightbox );
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
