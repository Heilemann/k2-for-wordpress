<?php
// WordPress Compatibility Functions

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file.
 *
 * @since 2.7.0
 *
 * @param array $template_names Array of template files to search for in priority order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @return string The template filename if one is located.
 */
if ( ! function_exists('locate_template') ):
	function locate_template($template_names, $load = false) {
		if (!is_array($template_names))
			return '';

		$located = '';
		foreach($template_names as $template_name) {
			if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
				$located = STYLESHEETPATH . '/' . $template_name;
				break;
			} else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
				$located = TEMPLATEPATH . '/' . $template_name;
				break;
			}
		}

		if ($load && '' != $located)
			load_template($located);

		return $located;
	}
endif;

/**
 * Display the classes for the post div.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 */
if ( ! function_exists('post_class') ):
	function post_class( $class = '', $post_id = null ) {
		// Separates classes with a single space, collates classes for post DIV
		echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
	}
endif;

/**
 * Retrieve the classes for the post div as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 * @return array Array of classes.
 */
if ( ! function_exists('get_post_class') ):
	function get_post_class( $class = '', $post_id = null ) {
		$post = get_post($post_id);

		$classes = array();

		$classes[] = $post->post_type;

		/*
		// sticky for Sticky Posts
		if ( is_sticky($post->ID) && is_home())
			$classes[] = 'sticky';
		*/

		// hentry for hAtom compliace
		$classes[] = 'hentry';

		// Categories
		foreach ( (array) get_the_category($post->ID) as $cat ) {
			if ( empty($cat->slug ) )
				continue;
			$classes[] = 'category-' . $cat->slug;
		}

		// Tags
		foreach ( (array) get_the_tags($post->ID) as $tag ) {
			if ( empty($tag->slug ) )
				continue;
			$classes[] = 'tag-' . $tag->slug;
		}

		if ( !empty($class) ) {
			if ( !is_array( $class ) )
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		}

		return apply_filters('post_class', $classes, $class, $post_id);
	}
endif;


/**
 * Generates semantic classes for each comment element
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list
 * @param int $comment_id An optional comment ID
 * @param int $post_id An optional post ID
 * @param bool $echo Whether comment_class should echo or return
 */
if ( ! function_exists('comment_class') ):
	function comment_class( $class = '', $comment_id = null, $post_id = null, $echo = true ) {
		// Separates classes with a single space, collates classes for comment DIV
		$class = 'class="' . join( ' ', get_comment_class( $class, $comment_id, $post_id ) ) . '"';
		if ( $echo)
			echo $class;
		else
			return $class;
	}
endif;


/**
 * Returns the classes for the comment div as an array
 *
 * @since 2.7.0
 *
 * @param string|array $class One or more classes to add to the class list
 * @param int $comment_id An optional comment ID
 * @param int $post_id An optional post ID
 * @return array Array of classes
 */
if ( ! function_exists('get_comment_class') ):
	function get_comment_class( $class = '', $comment_id = null, $post_id = null ) {
		global $comment_alt, $comment_depth, $comment_thread_alt;

		$comment = get_comment($comment_id);

		$classes = array();

		// Get the comment type (comment, trackback),
		$classes[] = ( empty( $comment->comment_type ) ) ? 'comment' : $comment->comment_type;

		// If the comment author has an id (registered), then print the log in name
		if ( $comment->user_id > 0 && $user = get_userdata($comment->user_id) ) {
			// For all registered users, 'byuser'
			$classes[] = 'byuser comment-author-' . $user->user_nicename;
			// For comment authors who are the author of the post
			if ( $post = get_post($post_id) ) {
				if ( $comment->user_id === $post->post_author )
					$classes[] = 'bypostauthor';
			}
		}

		if ( empty($comment_alt) )
			$comment_alt = 0;
		if ( empty($comment_depth) )
			$comment_depth = 1;
		if ( empty($comment_thread_alt) )
			$comment_thread_alt = 0;

		if ( $comment_alt % 2 ) {
			$classes[] = 'odd';
			$classes[] = 'alt';
		} else {
			$classes[] = 'even';
		}

		$comment_alt++;

		// Alt for top-level comments
		if ( 1 == $comment_depth ) {
			if ( $comment_thread_alt % 2 ) {
				$classes[] = 'thread-odd';
				$classes[] = 'thread-alt';
			} else {
				$classes[] = 'thread-even';
			}
			$comment_thread_alt++;
		}

		$classes[] = "depth-$comment_depth";

		if ( !empty($class) ) {
			if ( !is_array( $class ) )
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		}

		return apply_filters('comment_class', $classes, $class, $comment_id, $post_id);
	}
