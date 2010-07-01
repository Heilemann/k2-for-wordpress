<?php
/**
 * K2 Pluggable Functions.
 *
 * These functions can be replaced via styles/plugins. If styles/plugins do
 * not redefine these functions, then these will be used instead.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

// Prevent users from directly loading this include file
defined( 'K2_CURRENT' ) or die ( __('Error: This file can not be loaded directly.', 'k2') );

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

		$output = '<span class="published entry-date"><span class="value-title" title="' . get_the_time('Y-m-d\TH:i:sO') . '"> </span>';

		if ( function_exists('time_since') )
			$output .= sprintf( __('%s ago', 'k2'), time_since( abs( strtotime( $post->post_date_gmt . ' GMT' ) ), time() ) );
		else
			$output .= get_the_time( get_option('date_format') );

		$output .= '</span>';

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
		return '<span class="entry-categories">' . k2_nice_category(', ', __(' and ', 'k2')) . '</span>';
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
		return '<span class="vcard author entry-author"><a href="' . get_author_posts_url( get_the_author_meta('ID') ) .
					'" class="url fn" title="' . sprintf( esc_attr__('View all posts by %s', 'k2'), get_the_author() ) .
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
	if ( $tags = get_the_tag_list( __('<span>Tags:</span> ', 'k2'), ', ', '.' ) )
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

		comments_popup_link( __('0 <span>Comments</span>', 'k2'), __('1 <span>Comment</span>', 'k2'), _n('% <span>Comment</span>', '% <span>Comments</span>', get_comments_number(), 'k2'), 'commentslink', __('<span>Closed</span>', 'k2') );

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
		if ( function_exists('register_sidebar') ) {

		    register_sidebar(array(
		        'id' 		=>	'widgets-top',
		        'name'		=>	__('Below Page Header', 'k2'),
		        'description'	=>	__('Just below the header.', 'k2'),
		        'before_widget'	=>	'<div><div id="%1$s" class="widget %2$s">', // <span>'s is used for horizontal sizing, <div> for padding
		        'after_widget'	=>	'</div></div>',
		        'before_title'	=>	'<h4 class="widgettitle">',
		        'after_title'	=>	'</h4>'
		    ));

		    register_sidebar(array(
		        'id'		=>	'widgets-sidebar-1',
		        'name'		=>	__('Left Sidebar', 'k2'),
		        'before_widget'	=>	'<div id="%1$s" class="widget %2$s">',
		        'after_widget'	=>	'</div>',
		        'before_title'	=>	'<h4 class="widgettitle">',
		        'after_title'	=>	'</h4>'
		    ));

		    register_sidebar(array(
		        'id'		=>	'widgets-sidebar-2',
		        'name'		=>	__('Right Sidebar', 'k2'),
		        'before_widget'	=>	'<div id="%1$s" class="widget %2$s">',
		        'after_widget'	=>	'</div>',
		        'before_title'	=>	'<h4 class="widgettitle">',
		        'after_title'	=>	'</h4>'
		    ));

		    register_sidebar(array(
		        'id' 		=>	'widgetspost',
		        'name'		=>	__('After Posts', 'k2'),
		        'description'	=>	__('On single blog post pages, following the post and preceeding the comments.', 'k2'),
		        'before_widget'	=>	'<div id="%1$s" class="widget %2$s">',
		        'after_widget'	=>	'</div>',
		        'before_title'	=>	'<h4 class="widgettitle">',
		        'after_title'	=>	'</h4>'
		    ));

		    register_sidebar(array(
		        'id' 		=>	'widgets-bottom',
		        'name'		=>	__('Page Footer', 'k2'),
		        'description'	=>	__('At the bottom of the page.', 'k2'),
		        'before_widget'	=>	'<div id="%1$s" class="widget %2$s">',
		        'after_widget'	=>	'</div>',
		        'before_title'	=>	'<h4 class="widgettitle">',
		        'after_title'	=>	'</h4>'
		    ));
		}
	}
endif;


add_shortcode('entry_author', 'k2_entry_author');
add_shortcode('entry_categories', 'k2_entry_categories');
add_shortcode('entry_comments', 'k2_entry_comments');
add_shortcode('entry_date', 'k2_entry_date');
add_shortcode('entry_tags', 'k2_entry_tags');
add_shortcode('entry_time', 'k2_entry_time');


/**
 * Template for comments and pingbacks / trackbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own k2_comment_type_switch(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since K2 1.1
 *
 * @param object $comment Comment data object.
 * @param array $args
 * @param int $depth Depth of comment in reference to parents.
 */

