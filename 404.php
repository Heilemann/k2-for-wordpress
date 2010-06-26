<?php
/**
 * The template for displaying 404 pages (Not Found).
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

		<?php /* K2 Hook */ do_action('template_primary_begin'); ?>

		<div class="content hfeed">

			<?php locate_template( array('blocks/k2-404.php'), true ); ?>

		</div><!-- .content .hfeed -->

		<?php /* K2 Hook */ do_action('template_primary_end'); ?>

	</div><!-- .primary -->

	<?php get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>

</div> <!-- .wrapper -->

<?php get_footer(); ?>

<!-- I&hearts;Rikke -->
