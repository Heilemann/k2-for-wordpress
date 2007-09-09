<?php

function k2info($show='') {
	echo get_k2info($show);
}

function get_k2info($show='') {
	global $current;

	$output = '';
	switch ($show) {
		case 'version' :
    		$output = 'Beta '. $current;
			break;

		case 'style' :
			if ( get_option('k2scheme') != '' ) {
				$output = get_bloginfo('wpurl') .'/'. str_replace(ABSPATH, '', K2STYLESPATH) . get_option('k2scheme');
			}
			break;

		case 'styles_url' :
			$output = get_bloginfo('wpurl') .'/'. str_replace(ABSPATH, '', K2STYLESPATH);
			break;

		case 'headers_url' :
			$output = get_bloginfo('wpurl') .'/'. str_replace(ABSPATH, '', K2HEADERSPATH);
			break;

		case 'current_style_dir' :
			if ( get_option('k2scheme') != '' ) {
				$output = K2STYLESPATH . dirname(get_option('k2scheme'));
			}
			break;
	}
	return $output;
}

function k2_parse_query($query) {
	if ( is_array($query) and !empty($query) ) {
		$valid_keys = array(
			'error'
			, 'm'
			, 'hour'
			, 'second'
			, 'minute'
			, 'hour'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'w'
			, 's'
			, 'category_name'
			, 'author_name'
			, 'paged'
			, 'showposts'
			, 'posts_per_page'
			, 'posts_per_archive_page'
			, 'nopaging'
			, 'order'
			, 'orderby'
			, 'offset'
			, 'tag'
		);

		foreach ($query as $key => $value) {
			if ( ! in_array($key, $valid_keys) ) {
				unset($query[$key]);
			}
		}
	}

	return $query;
}

function k2_style_info() {
	$style_info = get_option('k2styleinfo');
	
	if ('' != $style_info) {
		echo '<p>' . stripslashes($style_info) . '</p>';
	}
}

function k2styleinfo_update() {
	$style_info = '';
	$data = get_style_info( get_option('k2scheme') );

	if ('' != $data) {
		$style_info = get_option('k2styleinfo_format');
		$style_info = str_replace("%author%", $data['author'], $style_info);
		$style_info = str_replace("%site%", $data['site'], $style_info);
		$style_info = str_replace("%style%", $data['style'], $style_info);
		$style_info = str_replace("%stylelink%", $data['stylelink'], $style_info);
		$style_info = str_replace("%version%", $data['version'], $style_info);
		$style_info = str_replace("%comments%", $data['comments'], $style_info);
	}
	
	update_option('k2styleinfo', $style_info, '','');	
}

function k2styleinfo_demo() {
	$style_info = get_option('k2styleinfo_format');
	$data = get_scheme_info( get_option('k2scheme') );

	if ('' != $data) {
		$style_info = str_replace("%style%", $data['style'], $style_info);
		$style_info = str_replace("%stylelink%", $data['stylelink'], $style_info);
		$style_info = str_replace("%author%", $data['author'], $style_info);
		$style_info = str_replace("%site%", $data['site'], $style_info);
		$style_info = str_replace("%version%", $data['version'], $style_info);
		$style_info = str_replace("%comments%", $data['comments'], $style_info);
	} else {
		$style_info = str_replace("%style%", __('Default'), $style_info);
		$style_info = str_replace("%author%", 'Michael Heilemann', $style_info);
		$style_info = str_replace("%site%", 'http://binarybonsai.com/', $style_info);
		$style_info = str_replace("%version%", '1.0', $style_info);
		$style_info = str_replace("%comments%", 'Loves you like a kitten.', $style_info);
		$style_info = str_replace("%stylelink%", 'http://getk2.com/', $style_info);
	}

	echo stripslashes($style_info);
}

