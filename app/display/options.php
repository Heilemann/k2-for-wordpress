<?php
	global $wpdb;

	// Update
	$update = K2Options::update();

	// Get the current K2 scheme
	$scheme_name = get_option('k2scheme');
	$scheme_title = $scheme_name !== false ? $scheme_name : __('No Scheme','k2_domain');

	// Get the scheme files
	$scheme_files = K2::files_scan(TEMPLATEPATH . '/styles/', 'css', 2);

	// Get the sidebar
	$sidebar_number = get_option('k2sidebarnumber');

	// Get the asides category
	$asides_id = get_option('k2asidescategory');
	$asides_title = $asides_id != 0 ? $wpdb->get_var("SELECT cat_name from $wpdb->categories WHERE cat_ID = $asides_id LIMIT 1") : __('No Asides','k2_domain');

	// Get the categories we might use for asides
	$asides_cats = $wpdb->get_results("SELECT cat_ID, cat_name FROM $wpdb->categories");

	// Update
	$update = K2Header::update();

	// Get the current K2 header picture
	$header_picture = get_option('k2header_picture');

	// Check that we can write to the headers folder and that it exists
	$is_headers_writable = is_writable(K2_HEADERS_PATH);
	$is_headers_dir = is_dir(K2_HEADERS_PATH);

	// Get the header pictures
	$picture_files = $is_headers_dir ? K2::files_scan(K2_HEADERS_PATH, array('gif','jpeg','jpg','png'), 1) : array();
?>

