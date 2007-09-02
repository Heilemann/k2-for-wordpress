<?php get_header(); ?>

<div class="content">

	<div id="primary">
		<div id="notices"></div>

		<div id="current-content" class="hfeed">

			<?php include (TEMPLATEPATH . '/theloop.php'); ?>
  			<?php if (!defined('K2_NOT_FOUND')) { comments_template(); } ?>  

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>