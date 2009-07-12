<?php get_header(); ?>

<div class="content">
	
<div id="primary-wrapper">
	<div id="primary" role="main">
		<div id="notices"></div>

		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">
			<div class="hentry four04">

				<div class="entry-head">
					<h1 class="entry-title"><?php _e('Error 404 - Not Found','k2_domain'); ?></h1>
				</div>

				<div class="entry-content">
					<p><?php _e('Oh no! You\'re looking for something which just isn\'t here!','k2_domain'); ?></p>
				</div>

			</div> <!-- .hentry .four04 -->
		</div> <!-- #current-content .hfeed -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->
</div> <!-- #primary-wrapper -->

<?php get_sidebar(); ?>

</div> <!-- .content -->

<?php get_footer(); ?>

<!-- jegelskerRikke -->