if ( ! function_exists('k2_comment_type_switch') ) :
	function k2_comment_type_switch( $comment, $args, $depth = 1 ) {
		$GLOBALS['comment'] = $comment;
		switch( $comment->comment_type ) :
			case '' :
		?>
		<li id="comment-<?php comment_ID(); ?>">
			<div <?php comment_class(); ?>>
				<div class="comment-head">
				<?php if ( get_option('show_avatars') ) : ?>
					<span class="gravatar"><?php echo get_avatar( $comment, 32 ); ?></span>
				<?php endif; ?>
					<span class="comment-author"><?php comment_author_link(); ?></span>

					<div class="comment-meta">
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" title="<?php _e('Permalink to this Comment', 'k2'); ?>">
						<?php
						if ( function_exists('time_since') ):
							printf( __('%s ago.', 'k2'), time_since( abs( strtotime($comment->comment_date_gmt . ' GMT') ), time() ) );
						else :
							/* translators: 1: comment date, 2: comment time */
							printf( __('%1$s at %2$s', 'k2'), get_comment_date(), get_comment_time() );
						endif;
						?>
						</a>
					</div> <!-- .comment-meta -->

				</div> <!-- .comment-head -->

				<div class="comment-content">
				<?php if ( $comment->comment_approved == '0' ): ?>
					<p class="comment-moderation alert"><?php _e('Your comment is awaiting moderation.', 'k2'); ?></p>
				<?php endif; ?>

					<?php comment_text(); ?>
				</div> <!-- .comment-content -->

				<div class="buttons">
					<?php if ( function_exists('quoter_comment') ) quoter_comment(); ?>

					<?php
					if ( function_exists('jal_edit_comment_link') ) :
						jal_edit_comment_link(__('Edit', 'k2'), '<span class="comment-edit">','</span>', '<em>(Editing)</em>');
					else :
						edit_comment_link(__('Edit', 'k2'), '<span class="comment-edit">', '</span>');
					endif;
					?>

					<div id="comment-reply-<?php comment_ID(); ?>" class="comment-reply">
						<?php comment_reply_link(array_merge( $args, array('add_below' => 'comment-reply', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
					</div> <!-- .comment-reply -->

				</div> <!-- .buttons -->

			</div> <!-- .comment -->
		<?php
			break;
			case 'pingback'  :
			case 'trackback' :
		?>
		<li id="comment-<?php comment_ID(); ?>">
			<div <?php comment_class(); ?>>

				<?php if ( function_exists('comment_favicon') ) : ?>
					<span class="favatar"><?php comment_favicon(); ?></span>
				<?php endif; ?>

					<span class="comment-author"><?php comment_author_link(); ?></span>

					<div class="comment-meta">
					<?php
					/* translators: 1: comment type (Pingback or Trackback), 2: datetime */
					printf( __('%1$s on %2$s', 'k2'),
						( $comment->comment_type == 'pingback' ) ? '<span class="pingback">' . __('Pingback', 'k2') . '</span>' : '<span class="trackback">' . __('Trackback', 'k2') . '</span>',
						sprintf('<a href="%1$s" title="%2$s">%3$s</a>',
							esc_url( get_comment_link( $comment->comment_ID ) ),
							(function_exists('time_since') ?
								sprintf( esc_attr__('%s ago.', 'k2'),
									time_since( abs( strtotime($comment->comment_date_gmt . " GMT") ), time() )
								) :
								esc_attr__('Permalink to this Comment', 'k2')
							),
							/* translators: 1: comment date, 2: comment time */
							sprintf( __('%1$s at %2$s', 'k2'),
								/* translators: comment type (Pingback or Trackback) date format (example: Jul 1st, 2010), see http://php.net/date */
								get_comment_date( __('M jS, Y', 'k2') ),
								get_comment_time()
							)
						)
					);
						edit_comment_link( __('Edit', 'k2'), '<span class="comment-edit">', '</span>' );
					?>
					</div> <!-- .comment-meta -->

			</div> <!-- .comment -->
		<?php
			break;
		endswitch;
	}
endif;
