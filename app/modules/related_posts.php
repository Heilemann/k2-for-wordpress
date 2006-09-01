<?php

function related_posts_sidebar_module($args) {
	global $notfound;

	extract($args);

	if(is_single() and ($notfound != '1')) {
		echo($before_module . $before_title . $title . $after_title);
		?>
			<ul>
				<?php related_posts(sbm_get_option('num_posts'), 0, '<li>', '</li>', '', '', false, false); ?>
			</ul>
		<?php
		echo($after_module);
	}
}

function related_posts_sidebar_module_control() {
	if(isset($_POST['related_posts_module_num_posts'])) {
		sbm_update_option('num_posts', $_POST['related_posts_module_num_posts']);
	}

	?>
		<p><label for="related-posts-module-num-posts"><?php _e('Maximum number of posts:', 'k2_domain'); ?></label> <input id="related-posts-module-num-posts" name="related_posts_module_num_posts" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
}

if(function_exists('related_posts')) {
	register_sidebar_module('Related posts module', 'related_posts_sidebar_module', 'sb-related', array('num_posts' => 10));
	register_sidebar_module_control('Related posts module', 'related_posts_sidebar_module_control');
}

?>
