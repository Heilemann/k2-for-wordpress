<?php
// Prevent users from directly loading this class file
defined('K2_CURRENT') or die ('Error: This file can not be loaded directly.');

/**
 * K2 - Main class
 *
 * @package K2
 */
class K2 {

	/**
	 * init() - Class constructor
	 *
	 * @uses do_action() Provides 'k2_init' action
	 */
	function init() {
		// Load the localisation text
		load_theme_textdomain('k2_domain');

		if ( ! defined('K2_STYLES_DIR') )
			define('K2_STYLES_DIR', TEMPLATEPATH . '/styles');

		if ( ! defined('K2_STYLES_URL') )
			define('K2_STYLES_URL', get_template_directory_uri() . '/styles');

		if ( ! defined('K2_HEADERS_DIR') )
			define('K2_HEADERS_DIR', TEMPLATEPATH . '/images/headers');

		if ( ! defined('K2_HEADERS_URL') )
			define('K2_HEADERS_URL', get_template_directory_uri() . '/images/headers');

		// Load required classes and includes
		require_once(TEMPLATEPATH . '/app/classes/archive.php');
		require_once(TEMPLATEPATH . '/app/classes/header.php');
		require_once(TEMPLATEPATH . '/app/classes/options.php');
		require_once(TEMPLATEPATH . '/app/includes/info.php');
		require_once(TEMPLATEPATH . '/app/includes/display.php');
		require_once(TEMPLATEPATH . '/app/includes/comments.php');
		require_once(TEMPLATEPATH . '/app/includes/wp-compat.php');

		if ( defined('K2_LOAD_SBM') ) {
			require_once(TEMPLATEPATH . '/app/classes/sbm.php');
			require_once(TEMPLATEPATH . '/app/includes/sbm.php');
		} else {
			require_once(TEMPLATEPATH . '/app/classes/widgets.php');
			//require_once(TEMPLATEPATH . '/app/includes/widgets.php');
		}

		// Check installed version, upgrade if needed
		$k2version = get_option('k2version');

		if ( $k2version === false )
			K2::install();
		elseif ( version_compare($k2version, K2_CURRENT, '<') )
			K2::upgrade($k2version);

		// Set K2 to active
		if ( '0' == get_option('k2active') ) {
			update_option('k2active', '1');
		}

		// Register our scripts with script loader
		K2::register_scripts();

		// Register our sidebar with widgets
		register_sidebars( K2_SIDEBARS, array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		) );

		// Load the current style's functions.php if it is readable
		$style_functions = dirname( K2_STYLES_DIR . '/' . get_option('k2style') ) . '/functions.php';
		if ( K2_USING_STYLES and is_readable($style_functions) )
			include_once($style_functions);

