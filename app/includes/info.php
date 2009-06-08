<?php
// Prevent users from directly loading this file
defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );

function k2info( $show = '' ) {
	echo get_k2info($show);
}

function get_k2info( $show = '' ) {
	$output = '';

	switch ( $show ) {
		case 'version' :
    		$output = K2_CURRENT;
			break;

		case 'style' :
			if ( get_option('k2style') != '' )
				$output = K2_STYLES_URL . '/' . get_option('k2style');
			break;

		case 'style_footer' :
			$styleinfo = get_option('k2styleinfo');
			if ( !empty($styleinfo['footer']) )
				$output = stripslashes($styleinfo['footer']);
			break;

		case 'styles_url' :
			$output = K2_STYLES_URL . '/';
			break;

		case 'headers_url' :
			$output = K2_HEADERS_URL . '/';
			break;

		case 'current_style_dir' :
			if ( get_option('k2style') != '' )
				$output = K2_STYLES_DIR . '/' . dirname( get_option('k2style') );
			break;

		case 'current_style_url' :
			if ( get_option('k2style') != '' )
				$output = K2_STYLES_URL . '/' . dirname( get_option('k2style') );
			break;
	}
	return $output;
}

function k2_add_styles_to_theme_editor() {
	global $wp_themes, $pagenow;

	if ( ('theme-editor.php' == $pagenow) and strpos(K2_STYLES_DIR, WP_CONTENT_DIR) !== false ) {
		get_themes();
		$current = get_current_theme();

		// Get the path relative to wp-content
		$style_path = str_replace(WP_CONTENT_DIR, '', K2_STYLES_DIR);

		// Get a list of style css
		$styles = K2::files_scan( K2_STYLES_DIR, 'css', 2 );;

		// Loop through each style css and add to the list
		foreach ($styles as $style_css) {
			$wp_themes[$current]['Stylesheet Files'][] = "$style_path/$style_css";
		}
	}
}

if ( ! K2_CHILD_THEME ) {
	add_action( 'admin_init', 'k2_add_styles_to_theme_editor' );
}

function update_style_info() {
	$data = get_style_data( get_option('k2style') );

	if ( !empty($data) and ($data['stylename'] != '') and ($data['stylelink'] != '') and ($data['author'] != '') ) {
		// No custom style info
		if ( $data['footer'] == '' ) {
			$data['footer'] = __('Styled with <a href="%stylelink%" title="%style% by %author%">%style%</a>','k2_domain');
		}

		if ( strpos($data['footer'], '%') !== false ) {

			$keywords = array( '%author%', '%comments%', '%site%', '%style%', '%stylelink%', '%version%' );
			$replace = array( $data['author'], $data['comments'], $data['site'], $data['stylename'], $data['stylelink'], $data['version'] );
			$data['footer'] = str_replace( $keywords, $replace, $data['footer'] );
		}
	}

	update_option('k2styleinfo', $data);	

	return $data;
}

