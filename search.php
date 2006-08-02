<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="current_content">
			<div>
			<?php include (TEMPLATEPATH . '/rollingarchive.php'); ?>
			</div>
		</div>

		<div id="dynamic_content"></div>
	</div>

	<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>