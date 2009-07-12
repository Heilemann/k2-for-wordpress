<?php

// The active modules
$k2sbm_active_modules = array();

// The disabled modules
$k2sbm_disabled_modules = array();

// The module currently being manipulated
$k2sbm_current_module = false;

class K2SBM {
	function init() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules, $k2sbm_restore, $k2sbm_restore_error;

		if ( is_admin() ) {

			add_action('admin_menu', array('K2SBM', 'add_menus'));

			/*
			$k2sbm_restore = false;
			$k2sbm_restore_error = false;

			if($_POST['sbm_action'] == 'restore' && $_FILES['backup']['error'] == 0) {
				$data = (array)unserialize(file_get_contents($_FILES['backup']['tmp_name']));

				if(isset($data['sbm_version']) && version_compare($data['sbm_version'], SBM_VERSION) <= 0) {
					$k2sbm_active_modules = $data['active_widgets'];
					$k2sbm_disabled_modules = $data['disabled_widgets'];
					update_option('k2sbm_widgets_next_id', $data['id']);

					K2SBM::save_modules();

					$k2sbm_restore = true;
				} else {
					$k2sbm_restore_error = true;
				}
			}*/
		}

		K2SBM::load_modules();
	}

	function install() {
		add_option('k2sbm_modules', array(), 'The active sidebar modules.');
	}

	function upgrade() {
	}

	function uninstall() {
		delete_option('k2sbm_modules');
	}

	function direct_bootstrap() {
		global $k2sbm_registered_widgets, $k2sbm_active_modules, $k2sbm_disabled_modules, $k2sbm_error_text;

		// You MUST be an admin to access this stuff
		auth_redirect();

		K2SBM::load_modules();

		// Check for specific actions that return a HTML response
		if ( 'control-show' == $_POST['sbm_action'] ) {
			$module_id = sanitize_title( $_POST['module'] );

			// Widgets hack
			K2SBM::multi_widget_hack( $module_id );

			if ( isset($k2sbm_active_modules[ $module_id ]) )
				$k2sbm_active_modules[$module_id]->displayControl();

			exit;
		} elseif ('update' == $_POST['sbm_action'] ) {
			K2SBM::update_module( sanitize_title($_POST['sidebar']), sanitize_title($_POST['module']) );

			exit;
/*
		} elseif($_POST['sbm_action'] == 'backup') {
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename=sbm-' . date('Y-m-d') . '.dat');
			header('Content-Type: text/plain; charset=' . get_option('blog_charset'), true);
			echo serialize( array(
					'sbm_version' => SBM_VERSION,
					'active_modules' => $k2sbm_active_modules,
					'disabled_modules' => $k2sbm_disabled_modules
				)
			);
*/
		} else {
			// Set the output type
			header('Content-type: text/plain; charset: UTF-8');

			// Check what the action is
			switch( $_POST['sbm_action'] ) {
				// Add a module to the sidebar
				case 'add':
					$status = K2SBM::add_module( $_POST['sidebar'], $_POST['module'], $_POST['is_multi'] );
					break;

				// Remove a module from the sidebar
				case 'remove':
					$status = K2SBM::remove_module( $_POST['sidebar'], $_POST['module'] );
					break;

				// Re-order the modules in the sidebar
				case 'reorder':
					$status = K2SBM::reorder_sidebar( $_POST['sidebar_ordering'] );
					break;

				// Error
				default:
					K2SBM::set_error_text(__('Invalid call', 'k2_domain'));
					break;
			}

			// Begin the JSON response
			echo('{result: ');

			if ( true === $status ) {
				echo('true');
			} else {
				echo('false, error: "' . $status . '"');
			}

			// End the response
			echo('}');

			// Safeguard
			wp_cache_flush();
			exit;
		}
	}

	function add_menus() {
		// Add the submenus
		$page = add_theme_page(__('K2 Widgets Manager','k2_domain'), __('K2 Widgets Manager','k2_domain'), 'edit_themes', 'k2-widgets-manager', array('K2SBM', 'module_admin'));

		add_action("admin_head-$page", array('K2SBM', 'module_admin_head'));
		add_action("admin_print_scripts-$page", array('K2SBM', 'module_admin_scripts'));
	}

	function module_admin() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules, $k2sbm_restore, $k2sbm_restore_error;
		global $wp_registered_sidebars, $wp_registered_widgets;

		extract(array('restored' => $k2sbm_restore, 'error' => $k2sbm_restore_error));

		if ( count($wp_registered_sidebars) == 0 ): ?>
			<div class="wrap">You have no registered sidebars.</div>
		<?php elseif ( count($wp_registered_widgets) == 0 ): ?>
			<div class="wrap">You have no widgets installed &amp; activated.</div>
		<?php else:
			K2SBM::sync_modules();

			include(TEMPLATEPATH . '/app/display/widgets.php');
		endif;

	}

	function module_admin_scripts() {
		// Add our script to the queue
		wp_enqueue_script('k2widgets');
	}

	function module_admin_head() {
		?>
			<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/widgets.css" />
			<link type="text/css" rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/humanmsg.css" />

			<script type="text/javascript">
			//<![CDATA[
				var sbm_baseUrl = "<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php";
			//]]>
			</script>
		<?php
	}

	function set_error_text($text) {
		global $k2sbm_error_text;

		$k2sbm_error_text = $text;
	}

	function module_display_filter($params) {
		global $wp_registered_widgets, $k2sbm_active_modules;

		$module_id = $params[0]['widget_id'];
		$module = $k2sbm_active_modules[$module_id];

		if ( isset($module) ) {
			if ( ! $module->canDisplay() )
				$wp_registered_widgets[$module_id]['callback'] = false;

			if ( ! $module->output['show_title'] ) {
				$params[0]['before_title'] = '';
				$params[0]['title'] = '';
				$params[0]['after_title'] = '';
			}
		}

		return $params;
	}

	function load_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		if ( empty($k2sbm_active_modules) ) {
			$k2sbm_active_modules = get_option('k2sbm_modules');

			if ( empty($k2sbm_active_modules) ) $k2sbm_active_modules = array();
		}
	}

	function sync_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		K2SBM::load_modules();
		$sidebars_widgets = wp_get_sidebars_widgets();

		$sync_modules = array();
		foreach ( (array) $sidebars_widgets as $sidebar_id => $widgets ) {
			$sync_modules[$sidebar_id] = array();

			foreach ( (array) $widgets as $widget_id ) {
				if ( isset( $k2sbm_active_modules[$widget_id] ) ) {
					$sync_modules[$widget_id] = $k2sbm_active_modules[$widget_id];
				} else {
					$sync_modules[$widget_id] = new k2sbmModule($widget_id);
				}
			}
			
		}
		$k2sbm_active_modules = $sync_modules;
		
		K2SBM::save_modules();
	}

	function save_modules() {
		global $k2sbm_active_modules, $k2sbm_disabled_modules;

		update_option('k2sbm_modules', $k2sbm_active_modules);
	}

	function add_module($sidebar_id, $widget_id, $is_multi = false) {
		global $wp_registered_widgets, $wp_registered_widget_controls, $k2sbm_active_modules, $k2sbm_disabled_modules;

		if ( empty($sidebar_id) or empty($widget_id) ) {
			return 'add_module: ' . __('Missing required parameters.', 'k2_domain');
		}

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( ! isset($sidebars_widgets[$sidebar_id] ) )
			$sidebars_widgets[$sidebar_id] = array();

		$base_widget = $wp_registered_widgets[$widget_id];
		$base_control = $wp_registered_widget_controls[$widget_id];

		if ( !$is_multi and !isset($base_widget) )
			return 'add_module: ' . sprintf( __('%s is not a registered widget.', 'k2_domain'), $widget_id );

		// Handle multi-widgets
		if ( 'true' == $is_multi ) {
			if ( !is_callable( $base_control['callback'] ) )
				return 'add_module: ' . sprintf( __('Unable to add multi-widget: %s', 'k2_domain'), $widget_id );

			$id_base = $wp_registered_widget_controls[$widget_id]['id_base'];
			$number = time() - 1199145600; // Jan 1, 2008

			$new_widget = $wp_registered_widgets[$widget_id];

			$new_widget['params'][0]['number'] = $number;
			$widget_id = "$id_base-$number";
			$new_widget['id'] = $widget_id;

			$wp_registered_widgets[$widget_id] = $new_widget;
		}

		$sidebars_widgets[$sidebar_id][] = $widget_id;
		wp_set_sidebars_widgets($sidebars_widgets);
		$k2sbm_active_modules[$widget_id] = new k2sbmModule($widget_id);
		K2SBM::save_modules();

		if ( 'true' == $is_multi ) {
			$widget_ids = array();
			foreach ( $sidebars_widgets[$sidebar_id] as $_widget_id ) {
				if ( $base_widget['callback'] == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
					$widget_ids[] = $_widget_id;
				}
			}

			// hack
			$_POST['widget-id'] = $widget_ids;
			$_POST['sidebar'] = $sidebar_id;

			ob_start();
			call_user_func_array( $base_control['callback'], $number );
			ob_end_clean();
		}

		return true;
	}

	function update_module($sidebar_id, $module_id) {
		global $k2sbm_disabled_modules, $k2sbm_active_modules;

		// Widgets hack
		K2SBM::multi_widget_hack( $module_id );

		// Start the capture
		ob_start();

		if ($sidebar_id == 'disabled') {
			$k2sbm_disabled_modules[$module_id]->displayControl();
		} else {
			$k2sbm_active_modules[$module_id]->displayControl();
		}

		// Junk the capture
		ob_end_clean();

		$k2sbm_active_modules[$module_id]->showBadges();
		K2SBM::save_modules();
	}

	function multi_widget_hack($widget_id) {
		global $wp_registered_widgets;

		$my_widget = $wp_registered_widgets[$widget_id];
		$ids = array();
		if ( isset( $my_widget['params'][0]['number'] ) ) {
			foreach ($wp_registered_widgets as $widget) {
				if ( $widget['callback'] == $my_widget['callback'] )
					$ids[] = $widget['id'];
			}

			$_POST['widget-id'] = $ids;
		}
	}

	function remove_module($sidebar_id, $module_id) {
		if ( empty($sidebar_id) or empty($module_id) )
			return 'remove_module: ' . __('Missing required parameters.', 'k2_domain');

		$sidebars_widgets = wp_get_sidebars_widgets();
		$sidebars_widgets[$sidebar_id] = array_diff( $sidebars_widgets[$sidebar_id], array($module_id) );
		wp_set_sidebars_widgets( $sidebars_widgets );

		return true;
	}

	function reorder_sidebar( $ordering ) {
		if ( empty($ordering) )
			return 'reorder_sidebar: ' . __('Missing required parameters.', 'k2_domain');

		parse_str($ordering, $sidebars_widgets);
		wp_set_sidebars_widgets( $sidebars_widgets );

		return true;
	}

	function widget_uasort($a, $b) {
	    if ($a['name'] == $b['name']) {
	        return 0;
	    }

	    return ($a['name'] < $b['name']) ? -1 : 1;
	}

	function list_available_widgets() {
		global $wp_registered_widgets, $wp_registered_widget_controls;

		uasort( $wp_registered_widgets, array('K2SBM', 'widget_uasort') );

		$already_shown = array();
		foreach ( $wp_registered_widgets as $widget ) {
			$is_multi = isset( $widget['params'][0]['number'] );

			if ( in_array( $widget['callback'], $already_shown ) )
				continue;

			if ( is_active_widget( $widget['callback'], $widget['id'] ) and !$is_multi )
				continue;

			$widget_id = $widget['id'];
			if ( $is_multi ) {
				$num = (int) array_pop( $ids = explode( '-', $widget['id'] ) );
				$id_base = $wp_registered_widget_controls[$widget['id']]['id_base'];

				while ( isset($wp_registered_widgets["$id_base-$num"]) )
					$num++;

				$widget_id = "$id_base-$num";

				$already_shown[] = $widget['callback'];
			}
			?>

			<li id="<?php echo attribute_escape( $widget_id ); ?>" class="new-widget <?php if ( $is_multi ) { echo 'multi-widget'; } else { echo 'widget'; } ?>">
				<div class="modulewrapper">
					<span class="name"><?php echo $widget['name']; ?></span>
					<span class="desc"><?php echo wp_widget_description($widget['id']); ?></span>
					<span class="display">
						<img src="<?php echo get_template_directory_uri(); ?>/images/house.png" alt="<?php _e('Homepage','k2_domain'); ?>" />
						<img src="<?php echo get_template_directory_uri(); ?>/images/calendar.png" alt=""/>
						<img src="<?php echo get_template_directory_uri(); ?>/images/pencil.png" alt="" />
						<img src="<?php echo get_template_directory_uri(); ?>/images/zoom.png" alt="" />
						<img src="<?php echo get_template_directory_uri(); ?>/images/bug.png" />
						<img src="<?php echo get_template_directory_uri(); ?>/images/page.png" />
					</span>
					<a href="#" class="optionslink"></a>
					<a href="#" class="deletelink"></a>
				</div>
			</li>
		<?php 
		}
	}

	function list_sidebar_widgets( $sidebar ) {
		add_filter( 'dynamic_sidebar_params', array('K2SBM', 'list_sidebar_widgets_filter') );

		dynamic_sidebar( $sidebar );
	}

	function list_sidebar_widgets_filter( $params ) {
		global $wp_registered_widgets;

		$widget_id = $params[0]['widget_id'];
		$is_multi = isset( $wp_registered_widgets[$widget_id]['params'][0]['number'] );

		$params[0]['before_widget'] = '<li id="' . $widget_id . '" class="' . ( $is_multi ? 'multi-widget' : 'widget' ) . '">';
		$params[0]['after_widget'] = '</li>';
		$params[0]['before_title'] = '%BEG_OF_TITLE%';
		$params[0]['after_title'] = '%END_OF_TITLE%';
		if ( is_callable( $wp_registered_widgets[$widget_id]['callback'] ) ) {
			$wp_registered_widgets[$widget_id]['_callback'] = $wp_registered_widgets[$widget_id]['callback'];
			$wp_registered_widgets[$widget_id]['callback'] = array('K2SBM', 'list_sidebar_widgets_callback');
		}

		return $params;
	}

	function list_sidebar_widgets_callback( $sidebar_args ) {
		global $k2sbm_active_modules, $wp_registered_widgets, $wp_registered_widget_controls;

		$widget_id = $sidebar_args['widget_id'];
		$sidebar_id = isset($sidebar_args['id']) ? $sidebar_args['id'] : false;

		$control = isset($wp_registered_widget_controls[$widget_id]) ? $wp_registered_widget_controls[$widget_id] : 0;
		$widget  = $wp_registered_widgets[$widget_id];

		ob_start();
		$args = func_get_args();
		call_user_func_array( $widget['_callback'], $args );
		$widget_title = ob_get_clean();
		$widget_title = K2SBM::widget_control_ob_filter( $widget_title );

		if ( $widget_title && $widget_title != $sidebar_args['widget_name'] )
			$widget_title = sprintf( _c('%1$s: %2$s|1: widget name, 2: widget title' ), $sidebar_args['widget_name'], $widget_title );
		else
			$widget_title = wp_specialchars( strip_tags( $sidebar_args['widget_name'] ) );

		echo $sidebar_args['before_widget'];
?>
		<div class="modulewrapper">
			<span class="name"><?php echo $widget_title; ?></span>
			<span class="desc"><?php echo wp_widget_description($widget_id); ?></span>
			<span class="display"><?php $k2sbm_active_modules[$widget_id]->showBadges(); ?></span>
			<a href="#" class="optionslink" alt="<?php _e('Module Options', 'k2_domain') ?>"></a>
			<a href="#" class="deletelink" alt="<?php _e('Delete Module', 'k2_domain') ?>"></a>
		</div>
<?
		echo $sidebar_args['after_widget'];
	}

	function widget_control_ob_filter( $string ) {
		if ( false === $beg = strpos( $string, '%BEG_OF_TITLE%' ) )
			return '';
		if ( false === $end = strpos( $string, '%END_OF_TITLE%' ) )
			return '';
		$string = substr( $string, $beg + 14 , $end - $beg - 14);
		$string = str_replace( '&nbsp;', ' ', $string );
		return trim( wp_specialchars( strip_tags( $string ) ) );
	}
}