function get_style_info($style_file = '') {
	// if no style selected, exit
	if ( '' == $style_file ) {
		return false;
	}

	$style_path = K2STYLESPATH . $style_file;
	if (!file_exists($style_path)) return false;
	$style_data = implode( '', file( $style_path ) );

	// parse the data
	preg_match("|Author Name\s*:(.*)|i", $style_data, $author);
	preg_match("|Author Site\s*:(.*)|i", $style_data, $site);
	preg_match("|Style Name\s*:(.*)|i", $style_data, $style);
	preg_match("|Style URI\s*:(.*)|i", $style_data, $stylelink);
	preg_match("|Version\s*:(.*)|i", $style_data, $version);
	preg_match("|Comments\s*:(.*)|i", $style_data, $comments);
	preg_match("|Header Text Color\s*:\s*#*([\dABCDEF]+)|i", $style_data, $header_text_color);
	preg_match("|Header Width\s*:\s*(\d+)|i", $style_data, $header_width);
	preg_match("|Header Height\s*:\s*(\d+)|i", $style_data, $header_height);

	return array(
		'style' => trim($style[1]),
		'stylelink' => trim($stylelink[1]),
		'author' => trim($author[1]),
		'site' => trim($site[1]),
		'version' => trim($version[1]),
		'comments' => trim($comments[1]),
		'header_text_color' => trim($header_text_color[1]),
		'header_width' => trim($header_width[1]),
		'header_height' => trim($header_height[1])
	);
}

function get_k2_ping_type($trackbacktxt = 'Trackback', $pingbacktxt = 'Pingback') {
	$type = get_comment_type();
	switch( $type ) {
		case 'trackback' :
			return $trackbacktxt;
			break;
		case 'pingback' :
			return $pingbacktxt;
			break;
	}
	return false;
}

function get_rolling_page_dates($query) {
	global $wpdb;

	$per_page = get_query_var('posts_per_page');
	$num_pages = $query->max_num_pages;

	$search = '/FROM\s+?(.*)\s+?LIMIT/siU';
	preg_match($search, $query->request, $matches);

	$post_dates = $wpdb->get_results("SELECT {$wpdb->posts}.post_date_gmt FROM {$matches[1]}");

	$page_dates = array();
	for ($i = 0; $i < $num_pages; $i++) {
		$page_dates[] = date(__('F, Y','k2_domain'), abs(strtotime($post_dates[$i * $per_page]->post_date_gmt . ' GMT')));
	}

	return $page_dates;
}

function output_javascript_url($file) {
	$template_url = get_bloginfo('template_url');
	$url_parts = parse_url( $template_url );

	if ( $url_parts->host != $_SERVER['HTTP_HOST'] ) {
		$template_url = str_replace($url_parts->host, $_SERVER['HTTP_HOST'], $template_url);
	}

	echo $template_url .'/'. $file;
}

function output_javascript_array($array) {
	$output = '[';

	if ( is_array($array) ) {
		$last_item = array_pop($array);
		foreach ($array as $item) {
			$output .= '"' . $item . '", ';
		}
		$output .= '"' . $last_item . '"';
	}

	$output .= ']';

	echo $output;
}

function output_javascript_hash($array) {
	$output = '{';

	if ( is_array($array) and !empty($array) ) {
		$keys = array_keys($array);
		$values = array_values($array);
		$n = count($array);

		for ($i = 0; $i < $n; $i++) {
			$output .= $keys[$i] . ': ';

			if ( is_string($values[$i]) ) {
				$output .= "'$values[$i]'";
			} elseif ( empty($values[$i]) ) {
				$output .= '0';
			} else {
				$output .= strval($values[$i]);
			}

			if ($i < $n - 1) {
				$output .= ',';
			}
		}
	}

	$output .= '}';

	echo $output;
}


/* By mqchen at gmail dot com, http://us.php.net/manual/en/function.http-build-query.php#72911 */
if (!function_exists('http_build_query')) {
	function http_build_query($data, $prefix = null, $sep = '', $key = '') {
		$ret = array();
		foreach ( (array) $data as $k => $v) {
			$k = urlencode($k);

			if ( is_int($k) && $prefix != null ) {
				$k = $prefix.$k;
			}

			if ( !empty($key) ) {
				$k = $key.'['.$k.']';
			}

			if ( is_array($v) or is_object($v) ) {
				array_push($ret, http_build_query($v, '', $sep, $k));
			} else {
				array_push($ret, $k.'='.urlencode($v));
			}
		}

        if ( empty($sep) ) {
            $sep = ini_get("arg_separator.output");
        }

        return implode($sep, $ret);
    }
}


