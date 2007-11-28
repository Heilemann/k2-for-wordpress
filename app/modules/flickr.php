<?php

function flickr_sidebar_module($args) {
	extract($args);

	// Get flickrRSS Settings
	$type = get_option('flickrRSS_display_type');
	$tags = trim(get_option('flickrRSS_tags'));
	$userid = stripslashes(get_option('flickrRSS_flickrid'));

	// Generate Feed Link
	if ($type == 'public') {
		$rss_url = 'http://api.flickr.com/services/feeds/photos_public.gne?tags=' . $tags . '&amp;format=rss_200';
	} elseif ($type == 'user') {
		$rss_url = 'http://api.flickr.com/services/feeds/photos_public.gne?id=' . $userid . '&amp;tags=' . $tags . '&amp;format=rss_200';
	} elseif ($type == 'group') {
		$rss_url = 'http://api.flickr.com/services/feeds/groups_pool.gne?id=' . $userid . '&amp;format=rss_200';
	}

	echo($before_module . $before_title . $title . $after_title);
	?>
		<a href="<?php echo $rss_url; ?>" title="<?php _e('RSS Feed for flickr','k2_domain'); ?>" class="feedlink"><span><?php _e('RSS','k2_domain'); ?></span></a>

		<div>
			<?php get_flickrRSS(); ?>
		</div>
	<?php
	echo($after_module);
}

if(function_exists('get_flickrRSS')) {
	register_sidebar_module('Flickr', 'flickr_sidebar_module', 'sb-flickr');
}

?>
