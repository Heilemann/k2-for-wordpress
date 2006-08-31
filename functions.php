<?php 
/* Current version of K2 */
$current = 'svn';

load_theme_textdomain('k2_domain');

/* Blast you red baron! Initialise the k2 system */

require(TEMPLATEPATH . '/options/app/archive.php');
require(TEMPLATEPATH . '/options/app/options.php');
require(TEMPLATEPATH . '/options/app/update.php');
require(TEMPLATEPATH . '/options/app/info.php');
require(TEMPLATEPATH . '/options/app/tools.php');
require(TEMPLATEPATH . '/options/app/headers.php');

// Install and update K2 if necessary
global $options_revision;
if (!get_option('k2optionsrevision') or get_option('k2optionsrevision') < $options_revision) {
	installk2::installer();
}

// Let's add the options page.
add_action ('admin_menu', 'k2menu');

$k2loc = '../themes/'.basename(dirname($file)); 

function k2menu() {
	add_submenu_page('themes.php', __('K2 Options','k2_domain'), __('K2 Options','k2_domain'), 5, $k2loc . 'functions.php', 'menu');
}

function menu() {
	load_plugin_textdomain('k2options');
	//this begins the admin page

	include(TEMPLATEPATH . '/options/display/form.php');
}

// K2 Headers
headers::init();

// Sidebar Modules for K2
if(class_exists('k2sbm')) {
	k2sbm::k2_init();
}

// Sidebar registration for dynamic sidebars
if(function_exists('register_sidebar')) {
	register_sidebar(array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>'));
}

// this ends the admin page ?>
