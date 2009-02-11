<?php
	// This is the loop, which fetches entries from your database.
	// It is a very delicate piece of machinery. Be gentle!

	// Get the asides category
	$k2asidescategory = get_option('k2asidescategory');
	$k2rollingarchives = get_option('k2rollingarchives');

	global $post;
?>

	<?php /* Top Navigation */ if ( '0' == $k2rollingarchives ) k2_navigation('nav-above'); ?>

	<?php /* Headlines for archives */ if ( ! is_home() ): ?>
		<h1 class="page-head">
		<?php
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

			elseif ( is_tag() ):
				printf( __('Tag Archive for \'%s\'','k2_domain'), single_tag_title('', false) );
			
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

		?>
		</h1>
	<?php endif; ?>

	<?php 

	/* Check if there are posts */
	if ( have_posts() ):
		/* Post index for semantic classes */
		$post_index = 1;

		/* Start the loop */
		while ( have_posts() ): the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-head">
					<h3 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h3>

					<?php /* Edit Link */ edit_post_link( __('Edit','k2_domain'), '<span class="entry-edit">', '</span>' ); ?>

					<?php if ( 'post' == $post->post_type ): ?>
					<div class="entry-meta">
						<?php k2_entry_meta(1); ?>
					</div> <!-- .entry-meta -->
					<?php endif; ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div><!-- .entry-head -->

				<div class="entry-content">
					<?php k2_entry_content(); ?>
				</div><!-- .entry-content -->

				<div class="entry-foot">
					<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:','k2_domain') . '</span>', 'after' => '</div>' ) ); ?>

					<?php if ( 'post' == $post->post_type ): ?>
					<div class="entry-meta">
						<?php k2_entry_meta(2); ?>
					</div><!-- .entry-meta -->
					<?php endif; ?>

					<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
				</div><!-- .entry-foot -->
			</div><!-- #post-ID -->

		<?php endwhile; /* End The Loop */ ?>
	
<?php /* If there is nothing to loop */ else: define('K2_NOT_FOUND', true); ?>

	<div class="hentry four04">

		<div class="entry-head">
			<h3 class="center"><?php _e('Not Found','k2_domain'); ?></h3>
		</div>

		<div class="entry-content">
			<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
		</div>

	</div><!-- .hentry .four04 -->

<?php endif; /* End Loop Init  */ ?>

<?php /* Bottom Navigation */ if ( '0' == $k2rollingarchives) k2_navigation('nav-below'); ?> 
