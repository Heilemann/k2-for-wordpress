<?php
// Prevent users from directly loading this file
defined( 'K2_CURRENT' ) or die ( __('Error: This file can not be loaded directly.', 'k2') );

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
		$page_dates[] = strftime('%B, %Y', abs(strtotime($post_dates[$i * $per_page]->post_date_gmt . ' GMT')) );
	}

	return $page_dates;
}

function output_javascript_url($file) {
	echo get_bloginfo('template_url') .'/'. $file;
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


function k2_body_class_filter($classes) {
	global $wp_query, $blog_id;

	$classes[] = 'wordpress k2';

	/* Detect whether the sidebars are in use and add appropriate classes */
	if ( is_active_sidebar('widgets-sidebar-1') && is_active_sidebar('widgets-sidebar-2') )
		$classes[] = 'columns-three';

	else if ( is_active_sidebar('widgets-sidebar-1') )
		$classes[] = 'columns-two widgets-sidebar-1';

	else if ( is_active_sidebar('widgets-sidebar-2') )
		$classes[] = 'columns-two widgets-sidebar-2';

	else
		$classes[] = 'columns-one';


	// If animations are turned on *CURRENTLY NOT IN USE*
	if ( '1' == get_option('k2animations') )
		$classes[] = 'animations';

	// Only on single posts and static pages
	if ( is_single() or is_page() ) {
		// Add 'author-XXXX' class
		$author = get_userdata($wp_query->post->post_author);
		$classes[] = 'author-' . sanitize_html_class($author->user_nicename , $author->ID);

		// If the post or page has a relevant custom field set
		if ( get_post_custom_values('sidebarless') )
			$classes[] = 'sidebars-none';
		if ( get_post_custom_values('hidesidebar1') )
			$classes[] = 'hidewidgets-sidebar-1';
		if ( get_post_custom_values('hidesidebar2') )
			$classes[] = 'hidewidgets-sidebar-2';

		// Add 'slug-XXXX' for the post or page slug -- CONSIDER REMOVING; WHAT WORTH DOES IT HAVE OVER 'postid-X'?
		$classes[] = 'slug-' . $wp_query->post->post_name;

		// Only for posts...
		if ( is_single() ) {
			// Adds classes for the month, day, and hour when the post was published
			if ( isset($wp_query->post->post_date) )
				k2_date_classes( mysql2date( 'U', $wp_query->post->post_date ), $classes, 's-' );

			// Add 'category-XXXX' for each relevant category
			foreach ( (array) get_the_category($wp_query->post->ID) as $cat ) {
				if ( empty($cat->slug ) )
					continue;
				$classes[] = 'category-' . sanitize_html_class($cat->slug, $cat->cat_ID);
			}

			// Add 'tag-XXXX' for each relevant tag
			foreach ( (array) get_the_tags($wp_query->post->ID) as $tag ) {
				if ( empty($tag->slug ) )
					continue;
				$classes[] = 'tag-' . sanitize_html_class($tag->slug, $tag->term_id);
			}
		}
	}

	// Language settings
	$locale = get_locale();
	if ( empty($locale) ) {
		$locale = 'en';
	} else {
		$lang_array = split('_', $locale);
		$locale = $lang_array[0];
	}
	$classes[] = 'lang-' . $locale;

    // For WPMU. Set a class for the blog ID    
    if ( isset($blog_id) )
        $classes[] = 'wpmu-' . $blog_id;

	// Applies the time- and date-based classes (below) to BODY element
	k2_date_classes(time(), $classes);

	$classes = array_merge( $classes, k2_browser_classes() );

	return $classes;
}

add_filter('body_class', 'k2_body_class_filter');

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
