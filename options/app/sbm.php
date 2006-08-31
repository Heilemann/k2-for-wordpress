<?php

/**
 * Sidebar modules for K2
 **/

/* Constants **************************************************************************************/

// Rather messy, but it's cross platform!
define('SBMPLUGINPATH', (DIRECTORY_SEPARATOR != '/') ? str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)) : dirname(__FILE__));

/* Globals ****************************************************************************************/

// The registered sidebars
$k2sbm_registered_sidebars = array();

// The active modules
$k2sbm_active_modules = array();

// The disabled modules
$k2sbm_disabled_modules = array();

// The registered sidebar modules
$k2sbm_registered_modules = array();

// The module currently being manipulated
$k2sbm_current_module = false;

// Error text for XML
$k2sbm_error_text = '';


/* Classes ****************************************************************************************/

/**
 * The main class
 **/
class k2sbm {
	/* Interface functions ********************************************************************/

	/**
	 * The function to bootstrap for the WP interface
	 **/
	function wp_bootstrap() {
		global $k2sbm_active_modules;

		k2sbm::pre_bootstrap();

		// Post-bootstrap when everything is loaded
		add_action('init', array('k2sbm', 'post_bootstrap'));

		// Output the CSS files, if there are modules
		if($k2sbm_active_modules) {
			add_action('wp_head', array('k2sbm', 'output_module_css_files'));
		}
	}

	/**
	 * The function to bootstrap for the direct interface
	 **/
	function direct_bootstrap() {
		global $k2sbm_registered_modules, $k2sbm_registered_sidebars, $k2sbm_disabled_modules, $k2sbm_error_text;

		// You MUST be an admin to access this stuff
		auth_redirect();

		k2sbm::pre_bootstrap();
		k2sbm::post_bootstrap();

		// Check for specific actions that return a HTML response
		if($_GET['action'] == 'control-show') {
			if(isset($_POST['module_id'])) {
				$all_modules = k2sbm::get_all_modules();
				$all_modules[$_POST['module_id']]->displayControl();
			} else {
				echo(false);
			}
		} elseif($_GET['action'] == 'control-post-list-show') {
			if(isset($_POST['module_id'])) {
				$all_modules = k2sbm::get_all_modules();
				$all_modules[$_POST['module_id']]->displayPostList();
			} else {
				echo(false);
			}
		} elseif($_GET['action'] == 'control-page-list-show') {
			if(isset($_POST['module_id'])) {
				$all_modules = k2sbm::get_all_modules();
				$all_modules[$_POST['module_id']]->displayPageList();
			} else {
				echo(false);
			}
		} else {
			// Set the output type
			header('Content-type: text/xml');

			// XML prelude
			echo('<?xml version="1.0"?>');

			// Begin the response
			echo('<response>');

			// Check what the action is
			switch($_GET['action']) {
				// List of the modules in the sidebar
				case 'list':
					foreach($k2sbm_registered_sidebars as $sidebar) {
						$tmp_modules[] = $sidebar->modules;
					}

					$tmp_modules[] = $k2sbm_disabled_modules;

					if($tmp_modules) {
						// Output the modules
						foreach($tmp_modules as $modules) {
							echo('<modules>');

							if($modules) {
								foreach($modules as $module) {
									echo('<module id="' . $module->id . '">' . strip_tags($module->name) . '</module>');
								}
							}

							echo('</modules>');
						}
					}

					break;

				// Add a module to the sidebar
				case 'add':
					// Check the title was correct
					if(isset($_POST['add_name']) and trim((string)($_POST['add_name'])) != '') {
						k2sbm::add_module(stripslashes($_POST['add_name']), $_POST['add_type'], $_POST['add_sidebar']);
					} else {
						k2sbm::set_error_text(__('You must specify a valid module name', 'k2_domain'));
					}

					break;

				// Update a module
				case 'update':
					if(isset($_POST['sidebar_id']) and isset($_POST['module_id'])) {
						k2sbm::update_module($_POST['sidebar_id'], $_POST['module_id']);
					} else {
						k2sbm::set_error_text(__('Missing sidebar and module ids', 'k2_domain'));
					}

					break;

				// Remove a module from the sidebar
				case 'remove':
					if(isset($_POST['sidebar_id']) and isset($_POST['module_id'])) {
						k2sbm::remove_module($_POST['sidebar_id'], $_POST['module_id']);
					} else {
						k2sbm::set_error_text(__('Missing sidebar and module ids', 'k2_domain'));
					}

					break;

				// Re-order the modules in the sidebar
				case 'reorder':
					if(isset($_POST['sidebar_ordering'])) {
						k2sbm::reorder_sidebar($_POST['sidebar_ordering']);
					} else {
						k2sbm::set_error_text(__('Missing ordering data', 'k2_domain'));
					}

					break;

				// Error
				default:
					k2sbm::set_error_text(__('Invalid call', 'k2_domain'));
					break;
			}

			if($k2sbm_error_text != null) {
				echo('<error>' . $k2sbm_error_text . '</error>');
				echo(false);
			} else {
				echo(true);
			}

			// End the response
			echo('</response>');

			// Safeguard
			wp_cache_flush();
		}
	}

