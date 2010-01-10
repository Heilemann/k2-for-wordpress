<?php get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>
	
	<div id="primary">
		<a name="startcontent"></a>

		<div id="content" class="hfeed">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post-header">
					<h1 class="post-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link(__('Edit', 'k2'), '<span class="entry-edit">', '</span>'); ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div><!-- .post-header -->

				<div class="post-content">
					<?php the_content(); ?>
				</div><!-- .post-content -->

				<div class="post-footer">
					<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:', 'k2') . '</span>', 'after' => '</div>' ) ); ?>

					<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
				</div><!-- .post-footer -->
			</div><!-- #post-ID -->

			<?php if ( comments_open() ): ?> 
			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->
			<?php endif; ?>

		<?php endwhile; else: define('K2_NOT_FOUND', true); ?>

			<?php locate_template( array('blocks/k2-404.php'), true ); ?>

		<?php endif; ?>

		</div><!-- #current-content -->

	</div><!-- #primary -->

	
	<?php if ( ! get_post_custom_values('sidebarless') ) get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>
	

</div><!-- .wrapper -->
	
<?php get_footer(); ?>