function get_style_data( $style_file = '' ) {
	// if no style selected, exit
	if ( '' == $style_file )
		return false;

	$style_path = K2_STYLES_DIR . "/$style_file";

	if ( ! is_readable($style_path) )
		return false;

	$style_data = implode( '', file($style_path) );
	$style_data = str_replace( '\r', '\n', $style_data );

	if ( preg_match("|Author Name\s*:(.*)$|mi", $style_data, $author) )
		$author = trim( $author[1] );
	else
		$author = '';

	if ( preg_match("|Author Site\s*:(.*)$|mi", $style_data, $site) )
		$site = clean_url( trim( $site[1] ) );
	else
		$site = '';

	if ( preg_match("|Style Name\s*:(.*)$|mi", $style_data, $stylename) )
		$stylename = trim( $stylename[1] );
	else
		$stylename = '';

	if ( preg_match("|Style URI\s*:(.*)$|mi", $style_data, $stylelink) )
		$stylelink = clean_url( trim( $stylelink[1] ) );
	else
		$stylelink = '';

	if ( preg_match("|Style Footer\s*:(.*)$|mi", $style_data, $footer) )
		$footer = trim( $footer[1] );
	else
		$footer = '';

	if ( preg_match("|Version\s*:(.*)$|mi", $style_data, $version) )
		$version = trim( $version[1] );
	else
		$version = '';

	if ( preg_match("|Comments\s*:(.*)$|mi", $style_data, $comments) )
		$comments = trim( $comments[1] );
	else
		$comments = '';

	if ( preg_match("|Header Text Color\s*:\s*#*([\dABCDEF]+)|i", $style_data, $header_text_color) )
		 $header_text_color = $header_text_color[1];
	else
		 $header_text_color = '';

	if ( preg_match("|Header Width\s*:\s*(\d+)|i", $style_data, $header_width) )
		$header_width = (int) $header_width[1];
	else
		$header_width = 0;

	if ( preg_match("|Header Height\s*:\s*(\d+)|i", $style_data, $header_height) )
		$header_height = (int) $header_height[1];
	else
		$header_height = 0;

	$layout_widths = array();
	if ( preg_match("|Layout Widths\s*:\s*(\d+)\s*(px)?,\s*(\d+)\s*(px)?,\s*(\d+)|i", $style_data, $widths) ) {
		$layout_widths[1] = (int) $widths[1];
		$layout_widths[2] = (int) $widths[3];
		$layout_widths[3] = (int) $widths[5];
	}

	return array(
		'path' => $style_file,
		'modified' => filemtime($style_path),
		'author' => $author,
		'site' => $site,
		'stylename' => $stylename,
		'stylelink' => $stylelink,
		'footer' => $footer,
		'version' => $version,
		'comments' => $comments,
		'header_text_color' => $header_text_color,
		'header_width' => $header_width,
		'header_height' => $header_height,
		'layout_widths' => $layout_widths
	);
}


function get_rolling_page_dates($query) {
	global $wpdb;

	$per_page = intval(get_query_var('posts_per_page'));
	$num_pages = $query->max_num_pages;

	$search = '/FROM\s+?(.*)\s+?LIMIT/siU';
	preg_match($search, $query->request, $matches);

	$post_dates = $wpdb->get_results("SELECT {$wpdb->posts}.post_date_gmt FROM {$matches[1]}");

	$page_dates = array();
	setlocale(LC_TIME, WPLANG . '.' . get_option('blog_charset') );

	for ($i = 0; $i < $num_pages; $i++) {
		$page_dates[] = strftime( __('%B, %Y', 'k2_domain'), abs(strtotime($post_dates[$i * $per_page]->post_date_gmt . ' GMT')) );
	}

	return $page_dates;
}

function output_javascript_url($file) {
	echo get_bloginfo('template_url') .'/'. $file;
}


// Generate JavaScript array from an array
function output_javascript_array($array, $print = true) {
	$output = '[';

	if ( is_array($array) and !empty($array) ) {
		array_walk($array, 'js_format_array');
		$output .= implode(', ', $array);
	}

	$output .= ']';

	return $print ? print($output) : $output;
}


// Generate JavaScript hash from an associated array
function output_javascript_hash($array, $print = true) {
	$output = '{';

	if ( is_array($array) and !empty($array) ) {
		array_walk($array, 'js_format_hash');
		$output .= implode(', ', $array);
	}

	$output .= '}';

	return $print ? print($output) : $output;
}

function js_format_array(&$item, $key) {
	$item = js_value($item);
}

function js_format_hash(&$item, $key) {
	$item = '"' . js_escape($key) . '": ' . js_value($item);
}

function js_value($value) {
	if ( is_string($value) )
		return '"' . js_escape($value) . '"';
	
	if ( is_bool($value) )
      return $value ? 'true' : 'false';

	if ( is_numeric($value) )
		return $value;
		
	if ( empty($value) )
		return '0';

	return '""';
}


function k2_post_groupby($groupby) {
	// Only filter when asides_module is active
	if ( is_home() and function_exists('is_active_module') and is_active_module('asides_sidebar_module') ) {
		return '';
	}

	return $groupby;
}

add_filter('posts_groupby', 'k2_post_groupby');

