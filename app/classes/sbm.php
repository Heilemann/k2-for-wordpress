<?php

if(K2_USING_SBM) {
	define('SBM_VERSION', '1.0.0');

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

	// Counter for semantic classes
	$k2sbm_module_index = 0;
}

class K2SBM {
	function install() {
		add_option('k2sbm_modules_active', array(), 'The active sidebar modules.');
		add_option('k2sbm_modules_disabled', array(), 'The disabled sidebar modules.');
		add_option('k2sbm_modules_next_id', 1, 'The ID for the next sidebar module.');
	}

	function uninstall() {
		// Remove all existing sbm-stub paths
		$plugins = (array) get_option('active_plugins');

		foreach($plugins as $key => $value) {
			if (strpos($value, 'sbm-stub.php') !== false) {
				unset($plugins[$key]);
			}
		}

		update_option('active_plugins', $plugins);
	}

	function activate() {
		// Add the stub widget removal plugin
		$plugins = (array)get_option('active_plugins');
		$plugins[] = '../themes/' . get_template() . '/app/includes/widgets-removal.php';
		update_option('active_plugins', $plugins);
	}

	function deactivate() {
		// Remove the stub widget removal plugin
		$plugins = (array)get_option('active_plugins');
		$plugin = '../themes/' . basename(dirname(dirname(dirname(__FILE__)))) . '/app/includes/widgets-removal.php';

		foreach($plugins as $key => $value) {
			if($value == $plugin) {
				unset($plugins[$key]);
			}
		}

		update_option('active_plugins', $plugins);
	}

	function init() {
		global $k2sbm_active_modules;

		add_action('admin_menu', array('K2SBM', 'add_menus'));

		K2SBM::pre_bootstrap();

		// Post-bootstrap when everything is loaded
		add_action('init', array('K2SBM', 'post_bootstrap'));

		// Output the CSS files, if there are modules
		if($k2sbm_active_modules) {
			add_action('wp_head', array('K2SBM', 'output_module_css_files'));
		}
	}

	function direct_bootstrap() {
		global $k2sbm_registered_modules, $k2sbm_registered_sidebars, $k2sbm_active_modules, $k2sbm_disabled_modules, $k2sbm_error_text;

		// You MUST be an admin to access this stuff
		auth_redirect();

		K2SBM::pre_bootstrap();

		// Check for specific actions that return a HTML response
		if($_POST['action'] == 'control-show') {
			if(isset($_POST['module_id'])) {
				$all_modules = K2SBM::get_all_modules();
				$all_modules[$_POST['module_id']]->displayControl();
			} else {
				echo(false);
			}
		} elseif($_POST['action'] == 'control-post-list-show') {
			if(isset($_POST['module_id'])) {
				$all_modules = K2SBM::get_all_modules();
				$all_modules[$_POST['module_id']]->displayPostList();
			} else {
				echo(false);
			}
		} elseif($_POST['action'] == 'control-page-list-show') {
			if(isset($_POST['module_id'])) {
				$all_modules = K2SBM::get_all_modules();
				$all_modules[$_POST['module_id']]->displayPageList();
			} else {
				echo(false);
			}
		} elseif($_POST['action'] == 'backup') {
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename=sbm-' . date('Y-m-d') . '.dat');
			header('Content-Type: text/plain; charset=' . get_option('blog_charset'), true);
			echo(serialize(array('sbm_version' => SBM_VERSION, 'active_modules' => $k2sbm_active_modules, 'disabled_modules' => $k2sbm_disabled_modules, 'id' => get_option('k2sbm_modules_next_id'))));
		} else {
			// Set the output type
			header('Content-type: text/plain; charset: UTF-8');

			// Check what the action is
			switch($_POST['action']) {
				// Add a module to the sidebar
				case 'add':
					// Check the title was correct
					if(isset($_POST['add_name']) and trim((string)($_POST['add_name'])) != '') {
						K2SBM::add_module(stripslashes($_POST['add_name']), $_POST['add_type'], $_POST['add_sidebar']);
					} else {
						K2SBM::set_error_text(__('You must specify a valid module name', 'k2_domain'));
					}

					break;

				// Update a module
				case 'update':
					if(isset($_POST['sidebar_id']) and isset($_POST['module_id'])) {
						K2SBM::update_module($_POST['sidebar_id'], $_POST['module_id']);
					} else {
						K2SBM::set_error_text(__('Missing sidebar and module ids', 'k2_domain'));
					}

					break;

				// Remove a module from the sidebar
				case 'remove':
					if(isset($_POST['sidebar_id']) and isset($_POST['module_id'])) {
						K2SBM::remove_module($_POST['sidebar_id'], $_POST['module_id']);
					} else {
						K2SBM::set_error_text(__('Missing sidebar and module ids', 'k2_domain'));
					}

					break;

				// Re-order the modules in the sidebar
				case 'reorder':
					if(isset($_POST['sidebar_ordering'])) {
						K2SBM::reorder_sidebar($_POST['sidebar_ordering']);
					} else {
						K2SBM::set_error_text(__('Missing ordering data', 'k2_domain'));
					}

					break;

				// Error
				default:
					K2SBM::set_error_text(__('Invalid call', 'k2_domain'));
					break;
			}

			// Begin the JSON response
			echo('{result: ');

			if($k2sbm_error_text != null) {
				echo('false, error: "' . $k2sbm_error_text . '"');
			} else {
				echo('true');
			}

			// End the response
			echo('}');

			// Safeguard
			wp_cache_flush();
		}
	}

