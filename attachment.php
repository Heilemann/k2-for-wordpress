<?php get_header(); ?>

<div class="content">

	<div id="primary">
		<div id="notices"></div>

		<div id="current-content" class="hfeed">

<?php
	/* Check if there are posts */
	if ( have_posts() ) {
		/* It saves time to only perform the following if there are posts to show */

		/* Count if there are 2+ users */
		$count_users = $wpdb->get_var("SELECT COUNT(1) FROM $wpdb->usermeta WHERE meta_key = '" . $table_prefix . "user_level' AND meta_value > 1 LIMIT 2");

		/* If there are 2+ users, this is a multiple-user blog */
		$multiple_users = ($count_users > 1);

		// Get date formats
		$dateformat = get_option('date_format');

		// This also populates the iconsize for the next line
		$attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); 

		// This lets us style narrow icons specially
		$_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; 
?>

	<?php /* Start the loop */ while ( have_posts() ) { the_post();	?>

				<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class(); ?>">
					<div class="entry-head">
						<h3 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php the_title(); ?></a></h3>

						<div class="entry-meta">
							<span class="chronodata">
								<?php /* Date & Author */
									printf(__('Published %1$s %2$s','k2_domain'),
										( $multiple_users  ? sprintf(__('by %s','k2_domain'), '<span class="vcard author"><a href="' . get_author_posts_url(get_the_author_ID()) .'" class="url fn" title="'. sprintf(__('View all posts by %s','k2_domain'), attribute_escape(get_the_author())) .'">' . get_the_author() . '</a></span>'): ('')	),
										'<abbr class="published" title="'. get_the_time('Y-m-d\TH:i:sO') . '">' .
										( function_exists('time_since') ? sprintf(__('%s ago','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time())) : get_the_time($dateformat) ) 
										. '</abbr>'						
										);
								?>
							</span>
						</div>
					</div> <!-- .entry-head -->

					<div class="entry-content">
						<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>
						<?php the_content(); ?>
					</div> <!-- .entry-content -->
				</div> <!-- #post-ID -->

				<?php comments_template(); ?>	

	<?php } } else { ?>

				<h2><?php _e('Sorry, no attachments matched your criteria.','k2_domain'); ?></h2>

	<?php } ?>
		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>

	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>