	/**
	 * The pre-bootstrap function
	 **/
	function pre_bootstrap() {
		// Load the modules
		k2sbm::load_modules();

		// Scan for in-built modules
		k2sbm::module_scan();

	}

	/**
	 * The post-bootstrap function
	 **/
	function post_bootstrap() {
		// Allow the Widgets and SBM defined in plugins & themes to be loaded
		do_action('sbm_init');
		do_action('widgets_init');
	}

	/**
	 * The function to init for K2
	 **/
	function k2_init() {
		// Add menus
		add_action('admin_menu', array('k2sbm', 'add_menus'));

		// Check if this page is the one being shown, if so then add stuff to the header
		if($_GET['page'] == 'k2-sbm-modules') {
			add_action('admin_head', array('k2sbm', 'module_admin_head'));
		}
	}

	/**
	 * The function to add menus
	 **/
	function add_menus() {
		// Add the submenus
		add_submenu_page('themes.php', __('K2 Sidebar Modules', 'k2_domain'), __('K2 Sidebar Modules', 'k2_domain'), 5, 'k2-sbm-modules', array('k2sbm', 'module_admin'));
	}

	/**
	 * The function to show the module admin interface
	 **/
	function module_admin() {
		global $k2sbm_registered_sidebars, $k2sbm_registered_modules;

		if(count($k2sbm_registered_sidebars) == 0) {
		?>
			<div class="wrap">You have no registered sidebars.</div>
		<?php
		} elseif(count($k2sbm_registered_modules) == 0) {
		?>
			<div class="wrap">You have no modules or Widgets installed &amp; activated.</div>
		<?php
		} else {
			include(TEMPLATEPATH . '/options/display/modules.php');
		}
	}

	/**
	 * The function to add stuff to the WP admin header
	 **/
	function module_admin_head() {
	?>
		<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/sbm.css" />

		<script type="text/javascript">
			//<![CDATA[
				var sbm_baseUrl = '<?php bloginfo('template_directory'); ?>/options/app/sbm-ajax.php';
			//]]>
		</script>

		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/prototype.js.php"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/effects.js.php"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/dragdrop.js"></script>
		<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/sbm.js"></script>
	<?php
	}

	/**
	 * The function to set the XML error text for a direct request
	 *
	 * $text - The new error text
	 **/
	function set_error_text($text) {
		global $k2sbm_error_text;

		$k2sbm_error_text = $text;
	}


	/* Sidebar functions **********************************************************************/

