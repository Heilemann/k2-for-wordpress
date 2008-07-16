<?php

function sbm_get_option($name) {
	global $k2sbm_current_module;

	return $k2sbm_current_module->get_option($name);
}

function sbm_add_option($name, $value = '', $description = '', $autoload = 'yes') {
	global $k2sbm_current_module;

	$k2sbm_current_module->add_option($name, $value, $description);
}

function sbm_update_option($name, $newvalue) {
	global $k2sbm_current_module;

	$k2sbm_current_module->update_option($name, $newvalue);
}

function sbm_delete_option($name) {
	global $k2sbm_current_module;

	$k2sbm_current_module->delete_option($name);
}

function register_sidebar($args = array()) {
	K2SBM::register_sidebar($args);
}

function unregister_sidebar($name) {
	K2SBM::unregister_sidebar($name);
}

function register_sidebars($count = 1, $args = array()) {
	K2SBM::register_sidebars($count, $args);
}

function dynamic_sidebar($name = 1) {
	return K2SBM::dynamic_sidebar($name);
}

function register_sidebar_module($name, $callback, $css_class = '', $options = array()) {
	K2SBM::register_sidebar_module($name, $callback, $css_class, $options);
}

function unregister_sidebar_module($name) {
	K2SBM::unregister_sidebar_module($name);
}

function is_active_module($callback) {
	return K2SBM::is_active_module($callback);
}

function register_sidebar_module_control($name, $callback) {
	K2SBM::register_sidebar_module_control($name, $callback);
}

function unregister_sidebar_module_control($name) {
	K2SBM::unregister_sidebar_module_control($name);
}



// New Widgets API - WordPress 2.4+

function wp_register_widget_control() {
}

function wp_register_sidebar_widget() {
}


// Old Widgets API

function register_sidebar_widget($name, $callback, $classname = '') {
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	K2SBM::register_sidebar_module($name, $callback, $classname);
}

function unregister_sidebar_widget($name) {
	K2SBM::unregister_sidebar_module($name);
}

function is_active_widget($callback) {
	return K2SBM::is_active_module($callback);
}

function register_widget_control($name, $callback, $width = false, $height = false) {
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	// Chop off W & H, not needed
	K2SBM::register_sidebar_module_control($name, $callback);
}

function unregister_widget_control($name) {
	K2SBM::unregister_sidebar_module_control($name);
}

?>
