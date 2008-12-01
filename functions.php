<?php 

// Current version of K2
define('K2_CURRENT', '1.0-RC7.1');

// Is this MU or no?
define('K2_MU', (isset($wpmu_version) or (strpos($wp_version, 'wordpress-mu') !== false)));

// Uncomment below to set a different path for K2 to look for styles or headers
//define('K2_STYLES_DIR', TEMPLATEPATH . '/styles');
//define('K2_STYLES_URL', get_template_directory_uri() . '/styles');
//define('K2_HEADERS_DIR', TEMPLATEPATH . '/images/headers');
//define('K2_HEADERS_URL', get_template_directory_uri() . '/images/headers');

// Are we using K2 Styles?
define('K2_USING_STYLES', get_stylesheet() == get_template());

// Number of sidebars to use
define('K2_SIDEBARS', 2);

// Default Header Sizes
define('K2_HEADER_WIDTH', 950);
define('K2_HEADER_HEIGHT', 200);

// WordPress compatibility
if ( !defined('WP_CONTENT_DIR') ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined('WP_CONTENT_URL') ) define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');

/* Blast you red baron! Initialise the k2 system */
require_once(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();
?>