class k2sbmModule {
	var $id;

	var $display;
	var $output;

	function k2sbmModule($id) {
		// Set the generic data from the parameters
		$this->id = $id;

		$this->display = array(
			'home' => true,
			'archives' => true,
			'post' => true,
			'search' => true,
			'pages' => true,
			'page_id' => array('show' => 'show', 'ids' => false),
			'error' => true
		);
		$this->output = array('show_title' => true);
	}

	function displayControl() {
		global $wp_registered_widgets, $wp_registered_widget_controls, $k2sbm_current_module;

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
			if(!isset($_POST['display']['page_id'])) {
				$this->display['page_id'] = $old_page_id;
			}
		}

		$k2sbm_current_module = $this;

		// Get the base module details
		$base_widget = $wp_registered_widgets[$this->id];
		$base_control = $wp_registered_widget_controls[$this->id];
?>
		<div id="optionstab-content" class="tabcontent">
			<p id="name-container">
				<input id="output-show-title" name="output[show_title]" type="checkbox"<?php if ($this->output['show_title']) { ?> checked="checked"<?php } ?> />
				<label for="output-show-title" class="showtitlelabel"><?php _e('Display Title', 'k2_domain'); ?></label>
			</p>

		<?php
			if ( is_callable($base_control['callback']) ) {
				// Call the control callback
				call_user_func_array( $base_control['callback'], $base_control['params'] );
			}
		?>
		</div><!-- #optionstab-content -->