	/**
	 * Function to register a sidebar
	 *
	 * $args - Sidebar's arguments
	 **/
	function register_sidebar($args = array()) {
		global $k2sbm_registered_sidebars;

		// Just in case they have not yet been loaded
		k2sbm::load_modules();

		// Apparently, WPW lets you pass arguments as a string
		if(is_string($args)) {
			parse_str($args, $args);
		}

		// Check the default arguments are there
		$args['name'] = isset($args['name']) ? $args['name'] : sprintf(__('Sidebar %d', 'k2_domain'), count($k2sbm_registered_sidebars) + 1);
		$args['before_widget'] = isset($args['before_widget']) ? $args['before_widget'] : '<li id="%1$s" class="widget %2$s">';
		$args['after_widget'] = isset($args['after_widget']) ? $args['after_widget'] : "</li>\n";
		$args['before_title'] = isset($args['before_title']) ? $args['before_title'] : '<h2 class="widgettitle">';
		$args['after_title'] = isset($args['after_title']) ? $args['after_title'] : "</h2>\n";

		$sidebar = new k2sbmSidebar($args['name'], $args['before_widget'], $args['after_widget'], $args['before_title'], $args['after_title']);

		// Add the sidebar to the list
		$k2sbm_registered_sidebars[$sidebar->id] = $sidebar;
	}

	/**
	 * Function to unregister a sidebar
	 **/
	function unregister_sidebar($name) {
		global $k2sbm_registered_sidebars;

		$id = k2sbm::name_to_id($name);

		// Unregister the sidebar
		unset($k2sbm_registered_sidebars[$id]);
	}

	/**
	 * Function to get the list of sidebars
	 **/
	function get_sidebars() {
		global $k2sbm_registered_sidebars;

		return $k2sbm_registered_sidebars;
	}

	/**
	 * Function to register n sidebars
	 *
	 * $count - # of sidebars to register
	 * $args - Arguments for the sidebars
	 **/
	function register_sidebars($count = 1, $args = array()) {
		// Apparently, WPW lets you pass arguments as a string
		if(is_string($args)) {
			parse_str($args, $args);
		}

		// Check for a name
		$arg_name = isset($args['name']) ? $args['name'] : __('Sidebar %d', 'k2_domain');

		// Check there is a count in the name
		if(!strstr($arg_name, '%d')) {
			$arg_name += __(' %d', 'k2_domain');
		}

		// Register the sidebars
		for($i = 0; $i < $count; $i++) {
			$args['name'] = sprintf($arg_name, $i + 1);

			register_sidebar($args);
		}
	}

	/**
	 * Function to show a sidebar
	 *
	 * $name - Name of the sidebar to show
	 **/
	function dynamic_sidebar($name = 1) {
		global $k2sbm_registered_sidebars;

		$return = false;

		if(count($k2sbm_registered_sidebars) > 0) {
			// Check if this is an integer ID of a sidebar
			if(is_int($name)) {
				$name = sprintf(__('Sidebar %d', 'k2_domain'), $name);
			}

			// Get the sidebar
			$id = k2sbm::name_to_id($name);

			if(isset($k2sbm_registered_sidebars[$id])) {
				$return = $k2sbm_registered_sidebars[$id]->display();
			}
		}

		return $return;
	}


	/* Module functions ***********************************************************************/

