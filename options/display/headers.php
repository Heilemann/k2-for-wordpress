<?php 
$path = TEMPLATEPATH . '/images/headers/';

function findrandompicture() {
	global $path;
	$picture_dir = @ dir($path);
	if ($picture_dir) {
		while (($file = $picture_dir->read()) !== false) {
			if (!preg_match('|^\.+$|', $file) && !preg_match('|^CVS$|', $file) && !preg_match('|^\.|', $file)) {
				$picture_files[] = $file;
			}
		}
	}
	$size = count($picture_files);
	return ($picture_files[rand(0, $size-1)]);
}

function k2uploadupdate() {
	global $path;

	if ($_POST['k2headertextcolor'] != "") { update_option('k2headertextcolor' , $_POST['k2headertextcolor']); }
 	if ($_POST['k2headertextalignment'] != "") { update_option('k2headertextalignment', $_POST['k2headertextalignment']); }
 	if ($_POST['k2headertextfontsize'] != "") { update_option('k2headertextfontsize', $_POST['k2headertextfontsize']); }
  	if ($_POST['backgroundcolor'] != "") { $backgroundcolor = substr(str_replace("#","", $_POST['backgroundcolor']),0,6); update_option('k2headerbackgroundcolor', "#".$backgroundcolor); }  
  	if ($_POST['brightcolor'] != "" && ($_POST['brightcolor'] != get_option('k2headertextcolor_bright'))) { update_option('k2headertextcolor_bright', $_POST['brightcolor']); }
	if ($_POST['darkcolor'] != "" && ($_POST['darkcolor'] != get_option('k2headertextcolor_dark'))) { update_option('k2headertextcolor_dark', $_POST['darkcolor']); }
	if (get_option('k2imagerandomfeature') == "") { update_option('k2imagerandomfeature', 'n'); }
	if ((($_POST['userandomfeature'] != get_option('k2imagerandomfeature')) && ($_POST['userandomfeature']))) { update_option('k2imagerandomfeature', $_POST['userandomfeature']); }
	if ($_FILES['picture']['name'] != "" && $_FILES['picture']['size'] > 0) { $target_path =  $path; move_uploaded_file($_FILES['picture']['tmp_name'], $target_path.$_FILES['picture']['name']); }
	if ($_POST['upload_activate'] == "active") { update_option('k2header_picture', $_FILES['picture']['name']); }
	if (($_POST['k2header_picture'] != "") && (trim($_POST['k2header_picture']) != trim(get_option('k2header_picture'))) && ($_POST['upload_activate'] != "active")) { $k2header_picture = $_POST['k2header_picture']; update_option('k2header_picture', $k2header_picture, '',''); }
}

function k2_picupload_admin() {
	add_submenu_page('themes.php','K2 Upload Options', 'K2 Upload Options', 5, 'options/display/headers.php', 'uploadmenu');
	}

