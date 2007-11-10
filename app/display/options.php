<?php
	global $wpdb;

	// Get the current K2 Style
	$style_name = get_option('k2scheme');
	$style_title = $style_name !== false ? $style_name : __('No Style','k2_domain');

	// Check that the styles folder exists
	$is_styles_dir = is_dir(K2_STYLES_PATH);

	// Get the scheme files
	$style_files = K2::get_styles();

	// Get the sidebar
	$column_number = get_option('k2columns');
	$column_options = array(
		1 => __('Single Column','k2_domain'),
		__('Two Columns', 'k2_domain'),
		__('Three Columns', 'k2_domain')
	);

	// Get the asides category
	$asides_id = get_option('k2asidescategory');

	// Get the categories we might use for asides
	$asides_cats = get_categories('get=all');

	// Get the current K2 header picture
	$header_picture = get_option('k2header_picture');

	$header_sizes = array(
		1 => __('560 &#215; 200px','k2_domain'),
		__('780 &#215; 200px','k2_domain'),
		__('950 &#215; 200px','k2_domain')
	);

	// Check that we can write to the headers folder and that it exists
	$is_headers_writable = is_writable(K2_HEADERS_PATH);
	$is_headers_dir = is_dir(K2_HEADERS_PATH);

	// Get the header pictures
	$picture_files = K2Header::get_header_images();
?>

<script>
	jQuery(document).scroll(function() { smartPosition('.configstuff') });
</script>


