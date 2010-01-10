<?php get_header(); ?>

<div class="content">
	
	<div id="widgetsheader" class="widgets">
		<?php dynamic_sidebar('widgetsheader'); ?>
	</div>

	<div id="primary-wrapper">

		<div id="primary">
			<a name="startcontent" id="startcontent"></a>
			
			<div id="current-content" class="hfeed">
				<?php locate_template( array('blocks/k2-404.php'), true ); ?>
			</div> <!-- #current-content .hfeed -->
	
			<div id="dynamic-content"></div>
	
		</div> <!-- #primary -->

	</div> <!-- #primary-wrapper -->
	
	<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>

<!-- I&hearts;Rikke -->