<?php

function asides_sidebar_module($args) {
	extract($args);

	$k2asidescategory = get_option('k2asidescategory');

	if ( $k2asidescategory != '0') {
		echo $before_module . $before_title . $title . $after_title;
?>
		<span class="metalink"><a href="<?php bloginfo('url'); ?>/?feed=rss&amp;cat=<?php echo $k2asidescategory; ?>" title="<?php _e('RSS Feed for Asides','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>
		<div>
		<?php
			$asides_count = 1;
			$asides_posts = get_posts('category=' . $k2asidescategory . '&numberposts=' . sbm_get_option('num_posts'));

			foreach ($asides_posts as $aside) {
				setup_postdata($aside);
		?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class($asides_count++, true); ?>">
				<span>&raquo;&nbsp;</span><?php the_content(__('(more)','k2_domain')); ?>&nbsp;<span class="metalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php _e('Permanent Link to this aside','k2_domain'); ?>'>#</a></span>&nbsp;<span class="metalink"><?php comments_popup_link('0', '1', '%', '', ' '); ?></span><?php edit_post_link(__('edit','k2_domain'),'&nbsp;&nbsp;<span class="metalink">','</span>'); ?>
			</div>
		<?php } // End Asides loop ?>
		</div>
<?php
		echo $after_module;
	} // End Asides check
}

function asides_sidebar_module_control() {
	if (isset($_POST['asides_module_num_posts'])) {
		sbm_update_option('num_posts', $_POST['asides_module_num_posts']);
	}
	?>
		<p><label for="asides-module-num-posts"><?php _e('Number of posts:', 'k2_domain'); ?></label> <input id="asides-module-num-posts" name="asides_module_num_posts" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
}

register_sidebar_module('Asides module', 'asides_sidebar_module', 'sb-asides', array('num_posts' => 3));
register_sidebar_module_control('Asides module', 'asides_sidebar_module_control');
?>
