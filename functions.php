<?php
define('HEADER_IMAGE_WIDTH', 1000);

function k2_960_init() {
	if ( false === get_option('k2_960_primarywidth') ) {
		add_option('k2_960_primarywidth', '8', 'Width of #primary');
		add_option('k2_960_sidebarwidths', '4 4', 'Width of #sidebar-1');
		add_option('k2_960_bottombarwidths', '4 4 4 4', 'Width of #sidebar-1');
	}
}


function k2_register_sidebars() {
	if ( $sidebars = get_option('k2_960_sidebarwidths') ) {
		
		register_sidebars( count( explode( ' ', $sidebars ) ),
			array(
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h4>',
				'after_title' => '</h4>'
			)
		);
	}


	if ( $bottombars = get_option('k2_960_bottombarwidths') ) {
		$num_bottombars = count( explode( ' ', $bottombars ) );
		for ($i = 1; $i <= $num_bottombars; $i++) {
			register_sidebar(
				array(
					'id' => "bottombar-$i",
					'name' => sprintf(__('Bottombar %d', 'k2_domain'), $i),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget' => '</div>',
					'before_title' => '<h4>',
					'after_title' => '</h4>'
				)
			);
		}
	}
}


function k2_960_display_options() {
?>

	<div class="container">
		<h3><?php _e('K2 960 Options', 'k2_domain'); ?></h3>

		<p class="description">Enter values between 1 and 16. Sidebar Widths can have multiple values separated by a space; each value represents a sidebar. For example: "8 4 4" = 3 sidebars: first sidebar is 8 units wide, second is 4 units wide and third is also 4 units wide.</p>
		<p class="description"><strong>Warning:</strong> Before removing a sidebar, be sure to delete the widgets in the sidebar.</p>

		<div id="layout" class="page-block">
			<div class="page-header">
				<h4>Page</h4>
				(<span class="grid-units">16</span> units | <span class="grid-px">960</span> px)
			</div>

			<div id="primary" class="primary-block">
				<h4>Primary</h4>
				(<span class="grid-units">8</span> units)
			</div>
			<div id="sidebars">
				<div id="sidebar-1" class="sidebar-block">
					<h4>Sidebar 1</h4>
					(<span class="grid-units">4</span> units)
				</div>
				<div id="sidebar-2" class="sidebar-block">
					<h4>Sidebar 2</h4>
					(<span class="grid-units">4</span> units)
				</div>
			</div>
		</div>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="k2-960-primary-width"><?php _e('Content Width:', 'k2_domain'); ?></label>
					</th>
					<td>
						<input id="k2-960-primary-width" name="k2[primarywidth]" type="text" value="<?php echo attribute_escape( get_option('k2_960_primarywidth') ); ?>" />(Max: 16)
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="k2-960-sidebar-widths"><?php _e('Sidebar Widths:', 'k2_domain'); ?></label>
					</th>
					<td>
						<input id="k2-960-sidebar-widths" name="k2[sidebarwidths]" type="text" value="<?php echo attribute_escape( get_option('k2_960_sidebarwidths') ); ?>" />(Max: 16 - Content Width)
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="k2-960-bottombar-widths"><?php _e('Bottombar Widths:', 'k2_domain'); ?></label>
					</th>
					<td>
						<input id="k2-960-bottombar-widths" name="k2[bottombarwidths]" type="text" value="<?php echo attribute_escape( get_option('k2_960_bottombarwidths') ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</div><!-- .container -->

<?php
}


function k2_960_update_options() {
	if ( isset($_POST['k2']['primarywidth']) ) {
		update_option( 'k2_960_primarywidth', stripslashes($_POST['k2']['primarywidth']) );
	}

	if ( isset($_POST['k2']['sidebarwidths']) ) {
		update_option( 'k2_960_sidebarwidths', stripslashes($_POST['k2']['sidebarwidths']) );
	}

	if ( isset($_POST['k2']['bottombarwidths']) ) {
		update_option( 'k2_960_bottombarwidths', stripslashes($_POST['k2']['bottombarwidths']) );
	}
}


function k2_960_admin_print_scripts() {
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-resizable');
}

function k2_960_admin_head() {
?>

	<!-- <link type="text/css" href="http://jqueryui.com/latest/themes/base/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="http://jqueryui.com/latest/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.core.js"></script>
	<script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.resizable.js"></script> -->

	<link type="text/css" rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/options.css" />

	<script type="text/javascript" charset="utf-8">
	//<![CDATA[
		jQuery(document).ready(function(){
			/*
			jQuery(".layout-block").draggable({
				grid: [30, 50]
			});
			*/

			jQuery("#layout").resizable({
				distance: 10,
				grid: [30, 50],
				handles: 'e',
				maxWidth: 470,
				minWidth: 170,
				resize: function(event, ui) {
					jQuery(".page-header .grid-units").text( (jQuery(this).width() + 10) / 30 );
					jQuery(".page-header .grid-px").text( jQuery(this).width() * 2 );
					jQuery("#sidebars").width( jQuery(this).width() - jQuery("#primary").width() - 10 ).css("left", jQuery("#primary").width() + 15);
				}
			});

			jQuery("#primary").resizable({
				distance: 10,
				grid: [30, 50],
				handles: 'e',
				containment: 'parent',
				minWidth: 110,
				maxWidth: 470,
				resize: function(event, ui) {
					var largest_sidebar_width = 0;
					jQuery("#sidebars > .sidebar-block").each(function() {
						var width = jQuery(this).width();
						if ( width > largest_sidebar_width ) {
							largest_sidebar_width = width;
						}
					});
					jQuery('#primary').resizable('option', 'maxWidth', 470 - largest_sidebar_width);

					jQuery("#sidebars").width( 460 - jQuery(this).width() ).css("left", jQuery(this).width() + 15);
					jQuery("#primary .grid-units").text( (jQuery(this).width() + 10) / 30 );
				},
				stop: function(event, ui) {
					console.log( jQuery('#primary').resizable('option', 'maxWidth') );
				}
			});

			jQuery(".sidebar-block").resizable({
				distance: 10,
				grid: [30, 50],
				handles: 'e',
				containment: 'parent',
				minWidth: 50,
				maxWidth: 470,
				resize: function(event, ui) {
					jQuery(this).children("span").text( (jQuery(this).width() + 10) / 30 );
				}
			});
		});
	//]]>
	</script>
<?php
}


add_action('k2_init', 'k2_960_init');
add_action('k2_display_options', 'k2_960_display_options');
add_action('k2_update_options', 'k2_960_update_options');
add_action('admin_print_scripts-appearance_page_k2-options', 'k2_960_admin_print_scripts');
add_action('admin_head-appearance_page_k2-options', 'k2_960_admin_head');
