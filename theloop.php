<?php
	// This is the loop, which fetches entries from your database.
	// It is a very delicate piece of machinery. Be gentle!

	// Get core WP functions when loaded dynamically
	if ( isset($_GET['k2dynamic']) ):

		// Check for CGI Mode
		if ( 'cgi' == substr( php_sapi_name(), 0, 3 ) ):
			require_once( preg_replace( '/wp-content.*/', '', __FILE__ ) . 'wp-config.php' );
		else:
			require_once( preg_replace( '/wp-content.*/', '', $_SERVER['SCRIPT_FILENAME'] ) . 'wp-config.php' );
		endif;

		if ( $_GET['k2dynamic'] != 'init' ):
			// Send the header
			header('Content-Type: ' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset'));

			// K2 Hook
			do_action('k2_dynamic_content');

			// Initialize the Loop
			query_posts( $_GET );
		endif;
	?>

<div id="dynamictype" class="<?php k2_body_class(); ?>">

<?php endif;

	// Debugging
	if ( isset($_GET['k2debug']) ):
		echo '<div class="alert">';
		echo '<b>SQL:</b><br />'; var_dump($wp_query->request);
		echo '<b>Query:</b><br />'; var_dump($wp_query->query);
		echo '</div>';
	endif;

	// Get the asides category
	$k2asidescategory = get_option('k2asidescategory');
	$k2rollingarchives = get_option('k2rollingarchives');

	/* Check if there are posts */
	if ( have_posts() ):
		/* Post index for semantic classes */
		$post_index = 1;
?>

	<?php /* Top Navigation */ if ( ('0' == $k2rollingarchives) or is_single() ): k2_navigation('nav-above'); endif; ?>

	<?php /* Headlines for archives */ if ( ( ! is_single() and ! is_home() ) or is_paged() ): ?>
		<div class="page-head">
			<h2><?php

			// Load the post for date archive titles
			if ( is_date() ): the_post(); endif;

			// Figure out what kind of page is being shown
			if ( is_category() ):
				if ( get_query_var('cat') != $k2asidescategory ):
					printf( __('Archive for the \'%s\' Category','k2_domain'), single_cat_title('', false) );
				else:
					echo single_cat_title();
				endif;

			elseif ( is_day() ):
				printf( __('Daily Archive for %s','k2_domain'), get_the_time( __('F jS, Y','k2_domain') ) );

			elseif ( is_month() ):
				printf( __('Monthly Archive for %s','k2_domain'), get_the_time( __('F, Y','k2_domain') ) );

			elseif ( is_year() ):
				printf( __('Yearly Archive for %s','k2_domain'), get_the_time( __('Y','k2_domain') ) );

			elseif ( is_search() ):
				printf( __('Search Results for \'%s\'','k2_domain'), attribute_escape( get_search_query() ) );

			elseif ( function_exists('is_tag') and is_tag() ):
				if ( function_exists('single_tag_title') ):
					printf( __('Tag Archive for \'%s\'','k2_domain'), single_tag_title('', false) );
				else:
					printf( __('Tag Archive for \'%s\'','k2_domain'), attribute_escape( get_query_var('tag') ) );
				endif;
			
			elseif ( is_author() ):
				printf( __('Author Archive for %s','k2_domain'), get_author_name( get_query_var('author') ) );

			elseif ( is_paged() and ( intval(get_query_var('paged')) > 1) ):
				 _e('Archive','k2_domain');
			endif;

			if ( ( intval( get_query_var('paged') ) > 1 ) and ( '0' == $k2rollingarchives ) ):
				printf( '<span class="archivepages">' . __('Page %1$s of %2$s', 'k2_domain') . '</span>', intval( get_query_var('paged')), $wp_query->max_num_pages);
			endif;

			// Reset the post for date archive titles
			if ( is_date() ): rewind_posts(); endif;

			?></h2>
		</div>
	<?php endif; ?>

	<?php /* Start the loop */ while ( have_posts() ): the_post(); ?>

		<div id="post-<?php the_ID(); ?>" class="<?php echo attribute_escape(k2_post_class($post_index++, in_category($k2asidescategory), false)); ?>">
			<div class="entry-head">
				<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php the_title(); ?></a></h3>

				<div class="entry-meta">
					<?php
						printf(	__('<span class="meta-start">Published</span> %1$s %2$s %3$s<span class="meta-end">.</span>','k2_domain'),

							'<div class="entry-author">' .
							sprintf(  __('<span class="meta-prep">by</span> %s','k2_domain'),
								'<address class="vcard author"><a href="' . get_author_posts_url(get_the_author_ID()) .'" class="url fn" title="'. sprintf(__('View all posts by %s','k2_domain'), attribute_escape(get_the_author())) .'">' . get_the_author() . '</a></address>'
							) . '</div>',
							
							'<div class="entry-date">' .
							( function_exists('time_since') ?
									sprintf(__('%s ago','k2_domain'),
										'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">' . time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . '</abbr>') :
									sprintf(__('<span class="meta-prep">on</span> %s','k2_domain'),
										'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">'. get_the_time( get_option('date_format') ) . '</abbr>')
							) . '</div>',

							'<div class="entry-categories">' .
							sprintf( __('<span class="meta-prep">in</span> %s','k2_domain'),
								k2_nice_category(', ', __(' and ','k2_domain'))
							) . '</div>'
						);
					?>

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

	<?php endwhile; /* End The Loop */ ?>
	
<?php /* If there is nothing to loop */ else: define('K2_NOT_FOUND', true); ?>

	<div class="hentry four04">

		<div class="entry-head">
			<h3 class="center"><?php _e('Not Found','k2_domain'); ?></h3>
		</div>

		<div class="entry-content">
			<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
		</div>

	</div> <!-- .hentry .four04 -->

<?php endif; /* End Loop Init  */ ?>

<?php /* Bottom Navigation */ if ( ('0' == $k2rollingarchives) or is_single() ): k2_navigation('nav-below'); endif; ?> 

<?php if ( isset( $_GET['k2dynamic'] ) ): ?>
</div><!-- #dynamictype -->
<?php endif; ?>