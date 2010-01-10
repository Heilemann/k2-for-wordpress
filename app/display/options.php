<?php

	// Check that the K2 folder has no spaces
	$dir_has_spaces = (strpos(TEMPLATEPATH, ' ') !== false);

	// Get the asides category
	$asides_id = get_option('k2asidescategory');

	// Get the categories we might use for asides
	$asides_cats = get_categories('get=all');

	// Get post meta format
	$entrymeta1 = get_option('k2entrymeta1');
	if ( empty($entrymeta1) ) {
		$entrymeta1 = __('Published by %author% on %date% in %categories%. %comments%. %tags%.', 'k2');
	}
?>

<div class="wrap">
	<?php if ( isset($_GET['defaults']) ): ?>
	<div class="updated fade">
		<p><?php _e('K2 has been restored to default settings.', 'k2'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ( isset($_GET['saved']) ): ?>
	<div class="updated fade">
		<p><?php _e('K2 Options have been updated', 'k2'); ?></p>
	</div>
	<?php endif; ?>

	<?php if ($dir_has_spaces): ?>
		<div class="error">
		<?php printf( __('The K2 directory: <strong>%s</strong>, contains spaces. For K2 to function properly, you will need to remove the spaces from the directory name.', 'k2'), TEMPLATEPATH ); ?>
		</div>
	<?php endif; ?>

	<?php do_action('k2_options_top'); ?>

	<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
	<h2><?php _e('K2 Options', 'k2'); ?></h2>
	<form action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post" id="k2-options">
		<ul class="options-list">
			<li>
				<h3 class="main-label"><label for="k2-advnav"><?php _e('Advanced Navigation', 'k2'); ?></label></h3>

				<p class="main-option">
					<input id="k2-advnav" name="k2[advnav]" type="checkbox" value="1" <?php checked('1', get_option('k2livesearch')); ?> />
				</p>

				<p class="description"><?php _e('Seamlessly search and navigate old posts.', 'k2'); ?></p>

				<ul class="advanced-option">
					<li>
						<input id="k2-animations" name="k2[animations]" type="checkbox" value="1" <?php checked('1', get_option('k2animations')); ?> />
						<label for="k2-animations"><?php _e('JavaScript Animations', 'k2'); ?></label>
					</li>
					<li>
						<h4><label for="k2ajax"><?php _e('Ajax Success JavaScript', 'k2'); ?></label></h4>
						<p class="description"><?php _e('JavaScript code that will be executed whenever Advanced Navigation is dynamically loaded.', 'k2'); ?></p>
						<textarea id="k2ajax" name="k2[ajaxdonejs]" rows="8" cols="80" class="codepress javascript"><?php form_option('k2ajaxdonejs'); ?></textarea>
					</li>
				</ul>
			</li>
			<li>
				<h3 class="main-label"><label for="k2-archives"><?php _e('Archives Page', 'k2'); ?></label></h3>

				<p class="main-option">
					<input id="k2-archives" name="k2[archives]" type="checkbox" value="add_archive" <?php checked('1', get_option('k2archives')); ?> />
				</p>

				<p class="description"><?php _e('Installs a pre-made archives page.', 'k2'); ?></p>
			</li>
			<li>
				<h3 class="main-label"><label for="k2-asidescategory"><?php _e('Asides', 'k2'); ?></label></h3>

				<p class="main-option">
					<select id="k2-asidescategory" name="k2[asidescategory]">
						<option value="0" <?php selected($asides_id, '0'); ?>><?php _e('Off', 'k2'); ?></option>

						<?php foreach ( $asides_cats as $cat ): ?>
						<option value="<?php echo esc_attr($cat->cat_ID); ?>" <?php selected($asides_id, $cat->cat_ID); ?>><?php echo($cat->cat_name); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="description"><?php _e('Aside posts are styled differently and can be placed on the sidebar.', 'k2'); ?></p>
			</li>

			<li>
				<h3><?php _e('Post Entry', 'k2'); ?></h3>

				<p class="description">
					<?php _e('Use the following keywords: %author%, %categories%, %comments%, %date%, %tags% and %time%. <!--You can also use third-party shortcodes.-->', 'k2'); ?>
				</p>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="k2-post-meta-1"><?php _e('Top Meta:', 'k2'); ?></label>
							</th>
							<td>
								<input id="k2-post-meta-1" name="k2[entrymeta1]" type="text" value="<?php form_option('k2entrymeta1'); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="k2-post-meta-2"><?php _e('Bottom Meta:', 'k2'); ?></label>
							</th>
							<td>
								<input id="k2-post-meta-2" name="k2[entrymeta2]" type="text" value="<?php form_option('k2entrymeta2'); ?>" />
							</td>
						</tr>
					</tbody>
				</table>


				<div id="meta-preview" class="postbox">
					<h3 class="handle"><span><?php _e('Preview', 'k2'); ?></span></h3>
					<?php
						query_posts('showposts=1&what_to_show=posts&order=desc');
						if ( have_posts() ): the_post();
					?>
					<div id="post-<?php the_ID(); ?>" class="inside">
						<div class="post-header">
							<h5 class="post-title"><a href="#" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"', 'k2'), esc_html(strip_tags(the_title('', '', false)), 1) ); ?>'><?php the_title(); ?></a></h5>

							<div class="post-meta">
								<?php k2_entry_meta(1); ?>
							</div> <!-- .post-meta -->
						</div> <!-- .post-header -->

						<div class="post-content">
							<?php the_excerpt(); ?>
						</div> <!-- .post-content -->

						<div class="post-footer">
							<div class="post-meta">
								<?php k2_entry_meta(2); ?>
							</div><!-- .post-meta -->
						</div><!-- .post-footer -->
					</div> <!-- #post-ID -->
					<?php endif; ?>
				</div>
			</li>

			<?php /* K2 Hook */ do_action('k2_display_options'); ?>
		</ul>

		<div class="submit">
			<?php wp_nonce_field('k2options'); ?>
			<input type="hidden" name="k2-options-submit" value="k2-options-submit" />

			<input type="submit" id="save" name="save" class="button-primary" value="<?php esc_attr_e('Save Changes', 'k2'); ?>" />

			<input type="submit" name="restore-defaults" id="restore-defaults" onClick="return confirmDefaults();" value="<?php esc_attr_e('Revert to K2 Defaults', 'k2'); ?>" class="button-secondary" />
			<input type="submit" name="default-widgets" id="default-widgets-btn" class="button-secondary" value="<?php esc_attr_e('Install a Default Set of Widgets', 'k2'); ?>" />
		</div><!-- .submit -->
	</form>

</div><!-- .wrap -->
