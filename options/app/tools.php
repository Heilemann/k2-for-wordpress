<?php
class tools {
	function uninstall() {
		global $wpdb;
		$cleanup = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'k2%'");
		}
	}
?>
