<?php

/**
 * K2 Pluggable Functions.
 *
 * These functions can be replaced via styles/plugins. If styles/plugins do
 * not redefine these functions, then these will be used instead.
 *
 * @package K2
 */

// Prevent users from directly loading this include file
defined( 'K2_CURRENT' ) or die ( 'Error: This file can not be loaded directly.' );


/**
 * Displays the current post meta.
 *
 * @since 1.0-RC8
 *
 * @param integer $num Optional. Meta position, 1 for top, 2 for bottom
 *
 */
if ( ! function_exists('k2_entry_meta') ):
	function k2_entry_meta($num = 1) {
		$num = (int) $num;
		if ( $num < 1 ) $num = 1;

		$entrymeta = preg_replace( '/%(.+?)%/', '[entry_$1]', get_option('k2entrymeta' . $num) );

		echo do_shortcode($entrymeta);
	}
endif;


/**
 * Displays the current post date, if time since is installed, it will use that instead.
 * Formatted for hAtom microformat.
 *
 * @since 1.0-RC8
 *
 * @uses time_since
 *
 */
if ( ! function_exists('k2_entry_date') ):
	function k2_entry_date() {
		global $post;

		$output = '<abbr class="published entry-date" title="' . get_the_time('Y-m-d\TH:i:sO') . '">';

		if ( function_exists('time_since') )
			$output .= sprintf( __('%s ago','k2_domain'), time_since( abs( strtotime( $post->post_date_gmt . ' GMT' ) ), time() ) );
		else
			$output .= get_the_time( get_option('date_format') );

		$output .= '</abbr>';

		return $output;
	}
endif;


/**
 * Displays the current post categories
 *
 * @since 1.0-RC8
 *
 * @uses k2_nice_category
 *
 */
if ( ! function_exists('k2_entry_categories') ):
	function k2_entry_categories() {
		return '<span class="entry-categories">' . k2_nice_category(', ', __(' and ','k2_domain')) . '</span>';
	}
endif;


/**
 * Displays the current post author.
 * Formatted for hAtom microformat.
 *
 * @since 1.0-RC8
 *
 */
if ( ! function_exists('k2_entry_author') ):
	function k2_entry_author() {
		return '<span class="vcard author entry-author"><a href="' . get_author_posts_url( get_the_author_ID() ) .
					'" class="url fn" title="' . sprintf( __('View all posts by %s', 'k2_domain'), esc_attr( get_the_author() ) ) .
					'">' . get_the_author() . '</a></span>';
	}
endif;


/**
 * Displays the current post tags or blank if none.
 *
 * @since 1.0-RC8
 *
 */
if ( ! function_exists('k2_entry_tags') ):
function k2_entry_tags() {
	if ( $tags = get_the_tag_list( __('<span>Tags:</span> ','k2_domain'), ', ', '.' ) )
		return '<span class="entry-tags">' . $tags . '</span>';

	return $tags;
}
endif;


/**
 * Displays the number of comments in current post enclosed in a link.
 *
 * @since 1.0-RC8
 *
 */
if ( ! function_exists('k2_entry_comments') ):
	function k2_entry_comments() {
		ob_start();

		comments_popup_link( __('0 <span>Comments</span>', 'k2_domain'), __('1 <span>Comment</span>', 'k2_domain'), __('% <span>Comments</span>', 'k2_domain'), 'commentslink', __('<span>Closed</span>', 'k2_domain') );

		return '<span class="entry-comments">' . ob_get_clean() . '</span>';
	}
endif;


/**
 * Displays the current post time
 *
 * @since 1.0-RC8
 *
 */
if ( ! function_exists('k2_entry_time') ):
	function k2_entry_time() {
		return '<span class="entry-time">' . get_the_time( get_option('time_format') ) . '</span>';
	}
endif;


/**
 * Register our sidebar with widgets
 *
 * @since 1.0-RC8
 *
 */
if ( ! function_exists('k2_register_sidebars') ):
	function k2_register_sidebars() {
		register_sidebars( 2, array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		) );
	}
endif;

/**
 *	Provide page options to wp_get_pages in blocks/k2-header.php
 *
 *	@since 1.0-RC8
 */
if ( ! function_exists('k2_get_page_list_args') ):
function k2_get_page_list_args() {
	$list_args = 'sort_column=menu_order&depth=1&title_li=';
	
	// if a page is used as a front page, exclude it from page list
	if ( get_option('show_on_front') == 'page' )
		$list_args .= '&exclude=' . get_option('page_on_front');
	
	return $list_args;
}
endif;


add_shortcode('entry_author', 'k2_entry_author');
add_shortcode('entry_categories', 'k2_entry_categories');
add_shortcode('entry_comments', 'k2_entry_comments');
add_shortcode('entry_date', 'k2_entry_date');
add_shortcode('entry_tags', 'k2_entry_tags');
add_shortcode('entry_time', 'k2_entry_time');