	/**
	 * Function to load modules
	 **/
	function load_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		if(empty($k2sbm_active_modules) and empty($k2sbm_disabled_modules)) {
			$k2sbm_active_modules = get_option('k2sbm_modules_active');
			$k2sbm_disabled_modules = get_option('k2sbm_modules_disabled');
		}
	}

	/**
	 * Function to save modules
	 **/
	function save_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		update_option('k2sbm_modules_active', $k2sbm_active_modules);
		update_option('k2sbm_modules_disabled', $k2sbm_disabled_modules);
	}

	/**
	 * Function to register a module function
	 *
	 * $name - Module's name
	 * $callback - Callback function
	 * $css_class - The CSS class of this module
	 * $options - The module's default options
	 **/
	function register_sidebar_module($name, $callback, $css_class = '', $options = array()) {
		global $k2sbm_registered_modules;

		// Another odd bit of WPW code
		// Better include it for the sake of Widget developers
		if(is_array($name)) {
			$id = k2sbm::name_to_id(sprintf($name[0], $name[2]));
			$name = sprintf(__($name[0], $name[1], 'k2_domain'), $name[2]);
		} else {
			$id = k2sbm::name_to_id($name);
			$name = __($name, 'k2_domain');
		}

		$css_class = (string)$css_class == '' ? (string)$callback : $css_class;

		// Add the module to the array
		$k2sbm_registered_modules[$id] = array(
			'name' => $name,
			'callback' => $callback,
			'control_callback' => '',
			'css_class' => $css_class,
			'options' => $options
		);
	}

	/**
	 * Function to unregister a module
	 *
	 * $name - Name of the module to unregister
	 **/
	function unregister_sidebar_module($name) {
		global $k2sbm_registered_modules;

		$id = k2sbm::name_to_id($name);

		// Unset the module
		unset($k2sbm_registered_modules[$id]);
	}

	/**
	 * Function to check if a module is active
	 *
	 * $callback - Callback to check for
	 **/
	function is_active_module($callback) {
		global $k2sbm_registered_modules, $k2sbm_active_modules, $wp_query;

		$active = false;

		if($k2sbm_active_modules) {
			$tmp_modules = array_values($k2sbm_active_modules);

			// Check if a module with this callback is active
			for($i = 0; $i < count($tmp_modules) and !$active; $i++) {
				for($j = 0; $j < count($tmp_modules[$i]) and !$active; $j++) {
					$current_module = $tmp_modules[$i][$j];

					// We can only check if the module can be displayed if $wp_query is set
					// Otherwise, just assume it can. Ugly, but true.
					if($k2sbm_registered_modules[$current_module->type]['callback'] == $callback
						and (!$wp_query or $current_module->canDisplay())
					) {
						$active = true;
					}
				}
			}
		}

		return $active;
	}

	function register_sidebar_module_control($name, $callback) {
		global $k2sbm_registered_modules;

		// Another odd bit of WPW code
		// Better include it for the sake of Widget developers
		if(is_array($name)) {
			$id = k2sbm::name_to_id(sprintf($name[0], $name[2]));
			$name = sprintf(__($name[0], $name[1], 'k2_domain'), $name[2]);
		} else {
			$id = k2sbm::name_to_id($name);
			$name = __($name, 'k2_domain');
		}

		// Add the module control to the array
		if($k2sbm_registered_modules[$id]) {
			$k2sbm_registered_modules[$id]['control_callback'] = $callback;
		}
	}

	/**
	 * Function to unregister a module control
	 *
	 * $name - Name of the module control to unregister
	 **/
	function unregister_sidebar_module_control($name) {
		global $k2sbm_registered_modules;

		$id = k2sbm::name_to_id($name);

		// Unset the module control
		// Add the module control to the array
		if($k2sbm_registered_modules[$id]) {
			$k2sbm_registered_modules[$id]['control_callback'] = '';
		}
	}

	/**
	 * Function to get all the installed modules
	 **/
	function get_installed_modules() {
		global $k2sbm_registered_modules;

		// Sort the list of registered modules
		asort($k2sbm_registered_modules);

		// Return the list
		return $k2sbm_registered_modules;
	}

	/**
	 * Function to scan for all the modules
	 **/
	function module_scan() {
		k2sbm::module_scan_dir(dirname(dirname(dirname(__FILE__))) . '/modules/');
	}

	/**
	 * Function to scan a directory for usable modules
	 *
	 * $directory_path - The path to scan
	 **/
	function module_scan_dir($directory_path) {
		// Open the module directory
		$dir = dir($directory_path);

		// Get all the files from the directory
		while(($file = $dir->read()) !== false) {
			// Check the file is a module file
			if(is_file($directory_path . $file) and preg_match('/^(.+)\.php$/i', $file)) {
				// Include the file
				require_once($directory_path . $file);
			}
		}

		// Close the widget directory
		$dir->close();
	}

	/**
	 * Function to get all modules, reguardless of sidebar
	 **/
	function get_all_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		$all_modules = array();

		if($k2sbm_active_modules) {
			foreach($k2sbm_active_modules as $sidebar_modules) {
				foreach($sidebar_modules as $sidebar_module) {
					$all_modules[$sidebar_module->id] = $sidebar_module;
				}
			}
		}

		if($k2sbm_disabled_modules) {
			foreach($k2sbm_disabled_modules as $sidebar_module) {
				$all_modules[$sidebar_module->id] = $sidebar_module;
			}
		}

		return $all_modules;
	}

	/**
	 * Function to add a module
	 *
	 * $name - Name of the module
	 * $type - Type of the module
	 * $sidebar - Sidebar to add the module to
	 **/
	function add_module($name, $type, $sidebar) {
		global $k2sbm_registered_modules, $k2sbm_active_modules, $k2sbm_disabled_modules;

		$module_id = k2sbm::name_to_id($type);

		// Load the base module
		$base_module = $k2sbm_registered_modules[$module_id];

		// Check the base module is registered
		if($base_module) {
			// Create the ID for the module
			// Quick & cheap
			$next_id = get_option('k2sbm_modules_next_id');
			$module_id = 'module-' . $next_id;
			update_option('k2sbm_modules_next_id', ++$next_id);

			// Create the new module
			$new_module = new k2sbmModule($module_id, $name, $type, $base_module['options']);

			// Add the module to the list
			if($sidebar == 'disabled') {
				$k2sbm_disabled_modules[] = $new_module;
			} else {
				$k2sbm_active_modules[k2sbm::name_to_id($sidebar)][] = $new_module;
			}

			k2sbm::save_modules();
		}
	}

	/**
	 * Function to update module
	 *
	 * $sidebar_id - The ID of the sidebar the module resides on
	 * $module_id - The ID of the module itself
	 **/
	function update_module($sidebar_id, $module_id) {
		global $k2sbm_disabled_modules, $k2sbm_active_modules;

		// Start the capture
		ob_start();

		if($sidebar_id == 'disabled') {
			foreach($k2sbm_disabled_modules as $key => $module) {
				if($module->id == $module_id) {
					$k2sbm_disabled_modules[$key]->displayControl();
				}
			}
		} else {
			foreach($k2sbm_active_modules[$sidebar_id] as $key => $module) {
				if($module->id == $module_id) {
					$k2sbm_active_modules[$sidebar_id][$key]->displayControl();
				}
			}
		}

		k2sbm::save_modules();

		// Junk the capture
		ob_end_clean();
	}

	/**
	 * Function to remove module
	 *
	 * $sidebar_id - The ID of the sidebar the module resides on
	 * $module_id - The ID of the module itself
	 **/
	function remove_module($sidebar_id, $module_id) {
		global $k2sbm_disabled_modules, $k2sbm_active_modules;

		if($sidebar_id == 'disabled') {
			foreach($k2sbm_disabled_modules as $key => $module) {
				if($module->id == $module_id) {
					unset($k2sbm_disabled_modules[$key]);
				}
			}
		} else {
			foreach($k2sbm_active_modules[$sidebar_id] as $key => $module) {
				if($module->id == $module_id) {
					unset($k2sbm_active_modules[$sidebar_id][$key]);
				}
			}
		}

		k2sbm::save_modules();
	}

	/**
	 * Function to re-order modules
	 *
	 * $ordering - Array of modules' order in sidebars
	 **/
	function reorder_sidebar($ordering) {
		global $k2sbm_disabled_modules, $k2sbm_active_modules;

		$all_modules = k2sbm::get_all_modules();

		$k2sbm_disabled_modules = array();
		$k2sbm_active_modules = array();

		foreach($ordering as $sidebar_id => $modules) {
			if($sidebar_id == 'disabled') {
				foreach($modules as $module_id) {
					$k2sbm_disabled_modules[] = $all_modules[$module_id];
				}
			} else {
				foreach($modules as $module_id) {
					$k2sbm_active_modules[$sidebar_id][] = $all_modules[$module_id];
				}
			}
		}

		k2sbm::save_modules();
	}

	/**
	 * Function to output the CSS files of the modules to the header of the site
	 **/
	function output_module_css_files() {
		global $k2sbm_active_modules;

		$css_files = array();

		foreach($k2sbm_active_modules as $modules) {
			foreach($modules as $module) {
				// If this module has a CSS file to show, and will be shown on this page
				// then get the file
				if($module->output['css_file'] and $module->canDisplay()) {
					$css_files[] = $module->output['css_file'];
				}
			}
		}

		// Strip duplicates
		$css_files = array_unique($css_files);

		// Output the links
		if($css_files) {
			foreach($css_files as $css_file) {
				echo('<link rel="stylesheet" href="' . $css_file . '" type="text/css" media="screen" />');
			}
		}
	}


	/* Generic functions **********************************************************************/

	/**
	 * Function to change a name into an ID
	 *
	 * $name - Name to convert to an ID
	 **/
	function name_to_id($name) {
		// Use the WP function to do this
		return sanitize_title($name);
	}
}

