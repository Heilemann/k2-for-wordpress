<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to k2_comment_type_switch which is
 * located in the app/includes/pluggable.php file.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

	// Password Protection
	if ( post_password_required() ) return;
?>

		<h4><?php /* translators: 1: language string for the number of comment(s), 2: post title */
			printf( _n( '<span id="comments">1</span> Response to %2$s', '<span id="comments">%1$s</span> Responses to %2$s', get_comments_number(), 'k2' ),
				number_format_i18n( get_comments_number() ), '&#8220;' . get_the_title() . '&#8221;'
			);
		?></h4>

		<div class="metalinks">
			<span class="commentsrsslink"><?php post_comments_feed_link( __('Feed for this Entry', 'k2') ); ?></span>
			<?php if ( pings_open() ) : ?>
			<span class="trackbacklink"><a href="<?php trackback_url(); ?>" title="<?php _e('Copy this URI to trackback this entry.', 'k2'); ?>"><?php _e('Trackback Address', 'k2'); ?></a></span>
			<?php endif; ?>
		</div>

		<hr />

	<?php if ( have_comments() ) : ?>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div id="comments-nav-above" class="navigation">
			<?php paginate_comments_links(); ?>
		</div>
		<?php endif; // check for comment navigation ?>

		<ul id="commentlist">
			<?php wp_list_comments( array( 'callback' => 'k2_comment_type_switch' ) ); ?>
		</ul>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
		<div id="comments-nav-below" class="navigation">
			<?php paginate_comments_links(); ?>
		</div>
		<?php endif; // check for comment navigation ?>

	<?php elseif ( comments_open() ) : ?>
		<ul id="commentlist">
			<li id="leavecomment">
				<?php _e('No Comments', 'k2'); ?>
			</li>
		</ul>
	<?php endif; /* If there are comments */ ?>

	<?php /* Comments closed */ if ( !comments_open() and is_single() ) : ?>
		<div id="comments-closed-msg"><?php _e('Comments are currently closed.', 'k2'); ?></div>
	<?php endif; ?>

	<?php /* Reply Form */ if ( comments_open() ) : ?>
	<div id="respond">
		<h4 class="reply"><?php
				if ( isset( $_GET['jal_edit_comments'] ) ) :
					_e('Edit Your Comment', 'k2');
				elseif ( function_exists('comment_form_title') ) :
					comment_form_title( __('Leave a Reply', 'k2'), __('Leave a Reply to %s', 'k2') );
				else:
					_e('Leave a Reply', 'k2');
				endif;
		?></h4>

		<div class="quoter_page_container"><?php if ( function_exists('quoter_page') ) quoter_page(); ?></div>

		<div class="cancel-comment-reply">
			<?php cancel_comment_reply_link( __('Cancel Reply', 'k2') ); ?>
		</div>

		<?php if ( get_option('comment_registration') and !is_user_logged_in() ) : ?>
			<p><?php printf( __('You must be <a href="%s">logged in</a> to post a comment.', 'k2'), wp_login_url( get_permalink() )); ?></p>
		<?php else: ?>
			<form action="<?php echo site_url('wp-comments-post.php'); ?>" method="post" id="commentform">

			<?php
				if ( isset($_GET['jal_edit_comments']) ) :
					$jal_comment = jal_edit_comment_init();

					if ( ! $jal_comment ) :
						return;
					endif;
			?>
			<?php elseif ( is_user_logged_in() ) : ?>

				<p class="comment-login">
					<?php printf(__('Logged in as <a href="%1$s">%2$s</a>.', 'k2'), admin_url('profile.php'), $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'k2'); ?>"><?php _e('Log out &raquo;', 'k2'); ?></a>
				</p>

			<?php elseif ( '' != $comment_author ) : ?>

				<p class="comment-welcomeback"><?php printf(__('Welcome back <strong>%s</strong>', 'k2'), $comment_author); ?>

				<a href="javascript:toggleCommentAuthorInfo();" id="toggle-comment-author-info">
					(<?php _e('Change Your Details', 'k2'); ?>)
				</a>

				<script type="text/javascript" charset="utf-8">
				//<![CDATA[
					var changeMsg = "<?php echo esc_js( __('(Change)', 'k2') ); ?>";
					var closeMsg = "<?php echo esc_js( __('(Close)', 'k2') ); ?>";

					function toggleCommentAuthorInfo() {
						jQuery('#comment-author-info').slideToggle('slow', function(){
							if ( jQuery('#comment-author-info').css('display') == 'none' ) {
								jQuery('#toggle-comment-author-info').text(changeMsg);
							} else {
								jQuery('#toggle-comment-author-info').text(closeMsg);
							}
						});
					}

					jQuery(document).ready(function(){
						jQuery('#comment-author-info').hide();
					});
				//]]>
				</script>
			<?php endif; ?>

			<?php if ( ! is_user_logged_in() ) : ?>
				<div id="comment-author-info">
					<p>
						<input type="text" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" size="22" tabindex="1" />
						<label for="author">
							<strong><?php _e('Name', 'k2'); ?></strong> <?php if ( $req ) : _e('(required)', 'k2'); endif; ?>
						</label>
					</p>

					<p>
						<input type="text" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" size="22" tabindex="2" />
						<label for="email">
							<strong><?php _e('Email', 'k2'); ?></strong> <?php _e('(will not be published)', 'k2'); ?> <?php if ( $req ) : _e('(required)', 'k2'); endif; ?>
						</label>
					</p>

					<p>
						<input type="text" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="3" />
						<label for="url">
							<strong><?php _e('Website', 'k2'); ?></strong>
						</label>
					</p>
				</div> <!-- #comment-author-info -->
			<?php endif; /* If not logged in */ ?>

				<!-- <p><?php printf( __('<strong>XHTML:</strong> You can use these tags: <code>%s</code>', 'k2'), allowed_tags() ); ?></p> -->

				<p>
					<textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"><?php
						if ( function_exists('jal_edit_comment_link') ) :
							jal_comment_content($jal_comment);
						endif;

						if ( function_exists('quoter_comment_server') ) :
							quoter_comment_server();
						endif;
					?></textarea>
				</p>

				<?php do_action('comment_form', $post->ID); ?>

				<p>
					<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'k2'); ?>" />
					<?php comment_id_fields(); ?>
				</p>
			</form>

		<?php endif; /* If registration required and not logged in */ ?>

		<div class="clear"></div>
	</div> <!-- #respond -->

	<?php endif; /* Reply Form */ ?>