		<div id="displaytab-content" class="tabcontent">
		<p>
		<fieldset>
			<div><input id="display-home" name="display[home]" type="checkbox" <?php checked($this->display['home'], true); ?> /> <label for="display-home"><img src="<?php echo get_template_directory_uri() ?>/images/house.png" /><?php _e('Homepage', 'k2_domain'); ?></label></div>

			<div><input id="display-archives" name="display[archives]" type="checkbox" <?php checked($this->display['archives'], true); ?> /> <label for="display-archives"><img src="<?php echo get_template_directory_uri() ?>/images/calendar.png" /><?php _e('Archives', 'k2_domain'); ?></label></div>

			<div><input id="display-post" name="display[post]" type="checkbox" <?php checked($this->display['post'], true); ?> /> <label for="display-post"><img src="<?php echo get_template_directory_uri() ?>/images/pencil.png" /><?php _e('Single posts', 'k2_domain'); ?></label></div>

			<div><input id="display-search" name="display[search]" type="checkbox" <?php checked($this->display['search'], true); ?> /> <label for="display-search"><img src="<?php echo get_template_directory_uri() ?>/images/zoom.png" /><?php _e('Search results', 'k2_domain'); ?></label></div>

			<div><input id="display-error" name="display[error]" type="checkbox" <?php checked($this->display['error'], true); ?> /> <label for="display-error"><img src="<?php echo get_template_directory_uri() ?>/images/bug.png" /><?php _e('Error page', 'k2_domain'); ?></label></div>

