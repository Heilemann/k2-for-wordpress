<?php 
// Current version of K2
define('K2_CURRENT', '1.0-RC7.2');

// Is this MU or no?
define('K2_MU', (isset($wpmu_version) or (strpos($wp_version, 'wordpress-mu') !== false)));

// Are we using K2 Styles?
define('K2_USING_STYLES', get_stylesheet() == get_template());

// WordPress compatibility
if ( ! defined('WP_CONTENT_DIR') ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined('WP_CONTENT_URL') ) define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );

/* Blast you red baron! Initialise the k2 system */
require_once(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();
?>