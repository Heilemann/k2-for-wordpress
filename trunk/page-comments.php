<?php /*
	Template Name: Default Template w. Comments
*/ ?>

<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="current-content">
			<div>

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
			<div class="item">
	
				<div class="pagetitle">
					<h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), get_the_title()); ?>'><?php the_title(); ?></a></h2>
					<?php edit_post_link(__('Edit','k2_domain'), '<span class="editlink">','</span>'); ?>
				</div>
			
				<div class="itemtext">
					<?php the_content(); ?>
		
					<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>','number'); ?>
				</div>
	
			</div>
	
		<?php endwhile; endif; ?>

		<?php comments_template(); ?>
			</div>
		</div>

		<div id="dynamic-content"></div>
	</div>

	<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>