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
		<div class="nav-previous"><?php next_posts_link( '<span class="meta-nav">&laquo;</span> ' . __('Older Entries','k2_domain') ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __('Newer Entries','k2_domain').' <span class="meta-nav">&raquo;</span>' ); ?></div>
	<?php endif; ?>

		<div class="clear"></div>
	</div>

<?php
}



function k2_style_footer() {
	if ( get_k2info('style_footer') != '' ):
	?>
	<p class="footerstyledwith">
		<?php k2info('style_footer'); ?>
	</p>
<?php
	endif;
}

add_action('template_footer', 'k2_style_footer');



function k2_asides_permalink($content) {
	if ( in_category( get_option('k2asidescategory') ) and ! is_singular() )
		$content .= '<a href="' . get_permalink() . '" rel="bookmark" class="asides-permalink" title="' . k2_permalink_title(false) . '">(' . get_comments_number() . ')</a>';

	return $content;
}

add_filter('the_content', 'k2_asides_permalink');


function k2_permalink_title($echo = true) {
	$output = sprintf( __('Permanent Link to %s','k2_domain'), wp_specialchars( strip_tags( the_title('', '', false) ), 1) );
	
	if ($echo)
		echo $output;

	return $output;
}


function k2_entry_meta($num = 1) {
	$num = (int) $num;
	if ( $num < 1 ) $num = 1;

	$meta_format = apply_filters( 'k2_entry_meta_format', get_option('k2entrymeta' . $num) );

	// No keywords to replace
	if ( strpos($meta_format, '%' ) === false ) {
		echo $meta_format;
	} else {

		// separate the %keywords%
		$meta_array = preg_split('/(%.+?%)/', $meta_format, -1, PREG_SPLIT_DELIM_CAPTURE);

		// parse through the keywords
		foreach ($meta_array as $key => $str) {
			switch ($str) {
				case '%author%':
					$meta_array[$key] = k2_entry_author();
					break;

				case '%categories%':
					$meta_array[$key] = k2_entry_categories();
					break;

				case '%comments%':
					$meta_array[$key] = k2_entry_comments();
					break;

				case '%date%':
					$meta_array[$key] = k2_entry_date();
					break;

				case '%time%':
					$meta_array[$key] = k2_entry_time();
					break;

				case '%tags%':
					$meta_array[$key] = k2_entry_tags();
					break;
			}
		}

		// output the result
		echo implode('', $meta_array);
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
