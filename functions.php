<?php 

/**
 * Allows for the styles directory to be moved from the default location.
 *
 * @since 1.0-RC8
 */
define('K2_STYLES_DIR', TEMPLATEPATH . '/styles'); // no trailing slash, full paths only
define('K2_STYLES_URL', get_template_directory_uri() . '/styles'); // full url

/**
 * Allows for the headers directory to be moved from the default location.
 *
 * @since 1.0-RC8
 */
define('K2_HEADERS_DIR', TEMPLATEPATH . '/images/headers'); // no trailing slash, full paths only
define('K2_HEADERS_URL', get_template_directory_uri() . '/images/headers'); // full url

/**
 * Set the number of sidebars
 *
 * @since 1.0-RC6
 */
define('K2_SIDEBARS', 2);

/**
 * The default maximum width and height for the header image
 *
 * @since 1.0-RC6
 */
define('K2_HEADER_WIDTH', 950);
define('K2_HEADER_HEIGHT', 200);

/**
 * DO NOT MODIFY BELOW THIS LINE
 */

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