<?php get_header(); ?>

<div class="wrapper">
	
	<?php if ( is_active_sidebar('widgetsheader') ) : ?>
	<div id="widgetsheader" class="widgets">
		<?php dynamic_sidebar('widgetsheader'); ?>
	</div>
	<?php endif; ?>

	<div id="primary">
		<a name="startcontent" id="startcontent"></a>
		
		<div id="content" class="hfeed">
			<?php locate_template( array('blocks/k2-404.php'), true ); ?>
		</div> <!-- #content .hfeed -->

	</div> <!-- #primary -->
	
	<?php get_sidebar(); ?>

</div> <!-- .wrapper -->

<?php get_footer(); ?>

<!-- I&hearts;Rikke -->