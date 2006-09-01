<?php

/**
 * Helper function to set an option
 **/
function sbm_get_option($name) {
	global $k2sbm_current_module;

	return $k2sbm_current_module->get_option($name);
}

/**
 * Helper function to add an option
 **/
function sbm_add_option($name, $value = '', $description = '', $autoload = 'yes') {
	global $k2sbm_current_module;

	$k2sbm_current_module->add_option($name, $value, $description);
}

/**
 * Helper function to update an option
 **/
function sbm_update_option($name, $newvalue) {
	global $k2sbm_current_module;

	$k2sbm_current_module->update_option($name, $newvalue);
}

/**
 * Helper function to delete an option
 **/
function sbm_delete_option($name) {
	global $k2sbm_current_module;

	$k2sbm_current_module->delete_option($name);
}

/**
 * Helper function to register a sidebar
 **/
function register_sidebar($args = array()) {
	K2SBM::register_sidebar($args);
}

/**
 * Helper function to unregister a sidebar
 **/
function unregister_sidebar($name) {
	K2SBM::unregister_sidebar($name);
}

/**
 * Helper function to register n sidebars
 **/
function register_sidebars($count = 1, $args = array()) {
	K2SBM::register_sidebars($count, $args);
}

/**
 * Helper function to show a sidebar
 **/
function dynamic_sidebar($name = 1) {
	return K2SBM::dynamic_sidebar($name);
}

/**
 * Helper function to register a module
 **/
function register_sidebar_module($name, $callback, $css_class = '', $options = array()) {
	K2SBM::register_sidebar_module($name, $callback, $css_class, $options);
}

/**
 * Helper function to unregister a module
 **/
function unregister_sidebar_module($name) {
	K2SBM::unregister_sidebar_module($name);
}

/**
 * Helper function to check if a module is active
 **/
function is_active_module($callback) {
	return K2SBM::is_active_module($callback);
}

/**
 * Helper function to register a module's control
 **/
function register_sidebar_module_control($name, $callback) {
	K2SBM::register_sidebar_module_control($name, $callback);
}

/**
 * Helper function to unregister a module's control
 **/
function unregister_sidebar_module_control($name) {
	K2SBM::unregister_sidebar_module_control($name);
}



/**
 * Until SBM takes over the world of Wordpress sidebars ;), it's nice to allow Widgets to be transparently allowed.
 * Therefore, these helper functions are wrappers for WPW's hooks that allow SMB to use WPW widgets.
 *
 * Sorry about the daft acronyms. ;)
 **/

/**
 * WPW function to register a widget
 **/
function register_sidebar_widget($name, $callback, $classname = '') {
	K2SBM::register_sidebar_module($name, $callback, $classname);
}

/**
 * WPW function to unregister a widget
 **/
function unregister_sidebar_widget($name) {
	K2SBM::unregister_sidebar_module($name);
}

/**
 * WPW function to check if a widget is active
 **/
function is_active_widget($callback) {
	return K2SBM::is_active_module($callback);
}

/**
 * WPW function to register a widget's control
 **/
function register_widget_control($name, $callback, $width = false, $height = false) {
	// Chop off W & H, not needed
	K2SBM::register_sidebar_module_control($name, $callback);
}

/**
 * WPW function to unregister a widget's control
 **/
function unregister_widget_control($name) {
	K2SBM::unregister_sidebar_module_control($name);
}

?>
