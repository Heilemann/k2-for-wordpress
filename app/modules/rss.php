<?php

function rss_sidebar_module($args) {
	require_once(ABSPATH . WPINC . '/rss-functions.php');

	extract($args);

	$feed = sbm_get_option('feed');
	$num_items = sbm_get_option('num_items');

	if($feed) {
		$rss = fetch_rss($feed);

		// Only do this if we must show the title
		if($title) {
			$feed_link = wp_specialchars(strip_tags($rss->channel['link']), 1);

			// Link to the feed's channel link if we can
			if($feed_link) {
				$title = '<a href="'. $feed_link .'" title="' . __('Syndicate as RSS') . '">' . $title . '</a>';
			}

			// Get the RSS feed icon
			$icon = ($icon = sbm_get_option('icon')) ? $icon : get_bloginfo('template_directory') . '/images/feed.png';

			// Add a link to the feed using the feed icon
			$title = '<a href="' . sbm_get_option('feed') . '"><img src="' . $icon . '" alt="RSS Feed" /></a> ' . $title;
		}

		echo($before_module . $before_title . $title . $after_title);

		if($rss) {
			$rss->items = array_slice($rss->items, 0, $num_items);
		?>
		<ul>
			<?php
				foreach($rss->items as $item):
					$title = wp_specialchars(strip_tags($item['title']), 1);
					$title = $title ? $title : __('Untitled');
					$link = wp_specialchars(strip_tags($item['link']), 1);

					$description_words = str_word_count(str_replace(array("\r", "\n"), ' ', wp_specialchars(strip_tags(html_entity_decode($item['description'], ENT_QUOTES)), 1)), 1);

					$summary = implode(' ', array_slice($description_words, 0, 10)) . (count($description_words) > 10 ? '...' : '');
			?>
				<li><a href="<?php echo($link); ?>" title="<?php echo($summary); ?>"><?php echo($title); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php
		} else {
		?>
		<p><?php _e('No feed items, the feed may currently be unavaliable.'); ?></p>
		<?php
		}

		echo($after_module);
	}
}

function rss_sidebar_module_control() {
	if(isset($_POST['rss_module_feed'])) {
		sbm_update_option('feed', $_POST['rss_module_feed']);
	}

	if(isset($_POST['rss_module_icon'])) {
		sbm_update_option('icon', $_POST['rss_module_icon']);
	}

	if(isset($_POST['rss_module_num_items'])) {
		sbm_update_option('num_items', $_POST['rss_module_num_items']);
	}

	?>
		<p><label for="rss-module-feed">RSS feed's URL:</label> <input id="rss-module-feed" name="rss_module_feed" type="text" value="<?php echo(sbm_get_option('feed')); ?>" /></p>
		<p><label for="rss-module-icon">RSS feed's icon:</label> <input id="rss-module-icon" name="rss_module_icon" type="text" value="<?php echo(sbm_get_option('icon')); ?>" /></p>
		<p><label for="rss-module-num-items">Number of items:</label> <input id="rss-module-num-items" name="rss_module_num_items" type="text" value="<?php echo(sbm_get_option('num_items')); ?>" size="2" /></p>
	<?php
}

register_sidebar_module('RSS module', 'rss_sidebar_module', 'sb-feed', array('num_items' => '10'));
register_sidebar_module_control('RSS module', 'rss_sidebar_module_control');

?>