/**
 * Class to represent a sidebar
 **/
class k2sbmSidebar {
	var $id;
	var $name;
	var $before_module;
	var $after_module;
	var $before_title;
	var $after_title;

	var $modules;

	/**
	 * Sidebar constructor
	 *
	 * $name - The name of the sidebar
	 * $before_module - The HTML code to display before each module
	 * $after_module - The HTML code to display after each module
	 * $before_title - The HTML code to display before each module title
	 * $after_title - The HTML code to display after each module title
	 **/
	function k2sbmSidebar($name, $before_module, $after_module, $before_title, $after_title) {
		global $k2sbm_active_modules;

		// Set the generic data from the parameters
		$this->id = k2sbm::name_to_id($name);
		$this->name = $name;
		$this->before_module = $before_module;
		$this->after_module = $after_module;
		$this->before_title = $before_title;
		$this->after_title = $after_title;

		$this->modules = array();

		// Load the modules
		if($k2sbm_active_modules[$this->id]) {
			foreach($k2sbm_active_modules[$this->id] as $module_id => $module) {
				$this->modules[$module_id] = $module;
			}
		}
	}

	/**
	 * Displays the sidebar by outputting it's modules
	 **/
	function display() {
		// Check there are some modules present
		if(count($this->modules) > 0) {
			$return = false;

			// Output the modules
			foreach($this->modules as $module) {
				$return |= $module->display($this);
			}

			return $return;
		} else {
			return false;
		}
	}
}

