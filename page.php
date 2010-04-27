<?php get_header(); ?>

<div class="content">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-head">
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">', '</span>'); ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div><!-- .entry-head -->

				<div class="entry-content">
					<?php the_content(); ?>
				</div><!-- .entry-content -->

				<div class="entry-foot">
					<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:','k2_domain') . '</span>', 'after' => '</div>' ) ); ?>

					<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
				</div><!-- .entry-foot -->
			</div><!-- #post-ID -->

			<?php if ( comments_open() ): ?> 
			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->
			<?php endif; ?>

			<?php /* if ( get_post_custom_values('comments') ): ?>
			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->
			<?php endif; */ ?>

		<?php endwhile; else: define('K2_NOT_FOUND', true); ?>

			<?php locate_template( array('blocks/k2-404.php'), true ); ?>

		<?php endif; ?>

		</div><!-- #current-content -->

		<div id="dynamic-content"></div>
	</div><!-- #primary -->
</div><!-- #primary-wrapper -->

<?php if ( ! get_post_custom_values('sidebarless') ) get_sidebar(); ?>

</div><!-- .content -->
	
<?php get_footer(); ?>