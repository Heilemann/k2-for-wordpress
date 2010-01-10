<?php get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>
	
	<div id="primary">
		<a name="startcontent"></a>

		<?php /* K2 Hook */ do_action('template_primary_begin'); ?>


		<?php if ( '1' == get_option('k2rollingarchives') )
			include(TEMPLATEPATH . '/app/display/rollingarchive.php'); ?>

			<div id="content" class="hfeed">
	
				<?php include(TEMPLATEPATH . '/app/display/theloop.php'); ?>
	
			</div> <!-- #content -->


		<?php /* K2 Hook */ do_action('template_primary_end'); ?>

	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>
	
</div> <!-- .wrapper -->

<?php get_footer(); ?>