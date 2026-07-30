<?php
// Stop direct call
defined('ABSPATH') || exit;
?>
<?php
$urlp = plugins_url('/auto-attachments');
$opts = get_option('auto_attachments_options');
?>
<style>#icon-aa {background:url('<?php echo esc_url($urlp); ?>/includes/images/32x32aa.png') no-repeat;margin-left:3px;}</style>
<div class='wrap'>
	<div id="icon-aa" class="icon32" ></div><h2><?php _e('Auto Attachments Settings Page', 'autoa'); ?> (<?php echo esc_html(plugin_get_version()); ?>)</h2>
<!-- Right Widgets -->
	<div id="dashboard-widgets-wrap" style="float:right;">
		<div id="dashboard_plugins" class="metabox-holder">
			<div style="width:23%;" id="dashboard" class="postbox">
				<h3 class='hndle'><span><?php _e('Contributor', 'autoa'); ?></span></h3>
					<div class="inside">
						<p style="padding:5px;">
							<img src="http://www.gravatar.com/avatar/d9e0fb92795db0ad96cf2b37bf0fc042.png" align="right" style="width:80px;height:80px;padding:2px;"><br /><strong>Serkan Algur</strong>
								<ul style="padding:5px;">
									<li><a href="https://github.com/wpadami/auto-attachments" target="_blank"><?php _e('Project on GitHub', 'autoa'); ?></a></li>
									<li><a href="https://github.com/serkanalgur" target="_blank"><?php _e('Author on GitHub', 'autoa'); ?></a></li>
									<li><a href="http://www.wpfunc.com" target="_blank"><?php _e('WpFunC (functions share)', 'autoa'); ?></a></li>
									<li><a href="http://facebook.com/serkan.algur" target="_blank"><?php _e('Facebook', 'autoa'); ?></a></li>
									<li><a href="https://twitter.com/kaisercrazy" class="twitter-follow-button" data-show-count="true" data-lang="tr" data-show-screen-name="false"></a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>
								</ul>
						</p>
					</div>
			</div>
			<div style="width:25%;" id="dashboard" class="postbox">
				<h3><?php _e('Preview', 'autoa'); ?></h3>
					<div class="inside"><br />
						<div class='mp3info'><?php echo esc_html($opts['mp3_listen']); ?></div>
						<div class='videoinfo'><?php echo esc_html($opts['video_watch']); ?></div>
					</div>
			</div>
			<!-- Insert Info Headers' Style -->
			<style>
				.videoinfo,.mp3info,.afileinfo{ width:200px;padding: 5px 0 5px 5px;line-height: 20px;font-size: 14px;margin: 0 0 10px 10px;text-align:justify;text-shadow: 1px 1px 1px #FFF;display:block;font-weight:bold;}
				.mp3info{background: #f5f5f5;border: 1px solid #dadada;color: #666666;clear:both;}
				.videoinfo{background: #FFFFCC;border: 1px solid #FFCC66;color: #996600;clear:both;}
				/* The CSS */
			</style>
		</div>
	</div>
<!-- Right Widgets -->

	<form method="post" action='<?php echo esc_url($_SERVER["REQUEST_URI"]); ?>'>
	<?php wp_nonce_field('update-options'); ?>
		<div style="width:68%;float:left;margin-top:10px;">
			<div id="openit" class="ui-accordion ui-widget ui-helper-reset">
				<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-top"><a href="#"><?php _e('<strong>Header Text Settings</strong>', 'autoa'); ?></a></h3>
					<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
						<p><small><?php _e('You can now change <strong>Header Texts</strong> from here. You can localize to your language :)', 'autoa'); ?></small></p>
						<p><strong><?php _e('Add Header Text for Mp3 Files:', 'autoa'); ?></strong><br />
							<input type="text" name="autoa[mp3_listen]" size="25" value="<?php echo esc_attr($opts['mp3_listen']); ?>" />&nbsp;&nbsp;<span id="radio2"><input type="radio" id="showmp3info_yes" name="autoa[showmp3info]" value="yes" <?php checked($opts['showmp3info'], 'yes'); ?> />
							<label for="showmp3info_yes"><?php _e('Show', 'autoa'); ?></label>
							<input type="radio" id="showmp3info_no" name="autoa[showmp3info]" value="no" <?php checked($opts['showmp3info'], 'no'); ?>/>
							<label for="showmp3info_no"><?php _e('Hide', 'autoa'); ?></label>
						</span>

						</p>
						<p><strong><?php _e('Add Header Text for Video Files:', 'autoa'); ?></strong><br />
							<input type="text" name="autoa[video_watch]" size="25" value="<?php echo esc_attr($opts['video_watch']); ?>" />&nbsp;&nbsp;<span id="radio3"><input type="radio" id="showvideoinfo_yes" name="autoa[showvideoinfo]" value="yes" <?php checked($opts['showvideoinfo'], 'yes'); ?> />
							<label for="showvideoinfo_yes"><?php _e('Show', 'autoa'); ?></label>
							<input type="radio" id="showvideoinfo_no" name="autoa[showvideoinfo]" value="no" <?php checked($opts['showvideoinfo'], 'no'); ?>/>
							<label for="showvideoinfo_no"><?php _e('Hide', 'autoa'); ?></label>
						</span>
						</p>
						<p><strong><?php _e('Title Before Attachments:', 'autoa'); ?></strong><br />
							<input type="text" name="autoa[before_title]" size="25" value="<?php echo esc_attr($opts['before_title']); ?>" />&nbsp;&nbsp;<span id="radio9"><input type="radio" id="show_b_title_yes" name="autoa[show_b_title]" value="yes" <?php checked($opts['show_b_title'], 'yes'); ?> />
							<label for="show_b_title_yes"><?php _e('Show', 'autoa'); ?></label>
							<input type="radio" id="show_b_title_no" name="autoa[show_b_title]" value="no" <?php checked($opts['show_b_title'], 'no'); ?>/>
							<label for="show_b_title_no"><?php _e('Hide', 'autoa'); ?></label></span><br />
								<small><i><?php _e('HTML Accepted', 'autoa'); ?></i></small>
						</p>
					</div>

				<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><a href="#"><?php _e('<strong>Page & Homepage Settings</strong>', 'autoa'); ?></a></h3>
					<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
						<p><strong><?php _e('Show on Categories', 'autoa'); ?></strong></p>
							<span style="font-size: 10px;"><em><?php _e('If you set this settings "Yes", attachments shown on category and single pages. If not attachments shown only in posts', 'autoa');?></em></span>
						<p id="radio6"><input type="radio" id="category_ok_yes" name="autoa[category_ok]" value="yes" <?php checked($opts['category_ok'], 'yes'); ?> />
							<label for="category_ok_yes"><?php _e('Yes', 'autoa'); ?></label>
							<input type="radio" id="category_ok_no" name="autoa[category_ok]" value="no" <?php checked($opts['category_ok'], 'no'); ?>/>
							<label for="category_ok_no"><?php _e('No', 'autoa'); ?></label>
						</p>
						<p><strong><?php _e('Show on Homepage', 'autoa'); ?></strong></p>
							<span style="font-size: 10px;"><em><?php _e('If you set this settings "Yes", attachments shown on homepage and single pages. If not attachments shown only in posts', 'autoa');?></em></span>
						<p id="radio1"><input type="radio" id="homepage_ok_yes" name="autoa[homepage_ok]" value="yes" <?php checked($opts['homepage_ok'], 'yes'); ?> />
							<label for="homepage_ok_yes"><?php _e('Yes', 'autoa'); ?></label>
							<input type="radio" id="homepage_ok_no" name="autoa[homepage_ok]" value="no" <?php checked($opts['homepage_ok'], 'no'); ?>/>
							<label for="homepage_ok_no"><?php _e('No', 'autoa'); ?></label>
						</p>
					</div>

				<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><a href="#"><?php _e('<strong>Gallery Settings</strong>', 'autoa'); ?></a></h3>
					<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
						<p><strong><?php _e('Show Gallery?', 'autoa'); ?></strong></p>
							<span style="font-size: 10px;text-align:justify;"><em><?php _e('You can use gallery for show your image attachments without any plugin or else :) Also if you use another gallery lightbox plugin you can disable the bundled lightbox.', 'autoa'); ?></em></span><br />
						<span id="radio4"><input type="radio" id="galeri_yes" name="autoa[galeri]" value="yes" <?php checked($opts['galeri'], 'yes'); ?> />
							<label for="galeri_yes"><?php _e('Yes', 'autoa'); ?></label>
							<input type="radio" id="galeri_no" name="autoa[galeri]" value="no" <?php checked($opts['galeri'], 'no'); ?>/>
							<label for="galeri_no"><?php _e('No', 'autoa'); ?></label>
						</span><label><?php _e('Select Gallery Color Style','autoa'); ?></label>
							<?php $glsy = array("light","dark");
										$gyl = $opts['galstyle'];
								?>
									<select name="autoa[galstyle]" id="galstyle">
										<?php
											foreach ($glsy as $gls) {
										?>
											<option value="<?php echo esc_attr($gls); ?>" <?php selected($gyl, $gls); ?> /><?php echo esc_html($gls); ?></option>
										<?php } ?>
									</select>
						<p><strong><?php _e('Use Lightbox?', 'autoa'); ?></strong></p>
						<span id="radio5"><input type="radio" id="use_colorbox_yes" name="autoa[use_colorbox]" value="yes" <?php checked($opts['use_colorbox'], 'yes'); ?> />
							<label for="use_colorbox_yes"><?php _e('Yes', 'autoa'); ?></label>
							<input type="radio" id="use_colorbox_no" name="autoa[use_colorbox]" value="no" <?php checked($opts['use_colorbox'], 'no'); ?>/>
							<label for="use_colorbox_no"><?php _e('No', 'autoa'); ?></label>
						</span><label><?php _e('Select Lightbox Color Style', 'autoa'); ?></label>
							<?php $slsy = array("light","dark");
										$syl = $opts['slimstyle'];
								?>
									<select name="autoa[slimstyle]" id="slimstyle">
										<?php
											foreach ($slsy as $sls) {
										?>
											<option value="<?php echo esc_attr($sls); ?>" <?php selected($syl, $sls); ?> /><?php echo esc_html($sls); ?></option>
										<?php } ?>
									</select>

						<p><strong><?php _e('Gallery Thumb Size?', 'autoa'); ?></strong></p>
							<input type="text" name="autoa[thw]" size="3" value="<?php echo esc_attr($opts['thw']); ?>" />px <strong>(<?php _e('Width', 'autoa'); ?>)</strong>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="autoa[thh]" size="3" value="<?php echo esc_attr($opts['thh']); ?>" />px <strong>(<?php _e('Height', 'autoa'); ?>)</strong>
						<p><strong><?php _e('Gallery Big Image Size?', 'autoa'); ?></strong></p>
							<input type="text" name="autoa[tbhw]" size="3" value="<?php echo esc_attr($opts['tbhw']); ?>" />px <strong>(<?php _e('Width', 'autoa'); ?>)</strong>&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="autoa[tbhh]" size="3" value="<?php echo esc_attr($opts['tbhh']); ?>" />px <strong>(<?php _e('Height', 'autoa');?>)</strong>
					</div>

				<h3 class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all"><a href="#"><?php _e('<strong>Misc. Settings</strong>', 'autoa'); ?></a></h3>
					<div class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="min-height:500px;">
						<p><strong><?php _e('List View Of Files', 'autoa'); ?></strong></p>
							<span style="font-size: 10px;"><em><?php _e('If you activate this option your downloadable files seen in a list view.', 'autoa'); ?></em></span>

						<p id="radio7"><input type="radio" id="listview_yes" name="autoa[listview]" value="yes" <?php checked($opts['listview'], 'yes'); ?> />
							<label for="listview_yes"><?php _e('Yes', 'autoa'); ?></label>
							<input type="radio" id="listview_no" name="autoa[listview]" value="no" <?php checked($opts['listview'], 'no'); ?>/>
							<label for="listview_no"><?php _e('No', 'autoa'); ?></label>
						</p>
						<p><strong><?php _e('Open files in new window?', 'autoa'); ?></strong></p>
							<span style="font-size: 10px;"><em><?php _e('Do you want to open files in new window?.', 'autoa'); ?></em></span>
						<p id="radio8"><input type="radio" id="newwindow_yes" name="autoa[newwindow]" value="yes" <?php checked($opts['newwindow'], 'yes'); ?> />
							<label for="newwindow_yes"><?php _e('Yes', 'autoa'); ?></label>
							<input type="radio" id="newwindow_no" name="autoa[newwindow]" value="no" <?php checked($opts['newwindow'], 'no'); ?>/>
							<label for="newwindow_no"><?php _e('No', 'autoa'); ?></label>
						</p>
						<p><strong><?php _e('File Icon Size?', 'autoa'); ?></strong></p>
						<input type="text" name="autoa[fhw]" size="3" value="<?php echo esc_attr($opts['fhw']); ?>" />px <strong>(<?php _e('Width', 'autoa'); ?>)</strong>&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="autoa[fhh]" size="3" value="<?php echo esc_attr($opts['fhh']); ?>" />px <strong>(<?php _e('Height', 'autoa'); ?>)</strong>
						<p><strong><?php _e('Player Dimensions?', 'autoa'); ?></strong><br />
							<small><i><?php _e('This dimensions actually for video player. Mp3 player will use only width.','autoa'); ?></i></small></p>
								<input type="text" name="autoa[jhw]" size="3" value="<?php echo esc_attr($opts['jhw']); ?>" />px <strong>(<?php _e('Width', 'autoa'); ?>)</strong>&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="autoa[jhh]" size="3" value="<?php echo esc_attr($opts['jhh']); ?>" />px <strong>(<?php _e('Height', 'autoa'); ?>)</strong>
					</div>
			</div>
			<input type="hidden" name="serkoup" value="uppo"/>
				<p><input type="submit" name="Submit" value="<?php esc_attr_e('Save Changes', 'autoa'); ?>" class="button-primary" /></p>
		</div>
	</form>
</div>
