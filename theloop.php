<?php
	// This is the loop, which fetches entries from your database.
	// It is a very delicate piece of machinery. Be gentle!

	// Get core WP functions when loaded dynamically
	if (isset($_GET['rolling'])) {
		require (dirname(__FILE__).'/../../../wp-blog-header.php');
	}

	// Get the asides category
	$k2asidescategory = get_option('k2asidescategory');
?>

	<?php /* Headlines for archives */ if ((!is_single() and !is_home()) or is_paged()) { ?>
		<h2>
		<?php // Figure out what kind of page is being shown
			if (is_category()) {
				if ($cat != $k2asidescategory) {
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
	<?php } ?>

	<?php if ((get_option('k2rollingarchives') == 0) and !is_single() and !is_home() and is_paged()) include (TEMPLATEPATH . '/navigation.php'); ?> 

	<?php /* Check if there are posts */
		if ( have_posts() ) {
			/* It saves time to only perform the following if there are posts to show */

			// Count if there are 2+ users
			$count_users = $wpdb->get_var("SELECT COUNT(1) FROM $wpdb->usermeta WHERE meta_key = '" . $table_prefix . "user_level' AND meta_value > 1 LIMIT 2");

			// If there are 2+ users, this is a multiple-user blog
			$multiple_users = ($count_users > 1);

			// Get the user information
			get_currentuserinfo();
			global $user_level;

			// Post index for semantic classes
			$post_index = 1;

			// Check if to display asides inline or not
			if(is_archive() or is_search() or is_single() or (function_exists('is_tag') and is_tag())) {
				$k2asidescheck = '0';
			} else {
				$k2asidescheck = get_option('k2asidesposition');
			}
	?>

	<?php /* Start the loop */
		while ( have_posts() ) {
			the_post();

			// Post is an aside
			$post_asides = in_category($k2asidescategory);
	?>

	<?php /* Permalink nav has to be inside loop */ if (is_single()) include (TEMPLATEPATH . '/navigation.php'); ?>

	<?php /* Only display asides if sidebar asides are not active */ if(!$post_asides || $k2asidescheck == '0') { ?>

		<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class($post_index++, $post_asides); ?>">
			<div class="entry-head">
				<h3 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(get_the_title(),1) ); ?>'><?php the_title(); ?></a></h3>
				<?php /* Support for Noteworthy plugin */ if (function_exists('nw_noteworthyLink')) { nw_noteworthyLink($post->ID); } ?>

				<small class="entry-meta">
					<span class="chronodata">
						<?php /* Date & Author */
							printf(	__('Published %1$s %2$s','k2_domain'),
								( $multiple_users ? sprintf(__('by %s','k2_domain'), '<span class="vcard author"><a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) .'" class="url fn">' . get_the_author() . '</a></span>') : ('') ), 
								'<abbr class="published" title="'. get_the_time('Y-m-d\TH:i:sO') . '">' .
 								( function_exists('time_since') ? sprintf(__('%s ago','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time())) : get_the_time(__('F jS, Y','k2_domain')) ) 
 								. '</abbr>'
 								); 
 						?>
					</span>

					<?php /* Categories */ if (!$post_asides) { printf(__('<span class="entry-category">in %s.</span>','k2_domain'), k2_nice_category(', ', __(' and ','k2_domain')) ); } ?>

					<?php /* Comments */ comments_popup_link('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>', 'commentslink', '<span class="commentslink">'.__('Closed','k2_domain').'</span>'); ?>
				
					<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
				
					<?php /* Tags */ if (is_single() and function_exists('UTW_ShowTagsForCurrentPost')) { ?>
						<span class="entry-tags"><?php _e('Tags:','k2_domain'); ?> <?php UTW_ShowTagsForCurrentPost("commalist") ?>.</span>
					<?php } ?>
				</small> <!-- .entry-meta -->
			</div> <!-- .entry-head -->

			<div class="entry-content">
				<?php if (is_archive() or is_search() or (function_exists('is_tag') and is_tag())) {
					the_excerpt();
				} else {
					the_content(__('Continue reading','k2_domain') . " '" . the_title('', '', false) . "'");
				} ?>

				<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>', 'number'); ?>
			</div> <!-- .entry-content -->

		</div> <!-- #post-ID -->

	<?php } /* End sidebar asides test */ ?>

	<?php } /* End The Loop */ ?>
	
	<?php /* Insert Paged Navigation */ if (!is_single() and get_option('k2rollingarchives') != 1) { include (TEMPLATEPATH.'/navigation.php'); } ?>

<?php /* If there is nothing to loop */  } else { $notfound = '1'; ?>

	<div class="hentry four04">

		<div class="entry-head">
			<h3 class="center"><?php _e('Not Found','k2_domain'); ?></h2>
		</div>

		<div class="entry-content">
			<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
		</div>

	</div> <!-- .hentry .four04 -->

<?php } /* End Loop Init  */ ?>
