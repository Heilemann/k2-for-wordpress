<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="notices"></div>

		<?php if (get_option('k2rollingarchives') == 1) { ?>
		<div id="dynamic-content">

			<?php include (TEMPLATEPATH . '/rollingarchive.php'); ?>

		</div> <!-- #dynamic-content -->
		<?php } else { ?>
		<div id="current-content" class="hfeed">

			<?php include (TEMPLATEPATH . '/theloop.php'); ?>

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
		<?php } ?>
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>
	
</div> <!-- .content -->

<?php get_footer(); ?>