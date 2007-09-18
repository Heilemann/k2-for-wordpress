<?php
	global $wpdb, $post;

	$posts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type != 'page' ORDER BY post_date_gmt DESC");
?>

<?php if($posts): ?>
	<ul id="post-ids" class="checkbox-list">
	<?php foreach($posts as $post): ?>
		<li><input id="display-post-id-ids-<?php echo($post->ID); ?>" name="display[post_id][ids][<?php echo($post->ID); ?>]" type="checkbox"<?php if($module->display['post_id']['ids'][$post->ID]) { ?> checked="checked"<?php } ?> /> <label for="display-post-id-ids-<?php echo($post->ID); ?>"><?php the_title(); ?></label></li>
	<?php endforeach; ?>
	</ul>

	<div class="tools">
		<p class="checkoruncheck">
			<a id="check-post-ids" href="#"><?php _e('Check all', 'k2_domain'); ?></a> or <a id="uncheck-post-ids" href="#"><?php _e('Uncheck all', 'k2_domain'); ?></a></p>

		<p class="showorhide">
			<input id="display-post-id-show-show" name="display[post_id][show]" type="radio" value="show"<?php if($module->display['post_id']['show'] == 'show') { ?> checked="checked"<?php } ?> /> <label for="display-post-id-show-show"><?php _e('Show on checked', 'k2_domain'); ?></label>
			<input id="display-post-id-show-hide" name="display[post_id][show]" type="radio" value="hide"<?php if($module->display['post_id']['show'] == 'hide') { ?> checked="checked"<?php } ?> /> <label for="display-post-id-show-hide"><?php _e('Hide on checked', 'k2_domain'); ?></label>
		</p>
	</div>
	
<?php else: ?>
	<?php _e('No posts', 'k2_domain'); ?>
<?php endif; ?>
