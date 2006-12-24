<?php
	global $wpdb, $post;

	$pages = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE (post_status = 'static' OR post_type = 'page') ORDER BY menu_order");
?>

<?php if($pages): ?>
	<ul id="page-ids" class="checkbox-list">
	<?php foreach($pages as $post): ?>
		<li><input id="display-page-id-ids-<?php echo($post->ID); ?>" name="display[page_id][ids][<?php echo($post->ID); ?>]" type="checkbox"<?php if($module->display['page_id']['ids'][$post->ID]) { ?> checked="checked"<?php } ?> /> <label for="display-page-id-ids-<?php echo($post->ID); ?>"><?php the_title(); ?></label></li>
	<?php endforeach; ?>
	</ul>

	<p><a id="check-page-ids" href="#"><?php _e('Check all', 'k2_domain'); ?></a> | <a id="uncheck-page-ids" href="#"><?php _e('Uncheck all', 'k2_domain'); ?></a></p>

	<p>
		<input id="display-page-id-show-show" name="display[page_id][show]" type="radio" value="show"<?php if($module->display['page_id']['show'] == 'show') { ?> checked="checked"<?php } ?> /> <label for="display-page-id-show-show"><?php _e('Show on checked', 'k2_domain'); ?></label><br />
		<input id="display-page-id-show-hide" name="display[page_id][show]" type="radio" value="hide"<?php if($module->display['page_id']['show'] == 'hide') { ?> checked="checked"<?php } ?> /> <label for="display-page-id-show-hide"><?php _e('Hide on checked', 'k2_domain'); ?></label>
	</p>
<?php else: ?>
	<?php _e('No pages', 'k2_domain'); ?>
<?php endif; ?>
