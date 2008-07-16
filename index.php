<?php get_header(); ?>

<div class="content">
	
<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<?php if ( '1' == get_option('k2rollingarchives') ): ?>
		<div id="dynamic-content">

			<?php include(TEMPLATEPATH . '/rollingarchive.php'); ?>

		</div> <!-- #dynamic-content -->
		<?php else: ?>
		<div id="current-content" class="hfeed">

			<?php include(TEMPLATEPATH . '/theloop.php'); ?>

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
		<?php endif; ?>
	</div> <!-- #primary -->
</div> <!-- #primary-wrapper -->

	<?php get_sidebar(); ?>
	
</div> <!-- .content -->

<?php get_footer(); ?>