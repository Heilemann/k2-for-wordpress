<?php
/**
 * The template for displaying attachments.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

get_header(); ?>

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
