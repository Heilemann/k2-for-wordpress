<?php
/**
 * K2 definitions.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 1.0
 */

// Current version of K2
define( 'K2_CURRENT', '1.5a1' );

// URL for the template directory (for use with styles)
@define( 'TEMPLATEURL', get_bloginfo('stylesheet_directory') );

// Features that can be disabled by Child Themes
@define( 'K2_HEADERS', true );

// Loads localisation from K2's languages directory
load_theme_textdomain('k2', TEMPLATEPATH . '/languages');

/* Blast you red baron! Initialize the K2 system! */
require_once(TEMPLATEPATH . '/app/classes/k2.php');
add_action( 'after_setup_theme', array( 'K2', 'init' ) );
