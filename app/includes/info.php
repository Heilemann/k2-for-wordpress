<?php
/**
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

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

/*
		case 'style_footer' :
			if ( K2_STYLES ) {
				$styleinfo = get_option('k2styleinfo');
				if ( !empty($styleinfo['footer']) )
					$output = stripslashes($styleinfo['footer']);
			}
			break;
*/

/*
		case 'styles_url' :
			if ( K2_STYLES )
				$output = K2Styles::get_styles_url();
			break;
*/

		case 'headers_url' :
			$output = K2_HEADERS_URL . '/';
			break;
	}
	return $output;
}


/**
 * Initializes Rolling Archives and LiveSearch
 */
function k2_init_advanced_navigation() {
	global $wp_scripts;

	$rolling_state = k2_get_rolling_archives_state();
?>
<script type="text/javascript">
//<![CDATA[

	//  Set in motion all of K2's AJAX hotness (RA and LS).
	function initK2() {
		K2.AjaxURL	= "<?php bloginfo('url'); ?>/" // For our AJAX calls
		K2.Animations	= <?php echo (int) get_option('k2animations') ?> // Fetch the animations option

		// Insert the Rolling Archives UI and init.
		K2.RollingArchives = new RollingArchives({
			content:	".content",
			posts:		".content .post",
			parent:		".primary",
			pagetext:	"<?php /* translators: Page X of Y */ echo esc_js( __('of', 'k2') ); ?>",
			older:		"<?php echo esc_js( __('Older', 'k2') ); ?>",
			newer:		"<?php echo esc_js( __('Newer', 'k2') ); ?>",
			loading:	"<?php echo esc_js( __('Loading', 'k2') ); ?>",
			offsetTop:	50,
			pagenumber:	<?php echo $rolling_state['curpage']; ?>,
			pagecount:	<?php echo $rolling_state['maxpage']; ?>,
			query:		<?php echo json_encode( $rolling_state['query'] ); ?>,
			pagedates:	<?php echo json_encode( $rolling_state['pagedates'] ); ?>,
			search:		"<?php echo esc_js( __('Search','k2') ); ?>"
		});

		K2.LiveSearch	= new LiveSearch( RA.search || 'Search' );

		 // Looks for fragment changes
		jQuery(window).bind( 'hashchange', K2.parseFragments );

		// Parse and execute waiting fragments.
		jQuery(window).trigger( 'hashchange' );

		<?php /* JS to run after jQuery Ajax calls */ if ( get_option('k2ajaxdonejs') != '' ): ?>
		jQuery('.content').ajaxComplete(function () {
			<?php echo get_option('k2ajaxdonejs'); ?>
		});
		<?php endif; ?>
	}

	// Make ready K2's sub-systems
	jQuery(document).ready( function() { initK2(); });
//]]>
</script>
<?php
} // End Init_Scripts()

// Is advanced navigation enabled?
if ( get_option('k2advnav') != '0')
	add_action( 'wp_head', 'k2_init_advanced_navigation' );

/**
 * Helper function used by RollingArchives
 */
function k2_get_rolling_archives_state() {
	global $wp_query;

	$rolling_state = array(
			'curpage' => 1,
			'maxpage' => (int) $wp_query->max_num_pages,
			'query' => array(),
			'pagedates' => array()
		);

	// Get the query
	if ( is_array($wp_query->query) )
		$rolling_state['query'] = $wp_query->query;
	elseif ( is_string($wp_query->query) )
		parse_str($wp_query->query, $rolling_state['query']);

	// Future content will be dynamic.
	$rolling_state['query']['k2dynamic'] = 1;

	// Get list of page dates
	if ( !is_page() and !is_single() )
		$rolling_state['pagedates'] = k2_get_rolling_archives_dates($wp_query);

	// Get the current page
	$rolling_state['curpage'] = intval( get_query_var('paged') );
	if ( $rolling_state['curpage'] < 1 )
		$rolling_state['curpage'] = 1;

	return $rolling_state;
}


function k2_get_rolling_archives_dates($query) {
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

	switch ( get_option('k2usestyle') ) {
		case 0: // No CSS
			$classes[] = 'nok2css';
			break;
		case 1: // Sidebars Left
			$classes[] = 'sidebarsleft';
			break;
		case 2: // Sidebars Right
			$classes[] = 'sidebarsright';
			break;
		case 3: // Flanking Sidebars
			$classes[] = 'flankingsidebars';
			break;
	}

	// If animations are turned on
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
		$lang_array = explode( '_', $locale );
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
