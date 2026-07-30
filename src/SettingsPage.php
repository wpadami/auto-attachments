<?php
namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Auto Attachments" admin menu page and mounts the React
 * settings app there. All read/write goes through the REST API
 * (SettingsRestController); this class only owns the wp-admin chrome.
 */
class SettingsPage {

	const MENU_SLUG = 'auto_attachments';
	const HANDLE     = 'auto-attachments-admin';

	/** @var string */
	private $hook_suffix = '';

	public function register_hooks(): void {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
	}

	public function add_menu_page(): void {
		$this->hook_suffix = add_menu_page(
			__( 'Auto Attachments', 'auto-attachments' ),
			__( 'Auto Attachments', 'auto-attachments' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render' ),
			plugins_url( 'auto-attachments/includes/images/aamenu.png' )
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue( string $hook ): void {
		if ( $hook !== $this->hook_suffix ) {
			return;
		}

		$asset_file = __DIR__ . '/../build/admin/index.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}
		$asset = require $asset_file;

		wp_enqueue_script(
			self::HANDLE,
			plugins_url( 'auto-attachments/build/admin/index.js' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);
		wp_set_script_translations( self::HANDLE, 'auto-attachments' );

		wp_enqueue_style(
			self::HANDLE,
			plugins_url( 'auto-attachments/build/admin/style-index.css' ),
			array( 'wp-components' ),
			$asset['version']
		);
	}

	public function render(): void {
		echo '<div id="auto-attachments-settings-root" class="wrap"></div>';
	}
}
