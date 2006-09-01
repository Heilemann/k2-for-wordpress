<?php

function recent_comments_sidebar_module($args) {
	global $wpdb, $comment;

	extract($args);

	echo($before_module . $before_title . $title . $after_title);

	?>
	<a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('RSS Feed for all Comments','k2_domain'); ?>" class="feedlink"><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="RSS" /></a>
	<?php

	if(function_exists('blc_latest_comments')) {
		?>
		<ul>
			<?php blc_latest_comments(sbm_get_option('num_posts'), sbm_get_option('num_comments'), sbm_get_option('hide_trackbacks'), "<li class='alternate'>", '</li>', true, 10, sbm_get_option('new_color'), sbm_get_option('old_color')); ?>
		</ul>
		<?php
	} else {
		$num_comments = sbm_get_option('num_comments');
		$sql = "SELECT comment_ID, comment_post_ID, comment_author, comment_author_url FROM $wpdb->comments WHERE comment_approved = '1' ";

		if(sbm_get_option('hide_trackbacks') == true) {
			$sql .= "AND comment_type = '' ";
		}

		$sql .= "ORDER BY comment_date_gmt DESC LIMIT $num_comments";
		$comments = $wpdb->get_results($sql);

		if($comments) {
		?>
		<ul>
			<?php foreach($comments as $comment): ?>
				<li><?php printf(__('%1$s %2$s <a href="%3$s#comment-%4$s">%5$s</a>', 'k2_domain'), get_comment_author_link(), __('on post', 'k2_domain'), get_permalink($comment->comment_post_ID), $comment->comment_ID, get_the_title($comment->comment_post_ID)); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php
		} else {
		?>
			<p><?php _e('No comments', 'k2_domain'); ?></p>
		<?php
		}
	}

	echo($after_module);
}

function recent_comments_sidebar_module_control() {
	if(isset($_POST['recent_comments_module'])) {
		// Trackbacks are a special case
		if(isset($_POST['recent_comments_module']['hide_trackbacks'])) {
			sbm_update_option('hide_trackbacks', true);
			unset($_POST['recent_comments_module']['hide_trackbacks']);
		} else {
			sbm_update_option('hide_trackbacks', false);
		}

		foreach($_POST['recent_comments_module'] as $key => $value) {
			sbm_update_option($key, $value);
		}
	}

	if(function_exists('blc_latest_comments')) {
	?>
		<p><label for="recent-comments-module-num-posts"><?php _e('Number of posts:', 'k2_domain'); ?></label> <input id="recent-comments-module-num-posts" name="recent_comments_module[num_posts]" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
	}
	?>
		<p><label for="recent-comments-module-num-comments"><?php _e('Number of comments:', 'k2_domain'); ?></label> <input id="recent-comments-module-num-comments" name="recent_comments_module[num_comments]" type="text" value="<?php echo(sbm_get_option('num_comments')); ?>" size="2" /></p>

		<p><label for="recent-comments-module-hide-trackbacks"><?php _e('Hide trackbacks/pingbacks:', 'k2_domain'); ?></label> <input id="recent-comments-module-hide-trackbacks" name="recent_comments_module[hide_trackbacks]" type="checkbox"<?php if(sbm_get_option('hide_trackbacks')) { ?> checked="checked"<?php } ?> /></p>
	<?php

	if(function_exists('blc_latest_comments')) {
	?>
		<p><label for="recent-comments-module-new-color"><?php _e('Newest color:', 'k2_domain'); ?></label> <input id="recent-comments-module-new-color" name="recent_comments_module[new_color]" type="text" value="<?php echo(sbm_get_option('new_color')); ?>" size="7" /></p>

		<p><label for="recent-comments-module-old-color"><?php _e('Oldest color:', 'k2_domain'); ?></label> <input id="recent-comments-module-old-color" name="recent_comments_module[old_color]" type="text" value="<?php echo(sbm_get_option('old_color')); ?>" size="7" /></p>
	<?php
	}
}

register_sidebar_module('Recent comments module', 'recent_comments_sidebar_module', 'sb-comments' . (function_exists('blc_latest_comments') ? ' sb-comments-blc' : ''), array('num_posts' => 5, 'num_comments' => 10, 'hide_trackbacks' => false, 'new_color' => '#444444', 'old_color' => '#cccccc'));
register_sidebar_module_control('Recent comments module', 'recent_comments_sidebar_module_control');

?>
