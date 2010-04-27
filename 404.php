<?php get_header(); ?>

<div class="content">
	
<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>

		<a name="startcontent" id="startcontent"></a>
		
			<?php locate_template( array('blocks/k2-404.php'), true ); ?>
		
		</div> <!-- #current-content .hfeed -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->
</div> <!-- #primary-wrapper -->

<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>
