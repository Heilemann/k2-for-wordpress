<?php
	$primary_width = (int) get_option('k2_960_primarywidth');
?>

<?php get_header(); ?>

<div class="content container_16">
	
	<div id="primary" class="grid_<?php echo $primary_width; ?>">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<?php if ( '1' == get_option('k2rollingarchives') ): ?>
		<div id="dynamic-content">

			<?php include(TEMPLATEPATH . '/app/display/rollingarchive.php'); ?>

		</div><!-- #dynamic-content -->
		<?php else: ?>
		<div id="current-content" class="hfeed">

			<?php include(TEMPLATEPATH . '/app/display/theloop.php'); ?>

		</div><!-- #current-content -->

		<div id="dynamic-content"></div>
		<?php endif; ?>
	</div><!-- #primary -->

	<?php get_sidebar(); ?>

</div><!-- .content -->

<?php locate_template( array('bottombar.php'), true ); ?>

<?php get_footer(); ?>