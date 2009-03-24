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
		<h3><?php _e('Layout', 'k2_domain'); ?></h3>

		<p class="description">Drag the blue handles to resize the column widths. Drag the black handles to resize each individual sidebar. Drag <em>New Sidebar</em> and drop into a sidebar zone to add a new sidebar.</p>

		<div id="layout-design">
			<div id="page" class="page-block">
				<h4>Page</h4>
				<input type="hidden" id="page-width" name="k2[layout][page-width]" value="16" />
				<span class="grid-px">960 px</span>

				<div id="left-sidebars" class="sidebar-wrap">
					<h4>Left</h4>
					<input type="hidden" id="left-sidebars-width" name="k2[layout][left-sidebars-width]" value="4" />
					<span class="grid-px">220 px</span>

					<ul class="sidebar-list sidebar-sortable">
						<li id="sidebar-1">
							<h4>Sidebar 1</h4>
							<a href="#" class="remove-sidebar"><span>Remove</span></a>
							<input type="hidden" id="sidebar-1-width" value="4" />
							<div class="sidebar-block">
								<span class="grid-px">220 px</span>
							</div>
						</li>
					</ul>
				</div>
				<div id="primary" class="primary-block">
					<h4>Primary</h4>
					<input type="hidden" id="primary-width" value="8" />
					<span class="grid-px">460 px</span>
				</div>
				<div id="right-sidebars" class="sidebar-wrap">
					<h4>Right</h4>
					<input type="hidden" id="right-sidebars-width" value="4" />
					<span class="grid-px">220 px</span>

					<ul class="sidebar-list sidebar-sortable">
						<li id="sidebar-2">
							<h4>Sidebar 2</h4>
							<a href="#" class="remove-sidebar"><span>Remove</span></a>
							<input type="hidden" id="sidebar-2-width" value="4" />
							<div class="sidebar-block">
								<span class="grid-px">220 px</span>
							</div>
						</li>
					</ul>
				</div>
			
				<div id="bottombars" class="sidebar-wrap">
					<h4>Bottom</h4>
					<ul class="sidebar-list sidebar-sortable">
					</ul>
				</div>
			</div>

			<div id="layout-controls">
				<ul id="new-sidebar-list" class="sidebar-list">
					<li id="new-sidebar">
						<h4>New Sidebar</h4>
						<a href="#" class="remove-sidebar"><span>Remove</span></a>
						<input type="hidden" id="new-sidebar-width" value="4" />
						<div class="sidebar-block">
							<span class="grid-px">220 px</span>
						</div>
					</li>
				</ul>
			</div>
		</div>

	</div><!-- .container -->

<?php
}


function k2_960_update_options() {
	//var_dump($_POST['k2']);
}


