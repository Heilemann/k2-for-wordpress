<?php 
/* Current version of K2 */
$current = 'svn';

load_theme_textdomain('k2_domain');

/* Blast you red baron! Initialise the k2 system */

require(TEMPLATEPATH . '/options/app/archive.php');
require(TEMPLATEPATH . '/options/app/headers.php');
require(TEMPLATEPATH . '/options/app/info.php');
require(TEMPLATEPATH . '/options/app/install.php');
require(TEMPLATEPATH . '/options/app/options.php');
require(TEMPLATEPATH . '/options/app/tools.php');

// Install and update K2 if necessary
global $options_revision;
if (!get_option('k2optionsrevision') or get_option('k2optionsrevision') < $options_revision) {
	installk2::installer();
}

// K2 options
options::init();

// K2 header
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
