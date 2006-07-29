<?php

function subpage_nav_sidebar_module($args) {
	global $wpdb, $post;

	extract($args);

	// To check if there is a post here
	rewind_posts();

	if(is_page()
		&& have_posts()
		&& ($post->post_parent
			|| $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$post->ID' LIMIT 1")
		)
	) {
		echo($before_module);
		?>
		<ul>
			<li class="pagenav"><?php echo($before_title . $title . $after_title); ?></li>

			<?php wp_list_pages('sort_column=menu_order&child_of=' . $post->ID . '&title_li='); ?>

			<?php if($post->post_parent): ?>
				<li class="page_item"><a href="<?php echo(get_permalink($post->post_parent)); ?>"><?php _e('Back to'); ?> <?php echo(get_the_title($post->post_parent)); ?></a></li>
			<?php endif; ?>
		</ul>
		<?php
		echo($after_module);
	}
}

function subpage_nav_sidebar_module_control() {
	?>
	<p>This will only display on pages, nothing else.</p>
	<?php
}


register_sidebar_module('Subpage navigation module', 'subpage_nav_sidebar_module', 'sb-pages');
register_sidebar_module_control('Subpage navigation module', 'subpage_nav_sidebar_module_control');

?>
