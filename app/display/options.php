<?php
	if ( K2_USING_STYLES ) {
		// Get the current K2 Style
		$current_style = get_option('k2style');
		$style_info = get_option('k2styleinfo');

		// Check that the styles folder exists
		$is_styles_dir = is_dir(K2_STYLES_PATH);

		// Get the scheme files
		$style_files = K2::get_styles();
	}

	// Check that the K2 folder has no spaces
	$dir_has_spaces = (strpos(TEMPLATEPATH, ' ') !== false);

	// Get the sidebar
	$column_number = get_option('k2columns');
	$column_options = array(
		1 => __('Single Column', 'k2_domain'),
		__('Two Columns', 'k2_domain'),
		__('Three Columns', 'k2_domain'),
		'dynamic' => __('Dynamic Columns', 'k2_domain')
	);

	// Get the asides category
	$asides_id = get_option('k2asidescategory');

	// Get the categories we might use for asides
	$asides_cats = get_categories('get=all');

	// Get the current header picture
	$current_header_image = get_option('k2headerimage');

	// Get the header pictures
	$header_images = K2Header::get_header_images();

?>

<script type="text/javascript" charset="utf-8">
//<![CDATA[
	smartPosition('.configstuff');
//]]>
</script>

<?php if ( isset($_REQUEST['updated']) ): ?>
<div id="message2" class="updated fade">
	<p><?php _e('K2 Options have been updated', 'k2_domain'); ?></p>
</div>
<?php endif; ?>

<?php if ( isset($_REQUEST['configela']) ): ?>
<div id="message2" class="updated fade">
	<p><?php _e('The Extended Live Archives plugin has been setup for use with K2', 'k2_domain'); ?></p>
</div>
<?php endif; ?>

