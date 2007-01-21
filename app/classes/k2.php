<?php

class K2 {
	function init() {
		// Load the localisation text
		load_theme_textdomain('k2_domain');

		// Scan for includes and classes
		K2::include_all(TEMPLATEPATH . '/app/includes/', array('sbm-ajax.php', 'sbm-stub.php', 'sbm.php'));
		K2::include_all(TEMPLATEPATH . '/app/classes/');

		// Get the last modified time of the classes folder
		$last_modified = filemtime(dirname(__FILE__));
		$last_modified_check = get_option('k2lastmodified');

		// As only classes can add/remove options it's now time to install if there has been any changes
		if($last_modified_check === false || $last_modified_check < $last_modified) {
			K2::install($last_modified);
		}

		// Register the sidebar - This will be moved hopefully in future revisions
		if(function_exists('register_sidebar')) {
			register_sidebar(array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>'));
		}

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');
	}

	function install($last_modified) {
		global $current;

		// Add / update the version number
		if(get_option('k2version') === false) {
			add_option('k2version', $current, 'This option stores K2\'s version number');
		} else {
			update_option('k2version', $current);
		}

		// Add / update the last modified timestamp
		if(get_option('k2lastmodified') === false) {
			add_option('k2lastmodified', $last_modified, 'This option stores K2\'s last application modification. Used for version checking');
		} else {
			update_option('k2lastmodified', $last_modified);
		}

		// Call the install handlers
		do_action('k2_install');
	}

	function uninstall() {
		// Call the uninstall handlers
		do_action('k2_uninstall');

		// Delete version & last modified
		delete_option('k2version');
		delete_option('k2lastmodified');

		// Flush the dang cache
		wp_cache_flush();

		// Activate the default Wordpress theme so as not to re-install K2
		update_option('template', 'default');
		update_option('stylesheet', 'default');
		do_action('switch_theme', 'Default');

		// Go back to the themes page
		header('Location: themes.php');
		exit;
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
}

?>
