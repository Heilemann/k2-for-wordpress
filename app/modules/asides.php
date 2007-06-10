<?php

function asides_sidebar_module($args) {
	global $post;

	extract($args);

	$k2asidescategory = get_option('k2asidescategory');

	if ( $k2asidescategory != '0') {
		echo $before_module . $before_title . $title . $after_title;
?>
		<span class="metalink"><a href="<?php bloginfo('url'); ?>/?feed=rss&amp;cat=<?php echo $k2asidescategory; ?>" title="<?php _e('RSS Feed for Asides','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>
		<div>
			<?php
				$asides_count = 1;
				$asides = new WP_Query('cat=' . $k2asidescategory . '&showposts=' . sbm_get_option('num_posts'));

				foreach ($asides->posts as $post) {
					setup_postdata($post);
			?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class($asides_count++, true); ?>">
				<span>&raquo;&nbsp;</span><?php the_content(__('(more)','k2_domain')); ?>&nbsp;<span class="metalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php comments_number('(0)','(1)','(%)'); ?></a></span><?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
			</div>
			<?php } /* end asides loop */ ?>
		</div>
<?php
		echo $after_module;
	} /* end asides check */
}

function asides_sidebar_module_control() {
	if (isset($_POST['asides_module_num_posts'])) {
		sbm_update_option('num_posts', $_POST['asides_module_num_posts']);
	}
	?>
		<p><label for="asides-module-num-posts"><?php _e('Number of posts:', 'k2_domain'); ?></label> <input id="asides-module-num-posts" name="asides_module_num_posts" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
}

register_sidebar_module('Asides', 'asides_sidebar_module', 'sb-asides', array('num_posts' => 3));
register_sidebar_module_control('Asides', 'asides_sidebar_module_control');
?>