/* By Mark Jaquith, http://txfx.net */
function k2_nice_category($normal_separator = ', ', $penultimate_separator = ' and ') { 
	$categories = get_the_category(); 

	if (empty($categories)) { 
		return __('Uncategorized','k2_domain');
	} 

	$thelist = ''; 
	$i = 1; 
	$n = count($categories); 

	foreach ($categories as $category) { 
		if (1 < $i and $i != $n) {
			$thelist .= $normal_separator;
		}

		if (1 < $i and $i == $n) {
			$thelist .= $penultimate_separator;
		}

		$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '">'.$category->cat_name.'</a>'; 
		++$i; 
	} 
	return apply_filters('the_category', $thelist, $normal_separator);
}

function k2asides_filter($query) {
	$k2asidescategory = get_option('k2asidescategory');

	// Only filter when it's in the homepage
	if ( ($k2asidescategory != 0) and (
		(function_exists('is_active_module') and is_active_module('asides_sidebar_module')) or
		(function_exists('is_active_widget') and is_active_widget('k2_asides_widget'))
		) and ($query->is_home) ) {
		$priorcat = $query->get('cat');
		if ( !empty($priorcat) ) {
			$priorcat .= ',';
		}
		$query->set('cat', $priorcat . '-' . $k2asidescategory);
	}

	return $query;
}

// Filter to remove asides from the loop
add_filter('pre_get_posts', 'k2asides_filter');


function get_wp_version() {
	global $wp_version;

	preg_match("/\d\.\d/i", $wp_version, $match);

	// wpmu - increment version by 1.0 to match wp
	if (strpos($wp_version, 'wordpress-mu') !== false) {
		$match[0] = $match[0] + 1.0;
	}
	return $match[0];
}


function k2_body_id() {
	if (get_option('permalink_structure') != '' and is_page()) {
		if (get_query_var('name') != '') {
			$id_name = get_query_var('name');
		}else{
			$id_name = "home";
		}
		echo "id='" . $id_name . "'";
	}
}



// Semantic class functions from Sandbox 0.9.7 (http://www.plaintxt.org/themes/sandbox/)

