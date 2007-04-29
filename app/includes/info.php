<?php

function k2info($show='') {
	echo get_k2info($show);
}

function get_k2info($show='') {
	global $current;

	$output = '';
	switch ($show) {
		case 'version' :
    		$output = 'Beta Two '. $current;
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

	if (preg_match('/^http:\/\/[^\/]+(.+)/', $template_url, $url_parts)) {
		$output = 'window.location.href.match(/^(http:\\/\\/[^\\/]+)/)[1] + "' . $url_parts[1] .'/'. $file . '"';

	// This should never be executed, but it's well to be on the safe side
	} else {
		$output = $template_url .'/'. $file;
	}

	echo $output;
}

function output_javascript_array($array) {
	$output = 'new Array(';

	if ( is_array($array) ) {
		$last_item = array_pop($array);
		foreach ($array as $item) {
			$output .= '"' . $item . '", ';
		}
		$output .= '"' . $last_item . '"';
	}

	$output .= ')';

	echo $output;
}

function output_javascript_hash($array) {
	$output = 'new Hash({';

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

	$output .= '})';

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
		_e('Uncategorized','k2_domain'); 
		return; 
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
		(function_exists('is_active_widget') and is_active_widget('k2_widget_asides'))
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
		echo "id='" . get_query_var('name') . "'";
	}
}


// Semantic class functions from Sandbox 0.6.1 (http://www.plaintxt.org/themes/sandbox/)

// Template tag: echoes semantic classes in the <body>
function k2_body_class() {
	global $wp_query, $current_user;

	$c = array('wordpress', 'k2');

	k2_date_classes(time(), $c);

	is_home()       ? $c[] = 'home'       : null;
	is_archive()    ? $c[] = 'archive'    : null;
	is_date()       ? $c[] = 'date'       : null;
	is_search()     ? $c[] = 'search'     : null;
	is_paged()      ? $c[] = 'paged'      : null;
	is_attachment() ? $c[] = 'attachment' : null;
	is_404()        ? $c[] = 'four04'     : null; // CSS does not allow a digit as first character

	if ( is_single() ) {
		the_post();
		$c[] = 'single';
		if ( isset($wp_query->post->post_date) ) {
			k2_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');
		}
		foreach ( (array) get_the_category() as $cat ) {
			$c[] = 's-category-' . $cat->category_nicename;
		}
		$c[] = 's-author-' . get_the_author_login();
		rewind_posts();
	}

	else if ( is_author() ) {
		$author = $wp_query->get_queried_object();
		$c[] = 'author';
		$c[] = 'author-' . $author->user_nicename;
	}

	else if ( is_category() ) {
		$cat = $wp_query->get_queried_object();
		$c[] = 'category';
		$c[] = 'category-' . $cat->category_nicename;
	}

	else if ( is_page() ) {
		the_post();
		$c[] = 'page';
		$c[] = 'page-author-' . get_the_author_login();
		rewind_posts();
	}

	if ( $current_user->ID )
		$c[] = 'loggedin';

	if (function_exists('dynamic_sidebar')) {
		switch (get_option('k2sidebarnumber')) {
			case '0':
				$c[] = 'sidebar-none';
				break;
			case '1':
				$c[] = 'sidebar-single';
				break;
			case '2':
				$c[] = 'sidebar-dual';
				break;
		}
	} else {
		$c[] = 'sidebar-single';
	}

	echo join(' ', apply_filters('body_class',  $c));
}

// Template tag: echoes semantic classes in each post <div>
function k2_post_class( $post_count = 1, $post_asides = false ) {
	global $post;

	$c = array('hentry', "p$post_count", $post->post_type, $post->post_status);

	$c[] = 'author-' . get_the_author_login();

	foreach ( (array) get_the_category() as $cat ) {
		$c[] = 'category-' . $cat->category_nicename;
	}

	k2_date_classes(mysql2date('U', $post->post_date), $c);

	if ( $post_asides ) {
		$c[] = 'k2-asides';
	}

	if ( $post_count & 1 == 1 ) {
		$c[] = 'alt';
	}

	echo join(' ', apply_filters('post_class', $c));
}

// Template tag: echoes semantic classes for a comment <li>
function k2_comment_class( $comment_count = 1 ) {
	global $comment, $post;

	$c = array($comment->comment_type, "c$comment_count");

	if ( $comment->user_id > 0 ) {
		$user = get_userdata($comment->user_id);

		$c[] = "byuser commentauthor-$user->user_login";

		if ( $comment->user_id === $post->post_author ) {
			$c[] = 'bypostauthor';
		}
	}

	k2_date_classes(mysql2date('U', $comment->comment_date), $c, 'c-');

	if ( $comment_count & 1 == 1 ) {
		$c[] = 'alt';
	}
		
	if ( is_trackback() ) {
		$c[] = 'trackback';
	}

	echo join(' ', apply_filters('comment_class', $c));
}

// Adds four time- and date-based classes to an array
// with all times relative to GMT (sometimes called UTC)
function k2_date_classes($t, &$c, $p = '') {
	$t = $t + (get_settings('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t); // Year
	$c[] = $p . 'm' . gmdate('m', $t); // Month
	$c[] = $p . 'd' . gmdate('d', $t); // Day
	$c[] = $p . 'h' . gmdate('h', $t); // Hour
}

?>
