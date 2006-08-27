<?php
class tools {
	function uninstall() {
		global $wpdb;

		// Remove the K2 options from the database
		$cleanup = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'k2%'");

		// Remove the SBM stub
		$sbm_stub_path = '../themes/' . get_option('template') . '/options/app/sbm-stub.php';
		$plugins = (array)get_option('active_plugins');
		$found = false;

		for($i = 0; !$found && $i < count($plugins); $i++) {
			if($plugins[$i] == $sbm_stub_path) {
				unset($plugins[$i]);
				update_option('active_plugins', $plugins);
				$found = true;
			}
		}

		// Flush the dang cache
		wp_cache_flush();

		// Activate the default Wordpress theme so as not to re-install K2
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');

		// Go back to the themes page
		header('Location: themes.php');
		exit;
	}
}
?>