<?php if(isset($_POST['submit']) or isset($_GET['updated'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('K2 Options have been updated','k2_domain'); ?></p>
</div>
<?php } ?>

<?php if(isset($_POST['configela'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('The Extended Live Archives plugin has been setup for use with K2','k2_domain'); ?></p>
</div>
<?php } ?>

<div class="k2wrap">
	<?php if (!$is_styles_dir) { ?>
		<div class="error"><small>
		<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom styles is missing.</p><p>For you to be able to use custom styles, you need to add this directory.</p>','k2_domain'), K2_STYLES_PATH ); ?>
		</small></div>
	<?php } ?>



	<form name="dofollow" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="<?php echo attribute_escape($update); ?>" />
		<input type="hidden" name="page_options" value="'dofollow_timeout'" />

		<div class="configstuff">

			<div class="savebutton">
				<button><?php echo attribute_escape(__('Save','k2_domain')); ?></button>
			</div>

		
			<div class="container">
				<h3><label for="k2-sidebarmanager"><?php _e('Sidebar Manager','k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-sidebarmanager" name="k2[sidebarmanager]" type="checkbox" value="1" <?php checked('1', get_option('k2sidebarmanager')); ?> />
				<!--<label for="k2-sidebarmanager"><?php _e('Enable K2\'s Sidebar Manager','k2_domain'); ?></label>--></p>

				<p class="description"><?php printf(__('K2 has a neat sidebar system. If disabled, K2 reverts to WordPress widgets.', 'k2_domain'), $column_options[1]); ?></p>

				<?php if (get_option('k2sidebarmanager') == 0) { /* Only show column dropdown if SBM is disabled */ ?>
				<p>
					<select id="k2-columns" name="k2[columns]">
					<?php foreach ($column_options as $option => $label) { ?>
						<option value="<?php echo $option; ?>" <?php selected($column_number, $option); ?>><?php echo $label; ?></option>
					<?php } ?>
					</select>
				</p>
				<?php } ?>
			</div>


			<div class="container">
				<h3><label for="k2-advnav"><?php _e('Advanced Navigation','k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-advnav" name="k2[advnav]" type="checkbox" value="1" <?php checked('1', get_option('k2livesearch')); ?> />
				<!--<label for="k2-advnav"><?php _e('Enable Advanced Navigation','k2_domain'); ?></label>--></p>

				<p class="description"><?php _e('Seamlessly search and navigate old posts.','k2_domain'); ?></p>
			</div>


			<div class="container">
				<h3><label for="k2-archives"><?php _e('Archives Page','k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-archives" name="k2[archives]" type="checkbox" value="add_archive" <?php checked('add_archive', get_option('k2archives')); ?> />
				<!--<label for="k2-archives"><?php _e('Enable Archives Page','k2_domain'); ?></label>--></p>

				<p class="description"><?php _e('Installs a pre-made archives page.','k2_domain'); ?></p>

				<?php if (!function_exists('af_ela_set_config') && ($wp_version > 2.2)) { ?>
					<?php printf(__('We recommend you install %s for maximum archival pleasure.','k2_domain'), '<a href="http://www.sonsofskadi.net/index.php/extended-live-archive/">' . __('Arnaud Froment\'s Extended Live Archives','k2_domain') . '</a>'); ?></p>
				<?php } else if (function_exists('af_ela_set_config')) { ?>
					</p><p class="configelap"><input id="configela" name="configela" type="submit" value="<?php echo attribute_escape(__('Configure Extended Live Archives for K2','k2_domain')); ?>" /></p>
				<?php } ?>
			</div>


			<div class="container">
				<h3><label for="k2-livecommenting"><?php _e('Live Commenting','k2_domain'); ?></label></h3>

				<p class="checkboxelement"><input id="k2-livecommenting" name="k2[livecommenting]" type="checkbox" value="1" <?php checked('1', get_option('k2livecommenting')); ?> />
				<!--<label for="k2-livecommenting"><?php _e('Enable Live Commenting','k2_domain'); ?></label>--></p>
				
				<p class="description"><?php _e('Submit comments without reloading the page.','k2_domain'); ?></p>
			</div>


			<div class="container">
				<h3><?php _e('Asides','k2_domain'); ?></h3>

				<select id="k2-asidescategory" name="k2[asidescategory]">
					<option value="0" <?php selected($asides_id, '0'); ?>><?php _e('Off','k2_domain'); ?></option>

					<?php foreach ($asides_cats as $cat) { ?>
					<option value="<?php echo attribute_escape($cat->cat_ID); ?>" <?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
					<?php } ?>
				</select>

				<p class="description"><?php _e('Aside posts are styled differently and can be placed on the sidebar.','k2_domain'); ?></p>

			</div>


			<?php if ($is_styles_dir) { ?>
			<div class="container">
				<h3><?php _e('Style','k2_domain'); ?></h3>

				<select id="k2-scheme" name="k2[scheme]">
					<option value="" <?php selected($style_name, ''); ?>><?php _e('Off','k2_domain'); ?></option>

					<?php foreach($style_files as $style_file) { ?>
					<option value="<?php echo attribute_escape($style_file); ?>" <?php selected($style_name, $style_file); ?>><?php echo($style_file); ?></option>
					<?php } ?>
				</select>

				<p class="description"><?php printf(__('No need to edit core files, K2 is highly customizable using only CSS. %s','k2_domain'), '<a href="http://code.google.com/p/kaytwo/wiki/K2CSSandCustomCSS">' . __('Read&nbsp;more','k2_domain') . '</a>.'  ); ?></p>
			</div>
			<?php } ?>


			<div class="container headercontainer">
				<h3><?php _e('Header','k2_domain'); ?></h3>

				<p class="description"><?php printf(__('The header size for a default %s setup is <strong>%s</strong>.','k2_domain'), $column_options[$column_number], $header_sizes[$column_number] ); ?></p>

				<?php if (!$is_headers_dir) { ?>
					<div class="error">
					<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom headers is missing.</p>','k2_domain'), K2_HEADERS_PATH ); ?>
					</div>
				<?php } elseif (!$is_headers_writable) { ?>
					<div class="error">
					<?php printf(__('<p>The directory <code>%s</code> should be writable (CHMOD 777) to upload custom headers through this interface. You can still manually upload images to the directory however.</p>','k2_domain'), K2_HEADERS_PATH ); ?>
					</div>
				<?php } ?>

				<div class="headerwrap">
					<?php if ($is_headers_dir) { ?>
						<?php if ($is_headers_writable) { ?>
							<div>
								<span class="span1"><p><?php _e('Upload an Image','k2_domain'); ?></p></span>

								<span class="span2"><input type="file" id="image_upload" name="image_upload" /></span>

								<span class="span3"><input id="upload-activate" name="upload_activate" type="checkbox" value="1" /><label for="upload-activate"><?php _e('Activate immediately','k2_domain'); ?></span>
							</div>
						<?php } ?>

						<div>
							<span class="span1"><p><?php _e('Select an Image','k2_domain'); ?></p></span>

							<span class="span2">
								<select id="k2-header-picture" name="k2[header_picture]">
									<option value="" <?php selected($header_picture, ''); ?>><?php _e('Off','k2_domain'); ?></option>
									<?php foreach($picture_files as $picture_file) { ?>
									<option value="<?php echo attribute_escape($picture_file); ?>" <?php selected($header_picture, $picture_file); ?>><?php echo($picture_file); ?></option>
									<?php } ?>
								</select>
							</span>

							<span class="span3"><input id="k2-imagerandomfeature" name="k2[imagerandomfeature]" type="checkbox" value="1" <?php checked('1', get_option('k2imagerandomfeature')); ?> /><label for="k2-imagerandomfeature"><?php _e('Random','k2_domain'); ?></label></span>
						</div>
					<?php } ?>

					<div>
						<span class="span1"><p><?php _e('Rename the \'Blog\' tab','k2_domain'); ?></p></span>

						<span class="span2"><input id="k2-blogornoblog" name="k2[blogornoblog]" value="<?php echo attribute_escape(get_option('k2blogornoblog')); ?>" /></span>
					</div>
				</div>

			</div>
				
		</div>

</div>

<div class="k2wrap uninstall">


		<div class="configstuff">
			<h3><?php _e('Uninstall K2','k2_domain'); ?></h3>

			<script type="text/javascript">
			function confirmUninstall() {
				if (confirm("<?php _e('Delete your K2 settings?','k2_domain'); ?>") == true) {
					return true;
				} else {
					return false;
				}
			}
			</script>


			<p class="description"><?php _e('Remove all K2 settings and revert WordPress to its default theme. No files are deleted.','k2_domain'); ?></p>
			<p style="text-align: center;"><input id="uninstall" name="uninstall" type="submit" onClick="return confirmUninstall()" value="<?php echo attribute_escape(__('Reset and Uninstall K2','k2_domain')); ?>" /></p>
		</div>

</div>

	</form>
</div>
