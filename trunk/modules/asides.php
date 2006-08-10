<?php

function asides_sidebar_module($args) {
	extract($args);

	$k2asidescategory = get_option('k2asidescategory');

	if ( (get_option('k2asidesposition') != '0') and ($k2asidescategory != '0') ) {
		echo $before_module . $before_title . $title . $after_title;
?>
		<span class="metalink"><a href="<?php bloginfo('url'); ?>/?feed=rss&amp;cat=<?php echo $k2asidescategory; ?>" title="<?php _e('RSS Feed for Asides','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>
		
		<div>
		<?php // Choose a category to be an 'aside' in the K2 options panel
		$posts = get_posts('category='.$k2asidescategory.'&numberposts='.get_option('k2asidesnumber'));
		foreach ( $posts as $post ) { setup_postdata($post); ?>
			<p id="post-<?php the_ID(); ?>" class="aside"><span>&raquo;&nbsp;</span><?php echo str_replace(array('<p>', '</p>'), '', apply_filters('the_content', $post->post_content)); ?>&nbsp;<span class="metalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php _e('Permanent Link to this aside','k2_domain'); ?>'>#</a></span>&nbsp;<span class="metalink"><?php comments_popup_link('0', '1', '%', '', ' '); ?></span><?php edit_post_link(__('edit','k2_domain'), '&nbsp;&nbsp;<span class="metalink">', '</span>'); ?></p>
		<?php } // End Asides Loop ?>
		</div>
<?php
		echo $after_module;
	} // End Asides check
}

register_sidebar_module('Asides module', 'asides_sidebar_module', 'sb-asides');

?>