			<div><input id="display-pages" name="display[pages]" type="checkbox" <?php checked($this->display['pages'], true); ?> /> <label for="display-pages"><img src="<?php echo get_template_directory_uri() ?>/images/page.png" /><?php _e('Static pages', 'k2_domain'); ?></label></div>

			<div id="specific-pages" class="toggle-item">
				<?php
					global $wpdb, $post;

					$pages = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE (post_status = 'static' OR post_type = 'page') ORDER BY menu_order");
				?>

				<?php if($pages): ?>
					<ul id="page-ids" class="checkbox-list">
					<?php foreach($pages as $post): ?>
						<li><input id="display-page-id-ids-<?php echo($post->ID); ?>" name="display[page_id][ids][<?php echo($post->ID); ?>]" type="checkbox" <?php checked($this->display['page_id']['ids'][$post->ID], true); ?> /> <label for="display-page-id-ids-<?php echo($post->ID); ?>"><?php the_title(); ?></label></li>
					<?php endforeach; ?>
					</ul>

					<div class="tools">
						<p class="checkoruncheck"><a id="check-page-ids" href="#"><?php _e('Check all', 'k2_domain'); ?></a> | <a id="uncheck-page-ids" href="#"><?php _e('Uncheck all', 'k2_domain'); ?></a></p>

