<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="current-content">

			<?php include (TEMPLATEPATH . '/rollingarchive.php'); ?>

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>