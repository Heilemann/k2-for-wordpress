<?php

class K2 {
	function init() {
		global $wp_version;

		// Load the localisation text
		load_theme_textdomain('k2_domain');

		$exclude = array('sbm-direct.php', 'widgets-removal.php');

		// Exclude SBM if there's already a sidebar manager
		if(K2_USING_SBM) {
			$exclude[] = 'widgets.php';
		} else {
			$exclude[] = 'sbm.php';
		}

		// Scan for includes and classes
		K2::include_all(TEMPLATEPATH . '/app/includes/', $exclude);
		K2::include_all(TEMPLATEPATH . '/app/classes/');

		// Get the last modified time of the classes folder
		$last_modified = filemtime(dirname(__FILE__));
		$last_modified_check = get_option('k2lastmodified');

		// As only classes can add/remove options it's now time to install if there has been any changes
		if($last_modified_check === false || $last_modified_check < $last_modified) {
			K2::install($last_modified);
		}

		// Check if the theme is being activated/deactivated
		if(!get_option('k2active')) {
			update_option('k2active', true);
			do_action('k2_activate');

			// Ewww...
			if(is_admin()) {
				header('Location: themes.php?activated=true');
				exit;
			}
		}
		add_action('switch_theme', array('K2', 'theme_switch'));

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');

		// Register our sidebar with SBM/Widgets
		if ( function_exists('register_sidebars') ) {
			register_sidebars(2, array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>', 'before_title' => '<h4>', 'after_title' => '</h4>'));
		}

		// Check if there's a style, and if so if it has an attached PHP file
		if(($scheme = get_option('k2scheme')) != '') {
			$scheme_data = get_style_data($scheme);

			if($scheme_data['php'] && file_exists($scheme_data['php'])) {
				include_once($scheme_data['php']);
			}
		}
	}

	function install($last_modified) {
		global $wp_version;

		// Add / update the version number
		if(get_option('k2version') === false) {
			add_option('k2version', K2_CURRENT, 'This option stores K2\'s version number');
		} else {
			update_option('k2version', K2_CURRENT);
		}

		// Add / update the last modified timestamp
		if(get_option('k2lastmodified') === false) {
			add_option('k2lastmodified', $last_modified, 'This option stores K2\'s last application modification. Used for version checking');
		} else {
			update_option('k2lastmodified', $last_modified);
		}

		if(get_option('k2active') === false) {
			add_option('k2active', 0, 'This option stores if K2 has been activated');
		} else {
			update_option('k2active', 0);
		}

		// Create support folders for WordPressMU
		if(K2_MU) {
			if(!is_dir(ABSPATH . UPLOADS . 'k2support/')) {
				wp_mkdir_p(ABSPATH . UPLOADS . 'k2support/');
			}
			if(!is_dir(K2_STYLES_PATH)) {
				wp_mkdir_p(K2_STYLES_PATH);
			}
			if(!is_dir(K2_HEADERS_PATH)) {
				wp_mkdir_p(K2_HEADERS_PATH);
			}
		}

		// Call the install handlers
		do_action('k2_install');
	}

	function uninstall() {
		global $wpdb;

		// Activate the default Wordpress theme so as not to re-install K2
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');

		// Call the uninstall handlers
		do_action('k2_uninstall');

		// Delete options
		delete_option('k2active');
		delete_option('k2lastmodified');
		delete_option('k2version');

		// Remove the K2 options from the database
		// This is a catch-all
		$cleanup = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'k2%'");

		// Flush the dang cache
		wp_cache_flush();

		// Go back to the themes page
		header('Location: themes.php');
		exit;
	}

	function theme_switch() {
		update_option('k2active', 0);
		do_action('k2_deactivate');
	}

	function register_scripts() {
		// Unload the bundled jQuery
		wp_deregister_script('jquery');
		wp_deregister_script('interface');

		// Register jQuery
		wp_register_script('jquery',
			get_bloginfo('template_directory').'/js/jquery.js.php',
			false, '1.2.1');

		wp_register_script('interface',
			get_bloginfo('template_directory').'/js/jquery.interface.js.php',
			array('jquery'), '1.2');

		wp_register_script('jquery.dimensions',
			get_bloginfo('template_directory').'/js/jquery.dimensions.js.php',
			array('jquery'), '3238');

		// Register our scripts with WordPress, version is Last Changed Revision
		wp_register_script('k2functions',
			get_bloginfo('template_directory') . '/js/k2.functions.js.php',
			array('jquery'), '223');

		wp_register_script('k2rollingarchives',
			get_bloginfo('template_directory') . '/js/k2.rollingarchives.js.php',
			array('jquery', 'k2slider', 'k2trimmer'), '224');

		wp_register_script('k2livesearch',
			get_bloginfo('template_directory') . '/js/k2.livesearch.js.php',
			array('jquery'), '262');

		wp_register_script('k2slider',
			get_bloginfo('template_directory') . '/js/k2.slider.js.php',
			array('jquery'), '262');

		wp_register_script('k2comments',
			get_bloginfo('template_directory') . '/js/k2.comments.js.php',
			array('jquery'), '216');

		wp_register_script('k2trimmer',
			get_bloginfo('template_directory') . '/js/k2.trimmer.js.php',
			array('jquery', 'k2slider'), '247');

		wp_register_script('k2sbm',
			get_bloginfo('template_directory') . '/js/k2.sbm.js.php',
			array('jquery', 'interface', 'jquery.dimensions'), '');
	}

	function get_styles() {
		return K2::files_scan(K2_STYLES_PATH, 'css', 2);
	}

	function include_all($dir_path, $ignore = false) {
		// Open the directory
		$dir = @dir($dir_path) or die('Could not open required directory ' . $dir_path);

		// Get all the files from the directory
		while(($file = $dir->read()) !== false) {
			// Check the file is a file, and is a PHP file
			if(is_file($dir_path . $file) and (!$ignore or !in_array($file, $ignore)) and preg_match('/\.php$/i', $file)) {
				require_once($dir_path . $file);
			}
		}

		// Close the directory
		$dir->close();
	}

	function files_scan($path, $ext = false, $depth = 1, $relative = true) {
		$files = array();

		// Scan for all matching files
		K2::_files_scan($path, '', $ext, $depth, $relative, $files);

		return $files;
	}

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
					$files[] = $relative ? $file_path : $file_full_path;
				}
			}

			// Close the directory
			$dir->close();
		}
	}

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
?>