function k2_960_admin_print_scripts() {
	wp_enqueue_script('jquery-ui-sortable');
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
			var numSidebars = 2;

			jQuery('#page').resizable({
				distance: 10,
				grid: [30, 50],
				handles: 'e',
				maxWidth: 480,
				minWidth: 170,
				resize: function(event, ui) {
					thisWidth = jQuery(this).width();
					/*
					leftWidth = jQuery('#left-sidebars').width();
					primaryWidth = jQuery('#primary').width();
					rightWidth = jQuery('#right-sidebars').width();

					jQuery(this).resizable('option', 'minWidth', leftWidth + primaryWidth);
					*/
					jQuery('#page-width').val( thisWidth / 30 );
					jQuery('#page > .grid-px').text( (thisWidth * 2) + ' px');
				}
			});

			function calcMaxWidth() {
			/*var largest_left_sb, largest_right_sb = 0;
			jQuery('#left-sidebars > .sidebar-block').each(function() {
				var width = jQuery(this).width();
				if ( width > largest_sidebar_width ) {
					largest_sidebar_width = width;
				}
			});

			jQuery('#left-sidebars > .sidebar-block').each(function() {
				var width = jQuery(this).width();
				if ( width > largest_sidebar_width ) {
					largest_sidebar_width = width;
				}
			});*/
			}

			function initButtons() {
				jQuery('.remove-sidebar').unbind().click(function(){
					jQuery(this).parent().remove();
					return false;
				});
			}

			jQuery('#primary').resizable({
				distance: 10,
				grid: [30, 50],
				handles: 'w, e',
				containment: 'parent',
				minWidth: 110,
				maxWidth: 470,
				resize: function(event, ui) {
					var $this = jQuery(this),
						$leftsb = jQuery('#left-sidebars'),
						$rightsb = jQuery('#right-sidebars'),
						thisPosition = $this.position(),
						thisWidth = $this.width();
					
					if ( thisPosition.left == 5 ) {
						$leftsb.width(0);
						jQuery('#left-sidebars .sidebar-list').width(0);
						jQuery('#left-sidebars .sidebar-block').resizable('option', 'maxWidth', 0);
					} else {
						$leftsb.width( thisPosition.left - 15 );
						jQuery('#left-sidebars .sidebar-list').width( $leftsb.width() + 10 );
						jQuery('#left-sidebars .sidebar-block').resizable('option', 'maxWidth', $leftsb.width());
					}

					if ( thisWidth + $leftsb.width() >= 460) {
						$rightsb.width(0).css('left', 485);
						jQuery('#right-sidebars .sidebar-list').width(0);
						jQuery('#right-sidebars .sidebar-block').resizable('option', 'maxWidth', 0);
					} else if ( $leftsb.width() == 0 ) {
						$rightsb.width( 460 - thisWidth ).css('left', thisWidth + 15);
						jQuery('#right-sidebars .sidebar-list').width( $rightsb.width() + 10 );
						jQuery('#right-sidebars .sidebar-block').resizable('option', 'maxWidth', $rightsb.width());
					}
					else {
						$rightsb.width( 450 - thisWidth - $leftsb.width() ).css('left', thisWidth + $leftsb.width() + 25);
						jQuery('#right-sidebars .sidebar-list').width( $rightsb.width() + 10 );
						jQuery('#right-sidebars .sidebar-block').resizable('option', 'maxWidth', $rightsb.width());
					}

					calcWidth(this);
					calcWidth('#left-sidebars');
					calcWidth('#right-sidebars');
				},
				stop: function(event, ui) {
				}
			});

			function calcWidth(ele) {
				$ele = jQuery(ele);

				eleUnits = ($ele.width() + 10) / 30;
				elePx = (eleUnits * 60) - 20;

				jQuery( '#' + $ele.attr('id') + '-width' ).val( eleUnits );
				$ele.children('.grid-px').text( ($ele.width() * 2) + ' px');
			}

			function initResizables() {
				jQuery('#page .sidebar-block').resizable('destroy').resizable({
					distance: 10,
					grid: [30, 50],
					handles: 'e',
					containment: '#left-sidebars',
					minWidth: 40,
					maxWidth: 470,
					resize: function(e, ui) {
						jQuery(this).parent().width( jQuery(this).width() );
						calcWidth(this);
					},
					stop: function(e, ui) {
						var units = ( jQuery(this).width() + 10 ) / 30;
						jQuery( '#' + jQuery(this).parent().attr('id') + '-width' ).val( units );
					}
				});
			}

			jQuery('.sidebar-sortable').sortable({
				connectWith: jQuery('.sidebar-sortable'),
				cursor: 'move',
				forcePlaceholderSize: true,
				handle: 'h4',
				placeholder: 'sidebar-placeholder',
				tolerance: 'pointer',
				over: function(e, ui) {
					jQuery(this).parent().addClass('active');
				},

				out: function(e, ui) {
					jQuery(this).parent().removeClass('active');
				},
				
				stop: function(e, ui) {
					if ( ui.item.is('#new-sidebar') ) {
						++numSidebars;
						ui.item.removeClass('ui-draggable').attr('id', 'sidebar-' + numSidebars);
						ui.item.children('h4').text('Sidebar ' + numSidebars);
						ui.item.children('input').attr('id', 'sidebar-' + numSidebars + '-width');
						ui.item.draggable('destroy');

						initButtons();
					}
				}
			});

			jQuery('#new-sidebar').draggable({
				connectToSortable: '.sidebar-sortable',
				cursor: 'move',
				handle: 'h4',
				opacity: 0.8,
				helper: function() {
					$clone = jQuery(this).clone();
					$clone.children('h4').text('Sidebar ' + (numSidebars + 1));
					
					return $clone;
				}
			});

			initButtons();
			initResizables();
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
