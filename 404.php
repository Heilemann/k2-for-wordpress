<?php if ( ! isset($_GET['k2dynamic']) ) { ?>

<?php get_header(); ?>

<div class="content">
	
	<div id="primary">
		<div id="notices"></div>

		<div id="current-content" class="hfeed">
<?php } ?>
			<div class="hentry four04">

				<div class="page-head">
					<h2><?php _e('Error 404 - Not Found','k2_domain'); ?></h2>
				</div>

				<div class="entry-content">
					<p><?php _e('Oh no! You\'re looking for something which just isn\'t here!','k2_domain'); ?></p>
				</div>

			</div> <!-- .hentry .four04 -->

<?php if ( ! isset($_GET['k2dynamic']) ) { ?>
		</div> <!-- #current-content .hfeed -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<?php define('K2_NOT_FOUND', true); /* So we can tell the sidebar what to do */ ?>
	<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>

<!-- jegelskerRikke -->
<?php } ?>