endif;


/**
 * Whether post requires password and correct password has been provided.
 *
 * @since 2.7.0
 *
 * @param int|object $post An optional post.  Global $post used if not provided.
 * @return bool false if a password is not required or the correct password cookie is present, true otherwise.
 */
if ( ! function_exists('post_password_required') ):
	function post_password_required( $post = null ) {
		$post = get_post($post);

		if ( empty($post->post_password) )
			return false;

		if ( !isset($_COOKIE['wp-postpass_' . COOKIEHASH]) )
			return true;

		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password )
			return true;

		return false;
	}
endif;

/**
 * Returns the Log Out URL.
 *
 * Returns the URL that allows the user to log out of the site
 *
 * @since 2.7
 * @uses wp_nonce_url() To protect against CSRF
 * @uses site_url() To generate the log in URL
 * 
 * @param string $redirect Path to redirect to on logout.
 */
if ( ! function_exists('wp_logout_url') ):
	function wp_logout_url($redirect = '') {
		if ( strlen($redirect) )
			$redirect = "&redirect_to=$redirect";
	
		return wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' );
	}
endif;

/**
 * Display or retrieve list of pages with optional home link.
 *
 * The arguments are listed below and part of the arguments are for {@link
 * wp_list_pages()} function. Check that function for more info on those
 * arguments.
 *
 * <ul>
 * <li><strong>sort_column</strong> - How to sort the list of pages. Defaults
 * to page title. Use column for posts table.</li>
 * <li><strong>menu_class</strong> - Class to use for the div ID which contains
 * the page list. Defaults to 'menu'.</li>
 * <li><strong>echo</strong> - Whether to echo list or return it. Defaults to
 * echo.</li>
 * <li><strong>link_before</strong> - Text before show_home argument text.</li>
 * <li><strong>link_after</strong> - Text after show_home argument text.</li>
 * <li><strong>show_home</strong> - If you set this argument, then it will
 * display the link to the home page. The show_home argument really just needs
 * to be set to the value of the text of the link.</li>
 * </ul>
 *
 * @since 2.7.0
 *
 * @param array|string $args
 */
if ( ! function_exists('wp_page_menu') ):
	function wp_page_menu( $args = array() ) {
		$defaults = array('sort_column' => 'post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'wp_page_menu_args', $args );

		$menu = '';

		$list_args = $args;

		// Show Home in the menu
		if ( isset($args['show_home']) && ! empty($args['show_home']) ) {
			if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
				$text = __('Home');
			else
				$text = $args['show_home'];
			$class = '';
			if ( is_front_page() && !is_paged() )
				$class = 'class="current_page_item"';
			$menu .= '<li ' . $class . '><a href="' . get_option('home') . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
			// If the front page is a page, add it to the exclude list
			if (get_option('show_on_front') == 'page') {
				if ( !empty( $list_args['exclude'] ) ) {
					$list_args['exclude'] .= ',';
				} else {
					$list_args['exclude'] = '';
				}
				$list_args['exclude'] .= get_option('page_on_front');
			}
		}

		$list_args['echo'] = false;
		$list_args['title_li'] = '';
		$menu .= str_replace( array( "\r", "\n", "\t" ), '', wp_list_pages($list_args) );

		if ( $menu )
			$menu = '<ul>' . $menu . '</ul>';

		$menu = '<div class="' . $args['menu_class'] . '">' . $menu . "</div>\n";
		$menu = apply_filters( 'wp_page_menu', $menu, $args );
		if ( $args['echo'] )
			echo $menu;
		else
			return $menu;
	}
endif;

/**
 * Retrieve translated string with gettext context
 *
 * Quite a few times, there will be collisions with similar translatable text
 * found in more than two places but with different translated context.
 *
 * By including the context in the pot file translators can translate the two
 * string differently
 *
 * @since 2.8
 *
 * @param string $text Text to translate
 * @param string $context Context information for the translators
 * @param string $domain Optional. Domain to retrieve the translated text
 * @return string Translated context string without pipe
 */
if ( ! function_exists('_x') ):
	function _x( $single, $context, $domain = 'default' ) {
		return translate_with_gettext_context( $single, $context, $domain );
	}
endif;