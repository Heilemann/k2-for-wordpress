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
			<?php blc_latest_comments(sbm_get_option('num_comments'),'3','false'); ?>
		</ul>
		<?php
	} else {
		$num_comments = sbm_get_option('num_comments');
		$comments = $wpdb->get_results("SELECT comment_ID, comment_post_ID, comment_author, comment_author_url FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $num_comments");

		if($comments) {
		?>
		<ul>
			<?php foreach($comments as $comment): ?>
				<li><?php printf('%1$s %2$s <a href="%3$s#comment-%4$s">%5$s</a>', get_comment_author_link(), __('on post'), get_permalink($comment->comment_post_ID), $comment->comment_ID, get_the_title($comment->comment_post_ID)); ?></li>
			<?php endforeach; ?>
		</ul>
		<?php
		} else {
		?>
		<p>No comments</p>
		<?php
		}
	}

	echo($after_module);
}

function recent_comments_sidebar_module_control() {
	if(isset($_POST['recent_comments_module_num_comments'])) {
		sbm_update_option('num_comments', $_POST['recent_comments_module_num_comments']);
	}

	?>
		<p><label for="recent-comments-module-num-comments">Number of comments:</label> <input id="recent-comments-module-num-comments" name="recent_comments_module_num_comments" type="text" value="<?php echo(sbm_get_option('num_comments')); ?>" size="2" /></p>
	<?php
}

register_sidebar_module('Recent comments module', 'recent_comments_sidebar_module', 'sb-comments' . (function_exists('blc_latest_comments') ? ' sb-comments-blc' : ''), array('num_comments' => 10));
register_sidebar_module_control('Recent comments module', 'recent_comments_sidebar_module_control');

?>
