<hr />
<div id="sidebar-main" class="secondary">
<?php /* Widgets/SBM Check */ if ( !(function_exists('dynamic_sidebar') and dynamic_sidebar(1)) ) { ?>

	<div id="search"><h4><?php _e('Search','k2_domain'); ?></h4>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	</div>


	<?php /* Menu for subpages of current page */
		global $notfound;
		if (is_page() and ($notfound != '1')) {
			$current_page = $post->ID;
			while($current_page) {
				$page_query = $wpdb->get_row("SELECT ID, post_title, post_status, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
				$current_page = $page_query->post_parent;
			}
			$parent_id = $page_query->ID;
			$parent_title = $page_query->post_title;

			$page_menu = wp_list_pages('echo=0&sort_column=menu_order&title_li=&child_of='. $parent_id);
			if ($page_menu) {
	?>

	<div class="sb-pagemenu">
		<h4><?php echo $parent_title; ?> <?php _e('Subpages','k2_domain'); ?></h4>
		
		<ul>
			<?php echo $page_menu; ?>
		</ul>
			
		<?php if ($parent_id != $post->ID) { ?>
			<a href="<?php echo get_permalink($parent_id); ?>"><?php printf(__('Back to %s','k2_domain'), $parent_title ) ?></a>
		<?php } ?>
	</div>
	<?php } } ?>

	
	<?php if (is_attachment()) { ?>
		<div class="sb-pagemenu">
			<a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php printf(__('Back to \'%s\'','k2_domain'), get_the_title($post->post_parent) ) ?></a>
		</div>
	<?php } ?>

	<?php if (!is_home() and !is_page() and !is_single() or is_paged()) { ?>
		
	<div class="sb-about">
		<h4><?php _e('About','k2_domain'); ?></h4>
		
		<?php /* Category Archive */ if (is_category()) { ?>
		<p><?php printf(__('You are currently browsing the %1$s weblog archives for the %2$s category.','k2_domain'), '<a href="' . get_settings('siteurl') .'">' . get_bloginfo('name') . '</a>', single_cat_title('', false) ) ?></p>

		<?php /* Day Archive */ } elseif (is_day()) { ?>
		<p><?php printf(__('You are currently browsing the %1$s weblog archives for the day %2$s.','k2_domain'), '<a href="' . get_settings('siteurl') .'">' . get_bloginfo('name') . '</a>', get_the_time(__('l, F jS, Y','k2_domain'))) ?></p>

		<?php /* Monthly Archive */ } elseif (is_month()) { ?>
		<p><?php printf(__('You are currently browsing the %1$s weblog archives for the month %2$s.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', get_the_time(__('F, Y','k2_domain'))) ?></p>

		<?php /* Yearly Archive */ } elseif (is_year()) { ?>
		<p><?php printf(__('You are currently browsing the %1$s weblog archives for the year %2$s.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', get_the_time('Y')) ?></p>
		
		<?php /* Search */ } elseif (is_search()) { ?>
		<p><?php printf(__('You have searched the %1$s weblog archives for \'<strong>%2$s</strong>\'.','k2_domain'),'<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', wp_specialchars($s)) ?></p>

		<?php /* Author Archive */ } elseif (is_author()) { ?>
		<p><?php printf(__('Archive for <strong>%s</strong>.','k2_domain'), get_the_author()) ?></p>
		<p><?php the_author_description(); ?></p>

		<?php } elseif (function_exists('is_tag') and is_tag()) { ?>
		<p><?php printf(__('You are currently browsing the %1$s weblog archives for \'%2$s\' tag.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>', get_query_var('tag') ) ?></p>
		
		<?php /* Paged Archive */ } elseif (is_paged()) { ?>
		<p><?php printf(__('You are currently browsing the %s weblog archives.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>') ?></p>

		<?php } ?>
	</div>
	<?php } ?>


	<?php /* Brian's Latest Comments */ if ((function_exists('blc_latest_comments')) and is_home()) { ?> 
	<div class="sb-comments sb-comments-blc">
		<h4><?php _e('Comments','k2_domain'); ?></h4>	
		<span class="metalink"><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('RSS Feed for all Comments','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>
		<ul>
			<?php blc_latest_comments('5','3','false'); ?>
		</ul>
	</div>
	<?php } ?>

	<?php /* Latest Entries */ if ( (is_home()) or (is_search() or (is_404()) or (defined('K2_NOT_FOUND'))) or (function_exists('is_tag') and is_tag()) or ( (is_archive()) and (!is_author()) ) ) { ?>
	<div class="sb-latest">
		<h4><?php _e('Latest','k2_domain'); ?></h4>
		<span class="metalink"><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('RSS Feed for Blog Entries','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>

		<ul>
			<?php wp_get_archives('type=postbypost&limit=10'); ?>
		</ul>
	</div>
	<?php } ?>


	<?php /* Related Posts Plugin */ if ( (function_exists('related_posts')) and is_single() and !defined('K2_NOT_FOUND') ) { ?> 
	<div class="sb-related">
		<h4><?php _e('Related Entries','k2_domain'); ?></h4>
		
		<ul>
			<?php related_posts(); ?>
		</ul>
	</div>
	<?php } ?>

	<?php /* FlickrRSS Plugin */ if ((function_exists('get_flickrRSS')) and is_home() and !(is_paged())) { ?> 
	<div class="sb-flickr">
		<h4><?php _e('Flickr','k2_domain'); ?></h4>
		<span class="metalink"><a href="http://flickr.com/services/feeds/photos_public.gne?id=<?php echo get_option('flickrRSS_flickrid'); ?>&amp;format=rss_200" title="<?php _e('RSS Feed for flickr','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>

		<div>
			<?php get_flickrRSS(); ?>
		</div>
	</div>
	<?php } ?>


	<?php /* if ((function_exists('feedlist')) and is_home() and !(is_paged()) ) { ?> 
	<div class="sb-feedlist"><h4><?php _e('Feedlist','k2_domain'); ?></h4>
		<ul>
			<?php feedList(array("rss_feed_url"=>"",
				"num_items"=>10,
				"show_description"=>false,
				"random"=>true,
				"sort"=>"asc","new_window"=>true)); 
			?>
			</ul>
	</div>
	<?php } */ ?>


	<?php /* Links */ if ( (is_home()) and !(is_page()) and !(is_single()) and !(is_search()) and !(is_archive()) and !(is_author()) and !(is_category()) and !(is_paged()) ) { $links_list_exist = @$wpdb->get_var("SELECT link_id FROM $wpdb->links LIMIT 1"); if($links_list_exist) { ?>
	<div class="sb-links">
		<ul>
			<?php wp_list_bookmarks('title_before=<h4>&title_after=</h4>'); ?>
		</ul>
	</div>
	<?php } } ?>


	<?php /* Archives */ if ( is_archive() or is_search() or is_paged() or is_category() or (function_exists('is_tag') and is_tag()) or defined('K2_NOT_FOUND') ) { ?>
	<div class="sb-months">
		<h4><?php _e('Archives','k2_domain'); ?></h4>
	
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</div>

	<div class="sb-categories">
		<h4><?php _e('Categories','k2_domain'); ?></h4>
	
		<ul>
			<?php wp_list_categories('title_li=&show_count=1&hierarchical=0'); ?>
		</ul>
	</div>
	<?php } ?>

<?php } /* End Widgets/SBM check */ ?>
</div> <!-- #sidebar-main -->

<hr />
<div id="sidebar-alt" class="secondary">
<?php /* Widgets/SBM Check */ if ( !(function_exists('dynamic_sidebar') and dynamic_sidebar(2)) ) { ?>

<?php } ?>
</div> <!-- #sidebar-alt -->

<div class="clear"></div>
