<?php 
/* Current revision of K2 */
$current = 'svn';

load_theme_textdomain('k2_domain');

/* Blast you red baron! Initialise the k2 system */

require(TEMPLATEPATH . '/options/app/archive.php');
require(TEMPLATEPATH . '/options/app/options.php');
require(TEMPLATEPATH . '/options/app/update.php');
require(TEMPLATEPATH . '/options/app/info.php');

// If K2 isn't installed, install it. This should run only one more time for all our existing users, then they will just be getting the upgrade function if it exists.
if (!get_option('k2installed')) {
	installk2::installer();
}

// Here we handle upgrading our users with new options and such. If k2installed is in the DB but the version they are running is lower than our current version, trigger this event.
elseif (get_option('k2installed') < $current) {
/* Do something! */
//add_option('k2upgrade-test', 'this is the text', 'Just testing', $autoload);
}

// Let's add some support for WordPress Widgets
if (function_exists('register_sidebar')) register_sidebar(array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>'));

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

// include Hasse R. Hansen's K2 header plugin - http://www.ramlev.dk
require(TEMPLATEPATH . '/options/display/headers.php');

// this ends the admin page ?>
