<?php 

// Current version of K2
define('K2_CURRENT', '1.0-RC7');

// Is this MU or no?
define('K2_MU', (isset($wpmu_version) or (strpos($wp_version, 'wordpress-mu') !== false)));

// Define our folders for Wordpress
define('K2_STYLES_PATH', TEMPLATEPATH . '/styles/');
define('K2_HEADERS_PATH', TEMPLATEPATH . '/images/headers/');

// Define additional folders for Wordpress mu
if ( K2_MU ) {
	define('K2_MU_STYLES_PATH', ABSPATH . UPLOADS . 'k2support/styles/');
	define('K2_MU_HEADERS_PATH', ABSPATH . UPLOADS . 'k2support/headers/');
}

// Are we using K2 Styles?
define('K2_USING_STYLES', get_stylesheet() == get_template());

// Number of sidebars to use
define('K2_SIDEBARS', 2);

// Default Header Sizes
define('K2_HEADER_WIDTH', 950);
define('K2_HEADER_HEIGHT', 200);

/* Blast you red baron! Initialise the k2 system */
require_once(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();
?>