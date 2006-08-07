<?php
	global $wpdb;

	// Update
	$update = updater::k2update();

	// Get the current K2 scheme
	$scheme_name = get_option('k2scheme');
	$scheme_title = $scheme_name !== false ? $scheme_name : __('No Scheme','k2_domain');

	// Get the scheme files
	$scheme_files = array();
	$schemes_path = TEMPLATEPATH . '/styles/';

	// Scan for themes
	if(($dir = @dir($schemes_path)) !== false) {
		while(($file = $dir->read()) !== false) {
			if(is_dir($schemes_path . $file) and !preg_match('/^\.+$/i', $file)) {
				$schemes_subpath = $schemes_path . $file . '/';

				if(($sub_dir = @dir($schemes_subpath)) !== false) {
					while(($sub_file = $sub_dir->read()) !== false) {
						if(is_file($schemes_subpath . $sub_file) and preg_match('/\.css$/i', $sub_file)) {
							$scheme_files[] = $file . '/' . $sub_file;
						}
					}

					$sub_dir->close();
				}
			} elseif(is_file($schemes_path . $file) and preg_match('/\.css$/i', $file)) {
				$scheme_files[] = $file;
			}
		}

		$dir->close();
	}

	// Get the asides category
	$asides_id = get_option('k2asidescategory');
	$asides_title = $asides_id != 0 ? $wpdb->get_var("SELECT cat_name from $wpdb->categories WHERE cat_ID = $asides_id LIMIT 1") : __('No Asides','k2_domain');

	// Get the categories we might use for asides
	$asides_cats = $wpdb->get_results("SELECT cat_ID, cat_name FROM $wpdb->categories");
?>

<?php if(isset($_POST['configela'])) { ?>
<div class="updated">
	<p><?php _e('ELA Options for K2 have been set','k2_domain'); ?></p>
</div>
<?php } ?>

<?php if(isset($_POST['Submit'])) { ?>
<div class="updated">
	<p><?php _e('K2 Options have been updated','k2_domain'); ?></p>
</div>
<?php } ?>

<?php if (isset($_POST['uninstall'])) : ?>
	<div class="updated">
		<p><?php _e('K2 Options have been uninstalled','k2_domain'); ?></p>
	</div>
<?php endif;?>

