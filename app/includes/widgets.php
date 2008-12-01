<?php

function k2_about_widget($args) {
	extract($args);

	//$k2about = get_option('aboutblurp');
	$options = get_option('k2widgetoptions');
	$title = apply_filters('widget_title', $options['about']['title']);
	if ( empty($title) )
		$title = __('About', 'k2_domain');

	echo $before_widget . $before_title . $title . $after_title; ?>

	<?php if ( $k2about != '' ): // Frontpage ?>
	<p><?php echo stripslashes($k2about); ?></p>
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


function k2_asides_widget($args) {
	extract($args);
	global $post;

	$k2asidescategory = get_option('k2asidescategory');

	if ( $k2asidescategory != '0') {
		// backup current post
		$post_backup = $post;

		echo $before_widget . $before_title . $title . $after_title; ?>

		<a href="<?php bloginfo('url'); ?>/?feed=rss&amp;cat=<?php echo $k2asidescategory; ?>" title="<?php _e('RSS Feed for Asides', 'k2_domain'); ?>" class="feedlink"><span><?php _e('RSS', 'k2_domain'); ?></span></a>
		<div>
			<?php
				$asides_count = 1;
				$asides = new WP_Query('cat=' . $k2asidescategory . '&showposts=' . 5);

				while ( $asides->have_posts() ):
					$asides->the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class($asides_count++, true); ?>">
				<span>&raquo;&nbsp;</span><?php the_content(__('(more)','k2_domain')); ?>&nbsp;<span class="metalink"><a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), wp_specialchars(strip_tags(the_title('', '', false)),1) ); ?>'><?php comments_number('(0)','(1)','(%)'); ?></a></span><?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
			</div>
			<?php endwhile; ?>
		</div>
<?php
		echo $after_widget;

		// restore current post
		$post = $post_backup;
	} /* end asides check */
}

/*function asides_sidebar_module_control() {
	if (isset($_POST['asides_module_num_posts'])) {
		sbm_update_option('num_posts', $_POST['asides_module_num_posts']);
	}
	?>
		<p><label for="asides-module-num-posts"><?php _e('Number of posts:', 'k2_domain'); ?></label> <input id="asides-module-num-posts" name="asides_module_num_posts" type="text" value="<?php echo(sbm_get_option('num_posts')); ?>" size="2" /></p>
	<?php
}*/

function k2_widgets_init() {

	$widget_ops = array('classname' => 'sb-about', 'description' => __( 'K2 About') );
	wp_register_sidebar_widget('k2-about', __('K2 About', 'k2_domain'), 'k2_about_widget', $widget_ops);

	$widget_ops = array('classname' => 'sb-asides', 'description' => __( 'K2 Asides') );
	wp_register_sidebar_widget('k2-asides', __('K2 Asides', 'k2_domain'), 'k2_asides_widget', $widget_ops);
}

add_action( 'widgets_init', 'k2_widgets_init' );
