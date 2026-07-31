<?php
/**
 * Plugin bootstrap file.
 *
 * @package AutoAttachments
 */

/*
Plugin Name: Auto Attachments
Plugin URI: https://github.com/wpadami/auto-attachments
Description: This plugin makes your attachments more effective. Supported attachment types are Word, Excel, Pdf, PowerPoint, zip, rar, tar, tar.gz, mp3, flv, mp4
Version: 1.1.1
Author: Serkan Algur
Author URI: https://github.com/serkanalgur
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Stop direct call.
defined( 'ABSPATH' ) || exit;

// Autoload AutoAttachments\ classes without requiring `composer install` -
// composer.json is used for dev tooling (PHPCS) only, not runtime deps.
spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'AutoAttachments\\';
		if ( strncmp( $prefix, $class_name, strlen( $prefix ) ) !== 0 ) {
				return;
		}
		$relative = substr( $class_name, strlen( $prefix ) );
		$file     = __DIR__ . '/src/' . str_replace( '\\', '/', $relative ) . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

\AutoAttachments\Plugin::instance()->init( __FILE__ );
