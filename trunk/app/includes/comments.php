<?php
/**
 * Comment template functions
 *
 * @package K2
 */

/**
 * Updates comment count to only include comments
 *
 * @since 1.0
 * @global int $id The current post id
 *
 * @param int $count Current number of comments/pings of current post
 *
 * @return int The number of comments only
 */
function k2_comment_count( $count ) {
	global $id;

	if ($count == 0) return $count;

	$comments = get_approved_comments( $id );
	$comments = array_filter( $comments, 'k2_strip_trackback' );

	return count($comments);
}

add_filter('get_comments_number', 'k2_comment_count', 0);


/**
 * Separates comments from trackbacks
 *
 * @since 1.0
 * @global array $trackbacks Array of trackbacks/pings of current post
 *
 * @param array $comments Array of comments/trackbacks/pings of current post
 *
 * @return array Comments only
 */
function k2_seperate_comments( $comments ) {
	global $trackbacks;

	$comments_only = array_filter( $comments, 'k2_strip_trackback' );
	$trackbacks = array_filter( $comments, 'k2_strip_comment' );

	return $comments_only;
}

add_filter('comments_array', 'k2_seperate_comments');


/**
 * Strips out trackbacks/pingbacks
 *
 * @since 1.0
 *
 * @param object $var current comment
 *
 * @return boolean true if comment
 */
// 
function k2_strip_trackback($var) {
	return ($var->comment_type != 'trackback' and $var->comment_type != 'pingback');
}


/**
 * Strips out comments
 *
 * @since 1.0
 *
 * @param object $var current comment
 *
 * @return boolean true if trackback/pingback
 */
function k2_strip_comment($var) {
	return ($var->comment_type == 'trackback' or $var->comment_type == 'pingback');
}


/**
 * Displays current comment
 *
 * @since 1.0
 *
 * @param object $comment Comment data object.
 * @param array $args
 * @param int $depth Depth of comment in reference to parents.
 */
function k2_comment_start_el($comment, $args = array(), $depth = 1) {
	$GLOBALS['comment'] = $comment;

	extract($args, EXTR_SKIP);
?>

	<li id="comment-<?php comment_ID(); ?>">
		<div <?php comment_class(); ?>>

			<div class="comment-head">
				<?php if ( get_option('show_avatars') ): ?>
					<span class="gravatar">
						<?php echo get_avatar( $comment, 32 ); ?>
					</span>
				<?php endif; ?>

				<span class="comment-author"><?php comment_author_link(); ?></span>

				<div class="comment-meta">
					<a href="#comment-<?php comment_ID(); ?>" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>">
						<?php
							if ( function_exists('time_since') ):
								printf( __('%s ago.','k2_domain'), time_since( abs( strtotime($comment->comment_date_gmt . ' GMT') ), time() ) );
							else:
								printf( __('%1$s at %2$s','k2_domain'), get_comment_date(), get_comment_time() );
							endif;
						?>
					</a>
				</div><!-- .comment-meta -->
			</div><!-- .comment-head -->

			<div class="comment-content">
				<?php if ( ! $comment->comment_approved ): ?>
				<p class="comment-moderation alert"><?php _e('Your comment is awaiting moderation.','k2_domain'); ?></p>
				<?php endif; ?>

				<?php comment_text(); ?> 
			</div><!-- .comment-content -->

			<div class="buttons">
				<?php if ( function_exists('quoter_comment') ): quoter_comment(); endif; ?>
	
				<?php
					if ( function_exists('jal_edit_comment_link') ):
						jal_edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">','</span>', '<em>(Editing)</em>');
					else:
						edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">', '</span>');
					endif;
				?>

				<?php if ( function_exists('comment_reply_link') ): ?>
				<div id="comment-reply-<?php comment_ID(); ?>" class="comment-reply">
					<?php comment_reply_link(array_merge( $args, array('add_below' => 'comment-reply', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</div>
				<?php endif; ?>
			</div><!-- .buttons -->


		</div><!-- comment -->
<?php
}


/**
 * Displays current comment, only used when WP < 2.7
 *
 * @since 1.0
 *
 * @param object $comment Comment data object.
 */
function k2_comment_item($comment) {
	k2_comment_start_el($comment, array('style' => 'ul') );
	echo '</li>';
}


/**
 * Displays current pingback/trackback
 *
 * @since 1.0
 *
 * @param object $comment Comment data object.
 * @param array $args
 * @param int $depth Depth of comment in reference to parents.
 */
function k2_ping_start_el($comment, $args = array(), $depth = 1) {
	global $user_ID;
	
	$GLOBALS['comment'] = $comment;
?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<?php if ( function_exists('comment_favicon') ): ?>
			<span class="favatar"><?php comment_favicon(); ?></span>
		<?php endif; ?>

		<span class="comment-author"><?php comment_author_link(); ?></span>

		<div class="comment-meta">				
		<?php
			printf( _x('%1$s on %2$s', 'k2_comments', 'k2_domain'), 
				'<span class="pingtype">' . comment_type( __('Comment', 'k2_domain'), __('Trackback', 'k2_domain'), __('Pingback', 'k2_domain') ) . '</span>',
				sprintf('<a href="#comment-%1$s" title="%2$s">%3$s</a>',
					get_comment_ID(),	
					(function_exists('time_since')?
						sprintf( __('%s ago.', 'k2_domain'),
							time_since( abs( strtotime($comment->comment_date_gmt . " GMT") ), time() )
						):
						__('Permanent Link to this Comment', 'k2_domain')
					),
					sprintf( _x('%1$s at %2$s', 'k2_comments', 'k2_domain'),
						get_comment_date( __('M jS, Y','k2_domain') ),
						get_comment_time()
					)			
				)
			);
		
			if ( $user_ID )
				edit_comment_link( __('Edit', 'k2_domain'), '<span class="comment-edit">', '</span>' );
		?>
		</div><!-- .comment-meta -->
<?php
}


/**
 * Displays current pingback/trackback, only used when WP < 2.7
 *
 * @since 1.0
 *
 * @param object $comment Comment data object.
 */
function k2_ping_item($comment) {
	k2_ping_start_el($comment, array('style' => 'ul') );
	echo '</li>';
}
