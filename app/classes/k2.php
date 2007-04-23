<?php
$k2_registered_css = array();

class K2 {
	function init() {
		global $wp_version;

		// Load the localisation text
		load_theme_textdomain('k2_domain');

		// Define our folders for WordPress & WordpressMU
		if ( false === strpos($wp_version, 'wordpress-mu') ) {
			define('K2STYLESPATH', TEMPLATEPATH . '/styles/');
			define('K2HEADERSPATH', TEMPLATEPATH . '/images/headers/');
		} else {
			define('K2STYLESPATH', ABSPATH . UPLOADS . '/k2support/styles/');
			define('K2HEADERSPATH', ABSPATH . UPLOADS . '/k2support/headers/');
		}

		$exclude = array('sbm-ajax.php');

		// Exclude SBM if there's already a sidebar manager
		if ( function_exists('register_sidebar') ) {
			$exclude[] = 'sbm.php';
		}

		// Scan for includes and classes
		K2::include_all(TEMPLATEPATH . '/app/includes/', $exclude);
		K2::include_all(TEMPLATEPATH . '/app/classes/', $exclude);

		// Get the last modified time of the classes folder
		$last_modified = filemtime(dirname(__FILE__));
		$last_modified_check = get_option('k2lastmodified');

		// As only classes can add/remove options it's now time to install if there has been any changes
		if($last_modified_check === false || $last_modified_check < $last_modified) {
			K2::install($last_modified);
		}

		// Register our scripts with WordPress, version is Last Changed Revision
		wp_register_script('k2functions',
			get_bloginfo('template_directory') . '/js/k2functions.js.php',
			array('prototype', 'scriptaculous-effects'), '223');
		wp_register_script('k2rollingarchives',
			get_bloginfo('template_directory') . '/js/rollingarchives.js.php',
			array('k2functions', 'k2trimmer', 'scriptaculous-slider'), '224');
		wp_register_script('k2livesearch',
			get_bloginfo('template_directory') . '/js/livesearch.js.php',
			array('k2functions'), '262');
		wp_register_script('k2comments',
			get_bloginfo('template_directory') . '/js/comments.js.php',
			array('k2functions'), '216');
		wp_register_script('k2trimmer',
			get_bloginfo('template_directory') . '/js/trimmer.js.php',
			array('k2functions', 'scriptaculous-slider'), '247');
		wp_register_script('k2sbm',
			get_bloginfo('template_directory') . '/js/sbm.js.php',
			array('scriptaculous-effects', 'scriptaculous-dragdrop'), '248');

		// Register our css
		K2::register_css('k2rollingarchives',
			get_bloginfo('template_directory') . '/css/rollingarchives.css');
		K2::register_css('k2sbm',
			get_bloginfo('template_directory') . '/css/sbm.css');

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');

		// Register our sidebar with SBM/Widgets
		if ( function_exists('register_sidebars') ) {
			register_sidebars(2, array('before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>'));
		}
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

		// Create support folders for WordPressMU
		if ( false !== strpos($wp_version, 'wordpress-mu') ) {
			if ( ! is_dir(ABSPATH . UPLOADS . '/k2support/') ) {
				wp_mkdir_p(ABSPATH . UPLOADS . '/k2support/');
			}
			if ( ! is_dir(K2STYLESPATH) ) {
				wp_mkdir_p(K2STYLESPATH);
			}
			if ( ! is_dir(K2HEADERSPATH) ) {
				wp_mkdir_p(K2HEADERSPATH);
			}
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

	function get_styles() {
		return K2::files_scan(K2STYLESPATH, 'css', 2);
	}

	function replace_wp_scripts() {
		global $wp_version;

		if ( get_wp_version() < 2.2 ) {
			// Register our prototype
			wp_deregister_script('prototype');
			wp_register_script('prototype',	get_bloginfo('template_directory') . '/js/prototype.js.php',
				false, '1.5.0');
		}
	}

	function register_css($handle, $path) {
		global $k2_registered_css;

		$k2_registered_css[$handle] = $path;
	}

	function output_header_css() {
		global $wp_scripts, $k2_registered_css;
		
		// Output main css
?>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php bloginfo('stylesheet_url'); ?>" />
		<link rel="stylesheet" type="text/css" media="print" href="<?php bloginfo('template_url'); ?>/css/print.css" />

		<?php
		// Output any css that is associated with the printed scripts
		foreach ($wp_scripts->queue as $handle) {
			if ( isset($k2_registered_css[$handle]) ) {
				echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $k2_registered_css[$handle] . '" />'."\n";
			}
		}

		// Output the custom style
		if (get_option('k2scheme') != '') {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="' .get_k2info('style'). '" />'."\n";
		}
	}

	function output_admin_css() {
		global $wp_scripts, $k2_registered_css;

		// Output any css that is associated with the printed scripts
		foreach ($wp_scripts->queue as $handle) {
			if ( isset($k2_registered_css[$handle]) ) {
				echo '<link rel="stylesheet" type="text/css" media="screen" href="' . $k2_registered_css[$handle] . '" />'."\n";
			}
		}
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

add_action('wp_head', array('K2', 'output_header_css'));
add_action('admin_head', array('K2', 'output_admin_css'));
?>