	function pre_bootstrap() {
		// Load the modules
		K2SBM::load_modules();

		// Scan for in-built modules
		K2SBM::module_scan();

	}

	function post_bootstrap() {
		// Allow the Widgets and SBM defined in plugins & themes to be loaded
		do_action('sbm_init');
		do_action('widgets_init');
	}

	function add_menus() {
		// Add the submenus
		$page = add_theme_page(__('K2 Sidebar Manager','k2_domain'), __('K2 Sidebar Manager','k2_domain'), 'edit_themes', 'k2-sbm-manager', array('K2SBM', 'module_admin'));

		add_action("admin_head-$page", array('K2SBM', 'module_admin_head'));
		add_action("admin_print_scripts-$page", array('K2SBM', 'module_admin_scripts'));
	}

	function module_admin() {
		global $k2sbm_registered_sidebars, $k2sbm_registered_modules, $k2sbm_active_modules, $k2sbm_disabled_modules;

			$restored = false;
			$error = false;

			if($_POST['action'] == 'restore' && $_FILES['backup']['error'] == 0) {
				$data = (array)unserialize(file_get_contents($_FILES['backup']['tmp_name']));

				if(isset($data['sbm_version']) && version_compare($data['sbm_version'], SBM_VERSION) <= 0) {
					$k2sbm_active_modules = $data['active_modules'];
					$k2sbm_disabled_modules = $data['disabled_modules'];
					update_option('k2sbm_modules_next_id', $data['id']);

					K2SBM::save_modules();
					$restored = true;
				} else {
					$error = true;
				}
			}

			extract(array('restored' => $restored, 'error' => $error));

			if(count($k2sbm_registered_sidebars) == 0) {
			?>
				<div class="wrap">You have no registered sidebars.</div>
			<?php
			} elseif(count($k2sbm_registered_modules) == 0) {
			?>
				<div class="wrap">You have no modules or Widgets installed &amp; activated.</div>
			<?php
			} else {
				include(TEMPLATEPATH . '/app/display/sbm/modules.php');
			}

	}

	function module_admin_scripts() {
		// Register our scripts
		K2::register_scripts();

		// Add our script to the queue
		wp_enqueue_script('k2sbm');
	}

	function module_admin_head() {
		?>
			<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/sbm.css" />

			<script type="text/javascript">
				//<![CDATA[
					jQuery(document).ready(function(){ sbm_load(<?php echo(get_option('k2sbm_modules_next_id')); ?>, "<?php output_javascript_url('app/includes/sbm-direct.php'); ?>"); });
				//]]>
			</script>
		<?php
	}

	function set_error_text($text) {
		global $k2sbm_error_text;

		$k2sbm_error_text = $text;
	}