function uploadmenu() {
	global $path;
	$error = FALSE;

	if (!is_dir($path)) { 
		echo '<div class="error "><strong>ERROR: <br />\'' . $path .'\'</strong> is missing.<br /><br />Please add this directory at your earliest convenience. <br /><br />Remember to also <b>chmod 777</b> the headers directory.</div>'; 
		$error = TRUE;
	} 

	global $path;
	if (isset($_POST['Submit'])) {
?>

<div class="updated">
	<p><?php _e('Options has been updated.'); ?></p>
</div>
<?php
}
?>

<div class="wrap">
<h2><?php _e('K2 Upload Picture Options'); ?></h2>
<form name="dofollow" action="" method="post" enctype="multipart/form-data">
<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" /></p>
  <input type="hidden" name="action" value="<?php k2uploadupdate(); ?>" />
  <table width="700px" cellspacing="2" cellpadding="5" class="editform">
  <tr valign="top">
	<th scope="row"><?php echo __('Upload Picture'); ?></th>
	<td>
		<label for="k2upload_picture">
		<input <?php echo ($error ? 'disabled' : ''); ?> type="file" name="picture">
		<p><small>Choose the picture you would like to upload to the server</small><p>
	</td>
  </tr>
  <tr>
	<th valign="top" scope="row"><?php echo __('Active on upload'); ?></th>
	<td>
		<label for="k2upload_activate">
		<input type="checkbox" name="upload_activate" value="active">
		<p><small>Automatically activate the uploaded picture as the active picture</small>
  <tr valign="top">
	<th scope="row"><?php echo __('Select random picture'); ?></th>
	<td>
		<label for="k2upload_picture">
		Yes<input type="radio" name="userandomfeature" value="yes" <?php echo (get_option('k2imagerandomfeature') == "yes" ? 'checked' : '') ?> >&nbsp;
		No<input type="radio" name="userandomfeature" value="no" <?php echo (get_option('k2imagerandomfeature') == "no" ? 'checked' : '') ?> >
		<p><small>Show random header picture</small><p>
	</td>
  </tr>

  <tr valign="top">
	<th scope="row"><?php echo __('Manage Pictures'); ?></th>
	<td>
		<label for="k2manage_pictures">
		<?php
		global $wpdb;
		$picname = get_option('k2header_picture');
		if ($picname != '')
		{
			$picture_title = $picname;
		}
		else
		{
			$picture_title = 'No Picture';
		}
		?>
		<select name="k2header_picture">
			<option value="<?php echo get_option('k2header_picture')?>"><?php echo $picture_title; ?></option>
			<option value="">----</option>
			<option value="No Picture">No Picture</option>
			<?php
			$picture_dir = @ dir($path);
			if ($picture_dir) {
				while(($file = $picture_dir->read()) !== false) {
					if (!preg_match('|^\.+$|', $file) && !preg_match('|^CVS$|', $file) && !preg_match('|^\.|', $file)) {
						$picture_files[] = $file;
					}
				}

				if ($picture_dir || $picture_files) {
					foreach ($picture_files as $picture_file) {
						echo "<option value=\"".$picture_file."\">".$picture_file."</option>";
					}
				}
			}
			?>
		</select>
		<p><small>Select the picture you want to show at the frontpage</small></p>
  </tr>
  
<tr valign="top">
	<th scope="row"><?php echo __('Background color'); ?></th>
	<td>
		<input type="text" name="backgroundcolor" value="<?php echo strtoupper(get_option('k2headerbackgroundcolor')); ?>">
		<p><small>Define the backgroundcolor for the header (default is #3371A3).</small></p>
</tr>
  
<tr valign="top">
	<th scope="row"><?php echo __('Set header text alignment'); ?></th>
	<td>
		<label for="k2upload_align">
    <select name="k2headertextalignment">
      <option <?php echo (get_option('k2headertextalignment') == 'left' ? 'selected' : ''); ?> value="left">Left</option>
      <option <?php echo (get_option('k2headertextalignment') == 'center' ? 'selected' : ''); ?> value="center">Center</option>
      <option <?php echo (get_option('k2headertextalignment') == 'right' ? 'selected' : ''); ?> value="right">Right</option>
    </select>
		<p><small>Set the alignment of the text over the header image.</small><p>
	</td>
</tr>
  
<tr valign="top">
	<th scope="row"><?php echo __('Set header text size'); ?></th>
	<td>
		<input type="text" size=2 name="k2headertextfontsize" value="<?php echo get_option('k2headertextfontsize'); ?>"> px.
		<p><small>Set the font size in 'px' of the text over the header image.</small><p>
	</td>
</tr>

<tr valign="top">
	<th scope="row"><?php echo __('Set header text color'); ?></th>
	<td>
		<label for="k2upload_color">
    <select name="k2headertextcolor">
      <option <?php echo (get_option('k2headertextcolor_bright') == get_option('k2headertextcolor') ? 'selected' : ''); ?> value="<?php echo get_option('k2headertextcolor_bright'); ?>">Bright - <?php echo get_option('k2headertextcolor_bright'); ?></option>
      <option <?php echo (get_option('k2headertextcolor_dark') == get_option('k2headertextcolor') ? 'selected' : ''); ?> value="<?php echo get_option('k2headertextcolor_dark'); ?>">Dark - <?php echo get_option('k2headertextcolor_dark'); ?></option>
    </select>
		<p><small>Set the color of the text over the header image.</small><p>
	</td>
</tr>

<tr valign="top">
    <th scope="row"><?php echo __('Define Bright & Dark text color'); ?></th>
    <td valign="top">
       <table border=0 cellspacing=0 cellpadding=2>
          <tr>
            <td>Bright Color</td>
            <td><input type="text" name="brightcolor" value="<?php echo get_option('k2headertextcolor_bright'); ?>"></td>
          </tr>
          <tr>
            <td>Dark Color</td>
            <td><input type="text" name="darkcolor" value="<?php echo get_option('k2headertextcolor_dark'); ?>"></td>
          </tr>
       </table>
    </td>
    <tr>
  </table> 
<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" /></p>

</form>
</div>
<div class="wrap">
 	<p style="text-align: center;"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://binarybonsai.com/wordpress/k2/features-and-plugins/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ) ?></p>
</div>

<?php
}

add_action('admin_menu', 'k2_picupload_admin');

function change_hpicture() {
	if (get_option('k2imagerandomfeature') == "yes") {
		$picture = findrandompicture();
	} else {
		$picture = get_option('k2header_picture');
	}	

	if ($picture != "") {
		if ($picture != "No Picture") {
			$pic = 'background: url('.get_bloginfo('template_url').'/images/headers/'.$picture.') no-repeat center center;';
		}
	echo '
		<style type="text/css">
			#header {
				'.$pic.'
				background-color: '.get_option(k2headerbackgroundcolor).';
				}

			#header h1 {
				text-align:'.get_option('k2headertextalignment').';
				font-size:'.get_option('k2headertextfontsize').'px;
				}
      		
			h1, h1 a,h1 a:visited, #header .description {
				color: '.get_option('k2headertextcolor').';
      			}

			.description {
				display: block !important;
				text-align:'.get_option('k2headertextalignment').';
				}
		</style>
	';
	}
}
	add_action('wp_head','change_hpicture');
?>