/**
 * Class to represent a module
 **/
class k2sbmModule {
	var $id;
	var $name;
	var $type;

	var $display;
	var $output;
	var $options;

	/**
	 * Module constructor
	 *
	 * $id - The ID of the module
	 * $name - The name of the module
	 * $type - The base type of the module, used for accessing data such as callbacks
	 * $options - The default module options
	 **/
	function k2sbmModule($id, $name, $type, $options) {
		// Set the generic data from the parameters
		$this->id = $id;
		$this->name = $name;
		$this->type = $type;

		$this->display = array(
			'home' => true,
			'archives' => true,
			'post' => true,
			'post_id' => array('show' => 'show', 'ids' => false),
			'search' => true,
			'pages' => true,
			'page_id' => array('show' => 'show', 'ids' => false),
			'error' => true
		);
		$this->output = array('show_title' => true, 'css_file' => false);
		$this->options = $options;
	}

	/**
	 * Displays a module
	 *
	 * $sidebar - The sidebar the module belongs to
	 **/
	function display($sidebar) {
		global $k2sbm_registered_modules, $k2sbm_current_module, $post;
		static $k2sbm_count_id;

		// Get the base module details
		$base_module = $k2sbm_registered_modules[$this->type];

		// Check that the function exists & that this module is to be displayed
		if(function_exists($base_module['callback'])) {
			if($this->canDisplay()) {
				$k2sbm_current_module = $this;
				$id = k2sbm::name_to_id($this->name);

				$k2sbm_count_id[$id]++;

				$id = $id . ($k2sbm_count_id[$id] > 1 ? '-' . $k2sbm_count_id[$id] : '');

				// Call the display callback
				$params[0] = array(
					'before_module' => sprintf($sidebar->before_module, $id, $base_module['css_class']),
					'after_module' => $sidebar->after_module
				);

				// Allow the user to hide the title, simplest method is to unset the title elements
				if($this->output['show_title']) {
					$params[0]['before_title'] = $sidebar->before_title;
					$params[0]['title'] = $this->name;
					$params[0]['after_title'] = $sidebar->after_title;
				} else {
					$params[0]['before_title'] = '';
					$params[0]['title'] = '';
					$params[0]['after_title'] = '';
				}

				$params[0]['before_widget'] = $params[0]['before_module'];
				$params[0]['after_widget'] = $params[0]['after_module'];
				call_user_func_array($base_module['callback'], $params);

				// Update options in any PHP < 5
				if(version_compare(PHP_VERSION, '5.0') < 0) {
					foreach($k2sbm_current_module->options as $key => $value) {
						$this->update_option($key, $value);
					}
				}

				$k2sbm_current_module = false;

				return true;
			}
		} else {
			// Remove this module - it dosn't exist properly
			k2sbm::remove_module($sidebar->id, $this->id);
		}

		return false;
	}

