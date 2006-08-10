<?php get_header(); ?>

<div class="content">
	
	<div id="primary">

		<div id="current-content">

    	<?php if (have_posts()) { while (have_posts()) { the_post(); ?>

			<div class="<?php k2_post_class(); ?>">

				<div class="pagetitle">
					<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), get_the_title()) ?>'><?php the_title(); ?></a></h2>
					<?php edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
				</div>
		
				<div class="entry-content">
					<?php the_content(); ?>
	
					<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>', 'number'); ?>
				</div>

			</div>

		<?php } } else { $notfound = '1'; /* So we can tell the sidebar what to do */ ?>
		
			<div class="center">
				<h2><?php _e('Not Found','k2_domain'); ?></h2>
			</div>
		
			<div class="hentry">
			<div class="entry-content">
				<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
			</div>
			</div>

		<?php /* End Loop Init */ } ?>

		</div>

		<div id="dynamic-content"></div>

	</div>

	<?php get_sidebar(); ?>

</div>
	
<?php get_footer(); ?>