<?php

function nav_sidebar_module($args) {
	global $notfound, $post, $wpdb;

	extract($args);

	if(is_page() and ($notfound != '1')) {
		$current_page = $post->ID;

		while($current_page) {
			$page_query = $wpdb->get_row("SELECT ID, post_title, post_status, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
			$current_page = $page_query->post_parent;
		}

		$parent_id = $page_query->ID;
		$parent_title = $page_query->post_title;

		if ($wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_parent = '$parent_id' AND post_status != 'attachment'")) {
			echo($before_module);
			?>
				<h2><?php echo $parent_title; ?> <?php _e('Subpages','k2_domain'); ?></h2>

				<ul>
					<?php wp_list_pages('sort_column=menu_order&title_li=&child_of='. $parent_id); ?>
				</ul>

				<?php if ($parent_id != $post->ID) { ?>
					<a href="<?php echo get_permalink($parent_id); ?>"><?php printf(__('Back to %s','k2_domain'), $parent_title ) ?></a>
				<?php } ?>
			<?php
			echo($after_module);
		}
	}

	if(is_attachment()) {
	?>
		<div class="sb-pagemenu">
			<a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php printf(__('Back to \'%s\'','k2_domain'), get_the_title($post->post_parent) ) ?></a>
		</div>
	<?php
	}
}

register_sidebar_module(__('Navigation module', 'k2_domain'), 'nav_sidebar_module', 'sb-pagemenu');

?>