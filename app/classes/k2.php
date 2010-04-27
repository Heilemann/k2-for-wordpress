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
		// Loads localisation from K2's languages directory
		load_theme_textdomain('k2_domain', TEMPLATEPATH . '/languages');

		// Load required classes and includes
		require_once(TEMPLATEPATH . '/app/includes/wp-compat.php');
		require_once(TEMPLATEPATH . '/app/classes/archive.php');
		require_once(TEMPLATEPATH . '/app/includes/info.php');
		require_once(TEMPLATEPATH . '/app/includes/display.php');
		require_once(TEMPLATEPATH . '/app/includes/comments.php');

		if ( class_exists('WP_Widget') ) // WP 2.8+
			require_once(TEMPLATEPATH . '/app/includes/widgets.php');

		if ( defined('K2_STYLES') and K2_STYLES == true )
			require_once(TEMPLATEPATH . '/app/classes/styles.php');

		if ( defined('K2_HEADERS') and K2_HEADERS == true )
			require_once(TEMPLATEPATH . '/app/classes/header.php');

		// Check installed version, upgrade if needed
		$k2version = get_option('k2version');

		if ( $k2version === false )
			K2::install();
		elseif ( version_compare($k2version, K2_CURRENT, '<') )
			K2::upgrade($k2version);

		// Register our scripts with script loader
		K2::register_scripts();

		// There may be some things we need to do before K2 is initialised
		// Let's do them now
		do_action('k2_init');

		// Finally load pluggable functions
		require_once(TEMPLATEPATH . '/app/includes/pluggable.php');

		// Register our sidebars with widgets
		k2_register_sidebars();
		
		// Register the fact that K2 supports post-thumbnails
		if ( function_exists( 'add_theme_support' ) )
			add_theme_support( 'post-thumbnails' );

		// Automatically output feed links. Requires WP 2.8+
		automatic_feed_links();
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
		add_option('k2archives', '0', 'Set whether K2 has an archives page');
		add_option('k2columns', '2', 'Number of columns to display.');

		// Added 1.0-RC8
		add_option('k2animations', '1', 'JavaScript Animation effects.');
		add_option('k2entrymeta1', __('Published on %date% in %categories%. %comments% %tags%', 'k2_domain'), 'Customized metadata format before entry content.');
		add_option('k2entrymeta2', '', 'Customized metadata format after entry content.');

		$defaultjs = "// Lightbox v2.03.3 - Adds new images to lightbox\nif (typeof myLightbox != 'undefined' && myLightbox instanceof Lightbox && myLightbox.updateImageList) {\n\tmyLightbox.updateImageList();\n}\n";
		add_option('k2ajaxdonejs', $defaultjs, 'JavaScript to execute when Ajax is completed');

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
	 */
	function uninstall() {
		// Delete options
		delete_option('k2version');
		delete_option('k2asidescategory');
		delete_option('k2livesearch');
		delete_option('k2rollingarchives');
		delete_option('k2archives');
		delete_option('k2columns');
		delete_option('k2entrymeta1');
		delete_option('k2entrymeta2');
		delete_option('k2animations');
		delete_option('k2ajaxdonejs');

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

			// Reset K2
			if ( isset($_REQUEST['restore-defaults']) ) {
				K2::restore_defaults();
				wp_redirect('themes.php?page=k2-options&defaults=true');
				die;

			// Reset Sidebars
			} elseif ( isset($_REQUEST['default-widgets']) ) {
				k2_default_widgets();
				wp_redirect('themes.php?page=k2-options&widgets=true');
				die;

				// Save Settings
			} elseif ( isset($_REQUEST['save']) and isset($_REQUEST['k2']) ) {
				K2::update_options();
				wp_redirect('themes.php?page=k2-options&saved=true');
				die;
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
		<script type="text/javascript" charset="utf-8">
		//<![CDATA[
			var defaults_prompt = "<?php _e('Do you want to restore K2 to default settings? This will remove all your K2 settings.', 'k2_domain'); ?>";
		//]]>
		</script>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/options.css" />
	<?php
	}


	/**
	 * Enqueues scripts. Called by action: admin_print_scripts
	 */
	function admin_print_scripts() {
		// Add our script to the queue
		wp_enqueue_script('k2options');
	}


	/**
	 * Updates options
	 *
	 * @uses do_action() Provides 'k2_update_options' action
	 */
	function update_options() {
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

		// Top post meta
		if ( isset($_POST['k2']['entrymeta1']) ) {
			update_option( 'k2entrymeta1', stripslashes($_POST['k2']['entrymeta1']) );
		}

		// Bottom post meta
		if ( isset($_POST['k2']['entrymeta2']) ) {
			update_option( 'k2entrymeta2', stripslashes($_POST['k2']['entrymeta2']) );
		}

		// Ajax Success JavaScript
		if ( isset($_POST['k2']['ajaxdonejs']) ) {
			update_option( 'k2ajaxdonejs', stripslashes($_POST['k2']['ajaxdonejs']) );
		}

		// K2 Hook
		do_action('k2_update_options');
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

		wp_register_script('k2options',
			get_bloginfo('template_directory') . '/js/k2.options.js',
			array('jquery', 'jquery-ui-sortable'), K2_CURRENT);

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
	}


	/**
	 * Enqueues scripts needed by K2
	 */
	function enqueue_scripts() {
		// Load our scripts
		if ( ! is_admin() ) {

			wp_enqueue_script('k2functions');

			if ( '1' == get_option('k2rollingarchives') )
				wp_enqueue_script('k2rollingarchives');

			if ( '1' == get_option('k2livesearch') )
				wp_enqueue_script('k2livesearch');

			// WP 2.7 threaded comments
			if ( is_singular() )
				wp_enqueue_script( 'comment-reply' );
		}
	}
	

	/**
	 * Initializes scripts
	 */
	function init_scripts() {
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
				"<?php esc_attr_e('Type and Wait to Search','k2_domain'); ?>"
			);
			<?php endif; ?>

			<?php /* Rolling Archives */ if ( '1' == get_option('k2rollingarchives') ): ?>
			K2.RollingArchives = new RollingArchives(
				"<?php esc_attr_e('Page %1$d of %2$d', 'k2_domain'); ?>"
			);

			jQuery('body').addClass('rollingarchives');
			<?php endif; ?>

			jQuery('#dynamic-content').ajaxSuccess(function () {
				<?php echo get_option('k2ajaxdonejs'); ?>
			});

			initARIA();
		});
	//]]>
	</script>
<?php
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
add_action( 'wp_footer', array('K2', 'init_scripts') );
add_action( 'template_redirect', array('K2', 'dynamic_content') );
add_filter( 'query_vars', array('K2', 'add_custom_query_vars') );

// Decrease the priority of redirect_canonical
remove_action( 'template_redirect', 'redirect_canonical' );
add_action( 'template_redirect', 'redirect_canonical', 11 );
//add_filter( 'redirect_canonical', array('K2', 'prevent_dynamic_redirect') );