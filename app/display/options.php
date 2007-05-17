<?php
	// Handle style editing
	if ( isset($_POST['editstyle']) and !empty($_POST['k2']['style']) ) {
		$file = str_replace(ABSPATH, '', K2STYLESPATH) . attribute_escape($_POST['k2']['style']);
		header("Location: templates.php?file=$file");
		exit;
	}

	global $wpdb;

	// Update
	$update = K2Options::update();

	// Get the current K2 Style
	$style_name = get_option('k2scheme');
	$style_title = $style_name !== false ? $style_name : __('No Style','k2_domain');

	// Check that the styles folder exists
	$is_styles_dir = is_dir(K2STYLESPATH);

	// Get the scheme files
	$style_files = K2::get_styles();

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
	$is_headers_writable = is_writable(K2HEADERSPATH);
	$is_headers_dir = is_dir(K2HEADERSPATH);

	// Get the header pictures
	$picture_files = K2Header::get_header_images();
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
	<?php if (!$is_styles_dir) { ?>
		<div class="error"><small>
		<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom styles is missing.</p><p>For you to be able to use custom styles, you need to add this directory.</p>','k2_domain'), K2STYLESPATH ); ?>
		</small></div>
	<?php } ?>



	<form name="dofollow" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="<?php echo attribute_escape($update); ?>" />
		<input type="hidden" name="page_options" value="'dofollow_timeout'" />

		<p class="submit">
			<input type="submit" name="submit" value="<?php echo attribute_escape(__('Update Options &raquo;','k2_domain')); ?>" />
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
		
		.error {
		color #666;
		font-size: .8em;
		}

		.configstuff #k2-blogornoblog, .configstuff select  {
			width: 300px;
		}

		.configstuff p select {
			margin-left: 200px;
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
		
		.configelap {
			text-align: center;
		}
		
		table {
			margin: 0 auto;
			padding: 0;
			border-spacing: 0;
		}
		
		table tr td {
			padding: 5px;
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
				</small></p><p class="configelap"><input id="configela" name="configela" type="submit" value="<?php echo attribute_escape(__('Setup Extended Live Archives for K2','k2_domain')); ?>" /></p>
			<?php } ?>

			<h3><?php _e('Live Commenting','k2_domain'); ?></h3>

			<p><input id="k2-livecommenting" name="k2[livecommenting]" type="checkbox" value="1" <?php checked('1', get_option('k2livecommenting')); ?> /> <?php _e('Enable Live Commenting','k2_domain'); ?></p>
				
			<p><small><?php _e('Live comments use AJAX to submit comments to the server without reloading the page, making the experience more seamless for the user.','k2_domain'); ?></small></p>

			<?php if (function_exists('dynamic_sidebar')) { ?>
			<h3><?php _e('Sidebars','k2_domain'); ?></h3>

			<p><small><?php _e('This sets the number of sidebars that K2 will display. \'No Sidebars\' will place a default sidebar below the main column.', 'k2_domain'); ?></small></p>

			<p>
				<select id="k2-sidebarnumber" name="k2[sidebarnumber]">
					<option value="0" <?php selected($sidebar_number, '0'); ?>><?php _e('No Sidebars','k2_domain'); ?></option>
					<option value="1" <?php selected($sidebar_number, '1'); ?>><?php _e('One Sidebar','k2_domain'); ?></option>
					<option value="2" <?php selected($sidebar_number, '2'); ?>><?php _e('Two Sidebars','k2_domain'); ?></option>
				</select>
			</p>
			<?php } ?>

			<h3><?php _e('Asides','k2_domain'); ?></h3>

			<p><small><?php _e('\'Asides\' is a category of entries, meant to be \'smaller\' and perhaps of \'less importance\', like for instance links with minor commentary. They are styles differently than other entries to separate them content-wise. Below you can select a category to be shown as Asides.','k2_domain'); ?></small></p>

			<p>
				<select id="k2-asidescategory" name="k2[asidescategory]">
					<option value="0" <?php selected($asides_id, '0'); ?>><?php _e('No Asides','k2_domain'); ?></option>

					<?php foreach ($asides_cats as $cat) { ?>
					<option value="<?php echo attribute_escape($cat->cat_ID); ?>" <?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
					<?php } ?>
				</select>
			</p>

			<?php if ($is_styles_dir) { ?>
			<h3><?php _e('Style','k2_domain'); ?></h3>

			<p><small><?php printf(__('K2 Styles are CSS files that allow you to visually customize your blog, without ever touching K2\'s core files. The structure of K2 has been designed specifically for this purpose, and offers some truly great styling opportunities. %s','k2_domain'), '<a href="http://code.google.com/p/kaytwo/wiki/K2CSSandCustomCSS">' . __('Read more','k2_domain') . '</a>.'  ); ?></small></p>

			<p><select id="k2-scheme" name="k2[scheme]">
				<option value="" <?php selected($style_name, ''); ?>><?php _e('No Style','k2_domain'); ?></option>

				<?php foreach($style_files as $style_file) { ?>
				<option value="<?php echo attribute_escape($style_file); ?>" <?php selected($style_name, $style_file); ?>><?php echo($style_file); ?></option>
				<?php } ?>
			</select>
			</p>
			<?php } ?>

			<h3><?php _e('Header','k2_domain'); ?></h3>

			<p><small>
			<?php _e('Your header is the crown of your blog, with plenty of room to express yourself. Here you can decide on the ultimate question: Is it a a blog or a journal? But more importantly, you can upload images for use as header backgrounds. The default K2 header size for your current setup is 
			
			560x200px
			780x200px
			950x200px
			
			','k2_domain'); ?></small></p>

			<?php if (!$is_headers_dir) { ?>
				<div class="error">
				<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom headers is missing.</p>','k2_domain'), K2HEADERSPATH ); ?>
				</div>
			<?php } elseif (!$is_headers_writable) { ?>
				<div class="error">
				<?php printf(__('<p>The directory <code>%s</code> should be writable (CHMOD 777) to upload custom headers through this interface. You can still manually upload images to the directory however.</p>','k2_domain'), K2HEADERSPATH ); ?>
				</div>
			<?php } ?>

			<table>
			<?php if ($is_headers_dir) { ?>
				<?php if ($is_headers_writable) { ?>
			<tr><td>
				<small><?php _e('Upload an Image','k2_domain'); ?></small>
			</td><td>
				<input type="file" id="image_upload" name="image_upload" />
			</td><td>
				<label for="upload-activate"><input id="upload-activate" name="upload_activate" type="checkbox" value="1" /><small><?php _e('Activate immediately','k2_domain'); ?></small></label>
			</td></tr>
				<?php } ?>

			<tr><td style="width: 160px">
				<small><?php _e('Select an Image','k2_domain'); ?></small>
			</td><td>	
				<select id="k2-header-picture" name="k2[header_picture]">
					<option value="" <?php selected($header_picture, ''); ?>><?php _e('No Picture','k2_domain'); ?></option>
					<?php foreach($picture_files as $picture_file) { ?>
					<option value="<?php echo attribute_escape($picture_file); ?>" <?php selected($header_picture, $picture_file); ?>><?php echo($picture_file); ?></option>
					<?php } ?>
				</select>
			</td><td>	
				<label for="k2-imagerandomfeature"><input id="k2-imagerandomfeature" name="k2[imagerandomfeature]" type="checkbox" value="1" <?php checked('1', get_option('k2imagerandomfeature')); ?> /><small><?php _e('Randomize Header Image','k2_domain'); ?></small></label>
			</td></tr>
			<?php } ?>
			
			<tr><td style="width: 160px">
				<small><?php _e('Rename the \'Blog\' tab','k2_domain'); ?></small>
			</td><td>
				<input id="k2-blogornoblog" name="k2[blogornoblog]" value="<?php echo attribute_escape(get_option('k2blogornoblog')); ?>" />
			</td></tr>
			</table>

				
		</div>

<p class="submit">
	<input type="submit" name="submit" value="<?php echo attribute_escape(__('Update Options &raquo;','k2_domain')); ?>" />
</p>

		<div class="configstuff">
				<h3><?php _e('Uninstall K2','k2_domain'); ?></h3>

				<p><small><?php _e('Pressing the uninstall button does not delete any files. It simply disables K2 as a theme, reverting to the default theme, and removes all the K2 settings from the database. Perfect for if you want to start afresh.','k2_domain'); ?></small></p>

				<p style="text-align: center;"><input id="uninstall" name="uninstall" type="submit" value="<?php echo attribute_escape(__('Reset and Uninstall K2','k2_domain')); ?>" /></p>
		</div>

	</form>
</div>