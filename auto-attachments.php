<?php
/*
Plugin Name: Auto Attachments
Plugin URI: https://github.com/wpadami/auto-attachments
Description: This plugin makes your attachments more effective. Supported attachment types are Word, Excel, Pdf, PowerPoint, zip, rar, tar, tar.gz, mp3, flv, mp4 
Version: 0.13.0
Author: Serkan Algur
Author URI: https://github.com/serkanalgur
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
// Stop direct call
defined('ABSPATH') || exit;

// Autoload AutoAttachments\ classes without requiring `composer install` -
// composer.json is used for dev tooling (PHPCS) only, not runtime deps.
spl_autoload_register( function ( $class ) {
	$prefix = 'AutoAttachments\\';
	if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
		return;
	}
	$relative = substr( $class, strlen( $prefix ) );
	$file     = __DIR__ . '/src/' . str_replace( '\\', '/', $relative ) . '.php';
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

\AutoAttachments\Plugin::instance()->init();

function multilingual_aa( ) {
				// Internationalization, first(!)
				load_plugin_textdomain('autoa', false, dirname(plugin_basename(__FILE__)) . '/languages');
				// Other init stuff, be sure to it after load_plugins_textdomain if it involves translated text(!)
}
add_action('init', 'multilingual_aa');

//Call Metabox
include 'admin/metaboxes.php';
//Call Shortcode
include 'admin/shortcodes.php';

//ACTIVATE (MULTISITES)
register_activation_hook(__FILE__, 'aa_install');
function aa_install( ) {
				global $wpdb;
				if (function_exists('is_multisite') && is_multisite()) {
								// check if it is a network activation - if so, run the activation function for each blog id
								if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
												$old_blog = $wpdb->blogid;
												// Get all blog ids
												$blogids  = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
												foreach ($blogids as $blog_id) {
																switch_to_blog($blog_id);
																_aa_install();
												}
												switch_to_blog($old_blog);
												return;
								}
				}
				_aa_install();
}
function _aa_install() {
			$aaopt = \AutoAttachments\Settings::defaults();

			// if old (pre-0.5, individually-stored) options exist, migrate them
			foreach( $aaopt as $key => $value ) {
				if( $existing = get_option($key) ) {
				$aaopt[$key] = $existing;
				delete_option($key);
				}
			}
		add_option('auto_attachments_options', $aaopt);
}
//DeACTIVATE (MULTISITES)
register_deactivation_hook(__FILE__, 'aa_uninstall');
function aa_uninstall( ) {
				global $wpdb;
				if (function_exists('is_multisite') && is_multisite()) {
								// check if it is a network activation - if so, run the activation function for each blog id
								if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
												$old_blog = $wpdb->blogid;
												// Get all blog ids
												$blogids  = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
												foreach ($blogids as $blog_id) {
																switch_to_blog($blog_id);
																_aa_uninstall();
												}
												switch_to_blog($old_blog);
												return;
								}
				}
				_aa_uninstall();
}
function _aa_uninstall( ) {	
				delete_option('auto_attachments_options');
}
//Add Css into Header (Header Text Options (added with v0.2.6))
add_action('wp_head', 'addHeaderCode');
function addHeaderCode( ) {
				$opts = get_option('auto_attachments_options');
				$urlp = plugins_url('/auto-attachments');
				echo '<link type="text/css" rel="stylesheet" href="' . esc_url($urlp) . '/a-a.css" />' . "\n";
				//With 0.2.6 you can decide show or hide :)
				if ($opts['showmp3info'] == 'no') {
								echo '<style>div.mp3info {display:none;}</style>';
				}
				if ($opts['showvideoinfo'] == 'no') {
								echo '<style>div.videoinfo {display:none;}</style>';
				}
}
$opts = get_option('auto_attachments_options');
add_image_size('aa_big', $opts['tbhw'], $opts['tbhh']);
add_image_size('aa_thumb', $opts['thw'], $opts['thh'], TRUE);


// Function Area 
function get_attachment_icons( ) {
				$opts 			   = get_option('auto_attachments_options');
				$urlp              = plugins_url('/auto-attachments/includes');
				$before_title_text = $opts['before_title'];
				$b_title           = $opts['show_b_title'];
				$aa_string         = "<div class='dIW2'>";
				if ($b_title == 'yes') {
								$aa_string .= "$before_title_text<br />";
				} else {
				}
				if ($opts['listview'] == 'yes') {
								$aa_string .= "<ul>";
				}
				$ex_dosya = get_post_meta(get_the_ID(), 'ex_dosya', TRUE);
				if ($files = get_children(array( //do only if there are attachments of these qualifications
								'post_parent' => get_the_ID(),
								'post_type' => 'attachment',
								'numberposts' => -1,
								'post_mime_type' => array(
												"application/pdf",
												"application/rar",
												"application/msword",
												"application/vnd.ms-powerpoint",
												"application/vnd.ms-excel",
												"application/zip",
												"application/x-rar-compressed",
												"application/x-tar",
												"application/x-gzip",
												"application/vnd.oasis.opendocument.spreadsheet",
												"application/vnd.oasis.opendocument.formula",
												"text/plain",
												"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
												"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
												"application/vnd.openxmlformats-officedocument.presentationml.presentation",
												"application/x-compress",
												"application/mathcad",
												"application/postscript"
								),
								'exclude' => $ex_dosya
								//MIME Type condition (changed into this format with 0.4.1)
				))) {
								foreach ($files as $file) //setup array for more than one file attachment
												{
												$fhh = $opts['fhh'];
												$fhw = $opts['fhw'];
												if ($opts['newwindow'] == 'yes') {
																$target = 'target="_blank"';
												} else {
																$target = "";
												}
												$file_link       = wp_get_attachment_url($file->ID); //get the url for linkage
												$file_name_array = explode("/", $file_link);
												$file_post_mime  = str_replace("/", "-", $file->post_mime_type);
												$file_name       = array_reverse($file_name_array); //creates an array out of the url and grabs the filename
												if ($opts['listview'] == 'yes') {
																$aa_string .= "<li id='$file->ID'>";
																$aa_string .= "<a style='font-weight:bold;text-decoration:none;' href='$file_link' $target><span class='ikon kaydet'></span>" . $file->post_title . "</a> ";
																$aa_string .= "</li>";
												} else {
																$aa_string .= "<div class='dI' id='$file->ID'>";
																$aa_string .= "<a href='$file_link' $target>";
																$aa_string .= "<img src='$urlp/images/mime/" . $file_post_mime . ".png' width='$fhw' height='$fhh'/>";
																$aa_string .= "</a>";
																$aa_string .= "<a class='dItitle' href='$file_link'>" . $file->post_title . "</a>";
																$aa_string .= "</div>";
												}
								}
				}
				if ($opts['listview'] == 'yes') {
								$aa_string .= "</ul>";
				}
				$aa_string .= "</div><div style='clear:both;'></div>";
				//Audio Files
				$ex_muz = get_post_meta(get_the_ID(), 'ex_muz', TRUE);
				$mp3s = get_children(array( //do only if there are attachments of these qualifications
								'post_parent' => get_the_ID(),
								'post_type' => 'attachment',
								'numberposts' => -1,
								'post_mime_type' => 'audio', //MIME Type condition
								'exclude' => $ex_muz
				));
				if (!empty($mp3s)):
								$jhw  = $opts['jhw'];
								$aa_string .= "<div class='dIW'><div class='mp3info'>" . esc_html($opts['mp3_listen']) . "</div><ul>";
								foreach ($mp3s as $mp3):
												$aa_string .= "<li>";
												$aa_string .= wp_audio_shortcode(array(
																'src'   => wp_get_attachment_url($mp3->ID),
																'width' => $jhw,
												));
												$aa_string .= "<span class='mp3title'>" . esc_html($mp3->post_title . " - " . $mp3->post_content) . "</span>";
												$aa_string .= "</li>";
								endforeach;
								$aa_string .= "</ul></div>";
				endif;
				//Video Support flv, mp4, etc. added with 0.2
				$ex_vid = get_post_meta(get_the_ID(), 'ex_vid', TRUE);
				$videoss = get_children(array( //do only if there are attachments of these qualifications
								'post_parent' => get_the_ID(),
								'post_type' => 'attachment',
								'numberposts' => -1,
								'post_mime_type' => 'video', //MIME Type condition
								'exclude' => $ex_vid
				));
				if (!empty($videoss)):
								$jhw  = $opts['jhw'];
								$jhh  = $opts['jhh'];
								$aa_string .= "<div class='dIW'><div class='videoinfo'>" . esc_html($opts['video_watch']) . "</div><ul>";
								foreach ($videoss as $videos):
												$aa_string .= "<li>";
												$aa_string .= wp_video_shortcode(array(
																'src'    => wp_get_attachment_url($videos->ID),
																'width'  => $jhw,
																'height' => $jhh,
												));
												$aa_string .= "<span class='mp3title'>" . esc_html($videos->post_title . " - " . $videos->post_content) . "</span>";
												$aa_string .= "</li>";
								endforeach;
								$aa_string .= "</ul></div>";
				endif;
				if ($opts['galeri'] == 'yes') {
								$thumb_ID = get_post_thumbnail_id( get_the_ID());
								$ex_rsm = get_post_meta(get_the_ID(), 'ex_rsm', TRUE);
								$galeriresim = get_children(array( //do only if there are attachments of these qualifications
												'post_parent' => get_the_ID(),
												'post_type' => 'attachment',
												'numberposts' => -1,
												'post_mime_type' => 'image', //MIME Type condition
												'exclude' => $thumb_ID.','.$ex_rsm
								));
								$aa_string .= \AutoAttachments\Plugin::instance()->gallery_renderer()->render(
												$galeriresim,
												'aa-gallery-' . get_the_ID(),
												$opts['galstyle']
								);
				}
				$aa_string .= "<div style='clear:both;'></div>";
				// Last Check for attachments (Needed After "Before Title option") Thanks Kris! :)
				$aargu = get_children(array(
								'post_parent' => get_the_ID(),
								'post_type' => 'attachment',
								'numberposts' => -1
				));
				if (!empty($aargu)):
								return $aa_string;
				endif;
}
//Insert code after the_content (!important) Changed into 3 parts with 0.5 (after this suggestion http://wordpress.org/support/topic/plugin-auto-attachments-does-not-show-attachments-for-posts-on-the-home-page?replies=2#post-2627965 )
add_filter('the_content', 'insertintoContent');
function insertintoContent($content) {
				if (is_single()) {
								$content .= get_attachment_icons();
				}
				return $content;
}
// Home Page Function Corrected with 0.5.2
if ($opts['homepage_ok'] == 'yes') {
				function insertintoHome($content) {
								if (is_home()) {
												$content .= get_attachment_icons();
								}
								return $content;
				}
				add_filter('the_content', 'insertintoHome');
}

if ($opts['category_ok'] == 'yes') {
				function insertintoCategory($content) {
								if (is_category()&&is_archive()) {
												$content .= get_attachment_icons();
								}
								return $content;
				}
				add_filter('the_content', 'insertintoCategory');
}




function insertintoPage($content) {
		if (is_page()) {
				$post_id = get_the_ID();
					$aa_show_page = get_post_meta($post_id, 'aa_page_meta', TRUE);
						if ($aa_show_page['show'] == 'yes'){
							$content .= get_attachment_icons();
							}
						}
			return $content;
		}
add_filter('the_content', 'insertintoPage');

//Show Plugin Version into Admin Page
function plugin_get_version( ) {
				if (!function_exists('get_plugins'))
								require_once(ABSPATH . 'wp-admin/includes/plugin.php');
				$plugin_folder = get_plugins('/' . plugin_basename(dirname(__FILE__)));
				$plugin_file   = basename((__FILE__));
				return $plugin_folder[$plugin_file]['Version'];
}
?>