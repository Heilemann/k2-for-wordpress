<?php
/**
 * The template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 1.0
 */

get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>

	<div class="primary">
		<?php /* K2 Hook */ do_action('template_primary_begin'); ?>

		<div id="content" class="content">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<nav id="nav-above" class="navigation">
				<h1 class="section-heading"><?php _e( 'Post navigation', 'k2' ); ?></h1>
				<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> %title' ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&raquo;</span>' ); ?></div>
			</nav><!-- #nav-above -->

			<article id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php get_template_part('blocks/k2-post' , function_exists('get_post_format') ? get_post_format( $post->ID ) : '' ); ?>
			</article><!-- #entry-ID -->

			<?php if ( is_active_sidebar('widgets-post') ): ?>
			<div id="widgetspost" class="widgets">
				<?php dynamic_sidebar('widgets-post'); ?>
			</div>
			<?php endif; ?>

			<nav id="nav-below" class="navigation">
				<h1 class="section-heading"><?php _e( 'Post navigation', 'k2' ); ?></h1>
				<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> %title' ); ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&raquo;</span>' ); ?></div>
			</nav><!-- #nav-above -->

			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->

		<?php endwhile; else: get_template_part( 'blocks/k2-404' ); endif; ?>

		</div><!-- .content -->

		<?php /* K2 Hook */ do_action('template_primary_end'); ?>

	</div><!-- .primary -->

	<?php if ( ! get_post_custom_values('sidebarless') ) get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>

</div><!-- .wrapper -->

<?php
	/* Initialize Rolling Archives if needed */
	if ( defined('DOING_AJAX') and true == DOING_AJAX ) {
		add_action( 'k2_dynamic_content', array('K2', 'setup_rolling_archives') );
	}
?>

<?php get_footer(); ?>
