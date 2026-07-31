# [Auto Attachments] (http://wordpress.org/extend/plugins/auto-attachments/)

**Tags:** attachments, files, audio, video, gallery<br /> 
**License:** GPLv2 or later<br /> 
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html<br /> 
**Requires at least:** 5.0<br /> 
**Tested up to:** 7.0<br /> 
**Requires PHP:** 7.4<br /> 


This plugin makes your attachments more effective. 
**Supported attachment types:** Word, Excel, Pdf, PowerPoint, odf, ods, zip, rar, tar, mp3 and more...

## Description

Auto Attachments make your attachmens more effective. When you upload an attachment to your article, this plugin detects the attachment's type and creates a file list and a download area after <code>the_content</code>. If you add audio or video files, the plugin plays them inline using WordPress core's HTML5 audio/video player. Plugin works with mp3, ogg, mp4 and other video & audio types supported by WordPress.

**NOTICE**: Please set your thumbnail and big image sizes before uploading new images - changing these sizes doesn't retroactively resize already-uploaded attachments. Read [FAQ](faq/) please

The admin area (rebuilt in 0.11.0 - [look screenshots](#screenshots)) uses WordPress's native React-based settings UI. You can control all of the plugin's options, decide the downloadable files design (list or grid), and add header titles for video and audio files. You can find a .pot file in languages folder.

## Languages Included
+ Turkish (tr_TR)	(Default)
+ English (en_US)	(Default)
+ Romanian (ro_RO)	[Web Hosting Geeks](http://webhostinggeeks.com/)
+ Russian (ru_RU)	[Artiom Pulatov](http://papiko.ru/)
+ Italian (it_IT)   Andrea Primiani
+ Slovak (sk_SK)	[Branco, Web Hosting Geeks](http://webhostinggeeks.com/blog/)
+ Hebrew (he_IL)    [oriadam](http://wordpress.org/support/profile/oriadam)

**Note:** Translators, please update your language files. Lots of text added with 0.7.0

More Changes? [Look At Changelog](#changelog)
Some FAQ Added [FAQ](#frequently-asked-questions)

Plugin is multilingual. If you translate please open an issue on [GitHub](https://github.com/wpadami/auto-attachments) and share your files. Türkçe (Turkish) bilgi için : [Serkan Algur](https://github.com/serkanalgur)


## Installation

* Upload `auto-attachments` folder to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions 

**How i upload files to my blog or posts?**<br /> 
Use WordPress media uploader when you create new page or post **(Insert / Upload button)** and upload your files. No special things required. Plugin uses wordpress attachments.

**My Thumbnail is too big. How can i change size?**<br /> 
Go to Auto Attachments => Gallery Settings from accordion and set Thumbnail & Big size dimensions what px you want. This only applies to images uploaded after the change; already-uploaded attachments keep their existing thumbnail sizes.

**How i change file list type to multi-column (or reverse)**<br /> 
Go to Auto Attachments => Misc. Settings and if you want to use multi-column list set yes "List view of Files" (default No).

**How i create shortcodes**<br />
Go to **Posts (or Pages)** -> **Create New (or Edit)**. You will see a new **button with a paperclip**. Press this button and follow the instruction.

## Screenshots 

![Çalışır Halde Görünüm / The Plugin area](http://s.wordpress.org/extend/plugins/auto-attachments/screenshot-4.png)
![Yönetim Paneli / Admin Area] (http://s.wordpress.org/extend/plugins/auto-attachments/screenshot-3.png)

## Changelog

### Version 1.1.2
* Fixed the per-page "Show Auto Attachments?" checkbox: unchecking it (or checking it) could silently have no effect, because its save handler gated on `$_POST['post_type']`, which the block editor's meta-box-compat save request doesn't reliably include - the handler would bail before ever reaching the update/delete logic, freezing whatever the checkbox's state happened to be. Now uses the authoritative `get_post_type()` instead, plus standard autosave/revision save guards.
* Shortcode-panel selectboxes (Image/Audio/Video/File) now show the attachment ID first in each option's label (e.g. `(42) My Long File Name.pdf`, with the full name+id as a hover tooltip) - long filenames were pushing the id out of view in the narrow selectbox.
* Added `Requires at least: 5.0` / `Requires PHP: 7.4` to the plugin file header (auto-attachments.php) - previously only in readme.txt, but it's the plugin header WordPress core actually reads to block activation on incompatible sites.

### Version 1.1.1
* Fixed the classic-editor shortcode-builder dialog: its button did nothing and threw `$(...).dialog is not a function` in the console, because it relied on the jQuery UI "dialog" widget, which WordPress doesn't enqueue by default. Rebuilt on the native HTML `<dialog>` element with plain JS (no jQuery or jQuery UI at all), matching the rest of this plugin's dependency-free modernization.
* Trimmed the `Tags:` field in readme.txt from 21 entries (WordPress.org only honors the first 5) down to 5, and dropped the redundant "plugin" tag.

### Version 1.1.0
* Added `.github/workflows/release.yml`: on every `v*.*.*` tag push, verifies the tag matches the plugin header version, builds the admin UI and block (`npm run build`), assembles a clean plugin package per a new `.distignore` file, attaches a zip to a GitHub Release, and deploys the same package to the WordPress.org SVN repository (trunk + a version tag).
* `build/` (the compiled admin UI and block assets) is no longer committed to git - it's generated fresh by the release workflow above. Run `npm run build` locally if you need it for manual testing.

### Version 1.0.0
First stable release of the modernized plugin: WP 7.0/PHP 8 compatible, Flash-free, security-hardened, React-based settings UI, Gutenberg block, and a full OOP rewrite (see below) - enough cumulative change since the 0.x/2013-era codebase to call this the first 1.0 release rather than another 0.x increment.

* Converted all remaining procedural code (`auto-attachments.php`, `admin/shortcodes.php`, `admin/metaboxes.php`) to the `AutoAttachments\` OOP architecture mandated in claude.md - no more bare functions in the global namespace, no more legacy `admin/` includes.
* New classes: `AttachmentRepository` (attachment queries), `Renderer` interface with `FileRenderer`/`AudioRenderer`/`VideoRenderer` implementations (converging with the existing `GalleryRenderer`), `AttachmentIconsRenderer` (orchestrates the automatic listing), `ContentFilter` (the `the_content` hooks), `HeaderAssets`, `Installer` (activation/deactivation), `ShortcodeController` (`[imageaa]`/`[filesaa]`/`[musicaa]`/`[videoaa]`), `ShortcodePanel` and `ShortcodePanelAjax` (the classic-editor shortcode-builder dialog), and `PageMetaBox`.
* The shortcodes and the Gutenberg block (added in v0.12.0) now share the exact same renderer classes as the automatic listing, closing the last piece of the file/audio/video rendering duplication described in claude.md's DRY section.
* Fixed a real bug found while migrating the shortcode-builder dialog: its jQuery used the `.live()` method, removed in jQuery 1.9 (2013) and absent from the jQuery version WordPress ships today - every button in that dialog has been non-functional for years. Now uses `.on()`.
* Added nonce verification to the shortcode panel's "list this post's attachments" AJAX endpoint (previously capability-checked only).
* `GalleryRenderer::render()`'s signature changed from `(array $attachments, string $group, string $style)` to `(array $attachments, array $options)` (with `group`/`galstyle` keys) so it can implement the same `Renderer` interface as the other three renderers - internal API only, no user-facing change.

### Version 0.13.0
* Added `phpcs.xml.dist` (WordPress-Extra + WordPress-Docs + PHPCompatibilityWP, PHP 7.4+), scoped to `src/` - the OOP code under the architecture mandate in claude.md. Legacy procedural files (`auto-attachments.php`, `admin/*.php`) are excluded for now rather than retrofitted wholesale, matching the existing non-retroactive OOP policy.
* Added missing docblocks across all `src/` classes so `composer lint` passes clean.
* Added `.github/workflows/ci.yml`: runs `composer install` + `composer lint` + a full `php -l` syntax check on PHP 7.4 and 8.3 for every push/PR. JS lint isn't wired in yet - the current `wp-scripts lint-js` setup has an unrelated `@typescript-eslint`/ESLint version conflict in `node_modules` to sort out first.

### Version 0.12.1
* Updated stale documentation: the Description and admin-area text still described the removed Flash/JW Player and the old jQuery UI admin screen. Now describes the HTML5 audio/video player and the React settings UI. Also updated `Requires at least`/`Tested up to`/`Requires PHP` header fields. No code changes.

### Version 0.12.0
* Added a block editor block ("Attachment List (Auto Attachments)") for inserting file, image, audio, or video attachment lists - the block-editor equivalent of the `[imageaa]`/`[filesaa]`/`[musicaa]`/`[videoaa]` shortcodes, which keep working unchanged alongside it.
* The block is dynamic (server-rendered) and reuses the exact same rendering functions as the shortcodes, so there's one implementation, not a second copy.
* Fixed a bug in `getimages_aa()`/`getfiles_aa()`/`getmusic_aa()`/`getvideo_aa()`: they gated rendering on an internal "exclude from automatic listing" post-meta value instead of the shortcode's own `id` attribute, which only happened to work when the shortcode panel's AJAX call had run first. Also fixed an unrelated undefined-variable bug in `getfiles_aa()`'s file link.

### Version 0.11.0
* Rebuilt the admin settings page on WordPress's native React stack (`wp-element`/`wp-components`/`wp-api-fetch`, all bundled with WP core) instead of the old jQuery UI accordion form.
* Settings now read/write through a REST API (`auto-attachments/v1/settings`, `manage_options`-gated, validated `args` schema) instead of a raw `$_POST` form submit.
* Added `AutoAttachments\Settings` as the single source of truth for the option schema, defaults, and sanitization - previously split between `_aa_install()`'s defaults array and the settings-save function's sanitization groups.
* Added a `@wordpress/scripts` build (`package.json`, `src/admin/`); the compiled output is committed to `build/admin/` since there's no CI build step yet.
* Removed `admin/admin-area.php` and the jQuery-UI-accordion-only assets (`includes/js/ui.ms.js`, `includes/js/aa.js`, `includes/js/css/custom/`).
* The decorative "Contributor" sidebar (gravatar/links) on the old settings page was dropped, not recreated.
* Setting storage format is unchanged (`'yes'`/`'no'` strings in the `auto_attachments_options` option), so nothing else in the plugin needed to change.

### Version 0.10.0
* Removed the "Regen. Thumbnails" admin page and its AJAX handler (`admin/rebuild.php`) - this feature was carried over from another author's plugin and wasn't original to Auto Attachments. The plugin's own thumbnail sizes (`aa_thumb`/`aa_big`) are unaffected; only the manual regeneration tool is gone.
* Updated FAQ/notice text that referenced the removed "Rebuild Thumbnail page".

### Version 0.9.0
* Replaced the Slimbox2 (jQuery) gallery lightbox with a small first-party, dependency-free vanilla-JS lightbox (`includes/js/aa-lightbox.js`).
* Extracted `AutoAttachments\Lightbox` and `AutoAttachments\GalleryRenderer` classes, converging the gallery-rendering HTML that used to be duplicated between the automatic attachment listing and the `[imageaa]` shortcode.
* Removed the unused `includes/js/colorbox/` assets (already dead since Slimbox2 replaced Colorbox in v0.6) and the old `includes/js/slimbox/` assets.
* Renamed the "Use Slimbox?" / "Select Slimbox Color Style" admin labels to "Use Lightbox?" / "Select Lightbox Color Style" to match; the underlying settings (`use_colorbox`, `slimstyle`) are unchanged.

### Version 0.8.1
* Added `composer.json` (PSR-4 autoload map, PHPCS dev dependency) and a lightweight runtime autoloader for the new `AutoAttachments\` namespace, plus a `src/Plugin.php` bootstrap class. No behavior change - this is scaffolding for moving new/touched code to an OOP structure going forward.

### Version 0.8.0
* Replaced the Flash-based JW Player (dead in every browser since 2020) with WordPress core's HTML5 audio/video players (`wp_audio_shortcode()`/`wp_video_shortcode()`), in both the automatic attachment listing and the `[musicaa]`/`[videoaa]` shortcodes.
* Removed the swfobject.js include from every page's `<head>`.
* Removed the now-defunct "JW Player Skin" setting; the width/height fields are kept as general player dimensions.
* Removed the unused `includes/jw/` Flash assets (player.swf, skin archives, swfobject.js).

### Version 0.7.1
* Security: settings save now verifies the nonce that was already being rendered, and requires `manage_options`.
* Security: admin menu registration switched from a legacy numeric role level to the `manage_options` capability.
* Security: all admin settings output escaped; `checked()`/`selected()` used instead of translating raw HTML strings.
* Security: AJAX handlers (thumbnail rebuild, shortcode-panel attachment picker) now check nonces and capabilities before acting.
* Fix: removed `create_function()` (removed in PHP 8.0) and the PHP4-style class constructor in the thumbnail rebuilder, both of which broke that feature under PHP 8.

### Version 0.7 
* Tested with WordPress 3.5.1. Working!
* Admin area jQuery UI upgraded
* Admin area jQuery Uı theme changed
* Shortcode Panel Added
* Shortcodes added for Images, Files, Audio files and Videos
* Image size rebuilder code cleaned and changed some litte code
* Some jQuery code changed
* Tested with 3.5.1 & new shortcode panel. Working

### Version 0.6.7.1
* Bugfix for Open file in new window area (Thanks Matt!)

### Version 0.6.7
* Category pages `is_category()` support added
* Show on Categories Option added to Admin Area

### Version 0.6.6 
* Hebrew Language Added. Thanks oriadam!
* Start working on shortcodes & other new stuff.

### Version 0.6.5 
* jQuery implement was break dashboard left menu. Corrected. Thanks [Chris](http://wordpress.org/support/topic/seems-to-break-javascript-in-backend)
* Tested for WordPress 3.5. Working!
* Admin area code cleaned after jquery fix.

### Version 0.6.4 
* Pot file updated
* New language added (Slovak). Thanks Branco!
* One FAQ added about upload files

### Version 0.6.3
* Prevent from Featured Image in Gallery

### Version 0.6.2 
* Added a new icon for rar files

### Version 0.6.1 
* Works with WordPress 3.4.2

### Version 0.6 
* Rar Upload Option Deleted.
* Admin Area Code Cleaned.
* Options Serialized.
* Added meta box for "Show on Pages".
* Colorbox Replaced with Slimbox2 with light and dark style option.
* Gallery Thumbnail Style Added light and dark.
* Auto Attachments has a new menu area on the left.
* Thumbnail and Big image sizes are configurable now.
* Timthumb deleted from plugin. Plugin uses WordPress image crop.
* Thumbnail Rebuilder added. Please Use this first when you set image sizes.

### Version 0.5.6 
* We have a little "Title" problem after 0.5.5. Solved! Thanks for reported Kris.

### Version 0.5.5 
* Compatibility check for 3.4. Working!
* Added Option to Show title before attachments. (Thanks Kris :) )
* Php code format changes. More readable/ediatable code.

### Version 0.5.4 
* Multicolumn Listview
* Cache Folder Check
* Downloadable files' icon size select
* Downloadable files' link target attribute

### Version 0.5.3.1  
* Admin area CSS changes/fixes
* TimThumb Update to latest version
* Compatibility check for 3.3.2 Working!.

### Version 0.5.3 
* Settings Page Changed. It will be more beautiful with jQueru UI
* Added 7 themes for JW Player. You can select what theme you want (With Preview)
* Added option to change grid to list for downloadable files
* Some codes and folders/paths changed in base of plugin
* Colorbox Upgraded to latest version
* Timthumb problem solved for multisite. Timthumb can create thumbnails for subdirectory sites.

### Version 0.5.2 
* Homepage And Page Function corrected

### Version 0.5.1 
* Multisite Support Added.
* Timhumb upgraded to latest version.
* Security Update for file protect
* Compatibility check for 3.3.1 Working!.
* You can show on homepage if you want. Thanks to [venttom](http://wordpress.org/support/topic/plugin-auto-attachments-does-not-show-attachments-for-posts-on-the-home-page?replies=2#post-2627965)
* Checkboxes are radio buttons now. Option added "Yes" and "No".

### Version 0.4.3 
* Timhumb upgraded to latest version.
* Some mimetypes added.
* Compatibility check for 3.3. Working!.
* Multisite links fixed thanks [lopo](http://wordpress.org/support/topic/plugin-auto-attachments-does-not-work-if-wp-folder-is-different-than-home-with-fix?replies=2)
* Some code changes for multisite. Test it on your own multisites. Send me bug report if i have.

### Version 0.4.2 
* Support added for pages.
* You can decide to show attachments on pages.
* Minor Admin area changes.

### Version 0.4.1 
* After this update 4 more supported extensions.XLSX, PPTX, DOCX, TXT.
* More functional and fast code.
* Second security fix with folders and other things.

### Version 0.4 
* Long File names not lay on images if you use galelry option.
* Security fix for Timthumb.

### Version 0.3 
* A CSS Error Fixed. Thanks [Ramazan Benek](http://www.ramazanbenek.com/)

### Version 0.2.9 
* Tested for WordPress 3.2.1
* Changed admin area view. Preview area changed.
* Clean a messy code which breaks `#content` area in posts.

### Version 0.2.8 
* Tested for WordPress 3.2
* Gallery Thumbs are Resizeable now.

### Version 0.2.7 
* Gallery support added. Gallery use colorbox. You can enable or disable.
* Language files are updated.
* Some mess code cleaned.
* New screenshot added.

### Version 0.2.6 
* Admin Area Upgraded. Now you can decide rar upload, header text display.
* Plugin Now multilingual. en_US and tr_TR for now. If you translate plugin please contact me.
* Facebook Like and Share buttons added for WordPress plugin page.
* Admin area screenshot replaced

### Version 0.2.5 
* Some mess code cleaned.
* Improved Admin Area. If you dont fill Header text you'll get error.
* Improved Default Texts. Default array rewritten.
* Added admin area error screenshot.

### Version 0.2.4 
* Added user friendly admin setting page.
* Improve code when install. You will see a notice when you activate plugin.
* Added Default Texts for information bar.
* Added admin area screenshot.

### Version 0.2.3 
* Added some information bar for videos and audio files.
* Updated some old code.
* Added a new screenshot
* Tested for WP version 3.1.3 

### Version 0.2.2 
* Code Cleanup and version update. 

### Version 0.2.1 
* Security and Version Update. 
* ODF, ODS support added.
* RAR files can upload.

### Version 0.2 
* Video Support added. The plugin support for FLV, MP4 and other video types with JW player. Screenshot added.

### Version 0.1b 
* Plugin Released
