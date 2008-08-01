<?php get_header(); ?>

<div class="content">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

			<?php include (TEMPLATEPATH . '/theloop.php'); ?>

			<div class="entry-comments comments">
				<?php comments_template(); ?>
			</div> <!-- .entry-comments -->

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->
</div> <!-- #primary-wrapper -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>