	function register_sidebar($args = array()) {
		global $k2sbm_registered_sidebars;

		// Just in case they have not yet been loaded
		K2SBM::load_modules();

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

	function unregister_sidebar($name) {
		global $k2sbm_registered_sidebars;

		$id = K2SBM::name_to_id($name);

		// Unregister the sidebar
		unset($k2sbm_registered_sidebars[$id]);
	}

	function get_sidebars() {
		global $k2sbm_registered_sidebars;

		return $k2sbm_registered_sidebars;
	}

	function get_disabled() {
		global $k2sbm_disabled_modules;

		return $k2sbm_disabled_modules;
	}

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

			K2SBM::register_sidebar($args);
		}
	}

	function dynamic_sidebar($name = 1) {
		global $k2sbm_registered_sidebars;

		$return = false;

		if(count($k2sbm_registered_sidebars) > 0) {
			// Check if this is an integer ID of a sidebar
			if(is_int($name)) {
				$name = sprintf(__('Sidebar %d', 'k2_domain'), $name);
			}

			// Get the sidebar
			$id = K2SBM::name_to_id($name);

			if(isset($k2sbm_registered_sidebars[$id])) {
				$return = $k2sbm_registered_sidebars[$id]->display();
			}
		}

		return $return;
	}

	function load_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		if(empty($k2sbm_active_modules) and empty($k2sbm_disabled_modules)) {
			$k2sbm_active_modules = get_option('k2sbm_modules_active');
			$k2sbm_disabled_modules = get_option('k2sbm_modules_disabled');

			if ( empty($k2sbm_active_modules) ) $k2sbm_active_modules = array();
			if ( empty($k2sbm_disabled_modules) ) $k2sbm_disabled_modules = array();
		}
	}

	function save_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		update_option('k2sbm_modules_active', $k2sbm_active_modules);
		update_option('k2sbm_modules_disabled', $k2sbm_disabled_modules);
	}

	function register_sidebar_module($name, $callback, $css_class = '', $options = array()) {
		global $k2sbm_registered_modules;

		// Another odd bit of WPW code
		// Better include it for the sake of Widget developers
		if(is_array($name)) {
			$id = K2SBM::name_to_id(sprintf($name[0], $name[2]));
			$name = sprintf(__($name[0], $name[1], 'k2_domain'), $name[2]);
		} else {
			$id = K2SBM::name_to_id($name);
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

	function unregister_sidebar_module($name) {
		global $k2sbm_registered_modules;

		$id = K2SBM::name_to_id($name);

		// Unset the module
		unset($k2sbm_registered_modules[$id]);
	}

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
			$id = K2SBM::name_to_id(sprintf($name[0], $name[2]));
			$name = sprintf(__($name[0], $name[1], 'k2_domain'), $name[2]);
		} else {
			$id = K2SBM::name_to_id($name);
			$name = __($name, 'k2_domain');
		}

		// Add the module control to the array
		if($k2sbm_registered_modules[$id]) {
			$k2sbm_registered_modules[$id]['control_callback'] = $callback;
		}
	}

	function unregister_sidebar_module_control($name) {
		global $k2sbm_registered_modules;

		$id = K2SBM::name_to_id($name);

		// Unset the module control
		// Add the module control to the array
		if($k2sbm_registered_modules[$id]) {
			$k2sbm_registered_modules[$id]['control_callback'] = '';
		}
	}

	function get_installed_modules() {
		global $k2sbm_registered_modules;

		// Sort the list of registered modules
		asort($k2sbm_registered_modules);

		// Return the list
		return $k2sbm_registered_modules;
	}

	function module_scan() {
		K2SBM::module_scan_dir(dirname(dirname(__FILE__)) . '/modules/');
	}

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

	function get_active_modules($sidebar) {
		foreach($k2sbm_registered_sidebars as $sidebar) {
			$tmp_modules[] = $sidebar->modules;
		}

		return $active_modules;
	}

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

	function add_module($name, $type, $sidebar) {
		global $k2sbm_registered_modules, $k2sbm_active_modules, $k2sbm_disabled_modules;

		$module_id = K2SBM::name_to_id($type);

		// Load the base module
		$base_module = $k2sbm_registered_modules[$module_id];

		// Check the base module is registered
		if($base_module) {
			// Create the ID for the module
			// Quick & cheap
			$next_id = get_option('k2sbm_modules_next_id');

			// Make sure it's at least 1
			if ( $next_id < 1 ) $next_id = 1;

			$module_id = 'module-' . $next_id;
			update_option('k2sbm_modules_next_id', (++$next_id));

			// Create the new module
			$new_module = new k2sbmModule($module_id, $name, $type, $base_module['options']);

			// Add the module to the list
			if($sidebar == 'disabled') {
				$k2sbm_disabled_modules[] = $new_module;
			} else {
				$k2sbm_active_modules[K2SBM::name_to_id($sidebar)][] = $new_module;
			}

			K2SBM::save_modules();
		}
	}

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

		K2SBM::save_modules();

		// Junk the capture
		ob_end_clean();
	}

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

		K2SBM::save_modules();
	}

	function reorder_sidebar($ordering) {
		global $k2sbm_disabled_modules, $k2sbm_active_modules;

		$all_modules = K2SBM::get_all_modules();

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

		K2SBM::save_modules();
	}

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

	function name_to_id($name) {
		// Use the WP function to do this
		return sanitize_title($name);
	}
}