// Generates semantic classes for BODY element
function k2_body_class( $print = true ) {
	global $wp_query, $current_user;
	
	$c = array('wordpress', 'k2');

	// Applies the time- and date-based classes (below) to BODY element
	k2_date_classes(time(), $c);

	// Generic semantic classes for what type of content is displayed
	is_home()       ? $c[] = 'home'       : null;
	is_archive()    ? $c[] = 'archive'    : null;
	is_date()       ? $c[] = 'date'       : null;
	is_search()     ? $c[] = 'search'     : null;
	is_paged()      ? $c[] = 'paged'      : null;
	is_attachment() ? $c[] = 'attachment' : null;
	is_404()        ? $c[] = 'four04'     : null; // CSS does not allow a digit as first character
	is_tag()    	? $c[] = 'tag'        : null;
	is_category()   ? $c[] = 'category'   : null;

	// Special classes for BODY element when a single post
	if ( is_single() ) {
		$postID = $wp_query->post->ID;
		the_post();
		$c[] = 'single postid-' . $postID;

		if ( isset($wp_query->post->post_date) )
			k2_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');

		foreach ( (array) get_the_category() as $cat )
			$c[] = 's-category-' . $cat->category_nicename;

		$c[] = 's-author-' . sanitize_title_with_dashes(strtolower(get_the_author()));
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

	// Page author for BODY on 'pages'
	else if ( is_page() ) {
		$pageID = $wp_query->post->ID;
		the_post();
		$c[] = 'page pageid-' . $pageID;
		$c[] = 'page-author-' . sanitize_title_with_dashes(strtolower(get_the_author()));
		rewind_posts();
	}

	// For when a visitor is logged in while browsing
	if ( $current_user->ID )
		$c[] = 'loggedin';

	// Paged classes; for 'page X' classes of index, single, etc.
	if ( ( ( $page = $wp_query->get("paged") ) || ( $page = $wp_query->get("page") ) ) && $page > 1 ) {
		$c[] = 'paged-'.$page.'';
		if ( is_single() ) {
			$c[] = 'single-paged-'.$page.'';
		} else if ( is_page() ) {
			$c[] = 'page-paged-'.$page.'';
		} else if ( is_category() ) {
			$c[] = 'category-paged-'.$page.'';
		} else if ( is_date() ) {
			$c[] = 'date-paged-'.$page.'';
		} else if ( is_author() ) {
			$c[] = 'author-paged-'.$page.'';
		} else if ( is_search() ) {
			$c[] = 'search-paged-'.$page.'';
		}
	}

	// Sidebar layout settings
	if (function_exists('dynamic_sidebar')) {
		switch (get_option('k2columns')) {
			case '1':
				$c[] = 'columns-one';
				break;
			default:
			case '2':
				$c[] = 'columns-two';
				break;
			case '3':
				$c[] = 'columns-three';
				break;
		}
	} else {
		$c[] = 'columns-two';
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

	// Separates classes with a single space, collates classes for BODY
	$c = join(' ', apply_filters('body_class',  $c));

	// And tada!
	return $print ? print($c) : $c;
}

// Generates semantic classes for each post DIV element
function k2_post_class( $post_count = 1, $post_asides = false, $print = true ) {
	global $post;

	// hentry for hAtom compliace, gets 'alt' for every other post DIV, describes the post type and p[n]
	$c = array('hentry', "p$post_count", $post->post_type, $post->post_status);

	// Author for the post queried
	$c[] = 'author-' . sanitize_title_with_dashes(strtolower(get_the_author()));

	// Category for the post queried
	foreach ( (array) get_the_category() as $cat )
		$c[] = 'category-' . $cat->category_nicename;

	// For password-protected posts
	if ( $post->post_password )
		$c[] = 'protected';

	// Applies the time- and date-based classes (below) to post DIV
	k2_date_classes(mysql2date('U', $post->post_date), $c);

	// Asides post
	if ( $post_asides ) {
		$c[] = 'k2-asides';
	}

	// If it's the other to the every, then add 'alt' class
	if ( $post_count & 1 == 1 ) {
		$c[] = 'alt';
	}

	// Separates classes with a single space, collates classes for post DIV
	$c = join(' ', apply_filters('post_class', $c));

	// And tada!
	return $print ? print($c) : $c;
}

// Generates semantic classes for each comment LI element
function k2_comment_class( $comment_count = 1, $print = true ) {
	global $comment, $post;

	// Collects the comment type (comment, trackback),
	$c = array($comment->comment_type);

	// Counts trackbacks (t[n]) or comments (c[n])
	if ($comment->comment_type == 'trackback') {
		$c[] = "t$comment_count";
	} else {
		$c[] = "c$comment_count";
	}

	// If the comment author has an id (registered), then print the display name
	if ( $comment->user_id > 0 ) {
		$user = get_userdata($comment->user_id);

		// For all registered users, 'byuser'; to specificy the registered user, 'comment-author-[display_name]'
		$c[] = "byuser comment-author-" . sanitize_title_with_dashes(strtolower($user->display_name));
		// For comment authors who are the author of the post
		if ( $comment->user_id === $post->post_author )
			$c[] = 'bypostauthor';
	}

	// If it's the other to the every, then add 'alt' class; collects time- and date-based classes
	k2_date_classes(mysql2date('U', $comment->comment_date), $c, 'c-');
	if ( $comment_count & 1 == 1 ) {
		$c[] = 'alt';
	}

	// Separates classes with a single space, collates classes for comment LI
	$c = join(' ', apply_filters('comment_class', $c));

	// Tada again!
	return $print ? print($c) : $c;
}

// Generates time- and date-based classes for BODY, post DIVs, and comment LIs; relative to GMT (UTC)
function k2_date_classes($t, &$c, $p = '') {
	$t = $t + (get_settings('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t); // Year
	$c[] = $p . 'm' . gmdate('m', $t); // Month
	$c[] = $p . 'd' . gmdate('d', $t); // Day
	$c[] = $p . 'h' . gmdate('H', $t); // Hour
}

?>
