<?php 

// Current version of K2
define('K2_CURRENT', 'svn');

// Is this MU or no?
define('K2_MU', (isset($wpmu_version) or (strpos($wp_version, 'wordpress-mu') !== false)));

// Define our folders for WordPress & WordpressMU
if ( K2_MU ) {
	define('K2_STYLES_PATH', ABSPATH . UPLOADS . 'k2support/styles/');
	define('K2_HEADERS_PATH', ABSPATH . UPLOADS . 'k2support/headers/');
} else {
	define('K2_STYLES_PATH', TEMPLATEPATH . '/styles/');
	define('K2_HEADERS_PATH', TEMPLATEPATH . '/images/headers/');
}

// Are we using SBM?
define('K2_USING_SBM', !function_exists('register_sidebar') && get_option('k2sidebarmanager') == '1');

// Default style info format
define('K2_STYLE_FOOTER', '<a href="%stylelink%" title="%style% by %author%">%style%<!-- %version%--></a>');

// Number of sidebars to use
define('K2_SIDEBARS', 2);

// Default Header Sizes
define('K2_HEADER_WIDTH', 950);
define('K2_HEADER_HEIGHT', 200);

/* Blast you red baron! Initialise the k2 system */
require(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();

?>
