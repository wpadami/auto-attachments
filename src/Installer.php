<?php
/**
 * Plugin activation/deactivation, including multisite network activation.
 *
 * @package AutoAttachments
 */

namespace AutoAttachments;

defined( 'ABSPATH' ) || exit;

/**
 * Seeds the default option row on activation (migrating any pre-0.5,
 * individually-stored options into it) and removes it on deactivation.
 * On a network-wide multisite activation/deactivation, repeats the
 * operation for every site in the network.
 */
class Installer {

	/**
	 * Absolute path to the main plugin file.
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_file Absolute path to the main plugin file.
	 */
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Hook the activation/deactivation callbacks.
	 */
	public function register_hooks(): void {
		register_activation_hook( $this->plugin_file, array( $this, 'activate' ) );
		register_deactivation_hook( $this->plugin_file, array( $this, 'deactivate' ) );
	}

	/**
	 * Activation callback: seed the default options.
	 */
	public function activate(): void {
		$this->for_each_site( array( $this, 'install_site' ) );
	}

	/**
	 * Deactivation callback: remove the stored options.
	 */
	public function deactivate(): void {
		$this->for_each_site( array( $this, 'uninstall_site' ) );
	}

	/**
	 * Run a callback once, or once per site on a network-wide multisite
	 * activation/deactivation.
	 *
	 * @param callable $callback Site-level activate/deactivate routine.
	 */
	private function for_each_site( callable $callback ): void {
		global $wpdb;

		$networkwide = isset( $_GET['networkwide'] ) && '1' === $_GET['networkwide']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! is_multisite() || ! $networkwide ) {
			$callback();
			return;
		}

		$original_blog_id = get_current_blog_id();
		$blog_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( (int) $blog_id );
			$callback();
		}

		switch_to_blog( $original_blog_id );
	}

	/**
	 * Seed auto_attachments_options for the current site, migrating any
	 * pre-0.5 individually-stored option values first.
	 */
	private function install_site(): void {
		$defaults = Settings::defaults();

		foreach ( $defaults as $key => $value ) {
			$existing = get_option( $key );
			if ( $existing ) {
				$defaults[ $key ] = $existing;
				delete_option( $key );
			}
		}

		add_option( 'auto_attachments_options', $defaults );
	}

	/**
	 * Remove auto_attachments_options for the current site.
	 */
	private function uninstall_site(): void {
		delete_option( 'auto_attachments_options' );
	}
}
