<?php /*
	Template Name: Default Template w. Comments
*/ ?>

<?php get_header(); ?>

<div class="content">
	<div id="primary">
		<div id="current-content">
			<div id="primarycontent" class="hfeed">

				<?php while (have_posts()) { the_post(); ?>
				<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class(); ?>">

					<div class="page-head">
						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), get_the_title()) ?>'><?php the_title(); ?></a></h2>
						<?php edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
					</div>
		
					<div class="entry-content">
						<?php the_content(); ?>
	
						<?php link_pages('<p><strong>'.__('Pages:','k2_domain').'</strong> ', '</p>', 'number'); ?>
					</div>

				</div> <!-- #post-ID -->
				<?php comments_template(); ?>

				<?php } // End the Loop ?>

			</div> <!-- #primarycontent .hfeed -->
		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->
	
<?php get_footer(); ?>