	/**
	 * Display's the module's controls for editing
	 **/
	function displayControl() {
		global $k2sbm_registered_modules, $k2sbm_current_module;

		// Handle default control stuff

		// Handle the module name form
		if(isset($_POST['module_name']) and trim((string)$_POST['module_name']) != '') {
			$this->name = stripslashes((string)$_POST['module_name']);
		} else {
			k2sbm::set_error_text(__('You must specify a valid module name', 'k2_domain'));
		}

		// Handle the advanced output options form
		if(isset($_POST['output'])) {
			// Don't set anything...
			foreach($this->output as $key => $value) {
				$this->output[$key] = false;
			}

			// ...unless given
			foreach($_POST['output'] as $key => $value) {
				$this->output[$key] = $value;
			}
		}

		// Handle the module display form
		if(isset($_POST['display'])) {
			// Store the page and post IDs, AJAX mess
			$old_post_id = $this->display['post_id'];
			$old_page_id = $this->display['page_id'];

			// Don't display anything...
			foreach($this->display as $page => $display) {
				$this->display[$page] = false;
			}

			// ...unless specified
			foreach($_POST['display'] as $page => $display) {
				$this->display[$page] = $display;
			}

			// Add the exceptional circumstances, if required
			if(!isset($_POST['display']['post_id'])) {
				$this->display['post_id'] = $old_post_id;
			}

			if(!isset($_POST['display']['page_id'])) {
				$this->display['page_id'] = $old_page_id;
			}
		}

		// Display the generic edit form
		extract(array('module' => $this));
		include(TEMPLATEPATH . '/options/display/sbm-ajax/edit-module-form.php');

		// Get the base module details
		$base_module = $k2sbm_registered_modules[$this->type];

		if(function_exists($base_module['control_callback'])) {
			$k2sbm_current_module = $this;

			// Call the control callback
			call_user_func($base_module['control_callback']);

			// Update options in any PHP < 5
			if(version_compare(PHP_VERSION, '5.0') < 0) {
				foreach($k2sbm_current_module->options as $key => $value) {
					$this->update_option($key, $value);
				}
			}

			$k2sbm_current_module = false;

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Displays the checkbox list of specific posts this module will appear on
	 **/
	function displayPostList() {
		// Display the generic post list
		extract(array('module' => $this));
		include(TEMPLATEPATH . '/options/display/sbm-ajax/edit-module-posts-form.php');
	?>
		
	<?php
	}

	/**
	 * Displays the checkbox list of specific pages this module will appear on
	 **/
	function displayPageList() {
		// Display the generic post list
		extract(array('module' => $this));
		include(TEMPLATEPATH . '/options/display/sbm-ajax/edit-module-pages-form.php');
	}

	/**
	 * Returns whether this module is to be displayed on the current blog page
	 **/
	function canDisplay() {
		global $post;

		return ($this->display['home'] and is_home())
			or ($this->display['archives'] and (is_archive() or (function_exists('is_tag') and is_tag())))
			or ($this->display['post'] and is_single() and (
				   !$this->display['post_id']['ids']
				or ($this->display['post_id']['show'] == 'show' and $this->display['post_id']['ids'][$post->ID])
				or ($this->display['post_id']['show'] == 'hide' and !$this->display['post_id']['ids'][$post->ID]))
			)
			or ($this->display['search'] and is_search())
			or ($this->display['pages'] and is_page() and (
				   !$this->display['page_id']['ids']
				or ($this->display['page_id']['show'] == 'show' and $this->display['page_id']['ids'][$post->ID])
				or ($this->display['page_id']['show'] == 'hide' and !$this->display['page_id']['ids'][$post->ID]))
			)
			or ($this->display['error'] and (is_404() or !($post or have_posts()))
		);
	}

	/**
	 * Gets the value of an option relating to this module
	 *
	 * $name - The name of the option
	 **/
	function get_option($name) {
		return $this->options[$name];
	}

	/**
	 * Adds an option relating to this module
	 *
	 * $name - The name of the option
	 * $value - The value of the option
	 **/
	function add_option($name, $value = '') {
		$this->options[$name] = $value;
	}

	/**
	 * Updates an option relating to this module
	 *
	 * $name - The name of the option
	 * $value - The value of the option
	 **/
	function update_option($name, $newvalue) {
		$this->options[$name] = $newvalue;
	}

	/**
	 * Deletes an option relating to this module
	 *
	 * $name - The name of the option
	 **/
	function delete_option($name) {
		unset($this->options[$name]);
	}
}


/* Helper functions *******************************************************************************/

/**
 * Helper function to set an option
 **/
function sbm_get_option($name) {
	global $k2sbm_current_module;

	return $k2sbm_current_module->get_option($name);
}

/**
 * Helper function to add an option
 **/
function sbm_add_option($name, $value = '', $description = '', $autoload = 'yes') {
	global $k2sbm_current_module;

	$k2sbm_current_module->add_option($name, $value, $description);
}

/**
 * Helper function to update an option
 **/
function sbm_update_option($name, $newvalue) {
	global $k2sbm_current_module;

	$k2sbm_current_module->update_option($name, $newvalue);
}

/**
 * Helper function to delete an option
 **/
function sbm_delete_option($name) {
	global $k2sbm_current_module;

	$k2sbm_current_module->delete_option($name);
}

/**
 * Helper function to register a sidebar
 **/
function register_sidebar($args = array()) {
	k2sbm::register_sidebar($args);
}

/**
 * Helper function to unregister a sidebar
 **/
function unregister_sidebar($name) {
	k2sbm::unregister_sidebar($name);
}

/**
 * Helper function to register n sidebars
 **/
function register_sidebars($count = 1, $args = array()) {
	k2sbm::register_sidebars($count, $args);
}

/**
 * Helper function to show a sidebar
 **/
function dynamic_sidebar($name = 1) {
	return k2sbm::dynamic_sidebar($name);
}

/**
 * Helper function to register a module
 **/
function register_sidebar_module($name, $callback, $css_class = '', $options = array()) {
	k2sbm::register_sidebar_module($name, $callback, $css_class, $options);
}

/**
 * Helper function to unregister a module
 **/
function unregister_sidebar_module($name) {
	k2sbm::unregister_sidebar_module($name);
}

/**
 * Helper function to check if a module is active
 **/
function is_active_module($callback) {
	return k2sbm::is_active_module($callback);
}

/**
 * Helper function to register a module's control
 **/
function register_sidebar_module_control($name, $callback) {
	k2sbm::register_sidebar_module_control($name, $callback);
}

/**
 * Helper function to unregister a module's control
 **/
function unregister_sidebar_module_control($name) {
	k2sbm::unregister_sidebar_module_control($name);
}



/**
 * Until SBM takes over the world of Wordpress sidebars ;), it's nice to allow Widgets to be transparently allowed.
 * Therefore, these helper functions are wrappers for WPW's hooks that allow SMB to use WPW widgets.
 *
 * Sorry about the daft acronyms. ;)
 **/

/**
 * WPW function to register a widget
 **/
function register_sidebar_widget($name, $callback, $classname = '') {
	k2sbm::register_sidebar_module($name, $callback, $classname);
}

/**
 * WPW function to unregister a widget
 **/
function unregister_sidebar_widget($name) {
	k2sbm::unregister_sidebar_module($name);
}

/**
 * WPW function to check if a widget is active
 **/
function is_active_widget($callback) {
	return k2sbm::is_active_module($callback);
}

/**
 * WPW function to register a widget's control
 **/
function register_widget_control($name, $callback, $width = false, $height = false) {
	// Chop off W & H, not needed
	k2sbm::register_sidebar_module_control($name, $callback);
}

/**
 * WPW function to unregister a widget's control
 **/
function unregister_widget_control($name) {
	k2sbm::unregister_sidebar_module_control($name);
}

?>
