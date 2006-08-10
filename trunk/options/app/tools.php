<?php
class tools {
	function uninstall() {
		global $wpdb;

		// Remove the K2 options from the database
		$cleanup = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'k2%'");

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
