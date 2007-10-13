<?php
if (get_option('k2sidebarmanager') == '1') {
	remove_action('plugins_loaded', 'wp_maybe_load_widgets', 0);
}
?>
