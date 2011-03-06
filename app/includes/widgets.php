<?php
/**
 * K2 Widgets.
 *
 * Specific widgets for K2.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

// Prevent users from directly loading this include file
defined( 'K2_CURRENT' ) or die ( __('Error: This file can not be loaded directly.', 'k2') );

class K2_Widget_About extends WP_Widget {
	function K2_Widget_About() {
		$widget_ops = array( 'classname' => 'k2-widget-about', 'description' => __('Message about the current area and optional front-page message', 'k2') );
		$this->WP_Widget('k2-about', __('K2 About', 'k2'), $widget_ops);
	}

	function widget($args, $instance) {
		extract($args);

		$title = empty($instance['title']) ? __('About', 'k2') : apply_filters('widget_title', $instance['title']);
		$message = empty( $instance['message'] ) ? '' : stripslashes( $instance['message'] );

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

		<?php if ( is_category() ) : // Category Archive ?>
			<p><?php printf( __('You are currently browsing the <a href="%1$s">%2$s</a> weblog archives for the %3$s category.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name'),
						single_cat_title('', false)
					); ?></p>

		<?php elseif ( is_day() ) : // Day Archive ?>
			<p><?php printf( __('You are currently browsing the <a href="%1$s">%2$s</a> weblog archives for the day %3$s.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name'),
						get_the_time( __('l, F jS, Y', 'k2') )
					); ?></p>

		<?php elseif ( is_month() ) : // Monthly Archive ?>
			<p><?php printf( __('You are currently browsing the <a href="%1$s">%2$s</a> weblog archives for the month %3$s.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name'),
						get_the_time( __('F, Y', 'k2') )
					); ?></p>

		<?php elseif ( is_year() ) : // Yearly Archive ?>
			<p><?php printf( __('You are currently browsing the <a href="%1$s">%2$s</a> weblog archives for the year %3$s.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name'),
						get_the_time('Y')
					); ?></p>

		<?php elseif ( is_search() ) : // Search ?>
			<p><?php printf( __('You have searched the <a href="%1$s">%2$s</a> weblog archives for \'<strong>%3$s</strong>\'.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name'),
						esc_attr( get_search_query() )
					); ?></p>

		<?php elseif ( is_author() ) : // Author Archive ?>
			<p><?php printf( __('Archive for <strong>%s</strong>.', 'k2'), get_the_author_meta('display_name') ); ?></p>
			<p><?php the_author_meta('description'); ?></p>

		<?php elseif ( is_tag() ) : // Tag Archive ?>
			<p><?php printf( __('You are currently browsing the <a href="%1$s">%2$s</a> weblog archives for \'%3$s\' tag.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name'),
						get_query_var('tag')
					); ?></p>

		<?php elseif ( is_paged() ) : // Paged Archive ?>
			<p><?php printf( __('You are currently browsing the <a href="%1$s">%2$s</a> weblog archives.', 'k2'),
						get_bloginfo('url'), get_bloginfo('name')
					); ?></p>

		<?php endif; ?>
	<?php
			echo $after_widget;
		}
	}

	function form($instance) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('About', 'k2'), 'message' => '' ) );
		$title = esc_attr( $instance['title'] );
		$message = format_to_edit( $instance['message'] );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'k2'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('message'); ?>"><?php _e('About Text:', 'k2'); ?></label>
				<textarea id="<?php echo $this->get_field_id('message'); ?>" name="<?php echo $this->get_field_name('message'); ?>" rows="6" cols="30" class="widefat"><?php echo $message; ?></textarea>
				<small><?php _e('Enter a blurb about yourself here, and it will show up on the front page. Deleting the content disables the about blurb.', 'k2'); ?></small>
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


function k2_widgets_init() {
	register_widget('K2_Widget_About');
}
add_action( 'widgets_init', 'k2_widgets_init' );