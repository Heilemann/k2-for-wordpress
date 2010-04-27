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

		case 'style_footer' :
			if ( K2_STYLES ) {
				$styleinfo = get_option('k2styleinfo');
				if ( !empty($styleinfo['footer']) )
					$output = stripslashes($styleinfo['footer']);
			}
			break;

		case 'styles_url' :
			if ( K2_STYLES )
				$output = K2Styles::get_styles_url();
			break;

		case 'headers_url' :
			$output = K2_HEADERS_URL . '/';
			break;
	}
	return $output;
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
	$item = '"' . esc_js($key) . '": ' . js_value($item);
}

function js_value($value) {
	if ( is_string($value) )
		return '"' . esc_js($value) . '"';
	
	if ( is_bool($value) )
      return $value ? 'true' : 'false';

	if ( is_numeric($value) )
		return $value;
		
	if ( empty($value) )
		return '0';

	return '""';
}

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

	is_front_page()      					? $c[] = 'home'       		: null;
	is_home()            					? $c[] = 'blog'       		: null;
	is_archive()         					? $c[] = 'archive'    		: null;
	is_date()            					? $c[] = 'date'       		: null;
	is_search()          					? $c[] = 'search'     		: null;
	is_paged()           					? $c[] = 'paged'      		: null;
	is_attachment()      					? $c[] = 'attachment' 		: null;
	(get_option('k2rollingarchives')=='1') 	? $c[] = 'rollingarchives' 	: null;
	(get_option('k2animations')=='1') 		? $c[] = 'animations' 		: null;
	is_404()             					? $c[] = 'four04'     		: null; // CSS does not allow a digit as first character

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

	// Browser/Platform Specific Classes
	$c = array_merge( $c, k2_browser_classes() );
	
	// Separates classes with a single space, collates classes for BODY
	$c = esc_attr( join( ' ', apply_filters('body_class', $c) ) );

	// And tada!
	return $print ? print($c) : $c;
}

function k2_post_class( $post_count = 1, $post_asides = false, $print = true ) {
	_deprecated_function(__FUNCTION__, '0.0', 'post_class()');

	$c = join( ' ', get_post_class() );

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

/*
	Adapted from PHP CSS Browser Selector v0.0.1
	Bastian Allgeier (http://bastian-allgeier.de)
	http://bastian-allgeier.de/css_browser_selector
	License: http://creativecommons.org/licenses/by/2.5/
	Credits: This is a php port from Rafael Lima's original Javascript CSS Browser Selector: http://rafael.adm.br/css_browser_selector
*/
function k2_browser_classes($ua = null) {
		$ua = ($ua) ? strtolower($ua) : strtolower($_SERVER['HTTP_USER_AGENT']);		

		$g = 'gecko';
		$w = 'webkit';
		$s = 'safari';
		$b = array();
		
		// browser
		if ( !preg_match( '/opera|webtv/i', $ua ) && preg_match( '/msie\s(\d)/', $ua, $array ) ):
			$b[] = 'ie ie' . $array[1];
		elseif ( strstr( $ua, 'firefox/2' ) ):
			$b[] = $g . ' ff2';		
		elseif ( strstr( $ua, 'firefox/3.5' ) ):
			$b[] = $g . ' ff3 ff3_5';
		elseif ( strstr( $ua, 'firefox/3' ) ):
			$b[] = $g . ' ff3';
		elseif ( strstr( $ua, 'gecko/' ) ):
			$b[] = $g;
		elseif (preg_match('/opera(\s|\/)(\d+)/', $ua, $array ) ):
			$b[] = 'opera opera' . $array[2];
		elseif ( strstr( $ua, 'konqueror' ) ):
			$b[] = 'konqueror';
		elseif ( strstr( $ua, 'chrome' ) ):
			$b[] = $w . ' ' . $s . ' chrome';
		elseif ( strstr( $ua, 'iron' ) ):
			$b[] = $w . ' ' . $s . ' iron';
		elseif ( strstr( $ua, 'applewebkit/' ) ):
			$b[] = (preg_match('/version\/(\d+)/i', $ua, $array)) ? $w . ' ' . $s . ' ' . $s . $array[1] : $w . ' ' . $s;
		elseif ( strstr( $ua, 'mozilla/' ) ):
			$b[] = $g;
		endif;

		// platform				
		if ( strstr( $ua, 'j2me' ) ):
			$b[] = 'mobile';
		elseif ( strstr( $ua, 'iphone' ) ):
				$b[] = 'iphone';		
		elseif ( strstr( $ua, 'ipod' ) ):
				$b[] = 'ipod';		
		elseif ( strstr( $ua, 'mac' ) ):
				$b[] = 'mac';		
		elseif ( strstr( $ua, 'darwin' ) ):
				$b[] = 'mac';		
		elseif ( strstr( $ua, 'webtv' ) ):
				$b[] = 'webtv';
		elseif ( strstr( $ua, 'win' ) ):
				$b[] = 'win';
		elseif ( strstr( $ua, 'freebsd' ) ):
				$b[] = 'freebsd';
		elseif ( strstr( $ua, 'x11' ) || strstr( $ua, 'linux' ) ):
			$b[] = 'linux';
		endif;
		
		return $b;

}
