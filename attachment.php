<?php get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>

	<div class="primary">
		<a name="startcontent"></a>

		<div class="content hfeed">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<?php if ( ! empty($post->post_parent) ): ?>
			<div class="navigation">
				<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

			<div id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post">
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link( __('Edit', 'k2'), '<span class="entry-edit">', '</span>' ); ?>

					<div class="attachment-icon">
						<?php echo wp_get_attachment_link($post->ID, 'thumbnail', false, true); ?>
					</div>
				</div> <!-- .entry-header -->

				<div class="entry-content">
					<p class="downloadlink">
						<?php printf( __('Download %s', 'k2'), wp_get_attachment_link($post->ID, 'thumbnail') ); ?>
						<span class="file-size"><?php echo size_format( filesize( get_attached_file($post->ID) ) ); ?></span>
					<p>

					<?php the_content(); ?>
				</div><!-- .entry-content -->
			</div><!-- #entry-ID -->

			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->

			<?php if ( ! empty($post->post_parent) ): ?>
			<div class="navigation">
				<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

		<?php endwhile; else: define('K2_NOT_FOUND', true); ?>

			<?php locate_template( array('blocks/k2-404.php'), true ); ?>

		<?php endif; ?>

		</div><!-- .content -->

	</div><!-- .primary -->

	<?php get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>

</div><!-- .wrapper -->

<?php get_footer(); ?>
