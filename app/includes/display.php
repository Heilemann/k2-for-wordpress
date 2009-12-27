<?php

/**
 * K2 Display Functions.
 *
 * These functions are for displaying content
 *
 * @package K2
 */

// Prevent users from directly loading this include file
defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );


function k2_navigation($id = 'nav-above') {
?>

	<div id="<?php echo $id; ?>" class="navigation">

	<?php if ( is_single() ): ?>
		<div class="nav-previous"><?php previous_post_link('%link', '<span class="meta-nav">&laquo;</span> %title') ?></div>
		<div class="nav-next"><?php next_post_link('%link', '%title <span class="meta-nav">&raquo;</span>') ?></div>
	<?php else: ?>
		<?php $_SERVER['REQUEST_URI']  = preg_replace("/(.*?).php(.*?)&(.*?)&(.*?)&_=/","$2$3",$_SERVER['REQUEST_URI']); ?>
		<div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&laquo;</span> ' . __('Older','k2_domain') ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __('Newer','k2_domain').' <span class="meta-nav">&raquo;</span>' ); ?></div>
	<?php endif; ?>

		<div class="clear"></div>
	</div>

<?php
}


function k2_asides_permalink($content) {
	if ( in_category( get_option('k2asidescategory') ) and ! is_singular() )
		$content .= '<a href="' . get_permalink() . '" rel="bookmark" class="asides-permalink" title="' . k2_permalink_title(false) . '">(' . get_comments_number() . ')</a>';

	return $content;
}

add_filter('the_content', 'k2_asides_permalink');


function k2_permalink_title($echo = true) {
	$output = sprintf( __('Permanent Link to %s','k2_domain'), esc_html( strip_tags( the_title('', '', false) ), 1) );
	
	if ($echo)
		echo $output;

	return $output;
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
