<?php

function asides_sidebar_module($args) {
	global $post;

	extract($args);

	$k2asidescategory = get_option('k2asidescategory');

	if ( (get_option('k2asidesposition') != '0') and ($k2asidescategory != '0') ) {
		echo $before_module . $before_title . $title . $after_title;
?>
		<span class="metalink"><a href="<?php bloginfo('url'); ?>/?feed=rss&amp;cat=<?php echo $k2asidescategory; ?>" title="<?php _e('RSS Feed for Asides','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>
		<div>
		<?php /* Choose a category to be an 'aside' in the K2 options panel */
			$temp_query = $wp_query;
			$asides_count = 1;
			query_posts('cat='.$k2asidescategory."&showposts=".get_option('k2asidesnumber'));
			while (have_posts()) { the_post();
		?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class($asides_count++, true); ?>">
				<span>&raquo;&nbsp;</span><?php the_content(__('(more)','k2_domain')); ?>&nbsp;<span class="metalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php _e('Permanent Link to this aside','k2_domain'); ?>'>#</a></span>&nbsp;<span class="metalink"><?php comments_popup_link('0', '1', '%', '', ' '); ?></span><?php edit_post_link(__('edit','k2_domain'),'&nbsp;&nbsp;<span class="metalink">','</span>'); ?>
			</div>
		<?php /* End Asides Loop */ } $wp_query = $temp_query; ?>
		</div>
<?php
		echo $after_module;
	} // End Asides check
}

register_sidebar_module(__('Asides module', 'k2_domain'), 'asides_sidebar_module', 'sb-asides');

?>
