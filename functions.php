<?php 

// Current version of K2
define('K2_CURRENT', 'svn');

// Is this MU or no?
define('K2_MU', (strpos($wp_version, 'wordpress-mu') === true));

// Define our folders for WordPress & WordpressMU
if(K2_MU) {
	define('K2_STYLES_PATH', ABSPATH . UPLOADS . 'k2support/styles/');
	define('K2_HEADERS_PATH', ABSPATH . UPLOADS . 'k2support/headers/');
} else {
	define('K2_STYLES_PATH', TEMPLATEPATH . '/styles/');
	define('K2_HEADERS_PATH', TEMPLATEPATH . '/images/headers/');
}

// Sidebar manager options
define('K2_SIDEBAR_SBM', 0);
define('K2_SIDEBAR_WIDGETS', 1);
define('K2_SIDEBAR_HAND', 2);

// Are we using SBM?
define('K2_USING_SBM', !function_exists('register_sidebar') && get_option('k2sidebarmanager') == K2_SIDEBAR_SBM);

// Default style info format
define('K2_STYLE_INFO_FORMAT', '<a href="%stylelink%" title="%style% by %author%">%style% <!--%version%--></a>');

/* Blast you red baron! Initialise the k2 system */
require(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();

?>
