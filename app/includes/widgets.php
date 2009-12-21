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

/**
 * Display K2 About widget.
 *
 * @since 1.0-RC8
 *
 * @param array $args Widget arguments.
 */
function k2_about_widget($args) {
	extract($args);

	$options = get_option('k2widgetoptions');
	$title = empty($options['about']['title']) ? __('About', 'k2_domain') : apply_filters('widget_title', $options['about']['title']);

	echo $before_widget;
	if ( $title != '<none>' )
		echo $before_title . $title . $after_title;
?>

	<?php if ( ! empty($options['about']['message']) and ( is_home() or is_front_page() or is_page() ) ): ?>
		<div><?php echo stripslashes( $options['about']['message'] ); ?></div>
	<?php endif; ?>

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
					attribute_escape( get_search_query() )
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


/**
 * Manage K2 About widget options.
 *
 * Displays management form for changing the K2 About widget title and about blurp message.
 *
 * @since 1.0-RC8
 */
function k2_about_widget_control() {
	$options = $newoptions = get_option('k2widgetoptions');

	if ( empty($newoptions) )
		$newoptions = array();

	$defaults = array( 'title' => __('About', 'k2_domain'), 'message' => '' );
	$newoptions['about'] = wp_parse_args( $options['about'], $defaults );

	if ( isset($_POST['k2-about-submit']) ) {
		$newoptions['about']['title'] = strip_tags( $_POST['k2-about-title'] );
		$newoptions['about']['message'] = $_POST['k2-about-message'];

		if ( ! current_user_can('unfiltered_html') )
			$newoptions['about']['message'] = wp_filter_post_kses( $newoptions['about']['message'] );
	}

	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('k2widgetoptions', $options);
	}
?>
	<p>
		<label for="k2-about-title"><?php _e('Title:', 'k2_domain'); ?></label>
		<input type="text" class="widefat" id="k2-about-title" name="k2-about-title" value="<?php echo attribute_escape( $options['about']['title'] ); ?>" />
	</p>
	<p>
		<label for="k2-about-message"><?php _e('About Text:', 'k2_domain'); ?></label>
		<textarea id="k2-about-message" name="k2-about-message" rows="6" cols="30" class="widefat"><?php echo format_to_edit( $options['about']['message'] ); ?></textarea>
		<small><?php _e('Enter a blurp about yourself here, and it will show up on the front page. Deleting the content disables the about blurp.','k2_domain'); ?></small>
	</p>
	<input type="hidden" name="k2-about-submit" id="k2-about-submit" value="1" />
<?php
}


function k2_asides_widget($args) {
	extract($args);

	$k2asidescategory = get_option('k2asidescategory');

	if ( $k2asidescategory != '0') {
		$options = get_option('k2widgetoptions');
		if ( ! isset($options['asides']['number']) )
			$number = 5;
		else
			$number = (int) $options['asides']['number'];

		$asides = new WP_Query( array( 'cat' => $k2asidescategory, 'showposts' => $number, 'what_to_show' => 'posts', 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1 ) );

		$title = empty($options['asides']['title']) ? apply_filters('single_cat_title', get_the_category_by_ID($k2asidescategory)) : apply_filters('widget_title', $options['asides']['title']);

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


function k2_asides_widget_control() {
	$options = $newoptions = get_option('k2widgetoptions');

	if ( empty($newoptions) )
		$newoptions = array();

	$defaults = array( 'title' => '', 'number' => 5 );
	$newoptions['asides'] = wp_parse_args( $options['asides'], $defaults );

	if ( isset($_POST['k2-asides-submit']) ) {
		$newoptions['asides']['title'] = strip_tags( $_POST['k2-asides-title'] );
		$newoptions['asides']['number'] = (int) $_POST['k2-asides-number'];
	}

	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('k2widgetoptions', $options);
	}

	?>
	<p>
		<label for="k2-asides-title"><?php _e('Title:', 'k2_domain'); ?></label>
		<input type="text" class="widefat" id="k2-asides-title" name="k2-asides-title" value="<?php echo attribute_escape( $options['asides']['title'] ); ?>" />
	</p>
	<p>
		<label for="k2-asides-number"><?php _e('Number of asides to show:', 'k2_domain'); ?></label>
		<input type="text" id="k2-asides-number" name="k2-asides-number" value="<?php echo attribute_escape( $options['asides']['number'] ); ?>" size="2" />
	</p>
	<input type="hidden" name="k2-asides-submit" id="k2-asides-submit" value="1" />
	<?php
}


/**
 * Assigns a default set of widgets
 */
function k2_default_widgets() {
	$sidebar = array();

	k2_add_widget($sidebar, 'search');
	k2_add_widget($sidebar, 'k2-about');
	k2_add_widget($sidebar, 'recent-posts');
	k2_add_widget($sidebar, 'recent-comments');
	k2_add_widget($sidebar, 'archives');
	k2_add_widget($sidebar, 'tag_cloud');
	k2_add_widget($sidebar, 'links');

	wp_set_sidebars_widgets( array('sidebar-1' => $sidebar) );
}

function k2_add_widget(&$sidebar, $widget_id, $settings = false) {
	global $wp_registered_widgets;

	foreach ($wp_registered_widgets as $widget) {
		if ( $widget_id == $widget['id'] ) {
			$sidebar[] = $widget['id'];
			break;
		}

		if ( isset($widget['callback'][0]->id_base) and ($widget_id == $widget['callback'][0]->id_base) ) {
			$sidebar[] = $widget['callback'][0]->id;
			break;
		}
	}
}


function k2_widgets_init() {
	$widget_ops = array( 'classname' => 'sb-about', 'description' => __('Message about the current area and optional front-page message', 'k2_domain') );
	wp_register_sidebar_widget('k2-about', __('K2 About', 'k2_domain'), 'k2_about_widget', $widget_ops);
	wp_register_widget_control('k2-about', __('K2 About', 'k2_domain'), 'k2_about_widget_control');

	$widget_ops = array( 'classname' => 'sb-asides', 'description' => __( 'Asides on your sidebar', 'k2_domain') );
	wp_register_sidebar_widget('k2-asides', __('K2 Asides', 'k2_domain'), 'k2_asides_widget', $widget_ops);
	wp_register_widget_control('k2-asides', __('K2 Asides', 'k2_domain'), 'k2_asides_widget_control');
}

add_action( 'widgets_init', 'k2_widgets_init' );