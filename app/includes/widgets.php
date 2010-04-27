<?php
/**
 * K2 Widgets.
 *
 * Specific widgets for K2
 *
 * @package K2
 */

// Prevent users from directly loading this include file
defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );


class K2_Widget_About extends WP_Widget {
	function K2_Widget_About() {
		$widget_ops = array( 'classname' => 'k2-widget-about', 'description' => __('Message about the current area and optional front-page message', 'k2_domain') );
		$this->WP_Widget('k2-about', __('K2 About', 'k2_domain'), $widget_ops);
	}

	function widget($args, $instance) {
		extract($args);

		$title = empty($instance['title']) ? __('About', 'k2_domain') : apply_filters('widget_title', $instance['title']);
		$message = stripslashes( $instance['message'] );

		if ( is_home() or is_front_page() or is_page() ) {
			if ( ! empty($message) ) {
				echo $before_widget;
				if ( $title != '<none>' )
					echo $before_title . $title . $after_title;

				echo '<div>' . $message . '</div>' . $after_widget;
			}
		} elseif ( ! is_singular() ) {
			echo $before_widget;
			if ( $title != '<none>' )
				echo $before_title . $title . $after_title; ?>

		<?php if ( is_category() ): // Category Archive ?>
			<p><?php printf( __('The %1$s archives for the %2$s category.', 'k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>',
						single_cat_title('', false)
					); ?></p>

		<?php elseif ( is_day() ): // Day Archive ?>
			<p><?php printf( __('The %1$s archives for %2$s.', 'k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>',
						get_the_time( __('l, F jS, Y', 'k2_domain') )
					); ?></p>

		<?php elseif ( is_month() ): // Monthly Archive ?>
			<p><?php printf( __('The %1$s archives for %2$s.', 'k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>',
						get_the_time( __('F, Y', 'k2_domain') )
					); ?></p>

		<?php elseif ( is_year() ): // Yearly Archive ?>
			<p><?php printf( __('The %1$s archives for %2$s.', 'k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>',
						get_the_time('Y')
					); ?></p>

		<?php elseif ( is_search() ): // Search ?>
			<p><?php printf( __('You searched the %1$s archives for <strong>%2$s</strong>.', 'k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>',
						esc_attr( get_search_query() )
					); ?></p>

		<?php elseif ( is_author() ): // Author Archive ?>
			<p><?php printf( __('Archive for <strong>%s</strong>.', 'k2_domain'), get_the_author() ); ?></p>
			<p><?php the_author_description(); ?></p>

		<?php elseif ( is_tag() ): // Tag Archive ?>
			<p><?php printf( __('The %1$s archives for the <strong>%2$s</strong> tag.','k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>',
						get_query_var('tag')
					); ?></p>

		<?php elseif ( is_paged() ): // Paged Archive ?>
			<p><?php printf( __('The %s weblog archives.','k2_domain'),
						'<a href="' . get_option('siteurl') . '">' . get_bloginfo('name') . '</a>'
					); ?></p>

		<?php endif; ?>
	<?php
			echo $after_widget;
		}
	}

	function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('About', 'k2_domain'), 'message' => '' ) );
		$title = esc_attr( $instance['title'] );
		$message = format_to_edit( $instance['message'] );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'k2_domain'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('About Text:', 'k2_domain'); ?></label>
				<textarea id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" rows="6" cols="30" class="widefat"><?php echo $message; ?></textarea>
				<small><?php _e('Enter a blurb about yourself here, and it will show up on the front page. Deleting the content disables the about blurb.','k2_domain'); ?></small>
			</p>
		<?php 
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['message'] =  $new_instance['message'];
		else
			$instance['message'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['message']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}
}


class K2_Widget_Asides extends WP_Widget {
	function K2_Widget_Asides() {
		$widget_ops = array( 'classname' => 'k2-widget-asides', 'description' => __('Asides on your sidebar', 'k2_domain') );
		$this->WP_Widget('k2-asides', __('K2 Asides', 'k2_domain'), $widget_ops);
	}

	function widget($args, $instance) {
		extract($args);

		$k2asidescategory = get_option('k2asidescategory');

		if ( $k2asidescategory != '0') {
			$title = empty($instance['title']) ? apply_filters('single_cat_title', get_the_category_by_ID($k2asidescategory)) : apply_filters('widget_title', $instance['title']);

			$asides = new WP_Query( array( 'cat' => $k2asidescategory, 'showposts' => $instance['number'], 'what_to_show' => 'posts', 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1 ) );

			if ( $asides->have_posts() ) {
				echo $before_widget;

				if ( $title != '<none>' )
					echo $before_title . $title . $after_title;
				?>
				<div>
				<?php while ( $asides->have_posts() ): $asides->the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<span>&raquo;&nbsp;</span><?php the_content( __('(more)', 'k2_domain') ); ?>
						<?php /* Edit Link */ edit_post_link( __('Edit','k2_domain'), '<span class="entry-edit">', '</span>' ); ?>
					</div>
				<?php endwhile; ?>
				</div>
<?php
				echo $after_widget;
			}
			wp_reset_query();  // Restore global post data stomped by the_post().
		}
	}

	function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'number' => 5 ) );
		$title = esc_attr( $instance['title'] );
		$number = (int) $instance['number'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'k2_domain'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of asides to show:', 'k2_domain'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $number; ?>" size="2" />
		</p>
		<?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		return $instance;
	}
}


/**
 * Assigns a default set of widgets
 */
function k2_default_widgets() {
	$sidebars_widgets = wp_get_widget_defaults();

	if ( isset($sidebars_widgets['sidebar-1']) and isset($sidebars_widgets['sidebar-2']) ) {
		$sidebars_widgets['sidebar-1'] = $sidebars_widgets['sidebar-2'] = array();

		k2_add_widget($sidebars_widgets['sidebar-1'], 'search');
		k2_add_widget($sidebars_widgets['sidebar-1'], 'k2-about');
		k2_add_widget($sidebars_widgets['sidebar-1'], 'recent-posts');
		k2_add_widget($sidebars_widgets['sidebar-1'], 'recent-comments');
		k2_add_widget($sidebars_widgets['sidebar-2'], 'archives');
		k2_add_widget($sidebars_widgets['sidebar-2'], 'tag_cloud');
		k2_add_widget($sidebars_widgets['sidebar-2'], 'links');

		wp_set_sidebars_widgets( $sidebars_widgets );
	}
}

function k2_add_widget(&$sidebar, $id_base, $settings = false) {
	global $wp_registered_widget_updates;

	foreach ( (array) $wp_registered_widget_updates as $name => $control ) {
		if ( $name == $id_base ) {
			if ( !is_callable( $control['callback'] ) )
				continue;

			$_POST['id_base'] = $id_base;
			if ( 1 == $control['callback'][0]->number ) {
				$_POST['multi_number'] = 2;
				$sidebar[] = $id_base . '-2';
			} else {
				$_POST['multi_number'] = $control['callback'][0]->number;
				$sidebar[] = $control['callback'][0]->id;
			}

			call_user_func_array( $control['callback'], $control['params'] );

			break;
		}
	}
}


function k2_widgets_init() {
	register_widget('K2_Widget_About');
	register_widget('K2_Widget_Asides');
}

add_action( 'widgets_init', 'k2_widgets_init' );

function k2_asides_filter($query) {
	$asidescat = get_option('k2asidescategory');

	// Only filter when it's in the homepage
	if ( ($asidescat != 0) and ($query->is_home) and is_active_widget(false, false, 'k2-asides') ) {
		$exclude_cats = $query->get('category__not_in');
		$include_cats = $query->get('category__in');

		// Remove asides from list of categories to include
		if ( !empty($include_cats) and in_array($asidescat, $include_cats) ) {
			$query->set( 'category__in', array_diff( $include_cats, array($asidescat) ) );
		}

		// Insert asides into list of categories to exclude
		if ( empty($exclude_cats) ) {
			$query->set( 'category__not_in', array($asidescat) );
		} else if ( !in_array( $asidescat, $exclude_cats ) ) {
			$query->set( 'category__not_in', array_merge( $exclude_cats, array($asidescat) ) );
		}
	}

	return $query;
}

// Filter to remove asides from the loop
add_filter('pre_get_posts', 'k2_asides_filter');