<?php if(isset($_POST['submit'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('K2 Options have been updated','k2_domain'); ?></p>
</div>
<?php } ?>

<?php if(isset($_POST['configela'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('The Extended Live Archives plugin has been setup for use with K2','k2_domain'); ?></p>
</div>
<?php } ?>

<div class="wrap">
	<form name="dofollow" action="" method="post">
		<input type="hidden" name="action" value="<?php echo($update); ?>" />
		<input type="hidden" name="page_options" value="'dofollow_timeout'" />

		<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Update Options &raquo;','k2_domain'); ?>" />
		</p>

		<style type="text/css">
		h3 {
			font: normal 1.8em Georgia;
			margin: 30px 0 0;
			color: #222;
		}

		small {
			color: #666;
		}
		
		.configstuff input[type=submit], #k2-blogornoblog, .configstuff select  {
			width: 300px;
		}

		.configstuff input[type=file] {
			width: 220px;
			background: none;
			border: none;
			padding: 0;
		}

		.configstuff input[type=checkbox] {
			margin-right: 8px;
		}

		.configstuff {
			width: 700px;
			margin: 0 auto;
			font-size: 1.1em;
		}
		
		table {
			margin: 0 auto;
			padding: 0;
			border-spacing: 0;
		}
		
		table tr td {
			height: 30px;
		}
		</style>

		<div class="configstuff">
			<h3><?php _e('Advanced Navigation','k2_domain'); ?></h3>

			<p><input id="k2-advnav" name="k2[advnav]" type="checkbox" value="1" <?php checked('1', get_option('k2livesearch')); ?> /> <?php _e('Enable Advanced Navigation','k2_domain'); ?></p>

			<p><small><?php _e('K2\'s Advanced Navigation is in reality a couple of features which, when combined, work to make the task of searching through your blog faster and easier. This includes inline AJAX-powered livesearch as well as the ability to flip back and forth between archive pages, without ever reloading the page.','k2_domain'); ?></small></p>

			<h3><?php _e('Archives Page','k2_domain'); ?></h3>

			<p><input id="k2-archives" name="k2[archives]" type="checkbox" value="add_archive" <?php checked('add_archive', get_option('k2archives')); ?> /> <?php _e('Enable Archives Page','k2_domain'); ?></p>

			<p><small><?php _e('To further enhance your precious backlog of writings, you can enable an archives page, which can assist both your readers as well as yourself in digging up the past.','k2_domain'); ?>

			<?php if (!function_exists('af_ela_set_config')) { ?>
				<?php printf(__('We highly recommend that you install %s for maximum archival pleasure.','k2_domain'), '<a href="http://www.sonsofskadi.net/index.php/extended-live-archive/">' . __('Arnaud Froment\'s Extended Live Archives','k2_domain') . '</a>'); ?></small></p>
			<?php } else { ?>
				</small></p><p style="text-align: center;"><input id="configela" name="configela" type="submit" value="<?php _e('Setup Extended Live Archives for K2','k2_domain') ?>" /></p>
			<?php } ?>

			<h3><?php _e('Live Commenting','k2_domain'); ?></h3>

			<p><input id="k2-livecommenting" name="k2[livecommenting]" type="checkbox" value="1" <?php checked('1', get_option('k2livecommenting')); ?> /> <?php _e('Enable Live Commenting','k2_domain'); ?></p>
				
			<p><small><?php _e('Live comments use AJAX to submit comments to the server without reloading the page, making the experience more seamless for the user.','k2_domain'); ?></small></p>


			<?php if (function_exists('dynamic_sidebar')) { ?>
			<h3><?php _e('Sidebars','k2_domain'); ?></h3>

			<p><small><?php _e('This sets the number of sidebars that K2 will display.','k2_domain'); ?></small></p>

			<p style="text-align: center">
				<select id="k2-sidebarnumber" name="k2[sidebarnumber]">
					<option value="0"<?php selected($sidebar_number, '0'); ?>><?php _e('No Sidebars','k2_domain'); ?></option>
					<option value="1"<?php selected($sidebar_number, '1'); ?>><?php _e('Single Sidebar','k2_domain'); ?></option>
					<option value="2"<?php selected($sidebar_number, '2'); ?>><?php _e('Dual Sidebars','k2_domain'); ?></option>
				</select>
			</p>
			<?php } ?>


			<h3><?php _e('Asides','k2_domain'); ?></h3>

			<p><small><?php _e('\'Asides\' is a category of entries, meant to be \'smaller\' and perhaps of \'less importance\', like for instance links with minor commentary. They are styles differently than other entries to separate them content-wise. Below you can select a category to be shown as Asides.','k2_domain'); ?></small></p>

			<p style="text-align: center">
				<select id="k2-asidescategory" name="k2[asidescategory]">
					<option value="0"<?php selected($asides_id, '0'); ?>><?php _e('No Asides','k2_domain'); ?></option>

					<?php foreach ($asides_cats as $cat) { ?>
					<option value="<?php echo($cat->cat_ID); ?>"<?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
					<?php } ?>
				</select>
			</p>

			<h3><?php _e('Custom Scheme','k2_domain'); ?></h3>

			<p><small><?php printf(__('K2 schemes are CSS files that allow you to visually customize your blog, without ever touching K2\'s core files. The structure of K2 has been designed specifically for this purpose, and offers some truly great styling opportunities. %s','k2_domain'), '<a href="http://code.google.com/p/kaytwo/wiki/K2CSSandCustomCSS">' . __('Read more','k2_domain') . '</a>.'  ); ?></small></p>

			<p style="text-align: center"><select id="k2-scheme" name="k2[scheme]">
				<option value=""<?php selected($scheme_name, ''); ?>><?php _e('No Scheme','k2_domain'); ?></option>

				<?php foreach($scheme_files as $scheme_file) { ?>
				<option value="<?php echo($scheme_file); ?>"<?php selected($scheme_name, $scheme_file); ?>><?php echo($scheme_file); ?></option>
				<?php } ?>
			</select></p>

			<h3><?php _e('Custom Header','k2_domain'); ?></h3>

			<p><small>
			<?php _e('Your header is the crown of your blog, with plenty of room to express yourself. Through here you can upload and manipulated images for use as header backgrounds, as well as decide whether to it\'s blog or journal.','k2_domain'); ?>
			<?php if (function_exists('add_custom_image_header')) { _e('Use the <b>Custom Image Header</b> panel to upload, crop and customize the header.','k2_domain'); } ?>
			</small></p>

			<table>
			<?php if ($is_headers_dir) { ?>

				<?php if (!function_exists('add_custom_image_header') and $is_headers_writable) { ?>
				<tr><td>
					<small><?php _e('Upload an Image','k2_domain'); ?></small>
				</td><td>
					<input id="picture" name="picture" type="file" disabled="disabled" />
					<input id="upload-activate" name="upload_activate" type="checkbox" value="1" />
					<label for="upload-activate"><small><?php _e('Activate immediately','k2_domain'); ?></small></label>
				</td></tr>
				<?php } ?>

			<tr><td style="width: 160px">
				<small><?php _e('Select an Image','k2_domain'); ?></small>
			</td><td>	
				<select id="k2-header-picture" name="k2[header_picture]">
					<option value=""<?php selected($header_picture, ''); ?>><?php _e('No Picture','k2_domain'); ?></option>
					<?php foreach($picture_files as $picture_file) { ?>
					<option value="<?php echo($picture_file); ?>"<?php selected($header_picture, $picture_file); ?>><?php echo($picture_file); ?></option>
					<?php } ?>
				</select>
			</td></tr>
			
			<tr><td>
				<small><?php _e('Randomize Header Image','k2_domain'); ?></small>
			</td><td>
				<input id="k2-imagerandomfeature" name="k2[imagerandomfeature]" type="checkbox" value="1" <?php checked('1', get_option('k2imagerandomfeature')); ?> />
			</td></tr>
			<?php } /* End is_headers_dir check */ ?>

			<?php if (!$is_headers_dir) { ?>
				<div class="error"><small>
				<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom headers is missing.</p><p>For you to be able to customize your header, you need to add this directory and <code>chmod 777</code> it, to make it writable.</p>','k2_domain'), K2_HEADERS_PATH ); ?>
				</small></div>
			<?php } elseif (!$is_headers_writable) { ?>
				<div class="error"><small>
				<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom headers is not writable.</p><p>You won\'t be able to upload any images here. You will need to <code>chmod 777</code> it to make it writable.</p>','k2_domain'), K2_HEADERS_PATH ); ?>
				</small></div>
			<?php } ?>

			<tr><td style="width: 160px">
				<small><?php _e('Rename the \'Blog\' tab','k2_domain'); ?></small>
			</td><td>
				<input id="k2-blogornoblog" name="k2[blogornoblog]" value="<?php echo(stripslashes(get_option('k2blogornoblog'))); ?>" />
			</td></tr>
			</table>
				
		</div>

<p class="submit">
	<input type="submit" name="submit" value="<?php _e('Update Options &raquo;','k2_domain'); ?>" />
</p>

		<div class="configstuff">
				<h3><?php _e('Uninstall K2','k2_domain'); ?></h3>

				<p><small><?php _e('Pressing the uninstall button does not delete any files. It simply disables K2 as a theme, reverting to the default theme, and removes all the K2 settings from the database. Perfect for if you want to start afresh.','k2_domain'); ?></small></p>

				<p style="text-align: center;"><input id="uninstall" name="uninstall" type="submit" value="<?php _e('Reset and Uninstall K2','k2_domain'); ?>" /></p>
		</div>

	</form>
</div>