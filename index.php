<?php get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>
	
	<div class="primary">
		<a name="startcontent"></a>

		<?php /* K2 Hook */ do_action('template_primary_begin'); ?>

		<?php /* Top Navigation */ k2_navigation('nav-above'); ?>

		<div class="content" class="hfeed">

			<?php include(TEMPLATEPATH . '/app/display/theloop.php'); ?>

		</div> <!-- .content -->

		<?php /* Bottom Navigation */ k2_navigation('nav-below'); ?> 
	
		<?php /* K2 Hook */ do_action('template_primary_end'); ?>

	</div> <!-- .primary -->

	<?php get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>
	
</div> <!-- .wrapper -->

<?php get_footer(); ?>