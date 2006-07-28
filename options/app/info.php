<?php

function k2info($show='') {
        $info = get_k2info($show);
        echo $info;
}

function get_k2info($show='') {
global $current;
	switch($show) {
		case 'version' :
    			$output = 'Beta Two '. $current;
			break;
		case 'scheme' :
			$output = bloginfo('template_url') . '/styles/' . get_option('k2scheme');
			break;
	}
	return $output;
}


function k2_style_info() {
	$style_info = get_option('k2styleinfo');
	echo stripslashes($style_info);
}

function k2styleinfo_update() {
	$style_info = '';
	$data = k2styleinfo_parse( get_option('k2scheme') );

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
	$data = k2styleinfo_parse( get_option('k2scheme') );

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

function k2styleinfo_parse($style_file = '') {
	// if no style selected, exit
	if ( '' == $style_file ) {
		return;
	}

	$style_path = TEMPLATEPATH . '/styles/' . $style_file;
	if (!file_exists($style_path)) return;
	$style_data = implode( '', file( $style_path ) );
	
	// parse the data
	preg_match("|Author Name\s*:(.*)|i", $style_data, $author);
	preg_match("|Author Site\s*:(.*)|i", $style_data, $site);
	preg_match("|Style Name\s*:(.*)|i", $style_data, $style);
	preg_match("|Style URI\s*:(.*)|i", $style_data, $stylelink);
	preg_match("|Version\s*:(.*)|i", $style_data, $version);
	preg_match("|Comments\s*:(.*)|i", $style_data, $comments);

	return array('style' => trim($style[1]), 'stylelink' => trim($stylelink[1]), 'author' => trim($author[1]), 'site' => trim($site[1]), 'version' => trim($version[1]), 'comments' => trim($comments[1]));
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

function k2countposts() {
	global $wpdb;

	$k2asidescategory = get_option('k2asidescategory');
	$k2asidesposition = get_option('k2asidesposition');

	$join = '';
	$where = "1=1 AND post_status = 'publish'";

	if (($k2asidescategory != '0') and ($k2asidesposition == '1') and ($_GET["dynamic"] != '1')) {
		$join = " LEFT JOIN $wpdb->post2cat ON ($wpdb->posts.ID = $wpdb->post2cat.post_id) ";
		$where .= " AND (category_id != $k2asidescategory)";
	}

	$query = "SELECT DISTINCT $wpdb->posts.ID FROM $wpdb->posts $join WHERE $where GROUP BY $wpdb->posts.ID";

	return (int) count($wpdb->get_col($query));
}


function k2countpages() {
	return (int) ceil(k2countposts() / get_settings('posts_per_page'));
}


function k2countsearchposts($uriquery = '') {
	$search_count = 0;

	$search = new WP_Query($uriquery.'&showposts=-1');
	if ($search->have_posts()) {
		while($search->have_posts()) {
			$search->the_post();
			$search_count++;
		}
	}

	return (int) $search_count;
}


function k2countsearchpages($uriquery) {
	return (int) ceil(k2countsearchposts($uriquery) / get_settings('posts_per_page'));
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
            $category->cat_name = $category->cat_name; 
                if (1 < $i && $i != $n) $thelist .= $normal_separator; 
                if (1 < $i && $i == $n) $thelist .= $penultimate_separator; 
            $thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf(__("View all posts in %s"), $category->cat_name) . '">'.$category->cat_name.'</a>'; 
                     ++$i; 
        } 
    return apply_filters('the_category', $thelist, $normal_separator); 
}
?>