		// Finally load pluggable functions
		require_once(TEMPLATEPATH . '/app/includes/pluggable.php');

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');
	}


	/**
	 * install() - Starts the installation process and creates supporting folders for WordPress mu.
	 *
	 * @uses do_action() Provides 'k2_install' action
	 */
	function install() {
		// Add the version number
		add_option('k2version', K2_CURRENT, 'This option stores K2\'s version number');

		// Call the install handlers
		do_action('k2_install');
	}


	/**
	 * upgrade() - Starts the upgrade process
	 *
	 * @uses do_action() Provides 'k2_upgrade' action
	 * @param string $previous Previous version K2
	 */
	function upgrade($previous) {
		// Call the upgrade handlers
		do_action('k2_upgrade', $previous);

		if ( version_compare( $previous, '1.0-RC6.2', '<' ) ) {

			// Remove previous SBM hackery
			$found = false;
			$plugins = (array) get_option('active_plugins');

			foreach ($plugins as $key => $plugin) {
				if ( (strpos( $plugin, 'sbm-stub.php' ) !== false)
					or (strpos( $plugin, 'widgets-removal.php' ) !== false )
					or (strpos( $plugin, 'k2-sbm-loader.php' ) !== false ) ) {

					unset( $plugins[$key] );
					$found = true;
				}
			}

			if ( $found )
				update_option('active_plugins', $plugins);
		}

		// Update the version
		update_option('k2version', K2_CURRENT);
	}


	/**
	 * uninstall() - Activates Default theme and removes K2 options
	 *
	 * @uses do_action() Provides 'k2_uninstall' action
	 * @global mixed $wpdb
	 */
	function uninstall($switch_theme = false) {
		global $wpdb;

		// Call the uninstall handlers
		do_action('k2_uninstall');

		// Delete options
		delete_option('k2version');
		delete_option('k2active');

		// Remove the K2 options from the database. This is a catch-all
		$cleanup = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'k2%'");

		if ( $switch_theme ) {
			// Flush the dang cache
			wp_cache_flush();

			// Switch to the default theme
			update_option('template', 'default');
			update_option('stylesheet', 'default');
			delete_option('current_theme');
			$theme = get_current_theme();
			do_action('switch_theme', $theme);

			wp_redirect('themes.php?activated=true');
			exit;
		}
	}


	/**
	 * switch_theme() - Called when user switches out of K2
	 */
	function switch_theme() {
		update_option('k2active', '0');
	}


	/**
	 * Register K2 scripts to script loader
	 */

	function register_scripts() {
		// Register jQuery
		wp_register_script('jquery',
			get_bloginfo('template_directory') . '/js/jquery.js',
			false, '1.2.6');

		wp_register_script( 'jquery-ui-core',
			get_bloginfo('template_directory') . '/js/ui.core.js',
			array('jquery'), '1.5.2' );

		wp_register_script( 'jquery-ui-sortable',
			get_bloginfo('template_directory') . '/js/ui.sortable.js',
			array('jquery-ui-core'), '1.5.2' );

		wp_register_script( 'jquery-ui-draggable',
			get_bloginfo('template_directory') . '/js/ui.draggable.js',
			array('jquery-ui-core'), '1.5.2' );

		wp_register_script( 'jquery-ui-droppable',
			get_bloginfo('template_directory') . '/js/ui.droppable.js',
			array('jquery-ui-core', 'jquery-ui-draggable'), '1.5.2' );

		wp_register_script('jquery-dimensions',
			get_bloginfo('template_directory') . '/js/jquery.dimensions.js',
			array('jquery'), '1.2');

		wp_register_script('jquery-easing',
			get_bloginfo('template_directory') . '/js/jquery.easing.js',
			array('jquery'), '1.1.2');

		// Register our scripts with WordPress
		wp_register_script('k2functions',
			get_bloginfo('template_directory') . '/js/k2.functions.js',
			array('jquery'), K2_CURRENT);

		wp_register_script('humanmsg',
			get_bloginfo('template_directory') . '/js/jquery.humanmsg.js',
			array('jquery', 'jquery-easing'), K2_CURRENT);

		wp_register_script('humanundo',
			get_bloginfo('template_directory') . '/js/jquery.humanundo.js',
			array('jquery'), K2_CURRENT);

		wp_register_script('k2rollingarchives',
			get_bloginfo('template_directory') . '/js/k2.rollingarchives.js',
			array('jquery', 'k2slider', 'k2trimmer'), K2_CURRENT);

		wp_register_script('k2livesearch',
			get_bloginfo('template_directory') . '/js/k2.livesearch.js',
			array('jquery'), K2_CURRENT);

		wp_register_script('k2slider',
			get_bloginfo('template_directory') . '/js/k2.slider.js',
			array('jquery'), K2_CURRENT);

		wp_register_script('k2trimmer',
			get_bloginfo('template_directory') . '/js/k2.trimmer.js',
			array('jquery', 'k2slider'), K2_CURRENT);

		wp_register_script('k2sbm',
			get_bloginfo('template_directory') . '/js/k2.sbm.js',
			array('jquery', 'interface', 'jquery-dimensions', 'humanmsg', 'humanundo'), K2_CURRENT);

		wp_register_script('k2widgets',
			get_bloginfo('template_directory') . '/js/k2.widgets.js',
			array('jquery', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-dimensions', 'humanmsg', 'humanundo'), K2_CURRENT);
	}

	/**
	 * Searches through 'styles' directory for css files
	 *
	 * @return array paths to style files
	 */
	
	function get_styles() {
		$k2_styles = array();

		$style_files = K2::files_scan( K2_STYLES_DIR, 'css', 2 );

		sort($style_files);

		foreach ( (array) $style_files as $style_file ) {
			$style_data = get_style_data($style_file);

			if ( ! empty($style_data) )
				$k2_styles[] = $style_data;
		}

		return $k2_styles;
	}


	/**
	 * Helper function to load all php files in given directory using require_once
	 *
	 * @param string $dir_path directory to scan
	 * @param array $ignore list of files to ignore
	 */
	
	function include_all($dir_path, $ignore = false) {
		// Open the directory
		$dir = @dir($dir_path) or die('Could not open required directory ' . $dir_path);

		// Get all the files from the directory
		while(($file = $dir->read()) !== false) {
			// Check the file is a file, and is a PHP file
			if(is_file($dir_path . $file) and (!$ignore or !in_array($file, $ignore)) and preg_match('/\.php$/i', $file)) {
				include_once($dir_path . $file);
			}
		}

		// Close the directory
		$dir->close();
	}


	/**
	 * Helper function to search for files based on given criteria
	 *
	 * @param string $path directory to search
	 * @param array $ext file extensions
	 * @param integer $depth depth of search
	 * @param mixed $relative relative to which path
	 * @return array paths of files found
	 */
	
	function files_scan($path, $ext = false, $depth = 1, $relative = true) {
		$files = array();

		// Scan for all matching files
		K2::_files_scan( trailingslashit($path), '', $ext, $depth, $relative, $files);

		return $files;
	}


	/**
	 * Recursive function for files_scan
	 *
	 * @param string $base_path 
	 * @param string $path 
	 * @param string $ext 
	 * @param string $depth 
	 * @param mixed $relative 
	 * @param string $files 
	 * @return array paths of files found
	 */
	
	function _files_scan($base_path, $path, $ext, $depth, $relative, &$files) {
		if (!empty($ext)) {
			if (!is_array($ext)) {
				$ext = array($ext);
			}
			$ext_match = implode('|', $ext);
		}

		// Open the directory
		if(($dir = @dir($base_path . $path)) !== false) {
			// Get all the files
			while(($file = $dir->read()) !== false) {
				// Construct an absolute & relative file path
				$file_path = $path . $file;
				$file_full_path = $base_path . $file_path;

				// If this is a directory, and the depth of scan is greater than 1 then scan it
				if(is_dir($file_full_path) and $depth > 1 and !($file == '.' or $file == '..')) {
					K2::_files_scan($base_path, $file_path . '/', $ext, $depth - 1, $relative, $files);

				// If this is a matching file then add it to the list
				} elseif(is_file($file_full_path) and (empty($ext) or preg_match('/\.(' . $ext_match . ')$/i', $file))) {
					if ( $relative === true ) {
						$files[] = $file_path;
					} elseif ( $relative === false ) {
						$files[] = $file_full_path;
					} else {
						$files[] = str_replace($relative, '', $file_full_path);
					}
				}
			}

			// Close the directory
			$dir->close();
		}
	}


	/**
	 * Move an existing file to a new path
	 *
	 * @param string $source original path
	 * @param string $dest new path
	 * @param boolean $overwrite if destination exists, overwrite
	 * @return string new path to file
	 */
	
	function move_file($source, $dest, $overwrite = false) {
		return K2::_copy_or_move_file($source, $dest, $overwrite, true);
	}

	function copy_file($source, $dest, $overwrite = false) {
		return K2::_copy_or_move_file($source, $dest, $overwrite, false);
	}

	function _copy_or_move_file($source, $dest, $overwrite = false, $move = false) {
		// check source and destination folder
		if ( file_exists($source) and is_dir(dirname($dest)) ) {

			// destination is a folder, assume move to there
			if ( is_dir($dest) ) {
				if ( DIRECTORY_SEPARATOR != substr($dest, -1) )
					$dest .= DIRECTORY_SEPARATOR;

				$dest = $dest . basename($source);
			}

			// destination file exists
			if ( is_file($dest) ) {
				if ($overwrite) {
					// Delete existing destination file
					@unlink($dest);
				} else {
					// Find a unique name
					$dest = K2::get_unique_path($dest);
				}
			}

			if ($move) {
				if ( rename($source, $dest) )
					return $dest;
			} else {
				if ( copy($source, $dest) )
					return $dest;
			}
		}
		return false;
	}

	function get_unique_path($source) {
		$source = pathinfo($source);
		
		$path = trailingslashit($source['dirname']);
		$filename = $source['filename'];
		$ext = $source['extension'];

		$number = 0;
		while ( file_exists($path . $filename . ++$number . $ext) );

		return $path . sanitize_title_with_dashes($filename . $number) . $ext;
	}
}


// Actions and Filters
add_action( 'switch_theme', array('K2', 'switch_theme'), 0 );
