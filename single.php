<?php get_header(); ?>

<div class="content">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<?php k2_navigation('nav-above'); ?> 

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

			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->

			<?php k2_navigation('nav-below'); ?> 

		<?php endwhile; else: define('K2_NOT_FOUND', true); ?>

			<div class="hentry four04">
				<div class="entry-head">
					<h1 class="center"><?php _e('Not Found','k2_domain'); ?></h1>
				</div>

				<div class="entry-content">
					<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
				</div>
			</div><!-- .hentry .four04 -->

		<?php endif; ?>

		</div><!-- #current-content -->

		<div id="dynamic-content"></div>
	</div><!-- #primary -->
</div><!-- #primary-wrapper -->

	<?php get_sidebar(); ?>

</div><!-- .content -->

<?php get_footer(); ?>