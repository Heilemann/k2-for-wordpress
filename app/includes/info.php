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
				$output = get_bloginfo('wpurl') . '/' . get_option('k2style');
			break;

		case 'style_footer' :
			$styleinfo = get_option('k2styleinfo');
			$output = stripslashes($styleinfo['footer']);
			break;

		case 'styles_url' :
			$output = get_bloginfo('wpurl') . '/' . str_replace(ABSPATH, '', K2_STYLES_PATH);
			break;

		case 'headers_url' :
			$output = get_bloginfo('wpurl') . '/' . str_replace(ABSPATH, '', K2_HEADERS_PATH);
			break;

		case 'current_style_dir' :
			if ( get_option('k2style') != '' )
				$output = ABSPATH . dirname( get_option('k2style') );
			break;

		case 'current_style_url' :
			if ( get_option('k2style') != '' )
				$output = get_bloginfo('wpurl') . '/' . dirname( get_option('k2style') );
			break;
	}
	return $output;
}


function k2_parse_query($query) {
	if ( is_array($query) and !empty($query) ) {
		$valid_keys = array(
			'error'
			, 's'
			, 'exact'
			, 'search_terms'
			, 'sentence'

			, 'subpost'
			, 'subpost_id'
			, 'attachment'
			, 'attachment_id'

			, 'p'
			, 'name'
			, 'static'
			, 'pagename'
			, 'page_id'

			, 'author'
			, 'author_name'
			, 'feed'
			, 'tb'
			, 'paged'
			, 'comments_popup'
			, 'preview'
			, 'withcomments'
			, 'withoutcomments'
			, 'post_status'
			, 'post_type'

			, 'hour'
			, 'minute'
			, 'second'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'w'
			, 'm'

			, 'cat'
			, 'category_name'
			, 'category__in'
			, 'category__not_in'
			, 'category__and'

			, 'tag'
			, 'tag_id'
			, 'tag__in'
			, 'tag__not_in'
			, 'tag__and'
			, 'tag_slug__in'
			, 'tag_slug__and'

			, 'k2dynamic'
			, 'k2debug'
		);

		foreach ($query as $key => $value) {
			if ( ! in_array($key, $valid_keys) ) {
				unset($query[$key]);
			}
		}

		// Parse the advance search operators
		if ( !empty($query['s']) ) {

			//preg_match_all('/"(.*?)"/', $query['s'], $_quotes);
			//$search_terms = explode(' ', preg_replace( '/".*?"/', '', $query['s'] ));

			$search_terms = explode(' ', $query['s']);

			foreach ($search_terms as $key => $term) {

				// operators:
				if ( strpos($term, ':') !== false ) {
					$operation = explode(':', $term, 2);

					switch( $operation[0] ) {

						/*case 'cat':
						case 'category':
							if ( isset($query['cat']) ) {
								$query['cat'] = $query['cat'] . ',' . get_cat_ID($operation[1]);
							} else {
								$query['cat'] = get_cat_ID($operation[1]);
							}
							break;
						*/

						case 'tag':
							if ( isset($query['tag']) ) {
								$query['tag'] .= ',' . $operation[1];
							} else {
								$query['tag'] = $operation[1];
							}
							break;

						case 'month':
							$query['monthnum'] = $operation[1];
							break;

						case 'author':
							$query['author_name'] = $operation[1];
							break;
					}

					unset($search_terms[$key]);
				}
			}

			$query['s'] = implode($search_terms);
		}

	}

	return $query;
}

function update_style_info() {
	$data = get_style_data( get_option('k2style') );

	if ( !empty($data) and ($data['stylename'] != '') and ($data['stylelink'] != '') and ($data['author'] != '') ) {
		// No custom style info
		if ( $data['footer'] == '' ) {
			$data['footer'] = __('Styled with <a href="%stylelink%" title="%style% by %author%">%style%</a>','k2_domain');
		}

		if ( strpos($data['footer'], '%') !== false ) {
			$data['footer'] = str_replace("%author%", $data['author'], $data['footer']);
			$data['footer'] = str_replace("%site%", $data['site'], $data['footer']);
			$data['footer'] = str_replace("%style%", $data['stylename'], $data['footer']);
			$data['footer'] = str_replace("%stylelink%", $data['stylelink'], $data['footer']);
			$data['footer'] = str_replace("%version%", $data['version'], $data['footer']);
			$data['footer'] = str_replace("%comments%", $data['comments'], $data['footer']);
		}
	}

	update_option('k2styleinfo', $data);	

	return $data;
}

