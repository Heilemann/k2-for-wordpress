<?php
	global $k2_headers_path;

	// Update
	$update = K2Header::update();

	// Get the current K2 header picture
	$header_picture = get_option('k2header_picture');

	// Check that the header path is there
	$is_header_dir = is_dir($k2_headers_path);

	// Get the header pictures
	$picture_files = $is_header_dir ? K2::files_scan($k2_headers_path, false, 1) : array();
?>

<?php if(isset($_POST['submit'])) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('K2 Options have been updated','k2_domain'); ?></p>
</div>
<?php } ?>

<?php if(!$is_header_dir) { ?>
<div class="error">
	<?php printf(__('<strong>ERROR:</strong><p>%s is missing.</p><p>Please add this directory at your earliest convenience.</p><p>Remember to also <strong>chmod 777</strong> the headers directory.</p>','k2_domain'), $k2_headers_path ); ?>
</div>
<?php } ?>

<div class="wrap">
	<h2><?php _e('K2 Custom Header Options','k2_domain'); ?></h2>

	<form name="dofollow" action="" method="post" enctype="multipart/form-data">
		<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Update Options','k2_domain') ?> &raquo;" />
		</p>

		<input type="hidden" name="action" value="<?php echo($update); ?>" />

		<table width="700px" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
				<th scope="row"><?php _e('Upload Picture','k2_domain'); ?></th>

				<td>
					<input id="picture" name="picture" type="file"<?php if(!$is_header_dir) { ?> disabled="disabled"<?php } ?> />
					<p><input id="upload-activate" name="upload_activate" type="checkbox" value="1"<?php if(!$is_header_dir) { ?> disabled="disabled"<?php } ?> /> <label for="upload-activate"><?php _e('Active on upload','k2_domain'); ?></label></p>

					<p><small><?php _e('Choose the picture you would like to upload to the server and whether to activate it upon uploading','k2_domain'); ?></small><p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Select random picture','k2_domain'); ?></th>

				<td>

					<p><small><?php _e('Show random header picture','k2_domain'); ?></small><p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Manage Pictures','k2_domain'); ?></th>

				<td>
					<select id="k2-header-picture" name="k2[header_picture]">
						<option value=""<?php selected($header_picture, ''); ?>><?php _e('No Picture','k2_domain'); ?></option>
						<?php foreach($picture_files as $picture_file) { ?>
						<option value="<?php echo($picture_file); ?>"<?php selected($header_picture, $picture_file); ?>><?php echo($picture_file); ?></option>
						<?php } ?>
					</select>

					<p><small><?php _e('Select the picture you want to show at the frontpage','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Background color','k2_domain'); ?></th>

				<td>
					<input id="k2-headerbackgroundcolor" name="k2[headerbackgroundcolor]" type="text" size="7" value="<?php echo(get_option('k2headerbackgroundcolor')); ?>" />

					<p><small><?php _e('Define the background color for the header (default is #3371A3).','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Set header text alignment','k2_domain'); ?></th>

				<td>
					<select id="k2-headertextalignment" name="k2[headertextalignment]">
						<option value="left"<?php selected('left', get_option('k2headertextalignment')); ?>><?php _e('Left','k2_domain'); ?></option>
						<option value="center"<?php selected('center', get_option('k2headertextalignment')); ?>><?php _e('Center','k2_domain'); ?></option>
						<option value="right"<?php selected('right', get_option('k2headertextalignment')); ?>><?php _e('Right','k2_domain'); ?></option>
					</select>

					<p><small><?php _e('Set the alignment of the text over the header image.','k2_domain'); ?></small><p>
				</td>
			</tr>
  
			<tr valign="top">
				<th scope="row"><?php _e('Set header text size','k2_domain'); ?></th>

				<td>
					<input id="k2-headertextfontsize" name="k2[headertextfontsize]" type="text" size="2" value="<?php echo(get_option('k2headertextfontsize')); ?>" /> px

					<p><small><?php _e('Set the font size of the text over the header image.','k2_domain'); ?></small><p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Set header text color','k2_domain'); ?></th>

				<td>
					<select id="k2-headertextcolor" name="k2[headertextcolor]">
						<option value="<?php echo(get_option('k2headertextcolor_bright')); ?>"<?php selected(get_option('k2headertextcolor_bright'), get_option('k2headertextcolor')); ?>><?php _e('Bright','k2_domain'); ?> - <?php echo(get_option('k2headertextcolor_bright')); ?></option>
						<option value="<?php echo(get_option('k2headertextcolor_dark')); ?>"<?php selected(get_option('k2headertextcolor_dark'), get_option('k2headertextcolor')); ?>><?php _e('Dark','k2_domain'); ?> - <?php echo(get_option('k2headertextcolor_dark')); ?></option>
					</select>

					<p><small><?php _e('Set the color of the text over the header image.','k2_domain'); ?></small><p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Define Bright & Dark text color','k2_domain'); ?></th>

				<td>
					<label for="k2-headertextcolor-bright"><?php _e('Bright Color:','k2_domain'); ?></label>
					<input id="k2-headertextcolor-bright" name="k2[headertextcolor_bright]" type="text" size="7" value="<?php echo(get_option('k2headertextcolor_bright')); ?>" /><br />

					<label for="k2-headertextcolor-dark"><?php _e('Dark Color:','k2_domain'); ?></label>
					<input id="k2-headertextcolor-dark" name="k2[headertextcolor_dark]" type="text" size="7" value="<?php echo(get_option('k2headertextcolor_dark')); ?>" />
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Update Options','k2_domain') ?> &raquo;" />
		</p>
	</form>
</div>

<div class="wrap">
 	<p style="text-align: center;"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://k2.stikipad.com/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ) ?></p>
</div>