<div class="wrap">
	<h2><?php _e('K2 Options','k2_domain'); ?></h2>

	<form name="dofollow" action="" method="post">
		<input type="hidden" name="action" value="<?php echo($update); ?>" />
		<input type="hidden" name="page_options" value="'dofollow_timeout'" />

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options','k2_domain'); ?> &raquo;" />
		</p>

		<table width="700px" cellspacing="2" cellpadding="5" class="editform">
			<tr valign="top">
				<th scope="row"><?php _e('K2 Scheme','k2_domain'); ?></th>

				<td>
					<select name="k2scheme_file" id="k2scheme_file" style="width: 300px;">
						<option value=""<?php selected($scheme_name, ''); ?>><?php _e('No Scheme','k2_domain'); ?></option>

						<?php foreach($scheme_files as $scheme_file) { ?>
						<option value="<?php echo($scheme_file); ?>"<?php selected($scheme_name, $scheme_file); ?>><?php echo($scheme_file); ?></option>
						<?php } ?>
					</select>
			
					<p><small><?php _e('Choose the Custom Style you would like to use on this site.','k2_domain'); ?></small></p>

					<p><?php _e('Formatting to be used for displaying the style info. Use: <strong>style</strong> for style name, <strong>stylelink</strong> for the style\'s homepage, <strong>author</strong> for author, <strong>site</strong> for author\'s site, <strong>version</strong> for version and <strong>comments</strong> for style comments.','k2_domain'); ?></p>

					<p><textarea name="format" id="format" rows="3" cols="80"><?php echo(stripslashes(get_option('k2styleinfo_format'))); ?></textarea></p>

					<p><strong><?php _e('Outputs:','k2_domain'); ?></strong> <?php k2styleinfo_demo(); ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="20%"><?php _e('Live Search','k2_domain'); ?></th>

				<td>
					<input name="livesearch" id="live-search" type="radio" value="1" <?php checked('1', get_option('k2livesearch')); ?> /> 
					<label for="live-search"><?php _e('Enable Livesearch (default)','k2_domain'); ?></label><br />

					<input name="livesearch" id="classic-search" type="radio" value="0" <?php checked('0', get_option('k2livesearch')); ?> /> 
					<label for="classic-search"><?php _e('Disable Livesearch','k2_domain'); ?></label>

					<p><small><?php printf(__('Livesearch is a javascript powered search-as-you-type solution. %s.','k2_domain'), '<a href="http://blog.bitflux.ch/wiki/LiveSearch">' .__('Would you like to know more?','k2_domain') . '</a>' ) ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="20%"><?php _e('Rolling Archives','k2_domain'); ?></th>

				<td>
					<input name="rollingarchives" id="rollingarchives-on" type="radio" value="1" <?php checked('1', get_option('k2rollingarchives')); ?> /> 
					<label for="rollingarchives-on"><?php _e('Enable Rolling Archives (default)','k2_domain'); ?></label><br />

					<input name="rollingarchives" id="rollingarchives-off" type="radio" value="0" <?php checked('0', get_option('k2rollingarchives')); ?> /> 
					<label for="rollingarchives-off"><?php _e('Disable Rolling Archives','k2_domain'); ?></label>

					<p><small><?php _e('Rolling Archives allow you to page back into the archives without reloading the page.','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" width="20%"><?php _e('AJAX Commenting','k2_domain'); ?></th>

				<td>
					<input name="livecommenting" id="live-commenting" type="radio" value="1" <?php checked('1', get_option('k2livecommenting')); ?> /> 
					<label for="live-commenting"><?php _e('Enable AJAX Commenting (default)','k2_domain'); ?></label><br />

					<input name="livecommenting" id="classic-commenting" type="radio" value="0" <?php checked('0', get_option('k2livecommenting')); ?> /> 
					<label for="classic-commenting"><?php _e('Disable AJAX Commenting','k2_domain'); ?></label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Width Type','k2_domain'); ?></th>

				<td>
					<input name="widthtype" id="fixed" type="radio" value="1" <?php checked('1', get_option('k2widthtype')); ?> /> 
					<label for="fixed"><?php _e('Fixed Width (default)','k2_domain'); ?></label><br />

					<input name="widthtype" id="flexible" type="radio" value="0" <?php checked('0', get_option('k2widthtype')); ?> /> 
					<label for="flexible"><?php _e('Flexible Width','k2_domain'); ?></label>

					<p><small><?php _e('Flexible Width has the K2 page adjust itself according to the width of the browser, up to a maximum and down to a minimum width, so as to not entirely break readability at very high resolutions. Fixed width fixes K2\'s page width at 780px.','k2_domain'); ?><br /><?php _e('<em><strong>Beta Note:</strong> Doesn\'t work in Internet Explorer as far as I know. Suggestions are welcome.</em>','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Asides','k2_domain'); ?></th>

				<td>
					<input name="asidesposition" id="primary-asides" type="radio" value="0" <?php checked('0', get_option('k2asidesposition')); ?> />
					<label for="primary-asides"><?php _e('Inline Asides','k2_domain'); ?></label><br />

					<input name="asidesposition" id="secondary-asides" type="radio" value="1" <?php checked('1', get_option('k2asidesposition')); ?> />
					<label for="secondary-asides"><?php _e('Sidebar Asides','k2_domain'); ?></label>

					<p><small><?php _e('Determines whether Asides (if they are active) are shown inline or on the sidebar.','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Asides Category','k2_domain'); ?></th>

				<td>
					<select name="asides_text" id="asides_text" style="width: 300px;">
						<option value="0"<?php selected($asides_id, '0'); ?>><?php _e('No Asides','k2_domain'); ?></option>

						<?php foreach ($asides_cats as $cat) { ?>
						<option value="<?php echo($cat->cat_ID); ?>"<?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
						<?php } ?>
					</select>

					<p><small><?php printf(__('Just select a category and it will be displayed using %s.','k2_domain'), '<a href="http://photomatt.net/2004/05/19/asides/">' .__('Matt\'s Asides Technique','k2_domain') .'</a>' ); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Asides Number','k2_domain'); ?></th>

				<td>
					<input name="asidesnumber" id="asidesnumber" type="text" value="<?php echo(get_option('k2asidesnumber')); ?>" size="2" /> 

					<p><small><?php _e('Set the number of Asides to show in the Sidebar. Defaults to 3.','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('About Text','k2_domain'); ?></th>

				<td>
					<textarea name="about_text" style="width: 98%;" rows="5" id="about_text"><?php echo(stripslashes(get_option('k2aboutblurp'))); ?></textarea>

					<p><small><?php _e('Enter a blurp about yourself here, and it will show up on the frontpage. Deleting the content disables the about blurp.','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Blog?','k2_domain'); ?></th>

				<td>
					<input name="blog_text" style="width: 98%;" id="blog_text" value="<?php echo(stripslashes(get_option('k2blogornoblog'))); ?>">

					<p><small><?php _e('The text on the first tab in the header navigation.','k2_domain'); ?></small></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e('Archives Page','k2_domain'); ?></th>

				<td>
					<input name="archives" id="add-archive" type="checkbox" value="add_archive" <?php checked('add_archive', get_option('k2archives')); ?> />
					<label for="add-archives"><?php _e('Enable the K2 Archives page','k2_domain'); ?></label>

					<p><small><?php _e('Enabling this checkbox will create an Archives Page, which will show up in your blog menu as the first page.','k2_domain'); ?></small></p>
				</td>
			</tr>

			<?php if(function_exists('af_ela_set_config')): ?>
			<tr valign="top">
				<th scope="row"><?php _e('Set Extended Live Archive','k2_domain'); ?></th>

				<td>
					<input name="configela" id="configela" class="button" type="submit" value="<?php _e('Setup ELA for K2 Archives page','k2_domain') ?>" />

					<p><small><?php printf(__('Set the options of Arnaud\'s brilliant %s for K2.','k2_domain'), '<a href="http://www.sonsofskadi.net/index.php/extended-live-archive/" title="' .__('Find out more about ELA','k2_domain') . '">Extended Live Archives</a>' ) ?></small></p>
				</td>
			</tr>	
			<?php endif; ?>

			<tr valign="top">
				<th scope="row"><?php _e('Uninstall K2','k2_domain'); ?></th>

				<td>
					<input name="uninstall" id="uninstall" class="button" type="submit" value="<?php _e('Uninstall K2','k2_domain'); ?>" />

					<p><small><?php _e('Having trouble with K2, or you have decided to "go another direction" with your site style? Fine, we don\'t care... be that way. I promised myself I wouldn\'t cry...','k2_domain'); ?></small></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options','k2_domain'); ?> &raquo;" />
		</p>
	</form>
</div>

<div class="wrap">
 	<p style="text-align: center;"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://k2.stikipad.com/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ); ?></p>
</div>