function get_style_data( $style_file = '' ) {
	// if no style selected, exit
	if ( '' == $style_file )
		return false;

	$style_file = ABSPATH . $style_file;

	if ( !is_readable($style_file) )
		return false;

	$style_data = implode( '', file($style_file) );
	$style_data = str_replace( '\r', '\n', $style_data );

	// parse the data
	preg_match("|Author Name\s*:(.*)$|mi", $style_data, $author);
	preg_match("|Author Site\s*:(.*)$|mi", $style_data, $site);
	preg_match("|Style Name\s*:(.*)$|mi", $style_data, $stylename);
	preg_match("|Style URI\s*:(.*)$|mi", $style_data, $stylelink);
	preg_match("|Style Footer\s*:(.*)$|mi", $style_data, $footer);
	preg_match("|Version\s*:(.*)$|mi", $style_data, $version);
	preg_match("|Comments\s*:(.*)$|mi", $style_data, $comments);
	preg_match("|Header Text Color\s*:\s*#*([\dABCDEF]+)|i", $style_data, $header_text_color);
	preg_match("|Header Width\s*:\s*(\d+)|i", $style_data, $header_width);
	preg_match("|Header Height\s*:\s*(\d+)|i", $style_data, $header_height);
	preg_match("|Layout Widths\s*:\s*(\d+)\s*(px)?,\s*(\d+)\s*(px)?,\s*(\d+)|i", $style_data, $widths);

	$layout_widths = array();
	if ( !empty($widths) ) {
		$layout_widths[1] = $widths[1];
		$layout_widths[2] = $widths[3];
		$layout_widths[3] = $widths[5];
	}

	return array(
		'path' => str_replace(ABSPATH, '', $style_file),
		'modified' => filemtime($style_file),
		'author' => trim($author[1]),
		'site' => clean_url( trim($site[1]) ),
		'stylename' => trim($stylename[1]),
		'stylelink' => clean_url( trim($stylelink[1]) ),
		'footer' => trim($footer[1]),
		'version' => trim($version[1]),
		'comments' => trim($comments[1]),
		'header_text_color' => $header_text_color[1],
		'header_width' => $header_width[1],
		'header_height' => $header_height[1],
		'layout_widths' => $layout_widths
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

	$per_page = intval(get_query_var('posts_per_page'));
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

function k2_post_groupby($groupby) {
	// Only filter when asides_module is active
	if ( is_home() and function_exists('is_active_module') and is_active_module('asides_sidebar_module') ) {
		return '';
	}

	return $groupby;
}

add_filter('posts_groupby', 'k2_post_groupby');

function k2_asides_filter($query) {
	global $k2sbm_current_module;

	$asides = get_option('k2asidescategory');

	// Only filter when it's in the homepage
	if ( ($asides != 0) and ($query->is_home) and (!$k2sbm_current_module) and function_exists('is_active_module') and is_active_module('asides_sidebar_module') ) {

		if ( get_wp_version() > 2.2 ) {
			
			$exclude_cats = $query->get('category__not_in');
			$include_cats = $query->get('category__in');

			// Remove asides from list of categories to include
			if ( !empty($include_cats) and in_array($asides, $include_cats) ) {
				$query->set( 'category__in', array_diff( $include_cats, array($asides) ) );
			}

			// Insert asides into list of categories to exclude
			if ( empty($exclude_cats) ) {
				$query->set( 'category__not_in', array($asides) );
			} else if ( !in_array( $asides, $exclude_cats ) ) {
				$query->set( 'category__not_in', array_merge( $exclude_cats, array($asides) ) );
			}

		} else {

			$priorcat = $query->get('cat');
			if ( !empty($priorcat) ) {
				$priorcat .= ',';
			}

			$query->set('cat', $priorcat . '-' . $asides);
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


// WordPress Compatibility Functions


/**
 * is_front_page() - Is it the front of the site, whether blog view or a WP Page?
 *
 * @since 2.5
 * @uses is_home
 * @uses get_option
 *
 * @return bool True if front of site
 */
if ( ! function_exists('is_front_page') ) {
	function is_front_page () {
		// most likely case
		if ( 'posts' == get_option('show_on_front') && is_home() )
			return true;
		elseif ( 'page' == get_option('show_on_front') && get_option('page_on_front') && is_page(get_option('page_on_front')) )
			return true;
		else
			return false;
	}
}



// Semantic class functions from Sandbox (http://www.plaintxt.org/themes/sandbox/)

// Generates semantic classes for BODY element
function k2_body_class( $print = true ) {
	global $wp_query, $current_user;
	
	$c = array('wordpress', 'k2');

	// Applies the time- and date-based classes (below) to BODY element
	k2_date_classes(time(), $c);

	// Generic semantic classes for what type of content is displayed

	if ( function_exists('is_front_page') ) {
		is_front_page()  ? $c[] = 'home'       : null;
		is_home()        ? $c[] = 'blog'       : null;
	} else {
		is_home()        ? $c[] = 'home'       : null;
	}

	is_archive()         ? $c[] = 'archive'    : null;
	is_date()            ? $c[] = 'date'       : null;
	is_search()          ? $c[] = 'search'     : null;
	is_paged()           ? $c[] = 'paged'      : null;
	is_attachment()      ? $c[] = 'attachment' : null;
	is_404()             ? $c[] = 'four04'     : null; // CSS does not allow a digit as first character

	// Special classes for BODY element when a single post
	if ( is_single() ) {
		$postID = $wp_query->post->ID;
		the_post();

		// Adds 'single' class and class with the post ID
		$c[] = 'single postid-' . $postID . ' s-slug-' . $wp_query->post->post_name;

		// Adds classes for the month, day, and hour when the post was published
		if ( isset($wp_query->post->post_date) )
			k2_date_classes(mysql2date('U', $wp_query->post->post_date), $c, 's-');

		// Adds category classes for each category on single posts
		if ( $cats = get_the_category() )
			foreach ( $cats as $cat )
				$c[] = 's-category-' . $cat->category_nicename;

		// Adds tag classes for each tags on single posts
		if ( function_exists('get_the_tags') )
			if ( $tags = get_the_tags() )
				foreach ( $tags as $tag )
					$c[] = 's-tag-' . $tag->slug;

		// Adds MIME-specific classes for attachments
		if ( is_attachment() ) {
			$the_mime = get_post_mime_type();
			$boring_stuff = array('application/', 'image/', 'text/', 'audio/', 'video/', 'music/');
			$c[] = 'attachment-' . str_replace($boring_stuff, '', $the_mime);
		}

		// Adds author class for the post author
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

	// Tag name classes for BODY on tag archives
	else if ( function_exists('is_tag') and is_tag() ) {
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

	// Separates classes with a single space, collates classes for BODY
	$c = join( ' ', attribute_escape( apply_filters('body_class', $c) ) );

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

	// Tags for the post queried
	if ( function_exists('get_the_tags') ) {
		$tags = get_the_tags();

		if ( !empty($tags) ) {
			foreach ( $tags as $tag ) {
				$c[] = 'tag-' . $tag->slug;
			}
		}
	}

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
	$c = join( ' ', attribute_escape( apply_filters('post_class', $c) ) );

	// And tada!
	return $print ? print($c) : $c;
}

// Generates semantic classes for each comment LI element
function k2_comment_class( $comment_count = 1, $print = true ) {
	global $comment, $post;

	// Collects the comment type (comment, trackback),
	$c = array($comment->comment_type);

	// Counts trackbacks (t[n]) or comments (c[n])
	if ($comment->comment_type == 'comment') {
		$c[] = "c$comment_count";
	} else {
		$c[] = "t$comment_count";
	}

	// If the comment author has an id (registered), then print the display name
	if ( $comment->user_id > 0 ) {
		$user = get_userdata($comment->user_id);

		// For all registered users, 'byuser'; to specify the registered user, 'comment-author-[display_name]'
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
	$c = join( ' ', attribute_escape( apply_filters('comment_class', $c) ) );

	// Tada again!
	return $print ? print($c) : $c;
}

// Generates time- and date-based classes for BODY, post DIVs, and comment LIs; relative to GMT (UTC)
function k2_date_classes($t, &$c, $p = '') {
	$t = $t + (get_option('gmt_offset') * 3600);
	$c[] = $p . 'y' . gmdate('Y', $t); // Year
	$c[] = $p . 'm' . gmdate('m', $t); // Month
	$c[] = $p . 'd' . gmdate('d', $t); // Day
	$c[] = $p . 'h' . gmdate('H', $t); // Hour
}

?>
