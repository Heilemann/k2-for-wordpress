<?php
/**
 * K2 Display Functions.
 *
 * These functions are for displaying content.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

// Prevent users from directly loading this include file
defined( 'K2_CURRENT' ) or die ( __('Error: This file can not be loaded directly.', 'k2') );

function k2_navigation($id = 'nav-above') {
?>

	<div id="<?php echo $id; ?>" class="navigation">

	<?php if ( is_single() ): ?>
		<div class="nav-previous"><?php previous_post_link('%link', '<span class="meta-nav">&laquo;</span> %title'); ?></div>
		<div class="nav-next"><?php next_post_link('%link', '%title <span class="meta-nav">&raquo;</span>'); ?></div>
	<?php else: ?>
		<div class="nav-previous"><?php next_posts_link( __('<span class="meta-nav">&laquo;</span> Older', 'k2') ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __('Newer <span class="meta-nav">&raquo;</span>', 'k2') ); ?></div>
	<?php endif; ?>

	</div>
	<div class="clear"></div>

<?php
}


function k2_permalink_title($echo = true) {
	$output = sprintf( esc_attr__( 'Permalink to %s', 'k2' ), the_title_attribute( 'echo=0' ) );

	if ($echo)
		echo $output;

	return $output;
}


/* By Mark Jaquith, http://txfx.net */
function k2_nice_category($normal_separator = ', ', $penultimate_separator = ' and ') {
	$categories = get_the_category();

	if (empty($categories)) {
		return __('Uncategorized', 'k2');
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

		$thelist .= '<a href="' . get_category_link($category->cat_ID) . '" title="' . sprintf( esc_attr__('View all posts in %s', 'k2'), $category->cat_name ) . '">'.$category->cat_name.'</a>';
		++$i;
	}
	return apply_filters('the_category', $thelist, $normal_separator);
}


function k2_page_css_class_filter( $css_class, $page ) {
	if ( ( 'page' == get_option('show_on_front') ) and ( $page->ID == get_option('page_for_posts') ) )
		$css_class[] = 'poststab';

	return $css_class;
}

add_filter('page_css_class', 'k2_page_css_class_filter', 10, 2);


function k2_page_menu_filter($menu) {
	$css = 'hometab';

	if ( 'page' != get_option('show_on_front') )
		$css .= ' poststab';

	$menu = preg_replace('/(<ul><li class=)"(.+?)"/', '$1"$2 ' . $css . '"', $menu, 1);

	return $menu;
}
add_filter('wp_page_menu', 'k2_page_menu_filter');

/**
 * Filters post content and resizes embedded videos and images to content width.
 */
function k2_resize_embeds( $content ) {
	return str_replace( 'width="560" height="340"></embed>', 'width="500" height="307"></embed>', $content );
/*
	return str_replace( 'width="480" height="295"></embed>', 'width="500" height="307"></embed>', $content );
	return str_replace( 'width="480" height="385"></embed>', 'width="500" height="400"></embed>', $content );
	return str_replace( 'width="425" height="344"></embed>', 'width="500" height="405"></embed>', $content );
	return str_replace( 'width="640" height="385"></embed>', 'width="500" height="307"></embed>', $content );
	return str_replace( 'width="640" height="505"></embed>', 'width="500" height="307"></embed>', $content );
	return str_replace( 'width="853" height="505"></embed>', 'width="500" height="307"></embed>', $content );
	return str_replace( 'width="960" height="745"></embed>', 'width="500" height="307"></embed>', $content );
	return str_replace( 'width="1280" height="745"></embed>', 'width="500" height="307"></embed>', $content );
*/
}

add_filter( 'the_content',	'k2_resize_embeds' );