function k2_asides_filter($query) {
	$asidescat = get_option('k2asidescategory');

	// Only filter when it's in the homepage
	if ( ($asidescat != 0) and ($query->is_home) and is_active_widget('k2_asides_widget', 'k2-asides') ) {

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


function get_wp_version() {
	global $wp_version;

	$version = floatval($wp_version);

	// Old versions of WordPress-mu
	if ( strpos($wp_version, 'wordpress-mu') !== false ) {
		$version = $version + 1.0;
	}

	return $version;
}


// Semantic class functions from Sandbox (http://www.plaintxt.org/themes/sandbox/)

// Generates semantic classes for BODY element
function k2_body_class( $print = true ) {
	global $wp_query, $current_user, $blog_id;
	
	$c = array('wordpress', 'k2');

	// Applies the time- and date-based classes (below) to BODY element
	k2_date_classes(time(), $c);

	// Generic semantic classes for what type of content is displayed

	is_front_page()      ? $c[] = 'home'       : null;
	is_home()            ? $c[] = 'blog'       : null;
	is_archive()         ? $c[] = 'archive'    : null;
	is_date()            ? $c[] = 'date'       : null;
	is_search()          ? $c[] = 'search'     : null;
	is_paged()           ? $c[] = 'paged'      : null;
	is_attachment()      ? $c[] = 'attachment' : null;
	is_404()             ? $c[] = 'four04'     : null; // CSS does not allow a digit as first character

	if ( is_attachment() ) {
		$postID = $wp_query->post->ID;
		the_post();

		// Adds 'single' class and class with the post ID
		$c[] = 'postid-' . $postID . ' s-slug-' . $wp_query->post->post_name;

		// Adds classes for the month, day, and hour when the post was published
		if ( isset($wp_query->post->post_date) )
			k2_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');

		$the_mime = get_post_mime_type();
		$boring_stuff = array('application/', 'image/', 'text/', 'audio/', 'video/', 'music/');
		$c[] = 'attachment-' . str_replace($boring_stuff, '', $the_mime);
		
		// Adds author class for the post author
		$c[] = 's-author-' . sanitize_title_with_dashes(strtolower(get_the_author()));
		rewind_posts();
	}

	// Special classes for BODY element when a single post
	elseif ( is_single() ) {
		$postID = $wp_query->post->ID;
		the_post();

		// Adds 'single' class and class with the post ID
		$c[] = 'single postid-' . $postID . ' s-slug-' . $wp_query->post->post_name;

		// Adds classes for the month, day, and hour when the post was published
		if ( isset($wp_query->post->post_date) )
			k2_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');

		// Categories
		foreach ( (array) get_the_category() as $cat ) {
			if ( empty($cat->slug ) )
				continue;
			$c[] = 's-category-' . $cat->slug;
		}

		// Tags
		foreach ( (array) get_the_tags() as $tag ) {
			if ( empty($tag->slug ) )
				continue;
			$c[] = 's-tag-' . $tag->slug;
		}

		// Adds author class for the post author
		$c[] = 's-author-' . sanitize_title_with_dashes(strtolower(get_the_author()));

		if ( get_post_custom_values('sidebarless') ) {
			$c[] = 'sidebars-none';
		}
		
		if ( get_post_custom_values('hidesidebar1') ) {
			$c[] = 'hidesidebar-1';
		}

		if ( get_post_custom_values('hidesidebar2') ) {
			$c[] = 'hidesidebar-2';
		}

		rewind_posts();
	}

	// Author name classes for BODY on author archives
	else if ( is_author() ) {
		$author = $wp_query->get_queried_object();
		$c[] = 'author';
		$c[] = 'author-' . $author->user_nicename;
	}

	// Category name classes for BODY on category archvies
	else if ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		$c[] = 'category';
		$c[] = 'category-' . $cat->category_nicename;
	}

	// Tag name classes for BODY on tag archives
	else if ( is_tag() ) {
		$tag = $wp_query->get_queried_object();
		$c[] = 'tag';
		$c[] = 'tag-' . $tag->slug;
	}

	// Page author for BODY on 'pages'
	else if ( is_page() ) {
		$pageID = $wp_query->post->ID;
		$page_children = wp_list_pages("child_of=$pageID&echo=0");
		the_post();
		$c[] = 'page pageid-' . $pageID;
		$c[] = 'page-author-' . sanitize_title_with_dashes(strtolower(get_the_author()));
		$c[] = 'page-slug-'.$wp_query->post->post_name;

		// Checks to see if the page has children and/or is a child page; props to Adam
		if ( $page_children != '' ) {
			$c[] = 'page-parent';
		}

		if ( $wp_query->post->post_parent ) {
			$c[] = 'page-child parent-pageid-' . $wp_query->post->post_parent;
		}

		if ( get_post_custom_values('sidebarless') ) {
			$c[] = 'sidebars-none';
		}

		if ( get_post_custom_values('hidesidebar1') ) {
			$c[] = 'hidesidebar-1';
		}

		if ( get_post_custom_values('hidesidebar2') ) {
			$c[] = 'hidesidebar-2';
		}

		rewind_posts();
	}

	// Paged classes; for 'page X' classes of index, single, etc.
	$page = intval( $wp_query->get('paged') );
	if ( is_paged() && $page > 1 ) {
		$c[] = 'paged-'.$page.'';
		if ( is_single() ) {
			$c[] = 'single-paged-'.$page.'';
		} else if ( is_page() ) {
			$c[] = 'page-paged-'.$page.'';
		} else if ( is_category() ) {
			$c[] = 'category-paged-'.$page.'';
		} else if ( function_exists('is_tag') and is_tag() ) {
			$c[] = 'tag-paged-'.$page.'';
		} else if ( is_date() ) {
			$c[] = 'date-paged-'.$page.'';
		} else if ( is_author() ) {
			$c[] = 'author-paged-'.$page.'';
		} else if ( is_search() ) {
			$c[] = 'search-paged-'.$page.'';
		}
	}

	// For when a visitor is logged in while browsing
	if ( $current_user->ID )
		$c[] = 'loggedin';

	// Sidebar layout settings
	switch (get_option('k2columns')) {
		case '1':
			$c[] = 'columns-one';
			break;
		default:
		case '2':
			$c[] = 'columns-two';
			break;
		case 'dynamic':
		case '3':
			$c[] = 'columns-three';
			break;
	}

	// Language settings
	$locale = get_locale();
	if ( empty($locale) ) {
		$locale = 'en';
	} else {
		$lang_array = split('_', $locale);
		$locale = $lang_array[0];
	}
	$c[] = 'lang-' . $locale;

    // For WPMU. Set a class for the blog ID    
    if ( isset($blog_id) )
        $c[] = 'wpmu-' . $blog_id;

	// Separates classes with a single space, collates classes for BODY
	$c = attribute_escape( join( ' ', apply_filters('body_class', $c) ) );

	// And tada!
	return $print ? print($c) : $c;
}


function k2_post_class_filter($classes) {
	global $k2_post_alt, $post;

	if ( !$k2_post_alt )
		$k2_post_alt = 1;

	$classes[] = "p$k2_post_alt";

	// If it's the other to the every, then add 'alt' class
	if ( ++$k2_post_alt % 2 )
		$classes[] = 'alt';

	// Asides post
	if ( in_category( get_option('k2asidescategory') ) )
		$classes[] = 'k2-asides';

	// Applies the time- and date-based classes (below) to post DIV
	k2_date_classes(mysql2date('U', $post->post_date), $classes);

	return $classes;
}

add_filter('post_class', 'k2_post_class_filter');


function k2_comment_class_filter($classes) {
	global $comment;

	k2_date_classes(mysql2date('U', $comment->comment_date), $classes, 'c-');

	return $classes;
}

add_filter('comment_class', 'k2_comment_class_filter');


// Generates time- and date-based classes for BODY, post DIVs, and comment LIs; relative to GMT (UTC)
function k2_date_classes($t, &$c, $p = '') {
	$t = $t + (get_option('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t); // Year
	$c[] = $p . 'm' . gmdate('m', $t); // Month
	$c[] = $p . 'd' . gmdate('d', $t); // Day
	$c[] = $p . 'h' . gmdate('H', $t); // Hour
}
