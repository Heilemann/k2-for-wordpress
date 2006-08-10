<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="current-content">
			<div>

	<?php if (have_posts()) { while (have_posts()) { the_post(); ?>

	<?php 
		// This also populates the iconsize for the next line
		$attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); 

		// This lets us style narrow icons specially
		$_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; 
	?>	

		<div id="post-<?php the_ID(); ?>" class="item entry">
			<div class="itemhead">
			<h3><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), strip_tags(get_the_title())) ?>'><?php the_title(); ?></a></h3>
			<small class="metadata">
				<span class="chronodata">
					<?php /* Date & Author */ $count_users = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->usermeta WHERE `meta_key` = '" . $table_prefix . "user_level' AND `meta_value` > 1");
					printf(__('Published %1$s %2$s','k2_domain'),
					(($count_users > 1) ? sprintf(__('by %s','k2_domain'), '<a href="' . get_author_link(0, $authordata->ID, $authordata->user_nicename) .'">' . get_the_author() . '</a>') : ('')),
						(function_exists('time_since') ? sprintf(__('%s ago','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time())) : get_the_time(__('F jS, Y','k2_domain')))); ?>
				</span>
			</small>
			</div>
			<div class="itemtext">
				<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>
				<?php the_content(); ?>
				<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>','number'); ?>
			</div>
		</div>

		<?php comments_template(); ?>	

	<?php } } else { ?>

		<h2><?php _e('Sorry, no attachments matched your criteria.','k2_domain'); ?></h2>

	<?php } ?>
			</div>
		</div>

		<div id="dynamic-content"></div>

	</div>

	<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>
