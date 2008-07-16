<?php

function latest_posts_sidebar_module($args) {
	extract($args);
	global $post;

	$query = 'showposts='.sbm_get_option('num_posts');

	$k2asidescategory = get_option('k2asidescategory');
	if ( ($k2asidescategory != '0') and (sbm_get_option('hide_asides')) ) {
		$query .= '&cat=-' . $k2asidescategory;
	}

	// backup current post
	$post_backup = $post;

	echo($before_module . $before_title . $title . $after_title);
	?>
	<a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('RSS Feed for Blog Entries','k2_domain'); ?>" class="feedlink"><span><?php _e('RSS','k2_domain'); ?></span></a>
		<ul>
		<?php $latest = new WP_Query($query); while ($latest->have_posts()): $latest->the_post(); ?>
			<li><a href="<?php the_permalink(); ?>" title="<?php echo wp_specialchars(strip_tags(the_title('', '', false)), 1); ?>"><?php the_title(); ?></a></li>
		<?php endwhile; ?>
		</ul>
	<?php
	echo($after_module);

	// restore current post
	$post = $post_backup;
}

function latest_posts_sidebar_module_control() {
	if (isset($_POST['latest_posts_module'])) {
		sbm_update_option('hide_asides', isset($_POST['latest_posts_module']['hide_asides']));

		if (isset($_POST['latest_posts_module']['num_posts'])) {
			sbm_update_option('num_posts', $_POST['latest_posts_module']['num_posts']);
		}
	}
	?>
		<p><label for="latest-posts-module-num-posts"><?php _e('Number of posts:', 'k2_domain'); ?></label> <input id="latest-posts-module-num-posts" name="latest_posts_module[num_posts]" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>

		<p><label for="latest-posts-module-hide-asides"><?php _e('Exclude Asides from list:', 'k2_domain'); ?></label> <input id="latest-posts-module-hide-asides" name="latest_posts_module[hide_asides]" type="checkbox"<?php if(sbm_get_option('hide_asides')) { ?> checked="checked"<?php } ?> /></p>
	<?php
}

register_sidebar_module('Latest Posts', 'latest_posts_sidebar_module', 'sb-latest', array( 'num_posts' => 10, 'hide_asides' => false ));
register_sidebar_module_control('Latest Posts', 'latest_posts_sidebar_module_control');

?>