if(K2_USING_SBM) {
	class k2sbmSidebar {
		var $id;
		var $name;
		var $before_module;
		var $after_module;
		var $before_title;
		var $after_title;

		var $modules;

		function k2sbmSidebar($name, $before_module, $after_module, $before_title, $after_title) {
			global $k2sbm_active_modules;

			// Set the generic data from the parameters
			$this->id = K2SBM::name_to_id($name);
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

		function display() {
			global $k2sbm_module_index;

			// Reset the counter
			$k2sbm_module_index = 1;

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

	class k2sbmModule {
		var $id;
		var $name;
		var $type;

		var $display;
		var $output;
		var $options;

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

		function display($sidebar) {
			global $k2sbm_registered_modules, $k2sbm_current_module, $k2sbm_module_index;
			static $k2sbm_count_id;

			// Get the base module details
			$base_module = $k2sbm_registered_modules[$this->type];

			// Check that the function exists & that this module is to be displayed
			if(function_exists($base_module['callback'])) {
				if($this->canDisplay()) {
					$k2sbm_current_module = $this;
					$id = K2SBM::name_to_id($this->name);

					$k2sbm_count_id[$id]++;

					$id = $id . ($k2sbm_count_id[$id] > 1 ? '-' . $k2sbm_count_id[$id] : '');

					if(strstr($id, '%') !== false) {
						$id = str_replace('%', '-', $id);
					}

					if(preg_match('/[0-9\-]/', ($first_char = $id[0]))) {
						$id = 'module' . ($first_char != '-' ? '-' : '') . $id;
					}

					// Call the display callback
					$params[0] = array(
						'before_module' => sprintf($sidebar->before_module, $id, $this->css_class($k2sbm_module_index++, $base_module['css_class'])),
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
				K2SBM::remove_module($sidebar->id, $this->id);
			}

			return false;
		}

		function displayControl() {
			global $k2sbm_registered_modules, $k2sbm_current_module;

			// Handle the module name form
			if(isset($_POST['module_name']) and trim((string)$_POST['module_name']) != '') {
				$this->name = stripslashes((string)$_POST['module_name']);
			} else {
				K2SBM::set_error_text(__('You must specify a valid module name', 'k2_domain'));
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

			$k2sbm_current_module = $this;

			// Display the generic edit form
			extract(array('module' => $this));
			include(TEMPLATEPATH . '/app/display/sbm/edit-module-form.php');

			// Update options in any PHP < 5
			if(version_compare(PHP_VERSION, '5.0') < 0) {
				foreach($k2sbm_current_module->options as $key => $value) {
					$this->update_option($key, $value);
				}
			}

			$k2sbm_current_module = false;

			// Get the base module details
			$base_module = $k2sbm_registered_modules[$this->type];

			if(function_exists($base_module['control_callback'])) {
				return true;
			} else {
				return false;
			}
		}

		function displayPostList() {
			// Display the generic post list
			extract(array('module' => $this));
			include(TEMPLATEPATH . '/app/display/sbm/edit-module-posts-form.php');
		}

		function displayPageList() {
			// Display the generic post list
			extract(array('module' => $this));
			include(TEMPLATEPATH . '/app/display/sbm/edit-module-pages-form.php');
		}

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

		function get_options() {
			return $this->options;
		}

		function get_option($name) {
			return $this->options[$name];
		}

		function add_option($name, $value = '') {
			$this->options[$name] = $value;
		}

		function update_option($name, $newvalue) {
			$this->options[$name] = $newvalue;
		}

		function delete_option($name) {
			unset($this->options[$name]);
		}

		function css_class($module_count, $module_class) {
			$c = array('module', "module$module_count", $module_class);

			if ( $module_count & 1 == 1 ) {
				$c[] = 'alt';
			}

			return join(' ', apply_filters('module_class', $c));
		}
	}

	add_action('k2_init', array('K2SBM', 'init'));
}

add_action('k2_install', array('K2SBM', 'install'));
add_action('k2_uninstall', array('K2SBM', 'uninstall'));
add_action('k2_activate', array('K2SBM', 'activate'));
add_action('k2_deactivate', array('K2SBM', 'deactivate'));

?>
