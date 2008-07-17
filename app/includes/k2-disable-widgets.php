<?php
/*
Plugin Name: K2 Disable Widgets
Plugin URI: http://getk2.com/
Description: This plugin disables the built-in WordPress widgets, thus enabling K2's SBM.
Author: Various Artists
Version: 1.0-RC7
Author URI: http://getk2.com/
*/

if ( '1' == get_option('k2active') ) {

	// Don't disable Widgets in WP 2.4+ Dashboard
	if ( is_admin() and version_compare($wp_version, '2.4', '>') and ( basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' or basename($_SERVER['SCRIPT_FILENAME']) == 'index-extra.php') ) return;

	// Disable Widgets
	remove_action('plugins_loaded', 'wp_maybe_load_widgets', 0);

	// Tell K2 to load SBM
	define('K2_LOAD_SBM', true);
}
?>