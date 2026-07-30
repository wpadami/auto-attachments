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
		// Future OOP subsystems (renderers, settings service, etc.) hook in here.
	}
}
