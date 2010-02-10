<?php 
// Current version of K2
define('K2_CURRENT', '1.1');

// Is this MU or no?
define('K2_MU', (isset($wpmu_version) or (strpos($wp_version, 'wordpress-mu') !== false)));

// Are we using K2 Styles?
define('K2_CHILD_THEME', get_stylesheet() != get_template());

// URL for the template directory (for use with styles)
@define( 'TEMPLATEURL', get_bloginfo('stylesheet_directory') );

// Features that can be disabled by Child Themes
@define( 'K2_STYLES', true );
@define( 'K2_HEADERS', true );

// WordPress compatibility
@define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
@define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content' );

// Loads localisation from K2's languages directory
load_theme_textdomain('k2', TEMPLATEPATH . '/languages');
		
/* Blast you red baron! Initialize the K2 system! */
require_once(TEMPLATEPATH . '/app/classes/k2.php');
K2::init();
?>