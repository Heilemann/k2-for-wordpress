<?php /*
	Template Name: Default Template w. Comments
*/ ?>

<?php get_header(); ?>

<div class="content">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

			<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class(); ?>">

				<div class="page-head">
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php the_title(); ?></a></h2>
					<?php edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
				</div>
	
				<div class="entry-content">
					<?php the_content(); ?>

					<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>', 'number'); ?>
				</div>

			</div> <!-- #post-ID -->

			<div class="comments">
				<?php comments_template(); ?>
			</div> <!-- .comments -->

		<?php endwhile; else: ?>

			<div class="hentry four04">

				<div class="entry-head">
					<h3 class="center"><?php _e('Not Found','k2_domain'); ?></h3>
				</div>

				<div class="entry-content">
					<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
				</div>

			</div> <!-- .hentry .four04 -->

		<?php endif; ?>

		</div> <!-- #current-content .hfeed -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->
</div> <!-- #primary-wrapper -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->
	
<?php get_footer(); ?>