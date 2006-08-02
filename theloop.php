<?php
	// This is the loop, which fetches entries from your database.
	// It is a delicate piece of machinery. Be gentle!

	// Get Core WP Functions If Needed
	if (isset($_GET['rolling'])) {
		require (dirname(__FILE__).'/../../../wp-blog-header.php');
	}

	//print_r($wp_query->query);
?>

<div id="primarycontent">

	<?php /* Headlines for archives */ if ((!is_single() and !is_home()) or is_paged()) { ?>
	<div class="pagetitle">
		<h2>
		<?php // Figure out what kind of page is being shown
			if (is_category()) {
				if (the_category_ID(false) != get_option('k2asidescategory')) {
					printf(__('Archive for the \'%s\' Category','k2_domain'), single_cat_title('', false));
				} else {
					echo single_cat_title();
				}

			} elseif (is_day()) {
				printf(__('Archive for %s','k2_domain'), get_the_time(__('F jS, Y','k2_domain')));

			} elseif (is_month()) {
				printf(__('Archive for %s','k2_domain'), get_the_time(__('F, Y','k2_domain')));

			} elseif (is_year()) {
				printf(__('Archive for %s','k2_domain'), get_the_time(__('Y','k2_domain')));

			} elseif (is_search()) {
				printf(__('Search Results for \'%s\'','k2_domain'), $s);

			} elseif (function_exists('is_tag') and is_tag()) {
				printf(__('Tag Archive for \'%s\'','k2_domain'), get_query_var('tag') );

			} elseif (is_author()) {
		   		$post = $wp_query->post; $the_author = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = '$post->post_author' AND meta_key = 'nickname'");
				printf(__('Author Archive for %s','k2_domain'), $the_author );

			} elseif (is_paged() and ($paged > 1)) { 
				 printf(__('Archive Page %s','k2_domain'), $paged );
			} ?>
		</h2>
	</div>
	<?php } ?>

	<?php if (get_option('k2rollingarchives') == 0 and !is_single() and !is_home() and is_paged()) include (TEMPLATEPATH . '/navigation.php'); ?> 
	
	<?php /* Start the loop */ if (have_posts()) { while (have_posts()) { the_post(); ?>

	<?php /* Permalink nav has to be inside loop */ if (is_single()) include (TEMPLATEPATH . '/navigation.php'); ?>

	<?php /* Asides (shown inline on all archive pages) */ if ( is_archive() or is_search() or is_single() or (function_exists('is_tag') and is_tag()) ) { $k2asidescheck = '0'; } else { $k2asidescheck = get_option('k2asidesposition'); } 
		if ( (get_option('k2asidescategory') != '0') and (in_category(get_option('k2asidescategory'))) ) { 
	    ?> 

		<div id="post-<?php the_ID(); ?>" class="item aside">
			<div class="itemhead">
				<h3 <?php /* Support for noteworthy plugin */ foreach((get_the_category()) as $cat) { if ($cat->cat_name == 'Noteworthy') { ?> class="noteworthy"<?php } } ?>><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), strip_tags(get_the_title())) ?>'><?php the_title(); ?></a></h3>
				<?php /* Support for noteworthy plugin */ get_currentuserinfo(); global $user_level; if (function_exists(nw_noteworthyLink) and ($user_level == 10) ) { ?><?php nw_noteworthyLink($post->ID); ?><?php } ?>
				
				<small class="metadata">
					<span class="chronodata">
						<?php /* Date & Author */ $count_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE `meta_key` = '" . $table_prefix . "user_level' AND `meta_value` > 1");
						printf(__('Published %1$s %2$s','k2_domain'),
						(($count_users > 1) ? sprintf(__('by %s','k2_domain'), '<a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) .'">' . get_the_author() . '</a>') : ('')),
						(function_exists('time_since') ? sprintf(__('%s ago','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time())) : get_the_time(__('F jS, Y','k2_domain')))); ?>
					</span>

					<?php /* Comments */ comments_popup_link('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>', 'commentslink', '<span class="commentslink">'.__('Closed','k2_domain').'</span>'); ?>

					<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="editlink">','</span>'); ?>

					<?php /* Tags */ if (is_single() and function_exists(UTW_ShowTagsForCurrentPost)) { ?>
						<span class="tagdata"><?php _e('Tags:','k2_domain'); ?> <?php UTW_ShowTagsForCurrentPost("commalist") ?>.</span>
					<?php } ?>
				</small>
			</div>

			<div class="itemtext">
				<?php the_content(__('Continue reading','k2_domain') . " '" . the_title('', '', false) . "'"); ?>
			</div>

		</div>

	<?php  /* Normal Entries */ } elseif (!(in_category($k2asidescategory))) { ?>

		<div id="post-<?php the_ID(); ?>" class="item entry">
			<div class="itemhead">
				<h3 <?php /* Support for noteworthy plugin */ foreach((get_the_category()) as $cat) {  if ($cat->cat_name == 'Noteworthy') { ?> class="noteworthy"<?php } } ?>><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), strip_tags(get_the_title())) ?>'><?php the_title(); ?></a></h3>
				<?php /* Support for noteworthy plugin */ get_currentuserinfo(); global $user_level; if (function_exists(nw_noteworthyLink) and ($user_level == 10) ) { ?><?php nw_noteworthyLink($post->ID); ?><?php } ?>

				<small class="metadata">
					<span class="chronodata">
						<?php /* Date & Author */ $count_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE `meta_key` = '" . $table_prefix . "user_level' AND `meta_value` > 1");
						printf(__('Published %1$s %2$s','k2_domain'),
						(($count_users > 1) ? sprintf(__('by %s','k2_domain'), '<a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) .'">' . get_the_author() . '</a>') : ('')),
 							(function_exists('time_since') ? sprintf(__('%s ago','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time())) : get_the_time(__('F jS, Y','k2_domain')))); ?>
					</span>

					<?php /* Categories */ printf(__('<span class="categorydata">in %s.</span>','k2_domain'), k2_nice_category(', ', __(' and ','k2_domain')) ); ?>

					<?php /* Comments */ comments_popup_link('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>', 'commentslink', '<span class="commentslink">'.__('Closed','k2_domain').'</span>'); ?>
				
					<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="editlink">','</span>'); ?>
				
					<?php /* Tags */ if (is_single() and function_exists(UTW_ShowTagsForCurrentPost)) { ?>
						<span class="tagdata"><?php _e('Tags:','k2_domain'); ?> <?php UTW_ShowTagsForCurrentPost("commalist") ?>.</span>
					<?php } ?>
				</small>
			</div>

			<div class="itemtext">
				<?php if (is_archive() or is_search() or (function_exists('is_tag') and is_tag())) {
					the_excerpt();
				} else {
					the_content(__('Continue reading','k2_domain') . " '" . the_title('', '', false) . "'");
				} ?>

				<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>', 'number'); ?>
			</div>

			<!--
			<?php trackback_rdf(); ?>
			-->
		</div>
				
	<?php /* End Asides Segregation Code */ }

	} /* End The Loop */ ?>
	
	<?php /* Insert Paged Navigation */ if (!is_single() && get_option('k2rollingarchives') != 1) { include (TEMPLATEPATH.'/navigation.php'); } ?>

<?php /* If there is nothing to loop */  } else { $notfound = '1'; ?>

	<div class="center">
		<h2><?php _e('Not Found','k2_domain'); ?></h2>
	</div>

	<div class="item">
		<div class="itemtext">
			<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
		</div>
	</div>

<?php /* End Loop Init  */ } ?>

</div><?php // ID: primarycontent ?>
