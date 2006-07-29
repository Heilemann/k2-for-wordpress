
<hr />

<div class="secondary<?php /* Flexible Width? */ if (get_option('k2widthtype') == 0) echo ' flex'; ?> ">

<?php /* WordPress Widget Support */ if (function_exists('dynamic_sidebar') and dynamic_sidebar()) { } else { ?>

	<div id="search"><h2><?php _e('Search','k2_domain'); ?></h2>
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

			if ($wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id' AND post_status != 'attachment'")) {
	?>

	<div class="sb-pagemenu">
		<h2><?php echo $parent_title; ?> <?php _e('Subpages','k2_domain'); ?></h2>
		
		<ul>
			<?php wp_list_pages('sort_column=menu_order&title_li=&child_of='. $parent_id); ?>
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

	<?php /* If there is a custom about message, use it on the frontpage. */ $k2about = get_option('k2aboutblurp'); if ((is_home() and $k2about != '') or !is_home() and !is_page() and !is_single() or is_paged()) { ?>
		
	<div class="sb-about">
		<h2><?php _e('About','k2_domain'); ?></h2>
		
		<?php /* Frontpage */ if (is_home() and !is_paged()) { ?>
		<p><?php echo stripslashes($k2about); ?></p>
		
		<?php /* Category Archive */ } elseif (is_category()) { ?>
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
		
		<?php /* Paged Archive */ } elseif (is_paged) { ?>
		<p><?php printf(__('You are currently browsing the %s weblog archives.','k2_domain'), '<a href="'.get_settings('siteurl').'">'.get_bloginfo('name').'</a>') ?></p>

		<?php /* Permalink */ } elseif (is_single()) { ?>
		<p><?php next_post('%', __('Next: ','k2_domain'),'yes') ?><br/>
		<?php previous_post('%', __('Previous: ','k2_domain') ,'yes') ?></p>

		<?php } ?>

		<?php $k2asidescategory = get_option('k2asidescategory'); if (!is_home() and !is_paged() and !in_category($k2asidescategory) or is_day() or is_month() or is_year() or is_author() or is_search() or (function_exists('is_tag') and is_tag())) { ?>
			<p><?php _e('Longer entries are truncated. Click the headline of an entry to read it in its entirety.','k2_domain'); ?></p>
		<?php } ?>
	</div>
			
	<?php } ?>


	<?php /* Brian's Latest Comments */ if ((function_exists('blc_latest_comments')) and is_home()) { ?> 
	<div class="sb-comments" id="brians-latest-comments">
		<h2><?php _e('Comments','k2_domain'); ?></h2>
		
		<a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('RSS Feed for all Comments','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a>
		<ul>
			<?php blc_latest_comments('5','3','false'); ?>
		</ul>
	</div>
	<?php } ?>

	<?php /* Show Asides only on the frontpage */ if (!is_paged() && is_home()) { if (get_option('k2asidesposition') != '0' and get_option('k2asidescategory') != '0') { ?>
	<div class="sb-asides">
		<h2><?php echo get_the_category_by_ID(get_option('k2asidescategory')); ?></h2>
		<span class="metalink"><a href="<?php bloginfo('url'); ?>/?feed=rss&amp;cat=<?php echo $k2asidescategory; ?>" title="<?php _e('RSS Feed for Asides','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>
		
		<div><?php /* Choose a category to be an 'aside' in the K2 options panel */ $temp_query = $wp_query; query_posts("cat=".get_option('k2asidescategory')."&showposts=".get_option('k2asidesnumber')); while (have_posts()) : the_post(); ?>
			<p id="post-<?php the_ID(); ?>" class="aside"><span>&raquo;&nbsp;</span><?php echo str_replace(array('<p>','</p>'), '', apply_filters('the_content', $post->post_content)); ?>&nbsp;<span class="metalink"><a href="<?php the_permalink($post->ID) ?>" rel="bookmark" title='<?php _e('Permanent Link to this aside','k2_domain'); ?>'>#</a></span>&nbsp;<span class="metalink"><?php comments_popup_link('0', '1', '%', '', ' '); ?></span><?php edit_post_link(__('edit','k2_domain'),'&nbsp;&nbsp;<span class="metalink">','</span>'); ?></p>
			<?php /* End Asides Loop */ endwhile; $wp_query = $temp_query; ?>
		</div>
	</div>
	<?php } } ?>


	<?php /* Latest Entries */ if ( (is_home()) or (is_search() or (is_404()) or ($notfound == '1')) or (function_exists('is_tag') and is_tag()) or ( (is_archive()) and (!is_author()) ) ) { ?>
	<div class="sb-latest">
		<h2><?php _e('Latest','k2_domain'); ?></h2>
		<span class="metalink"><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('RSS Feed for Blog Entries','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>

		<ul>
			<?php wp_get_archives('type=postbypost&limit=10'); ?>
		</ul>
	</div>
	<?php } ?>


	<?php /* FlickrRSS Plugin */ if ((function_exists('get_flickrRSS')) and is_home() and !(is_paged())) { ?> 
	<div class="sb-flickr">
		<h2>Flickr</h2>
		<span class="metalink"><a href="http://flickr.com/services/feeds/photos_public.gne?id=<?php echo get_option('flickrRSS_flickrid'); ?>&amp;format=rss_200" title="<?php _e('RSS Feed for flickr','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a></span>

		<div>
			<?php get_flickrRSS(); ?>
		</div>
	</div>
	<?php } ?>


	<?php /* if ((function_exists('feedlist')) and is_home() and !(is_paged()) ) { ?> 
	<div class="sb-feedlist"><h2><?php _e('Feedlist','k2_domain'); ?></h2>
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


	<?php /* Links */ if ( (is_home()) && !(is_page()) && !(is_single()) && !(is_search()) && !(is_archive()) && !(is_author()) && !(is_category()) && !(is_paged()) ) { $links_list_exist = @$wpdb->get_var("SELECT link_id FROM $wpdb->links LIMIT 1"); if($links_list_exist) { ?>
	<div class="sb-links">
		<ul>
			<?php get_links_list(); ?>
		</ul>
	</div>
	<?php } } ?>


	<?php /* Archives */ if ( (is_archive()) or (is_search()) or (is_paged()) or ($notfound == '1') or (function_exists('is_tag') and is_tag()) ) { ?>
	<div class="sb-months">
		<h2><?php _e('Archives','k2_domain'); ?></h2>
		
		<ul>
			<?php wp_get_archives('type=monthly'); ?>
		</ul>
	</div>

	<div class="sb-categories">
		<h2><?php _e('Categories','k2_domain'); ?></h2>
		
		<ul>
			<?php list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','','') ?>
		</ul>
	</div>
	<?php } ?>


	<?php /* Related Posts Plugin */ if ((function_exists('related_posts')) and is_single() and ($notfound != '1')) { ?> 
	<div class="sb-related">
		<h2><?php _e('Related Entries','k2_domain'); ?></h2>
		
		<ul>
			<?php related_posts(10, 0, '<li>', '</li>', '', '', false, false); ?>
		</ul>
	</div>
	<?php } ?>


	<?php /* Include users sidebar additions */ if ( file_exists(TEMPLATEPATH . '/sidebar-custom.php') ) { include(TEMPLATEPATH . '/sidebar-custom.php'); } ?>


<?php } ?>

</div>
<div class="clear"></div>
