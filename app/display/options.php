<?php
	if ( K2_USING_STYLES ) {
		// Get the current K2 Style
		$current_style = get_option('k2style');
		$style_info = get_option('k2styleinfo');

		// Check that the styles folder exists
		$is_styles_dir = is_dir(K2_STYLES_DIR);

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

	// Get post meta format
	$entrymeta1 = get_option('k2entrymeta1');
	if ( empty($entrymeta1) ) {
		$entrymeta1 = __('Published by %author% on %date% in %categories%. %comments% %tags%', 'k2_domain');
	}
?>

<div class="wrap">
	<?php if ( isset($_REQUEST['restore-defaults']) ): ?>
	<div class="updated fade">
		<p><?php _e('K2 has been restored to default settings.', 'k2_domain'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ( isset($_REQUEST['updated']) ): ?>
	<div class="updated fade">
		<p><?php _e('K2 Options have been updated', 'k2_domain'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ( isset($_REQUEST['configela']) ): ?>
	<div class="updated fade">
		<p><?php _e('The Extended Live Archives plugin has been setup for use with K2', 'k2_domain'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ( K2_USING_STYLES and !$is_styles_dir ): ?>
		<div class="error">
		<?php printf(__('The directory: <strong>%s</strong>, needed to store custom styles is missing. For you to be able to use custom styles, you need to add this directory.', 'k2_domain'), K2_STYLES_DIR ); ?>
		</div>
	<?php endif; ?>

	<?php if ($dir_has_spaces): ?>
		<div class="error">
		<?php printf( __('The K2 directory: <strong>%s</strong>, contains spaces. For K2 to function properly, you will need to remove the spaces from the directory name.', 'k2_domain'), TEMPLATEPATH ); ?>
		</div>
	<?php endif; ?>

	<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
	<h2><?php _e('K2 Options', 'k2_domain'); ?></h2>
	<form action="<?php echo attribute_escape($_SERVER['REQUEST_URI']); ?>" method="post">
		<?php wp_nonce_field('k2options'); ?>

 			<div class="container">
				<h3><label for="k2-sidebar-manager"><?php _e('Widgets Manager', 'k2_domain'); ?></label></h3>

				<p class="main-option"><input id="k2-sidebar-manager" name="k2[sidebarmanager]" type="checkbox" value="1" <?php checked('1', get_option('k2sidebarmanager')); ?> />
				<!--<label for="k2-sidebarmanager"><?php _e('Enable K2\'s Sidebar Manager', 'k2_domain'); ?></label>--></p>
				<p class="description"><?php _e('K2 has a neat sidebar system that allows you to control where/when each widget can appear.', 'k2_domain'); ?></p>
				<?php if ( defined('K2_LOAD_SBM') ): ?>
					<p class="description alert">Please disable the K2 Disable Widgets plugin to use the new Widgets Manager.</p>
				<?php endif; ?>
			
				<p class="hidden">
					<input type="submit" name="sbm-defaults" id="sbm-defaults" class="button-secondary" value="<?php echo attribute_escape( __('Revert to Widgets Manager Defaults', 'k2_domain') ); ?>" />

					<input type="submit" name="sbm-upgrade" id="sbm-upgrade" class="button-secondary" value="<?php echo attribute_escape( __('Import old SBM settings', 'k2_domain') ); ?>" />
				</p>
			</div><!-- .container -->

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
				<p class="secondary">
					<span>
						<input id="k2-animations" name="k2[animations]" type="checkbox" value="1" <?php checked('1', get_option('k2animations')); ?> />
						<label for="k2-animations"><?php _e('JavaScript Animations', 'k2_domain'); ?></label>
					</span>
				</p>
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
				<h3><label for="k2-asidescategory"><?php _e('Asides', 'k2_domain'); ?></label></h3>

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
				<h3><label for="k2-style"><?php _e('Style', 'k2_domain'); ?></label></h3>

				<p class="main-option">
					<select id="k2-style" name="k2[style]">
						<option value="" <?php selected($current_style, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>

						<?php foreach( $style_files as $style ): ?>
						<option value="<?php echo attribute_escape($style['path']); ?>" <?php selected($current_style, $style['path']); ?>>
						<?php
							if ( ! empty($style['stylename']) ) {
								echo $style['stylename'];
								
								if ( ! empty($style['version']) ) {
									echo ' ' . $style['version'];
								}
							} else {
								echo $style['path'];
							}
						?>
						</option>
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

				<p class="description">
				<?php
					printf( __('The current header size is <strong>%1$s px by %2$s px</strong>.', 'k2_domain'),
						K2Header::get_header_width(),
						empty($style_info['header_height'])? K2_HEADER_HEIGHT : $style_info['header_height']
					);

					if ( extension_loaded('gd') and function_exists('gd_info') ) {
						printf( __(' Use %s to customize the header.', 'k2_domain'),
							'<a href="themes.php?page=custom-header">' . __('Custom Image Header', 'k2_domain') . '</a>'
						);
					}
					?>
				</p>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="k2-header-image"><?php _e('Select an Image:', 'k2_domain'); ?></label>
							</th>
							<td>
								<select id="k2-header-image" name="k2[headerimage]">
									<option value="" <?php selected($current_header_image, ''); ?>><?php _e('Off', 'k2_domain'); ?></option>
									<option value="random" <?php selected($current_header_image, 'random'); ?>><?php _e('Random', 'k2_domain'); ?></option>
									<?php foreach($header_images as $image): ?>
										<?php if ( is_numeric($image) ): ?>
											<option value="<?php echo attribute_escape($image); ?>" <?php selected($current_header_image, $image); ?>><?php echo basename( get_attached_file($image) ); ?></option>
										<?php else: ?>
											<option value="<?php echo attribute_escape($image); ?>" <?php selected($current_header_image, $image); ?>><?php echo basename($image); ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="k2-blog-tab"><?php _e('Rename the \'Blog\' tab:', 'k2_domain'); ?></label>
							</th>
							<td>
								<input id="k2-blog-tab" name="k2[blogornoblog]" type="text" value="<?php echo attribute_escape(get_option('k2blogornoblog')); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</div><!-- .container -->
				

			<div class="container">
				<h3><?php _e('Post Entry', 'k2_domain'); ?></h3>

				<p class="description">
					<?php _e('Use the following keywords: %author%, %categories%, %comments%, %date%, %tags% and %time%.', 'k2_domain'); ?>
				</p>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="k2-entry-meta-1"><?php _e('Top Meta:', 'k2_domain'); ?></label>
							</th>
							<td>
								<input id="k2-entry-meta-1" name="k2[entrymeta1]" type="text" value="<?php echo attribute_escape( get_option('k2entrymeta1') ); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="k2-entry-meta-2"><?php _e('Bottom Meta:', 'k2_domain'); ?></label>
							</th>
							<td>
								<input id="k2-entry-meta-2" name="k2[entrymeta2]" type="text" value="<?php echo attribute_escape( get_option('k2entrymeta2') ); ?>" />
							</td>
						</tr>
					</tbody>
				</table>

				<div id="meta-preview">
					<h4><?php _e('Preview', 'k2_domain'); ?></h4>
					<?php
						query_posts('showposts=1&what_to_show=posts&order=desc');
						if ( have_posts() ): the_post();
					?>
					<div id="post-<?php the_ID(); ?>">
						<div class="entry-head">
							<h5 class="entry-title"><a href="#" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php the_title(); ?></a></h5>

							<div class="entry-meta">
								<?php k2_entry_meta(1); ?>
							</div> <!-- .entry-meta -->
						</div> <!-- .entry-head -->

						<div class="entry-content">
							<?php the_excerpt(); ?>
						</div> <!-- .entry-content -->

						<div class="entry-foot">
							<div class="entry-meta">
								<?php k2_entry_meta(2); ?>
							</div><!-- .entry-meta -->
						</div><!-- .entry-foot -->
					</div> <!-- #post-ID -->
					<?php endif; ?>
				</div>
			</div><!-- .container -->


			<?php /* K2 Hook */ do_action('k2_display_options'); ?>


		<div class="submit">
			<input type="button" name="advanced" id="advanced" value="<?php echo attribute_escape( __('Advanced Options', 'k2_domain') ); ?>" class="button-secondary advanced" />
			<input type="submit" name="restore-defaults" id="restore-defaults" onClick="return confirmDefaults();" value="<?php echo attribute_escape( __('Revert to K2 Defaults', 'k2_domain') ); ?>" class="button-secondary" />
			<input type="submit" id="save" name="save" class="button-primary" value="<?php echo attribute_escape( __('Save Changes', 'k2_domain') ); ?>" />
		</div><!-- .options-footer -->
	</form>

</div><!-- .wrap -->
