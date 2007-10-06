<?php
	// This is the loop, which fetches entries from your database.
	// It is a very delicate piece of machinery. Be gentle!

	// Get core WP functions when loaded dynamically
	if (isset($_GET['k2dynamic'])) {
		require_once(dirname(__FILE__).'/../../../wp-config.php');

		if ($_GET['k2dynamic'] != 'init') {
			$query = k2_parse_query($_GET);
			query_posts($query);
		}

		// Debugging
		if ( isset($_GET['k2debug']) ) {
			echo '<div class="alert">';
			echo '<b>Query:</b><br />'; var_dump($wp_query->query); echo '<br />';
			echo '<b>Request:</b><br />'; var_dump($wp_query->request); echo '<br />';
			echo '</div>';
		}
	?>

<div id="dynamictype" class="<?php k2_body_class(); ?>">

<?php }

	// Get the asides category
	$k2asidescategory = get_option('k2asidescategory');

	// Get date & time formats
	$dateformat = get_option('date_format');
	$timeformat = get_option('time_format');
?>

	<?php /* Headlines for archives */ if ((!is_single() and !is_home()) or is_paged()) { ?>
		<div class="page-head"><h2>
		<?php // Figure out what kind of page is being shown
			if (is_category()) {
				if (get_query_var('cat') != $k2asidescategory) {
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
				printf(__('Search Results for \'%s\'','k2_domain'), attribute_escape(stripslashes(get_query_var('s'))));

			} elseif (function_exists('is_tag') and is_tag()) {
				if (function_exists('single_tag_title')) {
					printf(__('Tag Archive for \'%s\'','k2_domain'), single_tag_title('',$display=false));
				} else {
					printf(__('Tag Archive for \'%s\'','k2_domain'), get_query_var('tag') );
				}
				
			} elseif (is_author()) {
				printf(__('Author Archive for %s','k2_domain'), get_author_name(get_query_var('author')));

			} elseif (is_paged() and (get_query_var('paged') > 1)) { 
				 _e('Archive','k2_domain');
			}
			if ( (get_query_var('paged') > 1) and (get_option('k2rollingarchives') == 0) ) {
				printf(__(' <span class="archivepages">Page %1$s of %2$s</span>','k2_domain'), get_query_var('paged'), $wp_query->max_num_pages);
			}
			?>

		</h2></div>
	<?php } ?>

	<?php if ((get_option('k2rollingarchives') == 0) and !is_single() and is_paged()) include (TEMPLATEPATH . '/navigation.php'); ?> 

<?php
	/* Check if there are posts */
	if ( have_posts() ) {
		/* It saves time to only perform the following if there are posts to show */

		/* Count if there are 2+ users */
		$count_users = $wpdb->get_var("SELECT COUNT(1) FROM $wpdb->usermeta WHERE meta_key = '" . $table_prefix . "user_level' AND meta_value > 1 LIMIT 2");

		/* If there are 2+ users, this is a multiple-user blog */
		$multiple_users = ($count_users > 1);

		/* Post index for semantic classes */
		$post_index = 1;
?>

	<?php /* Start the loop */ while ( have_posts() ) { the_post();	?>

		<?php /* Permalink nav has to be inside loop */ if (is_single()) include (TEMPLATEPATH . '/navigation.php'); ?>

		<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class($post_index++, in_category($k2asidescategory)); ?>">
			<div class="entry-head">
				<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php the_title(); ?></a></h3>

				<div class="entry-meta">
					<span class="chronodata">
						<?php /* Date & Author */
							printf(	__('Published %1$s %2$s','k2_domain'),
								( $multiple_users ? sprintf(__('by %s','k2_domain'), '<span class="vcard author"><a href="' . get_author_posts_url(get_the_author_ID()) .'" class="url fn" title="'. sprintf(__('View all posts by %s','k2_domain'), attribute_escape(get_the_author())) .'">' . get_the_author() . '</a></span>') : ('') ), 
								'<abbr class="published" title="'. get_the_time('Y-m-d\TH:i:sO') . '">' .
								( function_exists('time_since') ? sprintf(__('%s ago','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time())) : ' ' . sprintf(__('on %s','k2_domain'), get_the_time($dateformat)) ) 								
								. '</abbr>'
							); 
 						?>
					</span>

					<span class="entry-category">
						<?php /* Categories */ printf(__('in %s.','k2_domain'), k2_nice_category(', ', __(' and ','k2_domain')) ); ?>
					</span>

					<?php /* Comments */ comments_popup_link('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>', 'commentslink', '<span>'.__('Closed','k2_domain').'</span>'); ?>

					<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>

					<?php /* Tags */ if (is_single() and function_exists('UTW_ShowTagsForCurrentPost')) { ?>
						<span class="entry-tags"><?php _e('Tags: ','k2_domain'); ?><?php UTW_ShowTagsForCurrentPost("commalist"); ?>.</span>
					<?php } elseif (is_single() and function_exists('the_tags')) { ?>
						<span class="entry-tags"><?php the_tags(__('Tags: ','k2_domain'), ', ', '.'); ?></span>
					<?php } ?>
				</div> <!-- .entry-meta -->
			</div> <!-- .entry-head -->

			<div class="entry-content">
				<?php the_content(sprintf(__('Continue reading \'%s\'', 'k2_domain'), the_title('', '', false))); ?>

				<?php wp_link_pages('before=<p class="page-links"><strong>' . __('Pages:','k2_domain') . '</strong>&after=</p>'); ?>
			</div> <!-- .entry-content -->

		</div> <!-- #post-ID -->

	<?php } /* End The Loop */ ?>
	
	<?php /* Insert Paged Navigation */ if (!is_single() and get_option('k2rollingarchives') != 1) { include (TEMPLATEPATH.'/navigation.php'); } ?>

<?php /* If there is nothing to loop */  } else { define('K2_NOT_FOUND', true); ?>

	<div class="hentry four04">

		<div class="entry-head">
			<h3 class="center"><?php _e('Not Found','k2_domain'); ?></h3>
		</div>

		<div class="entry-content">
			<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
		</div>

	</div> <!-- .hentry .four04 -->

<?php } /* End Loop Init  */

	if (isset($_GET['k2dynamic'])) { ?> </div> <?php }

?>