						<p class="showorhide">
							<span class="soh-show"><input id="display-page-id-show-show" name="display[page_id][show]" type="radio" value="show" <?php checked($this->display['page_id']['show'], 'show'); ?> /> <label for="display-page-id-show-show"><?php _e('Show on checked', 'k2_domain'); ?></label></span>
							<span class="soh-hide"<input id="display-page-id-show-hide" name="display[page_id][show]" type="radio" value="hide" <?php checked($this->display['page_id']['show'], 'hide'); ?> /> <label for="display-page-id-show-hide"><?php _e('Hide on checked', 'k2_domain'); ?></label></span>
						</p>
					</div>
				<?php else: ?>
					<?php _e('No pages', 'k2_domain'); ?>
				<?php endif; ?>
			</div>
		</fieldset>
		</p>
		</div><!-- #displaytab-content -->

<?php
		$k2sbm_current_module = false;
	}

	function canDisplay() {
		global $post;

		return ( $this->display['home'] and is_home() )
			or ( $this->display['archives'] and ( is_archive() or (function_exists('is_tag') and is_tag())) )
			or ( $this->display['post'] and is_single() )
			or ( $this->display['search'] and is_search() )
			or ( $this->display['pages'] and is_page() and (
				   !$this->display['page_id']['ids']
				or ($this->display['page_id']['show'] == 'show' and $this->display['page_id']['ids'][$post->ID])
				or ($this->display['page_id']['show'] == 'hide' and !$this->display['page_id']['ids'][$post->ID]))
			)
			or ($this->display['error'] and (is_404() or !($post or have_posts())));
	}

	function showBadges() {
		if ( $this->display['home'] )
			echo '<img src="' . get_template_directory_uri() .'/images/house.png" alt="' . __('Homepage','k2_domain') . '" />';

		if ( $this->display['archives'] )
			echo '<img src="' . get_template_directory_uri() .'/images/calendar.png" alt="' . __('Archives', 'k2_domain') . '" />';

		if ( $this->display['post'] )
			echo '<img src="' . get_template_directory_uri() .'/images/pencil.png" alt="' . __('Single posts', 'k2_domain') . '" />';

		if ( $this->display['search'] )
			echo '<img src="' . get_template_directory_uri() .'/images/zoom.png" alt="' . __('Search results', 'k2_domain') . '" />';

		if ( $this->display['error'] )
			echo '<img src="' . get_template_directory_uri() .'/images/bug.png" alt="' . __('Error page', 'k2_domain') . '" />';

		if ( $this->display['pages'] )
			echo '<img src="' . get_template_directory_uri() .'/images/page.png" alt="' . __('Static pages', 'k2_domain') . '" />';
	}
}

add_action( 'k2_install', array('K2SBM', 'install') );
add_action( 'k2_upgrade', array('K2SBM', 'upgrade') );
add_action( 'k2_uninstall', array('K2SBM', 'uninstall') );

if ( get_option('k2sidebarmanager') ) {
	add_action( 'k2_init', array('K2SBM', 'init') );
	add_action( 'sidebar_admin_setup', array('K2SBM', 'sync_modules') );
	add_action( 'wp_ajax_k2sbm', array('K2SBM', 'direct_bootstrap') );

	if ( !is_admin() ) {
		add_filter( 'dynamic_sidebar_params', array('K2SBM', 'module_display_filter') );
	}
}