<div class="wrap">
	<?php if ( K2_USING_STYLES and !$is_styles_dir ): ?>
		<div class="error"><small>
		<?php printf(__('<p>The directory: <code>%s</code>, needed to store custom styles is missing.</p><p>For you to be able to use custom styles, you need to add this directory.</p>', 'k2_domain'), K2_STYLES_PATH ); ?>
		</small></div>
	<?php endif; ?>

	<?php if ($dir_has_spaces): ?>
		<div class="error"><small>
		<?php printf( __('<p>The K2 directory: <code>%s</code>, contains spaces. For K2 to function properly, you will need to remove the spaces from the directory name.</p>', 'k2_domain'), TEMPLATEPATH ); ?>
		</small></div>
	<?php endif; ?>


	<form action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>" method="post">
		<?php wp_nonce_field('k2options'); ?>

		<div class="configstuff">

			<div class="savebutton">
				<input type="submit" id="save" name="save" class="button" value="<?php echo attribute_escape(__('Save Changes', 'k2_domain')); ?>" />
			</div><!-- .savebutton -->


			<div class="container">
				<h3><label for="k2-columns"><?php _e('Columns', 'k2_domain'); ?></label></h3>

				<p class="main-option">
					<select id="k2-columns" name="k2[columns]">
						<?php foreach ( $column_options as $option => $label ): ?>
						<option value="<?php echo $option; ?>" <?php selected($column_number, $option); ?>><?php echo $label; ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="description">
					<?php _e('Select Dynamic Columns for K2 to dynamically reduce the number of columns depending on user\'s browser width.', 'k2_domain'); ?>
				</p>
			</div><!-- .container -->


			<div class="container">
				<h3><label for="k2-advnav"><?php _e('Advanced Navigation','k2_domain'); ?></label></h3>

				<p class="main-option"><input id="k2-advnav" name="k2[advnav]" type="checkbox" value="1" <?php checked('1', get_option('k2livesearch')); ?> />
				<!--<label for="k2-advnav"><?php _e('Enable Advanced Navigation','k2_domain'); ?></label>--></p>

				<p class="description"><?php _e('Seamlessly search and navigate old posts.','k2_domain'); ?></p>
			</div><!-- .container -->


			<div class="container">
				<h3><label for="k2-archives"><?php _e('Archives Page', 'k2_domain'); ?></label></h3>

				<p class="main-option"><input id="k2-archives" name="k2[archives]" type="checkbox" value="add_archive" <?php checked('1', get_option('k2archives')); ?> />
				<!--<label for="k2-archives"><?php _e('Enable Archives Page', 'k2_domain'); ?></label>--></p>

				<p class="description"><?php _e('Installs a pre-made archives page.', 'k2_domain'); ?></p>

				<?php if ( function_exists('af_ela_set_config') ): ?>
					<p class="center">
						<input id="configela" name="configela" class="button-secondary" type="submit" value="<?php echo attribute_escape(__('Configure Extended Live Archives for K2', 'k2_domain')); ?>" /></p>
				<?php endif; ?>
			</div><!-- .container -->


			<div class="container">
				<h3><label for="k2-livecommenting"><?php _e('Live Commenting', 'k2_domain'); ?></label></h3>

				<p class="main-option"><input id="k2-livecommenting" name="k2[livecommenting]" type="checkbox" value="1" <?php checked('1', get_option('k2livecommenting')); ?> />
				<!--<label for="k2-livecommenting"><?php _e('Enable Live Commenting', 'k2_domain'); ?></label>--></p>
				
				<p class="description"><?php _e('Submit comments without reloading the page.', 'k2_domain'); ?></p>
			</div><!-- .container -->


			<div class="container">
				<h3><?php _e('Asides', 'k2_domain'); ?></h3>

				<p class="main-option">
					<select id="k2-asidescategory" name="k2[asidescategory]">
						<option value="0" <?php selected($asides_id, '0'); ?>><?php _e('Off', 'k2_domain'); ?></option>

						<?php foreach ( $asides_cats as $cat ): ?>
						<option value="<?php echo attribute_escape($cat->cat_ID); ?>" <?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="description"><?php _e('Aside posts are styled differently and can be placed on the sidebar.', 'k2_domain'); ?></p>

			</div><!-- .container -->


			<?php if ( K2_USING_STYLES and $is_styles_dir ): ?>
			<div class="container">
				<h3><?php _e('Style', 'k2_domain'); ?></h3>

				<p class="main-option">
					<select id="k2-style" name="k2[style]">
						<option value="" <?php selected($current_style, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>

						<?php foreach($style_files as $style): ?>
						<option value="<?php echo attribute_escape($style['path']); ?>" <?php selected($current_style, $style['path']); ?>>
						<?php if ( '' == $style['stylename'] ): echo basename($style['path']); else: echo $style['stylename']; endif; if ( '' != $style['version'] ): echo ' (' . $style['version'] . ')'; endif; ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="description">
					<?php _e('No need to edit core files, K2 is highly customizable.', 'k2_domain'); ?>
					<a href="http://code.google.com/p/kaytwo/wiki/K2CSSandCustomCSS"><?php _e('Read&nbsp;more.', 'k2_domain'); ?></a>
				</p>
			</div><!-- .container -->
			<?php endif; ?>


			<div class="container">
				<h3><?php _e('Header', 'k2_domain'); ?></h3>

				<p class="description"><?php
					printf(
						__('The current header size is <strong>%1$s px by %2$s px</strong>. Use %3$s to customize the header.', 'k2_domain'),
						K2Header::get_header_width(),
						empty($style_info['header_height'])? K2_HEADER_HEIGHT : $style_info['header_height'],
						'<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'k2_domain') . '</a>'
					); ?></p>

				<dl class="form-list">
					<dt>
						<label for="k2-header-image"><?php _e('Select an Image:', 'k2_domain'); ?></label>
					</dt>
					<dd class="secondary-option">
						<select id="k2-header-image" name="k2[headerimage]">
							<option value="" <?php selected($current_header_image, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>
							<option value="random" <?php selected($current_header_image, 'random'); ?>><?php _e('Random', 'k2_domain'); ?></option>
							<?php foreach($header_images as $image): ?>
							<option value="<?php echo attribute_escape($image); ?>" <?php selected($current_header_image, $image); ?>><?php echo basename($image); ?></option>
							<?php endforeach; ?>
						</select>
					</dd>

					<dt>
						<label for="k2-blog-tab"><?php _e('Rename the \'Blog\' tab:', 'k2_domain'); ?></label>
					</dt>
					<dd class="secondary-option">
						<input id="k2-blog-tab" name="k2[blogornoblog]" type="text" value="<?php echo attribute_escape(get_option('k2blogornoblog')); ?>" />
					</dd>
				</dl>
			</div><!-- .container -->
				

			<?php /* K2 Hook */ do_action('k2_display_options'); ?>
		</div><!-- .configstuff -->

</div>

<div class="uninstall">


		<div class="configstuff">
			<h3><?php _e('Uninstall K2', 'k2_domain'); ?></h3>

			<script type="text/javascript">
			function confirmUninstall() {
				if (confirm("<?php _e('Delete your K2 settings?', 'k2_domain'); ?>") == true) {
					return true;
				} else {
					return false;
				}
			}
			</script>


			<p class="description">
				<?php _e('Remove all K2 settings and revert WordPress to its default theme. No files are deleted.', 'k2_domain'); ?>
			</p>

			<p class="center">
				<input id="uninstall" name="uninstall" type="submit" onClick="return confirmUninstall();" value="<?php echo attribute_escape(__('Reset and Uninstall K2', 'k2_domain')); ?>" class="button-secondary" />
			</p>
		</div>

</div>

	</form>
</div>
