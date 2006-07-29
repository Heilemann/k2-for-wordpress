<?php

function latest_posts_sidebar_module($args) {
	extract($args);

	$num_posts = sbm_get_option('num_posts');

	echo($before_module . $before_title . $title . $after_title);
	?>
	<ul>
		<?php wp_get_archives('type=postbypost&limit=' . $num_posts); ?>
	</ul>
	<?php
	echo($after_module);
}

function latest_posts_sidebar_module_control() {
	if(isset($_POST['latest_posts_module_num_posts'])) {
		sbm_update_option('num_posts', $_POST['latest_posts_module_num_posts']);
	}

	?>
		<p><label for="latest-posts-module-num-posts">Number of posts:</label> <input id="latest-posts-module-num-posts" name="latest_posts_module_num_posts" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
}

register_sidebar_module('Latest posts module', 'latest_posts_sidebar_module', 'sb-latest', array('num_posts' => 10));
register_sidebar_module_control('Latest posts module', 'latest_posts_sidebar_module_control');

?>
