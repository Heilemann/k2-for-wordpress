<?php

function nav_sidebar_module($args) {
	global $post, $wpdb;

	extract($args);

	if(is_page() and !defined('K2_NOT_FOUND')) {
		$current_page = $post->ID;

		while($current_page) {
			$page_query = $wpdb->get_row("SELECT ID, post_title, post_status, post_parent FROM $wpdb->posts WHERE ID = '$current_page'");
			$current_page = $page_query->post_parent;
		}

		$parent_id = $page_query->ID;
		$parent_title = $page_query->post_title;

		$page_menu = wp_list_pages('echo=0&sort_column=menu_order&title_li=&child_of='. $parent_id);
		if ($page_menu) {
			echo($before_module . $before_title . $title . $after_title);
			?>

				<ul>
					<?php echo $page_menu; ?>
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
			<a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php printf(__('Back to \'%s\'','k2_domain'), get_the_title($post->post_parent) ); ?></a>
		</div>
	<?php
	}
}

function nav_sidebar_module_control() {
	if(isset($_POST['nav_sidebar_module_custom_title'])) {
		sbm_update_option('custom_title', $_POST['nav_sidebar_module_custom_title']);
	}

	?>
		<p><label for="nav-sidebar-module-custom-title"><?php _e('Navigation heading:', 'k2_domain'); ?></label> <input id="nav-sidebar-module-custom-title" name="nav_sidebar_module_custom_title" type="text" value="<?php echo(sbm_get_option('custom_title')); ?>" size="30" /></p>
	<?php
}

register_sidebar_module('Page Menu', 'nav_sidebar_module', 'sb-pagemenu', array('custom_title' => __('%s Subpages', 'k2_domain') ));
register_sidebar_module_control('Page Menu', 'nav_sidebar_module_control');

?>
