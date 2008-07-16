<?php

if ( ('1' == get_option('k2sidebarmanager')) and ('1' == get_option('k2active')) ) {

	// Don't disable Widgets in WP 2.4+ Dashboard
	if ( is_admin() and version_compare($wp_version, '2.4', '>') and (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' or basename($_SERVER['SCRIPT_FILENAME']) == 'index-extra.php') ) return;

	// Disable Widgets
	remove_action('plugins_loaded', 'wp_maybe_load_widgets', 0);

	// Load SBM
	require_once( dirname(__FILE__) . '/../classes/sbm.php' );
	require_once( dirname(__FILE__) . '/../includes/sbm.php' );
}
?>