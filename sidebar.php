<hr />


<?php if ( ! get_post_custom_values('hidesidebar1') ): ?>

	<div id="sidebar-1" class="widgets secondary">
		<?php dynamic_sidebar('Sidebar #1'); ?>
	</div><!-- #sidebar-1 -->

<?php endif; ?>


<hr />


<?php if ( ! get_post_custom_values('hidesidebar2') ): ?>

	<div id="sidebar-2" class="widgets secondary">
		<?php dynamic_sidebar('Sidebar #2'); ?>
	</div><!-- #sidebar-2 -->

<?php endif; ?>


<div class="clear"></div>
