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
	 * Initializes K2
	 *
	 * @uses do_action() Provides 'k2_init' action
	 */
	function init() {
		// Load the localisation text
		load_theme_textdomain('k2_domain');

		@define('K2_STYLES_DIR', TEMPLATEPATH . '/styles');
		@define('K2_STYLES_URL', get_template_directory_uri() . '/styles');
		@define('K2_HEADERS_DIR', TEMPLATEPATH . '/images/headers');
		@define('K2_HEADERS_URL', get_template_directory_uri() . '/images/headers');

		// Load required classes and includes
		require_once(TEMPLATEPATH . '/app/includes/wp-compat.php');
		require_once(TEMPLATEPATH . '/app/classes/archive.php');
		require_once(TEMPLATEPATH . '/app/classes/header.php');
		require_once(TEMPLATEPATH . '/app/includes/info.php');
		require_once(TEMPLATEPATH . '/app/includes/display.php');
		require_once(TEMPLATEPATH . '/app/includes/comments.php');
		require_once(TEMPLATEPATH . '/app/includes/widgets.php');

		if ( ! defined('K2_LOAD_SBM') )
			require_once(TEMPLATEPATH . '/app/classes/widgets.php');

		// Check installed version, upgrade if needed
		$k2version = get_option('k2version');

		if ( $k2version === false )
			K2::install();
		elseif ( version_compare($k2version, K2_CURRENT, '<') )
			K2::upgrade($k2version);

		// Register our scripts with script loader
		K2::register_scripts();

		// Load the current style's functions.php if it is readable
		$style_functions = dirname( K2_STYLES_DIR . '/' . get_option('k2style') ) . '/functions.php';
		if ( K2_USING_STYLES and is_readable($style_functions) )
			include_once($style_functions);

		// Finally load pluggable functions
		require_once(TEMPLATEPATH . '/app/includes/pluggable.php');

		// Register our sidebars with widgets
		k2_register_sidebars();

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');
	}


	/**
	 * Starts the installation process
	 *
	 * @uses do_action() Provides 'k2_install' action
	 */
	function install() {
		add_option('k2version', K2_CURRENT, 'This option stores K2\'s version number');
		add_option('k2asidescategory', '0', 'A category which will be treated differently from other categories');
		add_option('k2livesearch', '1', "If you don't trust JavaScript and Ajax, you can turn off LiveSearch. Otherwise I suggest you leave it on"); // (live & classic)
		add_option('k2rollingarchives', '1', "If you don't trust JavaScript and Ajax, you can turn off Rolling Archives. Otherwise it is suggested you leave it on");
		add_option('k2archives', '0', 'Set whether K2 has a Live Archive page');
		add_option('k2sidebarmanager', '0', 'Set whether to use K2 Sidebar Manager');
		add_option('k2styleinfo', '', 'Metadata of current style.');
		add_option('k2blogornoblog', 'Blog', 'The text on the first tab in the header navigation.');
		add_option('k2columns', '2', 'Number of columns to display.');

		// Added 1.0-RC6
		add_option('k2style', '', 'Choose the Style you want K2 to use');
		add_option('k2headerimage', '', 'Current Header Image');

		// Added 1.0-RC8
		add_option('k2animations', '1', 'JavaScript Animation effects.');
		add_option('k2entrymeta1', __('Published on %date% in %categories%. %comments% %tags%', 'k2_domain'), 'Customized metadata format before entry content.');
		add_option('k2entrymeta2', '', 'Customized metadata format after entry content.');

		// Install a default set of widgets
		if ( function_exists('wp_get_sidebars_widgets') ) {
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( empty( $sidebars_widgets ) )
				K2::default_widgets();
		}

		// Call the install handlers
		do_action('k2_install');
	}


	/**
	 * Starts the upgrade process
	 *
	 * @uses do_action() Provides 'k2_upgrade' action
	 * @param string $previous Previous version K2
	 */
	function upgrade($previous) {
		// Delete deprecated options
		delete_option('k2advnav');
		delete_option('k2dynamiccolumns');
		delete_option('k2header_picture');
		delete_option('k2imagerandomfeature');
		delete_option('k2lastmodified');
		delete_option('k2scheme');

		// Install options
		K2::install();

		// Call the upgrade handlers
		do_action('k2_upgrade', $previous);

		// Update the version
		update_option('k2version', K2_CURRENT);
	}


	/**
	 * Removes K2 options
	 *
	 * @uses do_action() Provides 'k2_uninstall' action
	 * @global mixed $wpdb
	 */
	function uninstall($switch_theme = false) {
		global $wpdb;

		// Delete options
		delete_option('k2version');
		delete_option('k2asidescategory');
		delete_option('k2livesearch');
		delete_option('k2rollingarchives');
		delete_option('k2archives');
		delete_option('k2sidebarmanager');
		delete_option('k2style');
		delete_option('k2styleinfo');
		delete_option('k2blogornoblog');
		delete_option('k2columns');
		delete_option('k2headerimage');
		delete_option('k2entrymeta1');
		delete_option('k2entrymeta2');
		delete_option('k2animations');

		// Call the uninstall handlers
		do_action('k2_uninstall');
	}


	/**
	 * Restores K2 to default settings
	 */
	function restore_defaults() {
		K2::uninstall();
		K2::install();
	}


	/**
	 * 
	 */
	function admin_init() {
		// Inside K2 Options page
		if ( isset($_GET['page']) and ('k2-options' == $_GET['page']) and isset($_REQUEST['k2-options-submit']) ) {
			check_admin_referer('k2options');

			// Setup ELA
			if ( isset($_REQUEST['configela']) ) {
				K2Archive::setup_archive();

			// Reset K2
			} elseif ( isset($_REQUEST['restore-defaults']) ) {
				K2::restore_defaults();

			// Reset Sidebars
			} elseif ( isset($_REQUEST['default-widgets']) ) {
				K2::default_widgets();

				// Save Settings
			} elseif ( isset($_REQUEST['save']) and isset($_REQUEST['k2']) ) {
				K2::update_options();
			}
		}
	}


	/**
	 * Adds K2 Options to Appearance menu, adds actions for head and scripts
	 */
	function add_options_menu() {
		$page = add_theme_page(__('K2 Options','k2_domain'), __('K2 Options','k2_domain'), 'edit_themes', 'k2-options', array('K2', 'admin'));

		add_action( "admin_head-$page", array('K2', 'admin_head') );
		add_action( "admin_print_scripts-$page", array('K2', 'admin_print_scripts') );

		if ( function_exists('add_contextual_help') ) {
			add_contextual_help($page,
				'<a href="http://groups.google.com/group/k2-support/">' .  __('K2 Support Group', 'k2_domain') . '</a><br />' .
				'<a href="http://code.google.com/p/kaytwo/issues/list">' .  __('K2 Bug Tracker', 'k2_domain') . '</a><br />'
				);
		}
	}


	/**
	 * Displays K2 Options page
	 */
	function admin() {
		include(TEMPLATEPATH . '/app/display/options.php');
	}


	/**
	 * Displays content in HEAD tag. Called by action: admin_head
	 */
	function admin_head() {
		?>

		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/options.css" />
		<script type="text/javascript">
		function confirmDefaults() {
			if (confirm("<?php _e('Do you want to restore K2 to default settings? This will remove all your K2 settings.', 'k2_domain'); ?>") == true) {
				return true;
			} else {
				return false;
			}
		}
		</script>
	<?php
	}


	/**
	 * Enqueues scripts. Called by action: admin_print_scripts
	 */
	function admin_print_scripts() {
		// Add our script to the queue
		wp_enqueue_script('k2functions');
	}


	/**
	 * Updates options
	 *
	 * @uses do_action() Provides 'k2_update_options' action
	 */
	function update_options() {
		// Sidebar Manager
		if ( isset($_POST['k2']['sidebarmanager']) ) {
			update_option('k2sidebarmanager', '1');
		} else {
			update_option('k2sidebarmanager', '0');
		}

		// Columns
		if ( isset($_POST['k2']['columns']) ) {
			update_option('k2columns', $_POST['k2']['columns']);
		}

		// Advanced Navigation
		if ( isset($_POST['k2']['advnav']) ) {
			update_option('k2livesearch', '1');
			update_option('k2rollingarchives', '1');
		} else {
			update_option('k2livesearch', '0');
			update_option('k2rollingarchives', '0');
		}

		// JavaScript Animations
		if ( isset($_POST['k2']['animations']) ) {
			update_option('k2animations', '1');
		} else {
			update_option('k2animations', '0');
		}

		// Archives Page (thanks to Michael Hampton, http://www.ioerror.us/ for the assist)
		if ( isset($_POST['k2']['archives']) ) {
			update_option('k2archives', '1');
			K2Archive::create_archive();
		} else {
			update_option('k2archives', '0');
			K2Archive::delete_archive();
		}

		// Asides
		if ( isset($_POST['k2']['asidescategory']) ) {
			update_option('k2asidescategory', (int) $_POST['k2']['asidescategory']);
		}

		// Style
		if ( isset($_POST['k2']['style']) ) {
			update_option('k2style', $_POST['k2']['style']);
			update_style_info();
		} else {
			update_option('k2style', '');
			update_option('k2styleinfo', array());
		}

		// Header Image
		if ( isset($_POST['k2']['headerimage']) ) {
			update_option('k2headerimage', $_POST['k2']['headerimage']);

			// Update Custom Image Header
			if ( ('' == $_POST['k2']['headerimage']) or ('random' == $_POST['k2']['headerimage']) ) {
				remove_theme_mod('header_image');
			} else {
				set_theme_mod('header_image', K2Header::get_header_image_url() );
			}
		}

		// Blog tab
		if ( isset($_POST['k2']['blogornoblog']) ) {
			update_option( 'k2blogornoblog', strip_tags( stripslashes($_POST['k2']['blogornoblog']) ) );
		}


		// Top post meta
		if ( isset($_POST['k2']['entrymeta1']) ) {
			update_option( 'k2entrymeta1', stripslashes($_POST['k2']['entrymeta1']) );
		}

		// Bottom post meta
		if ( isset($_POST['k2']['entrymeta2']) ) {
			update_option( 'k2entrymeta2', stripslashes($_POST['k2']['entrymeta2']) );
		}

		// K2 Hook
		do_action('k2_update_options');
	}


	/**
	 * Assigns a default set of widgets
	 */
	function default_widgets() {
		/*
		global $wp_registered_widgets;

		$sidebars_widgets = array( 'sidebar-1' => array() );

		// Fill first sidebar
		if ( isset($wp_registered_widgets['search']) ) $sidebars_widgets['sidebar-1'][] = 'search';
		if ( isset($wp_registered_widgets['k2-about']) ) $sidebars_widgets['sidebar-1'][] = 'k2-about';
		if ( isset($wp_registered_widgets['recent-posts']) ) $sidebars_widgets['sidebar-1'][] = 'recent-posts';
		if ( isset($wp_registered_widgets['recent-comments']) ) $sidebars_widgets['sidebar-1'][] = 'recent-comments';
		if ( isset($wp_registered_widgets['archives']) ) $sidebars_widgets['sidebar-1'][] = 'archives';
		if ( isset($wp_registered_widgets['tag_cloud']) ) $sidebars_widgets['sidebar-1'][] = 'tag_cloud';
		if ( isset($wp_registered_widgets['links']) ) $sidebars_widgets['sidebar-1'][] = 'links';

		wp_set_sidebars_widgets( $sidebars_widgets );
		*/
	}


	/**
	 * Adds k2dynamic into the list of query variables, used for dynamic content
	 */
	function add_custom_query_vars($query_vars) {
		$query_vars[] = 'k2dynamic';

		return $query_vars;
	}


	/**
	 * Filter to prevent redirect_canonical() from redirecting dynamic content
	 */
	function prevent_dynamic_redirect($redirect_url) {
		if ( strpos($redirect_url, 'k2dynamic=' ) !== false )
			return false;

		return $redirect_url;
	}


	/**
	 * Return the home page link, used for dynamic content
	 */
	function get_home_url() {
		if ( ('page' == get_option('show_on_front')) and ($page_id = get_option('page_for_posts')) ) {
			return get_page_link($page_id);
		}
		
		return get_bloginfo('url') . '/';
	}


	/**
	 * Handles displaying dynamic content such as LiveSearch, RollingArchives
	 *
	 * @uses do_action() Provides 'k2_dynamic_content' action
	 */
	function dynamic_content() {
		$k2dynamic = get_query_var('k2dynamic');

		if ( $k2dynamic ) {
			define('DOING_AJAX', true);

			// Send the header
			header('Content-Type: ' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset'));

			if ( defined('WP_DEBUG') && true === WP_DEBUG ) {
				global $wp_query;
				var_dump($wp_query->query);
			}

			switch ( $k2dynamic ) {
				default:
					include(TEMPLATEPATH . '/app/display/theloop.php');
					break;

				case 'init':
					include(TEMPLATEPATH . '/app/display/rollingarchive.php');
					break;
			}

			// K2 Hook
			do_action('k2_dynamic_content');
			exit;
		}
	}


	/**
	 * Helper function used by RollingArchives
	 */
	function setup_rolling_archives() {
		global $wp_query;

		// Get the query
		if ( is_array($wp_query->query) )
			$rolling_query = $wp_query->query;
		elseif ( is_string($wp_query->query) )
			parse_str($wp_query->query, $rolling_query);

		// Get list of page dates
		if ( !is_page() and !is_single() )
			$page_dates = get_rolling_page_dates($wp_query);

		// Get the current page
		$rolling_page = intval( get_query_var('paged') );
		if ( $rolling_page < 1 )
			$rolling_page = 1;

		?>
			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function() {
					K2.RollingArchives.setState(
						<?php echo (int) $rolling_page; ?>,
						<?php echo (int) $wp_query->max_num_pages; ?>,
						<?php output_javascript_hash($rolling_query); ?>,
						<?php output_javascript_array($page_dates); ?>
					);

					if (K2.Animations) {
						smartPosition('#dynamic-content');
					}
				});
			// ]]>
			</script>
		<?php
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
			array('jquery', 'k2slider', 'k2trimmer'), K2_CURRENT, true);

		wp_register_script('k2livesearch',
			get_bloginfo('template_directory') . '/js/k2.livesearch.js',
			array('jquery'), K2_CURRENT, true);

		wp_register_script('k2slider',
			get_bloginfo('template_directory') . '/js/k2.slider.js',
			array('jquery'), K2_CURRENT, true);

		wp_register_script('k2trimmer',
			get_bloginfo('template_directory') . '/js/k2.trimmer.js',
			array('jquery', 'k2slider'), K2_CURRENT, true);

		wp_register_script('k2widgets',
			get_bloginfo('template_directory') . '/js/k2.widgets.js',
			array('jquery', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-dimensions', 'humanmsg', 'humanundo'), K2_CURRENT);
	}


	/**
	 * Enqueues scripts needed by K2
	 */
	function enqueue_scripts() {
		// Load our scripts
		wp_enqueue_script('k2functions');

		if (get_option('k2rollingarchives') == 1) {
			wp_enqueue_script('k2rollingarchives');
		}

		if (get_option('k2livesearch') == 1) {
			wp_enqueue_script('k2livesearch');
		}

		// WP 2.7 threaded comments
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
	}
	

	/**
	 * Initializes scripts in the header
	 */
	function header_scripts() {
?>
	<script type="text/javascript">
	//<![CDATA[
	<?php if ( 'dynamic' == get_option('k2columns') ): ?>
		K2.layoutWidths = <?php /* Style Layout Widths */
			if ( K2_USING_STYLES ) {
				$styleinfo = get_option('k2styleinfo');
				if ( empty($styleinfo['layout_widths']) )
					echo '[560, 780, 950]';
				else
					output_javascript_array($styleinfo['layout_widths']);
			} else {
				echo '[560, 780, 950]';
			}
		?>;

		jQuery(document).ready(dynamicColumns);
		jQuery(window).resize(dynamicColumns);
	<?php endif; ?>

		K2.AjaxURL = "<?php bloginfo('url'); ?>/";
		K2.Animations = <?php echo (int) get_option('k2animations') ?>;

		jQuery(document).ready(function(){
			<?php /* LiveSearch */ if ( '1' == get_option('k2livesearch') ): ?>
			K2.LiveSearch = new LiveSearch(
				"<?php echo attribute_escape(__('Type and Wait to Search','k2_domain')); ?>"
			);
			<?php endif; ?>

			<?php /* Rolling Archives */ if ( '1' == get_option('k2rollingarchives') ): ?>
			K2.RollingArchives = new RollingArchives(
				"<?php echo attribute_escape( __('Page %1$d of %2$d', 'k2_domain') ); ?>"
			);
			<?php endif; ?>
		});
	//]]>
	</script>
<?php
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
add_action( 'admin_menu', array('K2', 'add_options_menu') );
add_action( 'admin_init', array('K2', 'admin_init') );
add_action( 'wp_print_scripts', array('K2', 'enqueue_scripts') );
add_action( 'wp_head', array('K2', 'header_scripts') );
add_action( 'template_redirect', array('K2', 'dynamic_content') );
add_filter( 'query_vars', array('K2', 'add_custom_query_vars') );

// Decrease the priority of redirect_canonical
remove_action( 'template_redirect', 'redirect_canonical' );
add_action( 'template_redirect', 'redirect_canonical', 11 );
//add_filter( 'redirect_canonical', array('K2', 'prevent_dynamic_redirect') );