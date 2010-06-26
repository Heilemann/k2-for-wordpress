<?php
/**
 * The template for displaying the sidebar.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

if ( ! get_post_custom_values('hidesidebar1') && is_active_sidebar('widgets-sidebar-1') ): ?>

	<div id="widgets-sidebar-1" class="widgets secondary">
		<?php dynamic_sidebar('widgets-sidebar-1'); ?>
	</div><!-- #widgets-sidebar-1 -->

<?php endif; ?>


<?php if ( ! get_post_custom_values('hidesidebar2') && is_active_sidebar('widgets-sidebar-2') ): ?>

	<div id="widgets-sidebar-2" class="widgets secondary">
		<?php dynamic_sidebar('widgets-sidebar-2'); ?>
	</div><!-- #widgets-sidebar-2 -->

<?php endif; ?>


<div class="clear"></div>
