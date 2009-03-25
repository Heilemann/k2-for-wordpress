<?php
/*
Plugin Name: K2 Sidebar Manager
Plugin URI: http://getk2.com/
Description: This is the K2 Sidebar Manager from K2 1.0-RC7 based on Sidebar Modules. Widgets will be disabled. This is no longer supported.
Author: Various Artists
Version: 1.0-RC7
Author URI: http://getk2.com/
*/

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

// Don't disable Widgets in WP 2.4+ Dashboard
if ( is_admin() and version_compare($wp_version, '2.4', '>') and ( basename($_SERVER['SCRIPT_FILENAME']) == 'index.php' or basename($_SERVER['SCRIPT_FILENAME']) == 'index-extra.php') ) return;

// Disable Widgets
remove_action('plugins_loaded', 'wp_maybe_load_widgets', 0);

// Tell K2 we're loading SBM
define('K2_LOAD_SBM', true);

if ( ! function_exists('register_sidebar') ) {
	require_once( dirname(__FILE__) . '/class.sbm.php');
	K2SBM::init();
}