<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="dynamic-content">

			<?php include (TEMPLATEPATH . '/rollingarchive.php'); ?>

		</div> <!-- #dynamic-content -->
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>
	
</div> <!-- .content -->

<?php get_footer(); ?>