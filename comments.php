<?php 
	// Do not access this file directly
	if ( !empty($_SERVER['SCRIPT_FILENAME']) and 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']) )
		die( __('Please do not load this page directly. Thanks!', 'k2') );

 	// Password Protection
	if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e('This post is password protected. Enter the password to view comments.', 'k2'); ?></p>
	<?php return; endif; ?>

		<h4><?php /* translators: 1: language string for the number of comment(s), 2: post title */
			printf( __('%1$s to &#8220;%2$s&#8221;', 'k2'),
				sprintf( _n('<span id="comments">%s</span> Response', '<span id="comments">%s</span> Responses', get_comments_number(), 'k2'), number_format_i18n(get_comments_number()) ),
				the_title('', '', false) 
			);
		?></h4>

		<div class="metalinks">
			<span class="commentsrsslink"><?php post_comments_feed_link( __('Feed for this Entry', 'k2') ); ?></span>
			<?php if ( pings_open() ) : ?><span class="trackbacklink"><a href="<?php trackback_url(); ?>" title="<?php _e('Copy this URI to trackback this entry.', 'k2'); ?>"><?php _e('Trackback Address', 'k2'); ?></a></span><?php endif; ?>
		</div>

		<hr />

	<?php if ( have_comments() ) : $GLOBALS['comment_index'] = 0; ?>
		<ul id="commentlist">
		<?php
			if ( function_exists('wp_list_comments') ) :
				wp_list_comments('callback=k2_comment_start_el');
			else:
				foreach ($comments as $comment) :
					k2_comment_item($comment);
				endforeach;
			endif;
		?>
		</ul>

		<?php if ( function_exists('wp_list_comments') ) :
			/* Navigation */ k2_navigation('nav-comments');
		endif; ?>

	<?php elseif ( comments_open() ) : ?>
		<ul id="commentlist">
			<li id="leavecomment">
				<?php _e('No Comments', 'k2'); ?>
			</li>
		</ul>
	<?php endif; // If there are comments ?>

	<?php if ( !empty($GLOBALS['trackbacks']) ) : $GLOBALS['comment_index'] = 0; ?>
		<ul id="pinglist">
		<?php
			foreach ($GLOBALS['trackbacks'] as $comment) :
				k2_ping_item($comment);
			endforeach;
		?>
		</ul>
	<?php endif; // If there are trackbacks / pingbacks ?>
		
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
		
		<?php if ( function_exists('cancel_comment_reply_link') ) : ?>
		<div class="cancel-comment-reply">
			<?php cancel_comment_reply_link( __('Cancel Reply', 'k2') ); ?>
		</div>
		<?php endif; ?>

		<?php if ( get_option('comment_registration') and !is_user_logged_in() ) : ?>
			<p><?php printf( __('You must be <a href="%s">logged in</a> to post a comment.', 'k2'), wp_login_url( get_permalink() )); ?></p>
		<?php else: ?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

			<?php
				if ( isset($_GET['jal_edit_comments']) ) :
					$jal_comment = jal_edit_comment_init();

					if ( ! $jal_comment ) :
						return;
					endif;
			?>
			<?php elseif ( is_user_logged_in() ) : ?>
		
				<p class="comment-login">
					<?php printf(__('Logged in as <a href="%1$s">%2$s</a>.', 'k2'), get_option('siteurl') . '/wp-admin/profile.php', $user_identity); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'k2'); ?>"><?php _e('Log out &raquo;', 'k2'); ?></a>
				</p>
	
			<?php elseif ( '' != $comment_author ) : ?>

				<p class="comment-welcomeback"><?php printf(__('Welcome back <strong>%s</strong>', 'k2'), $comment_author); ?>
				
				<a href="javascript:toggleCommentAuthorInfo();" id="toggle-comment-author-info">
					<?php _e('(Change)', 'k2'); ?>
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
							<strong><?php _e('Mail', 'k2'); ?></strong> <?php _e('(will not be published)', 'k2'); ?> <?php if ( $req ) : _e('(required)', 'k2'); endif; ?>
						</label>
					</p>
					
					<p>
						<input type="text" name="url" id="url" value="<?php echo esc_attr($comment_author_url); ?>" size="22" tabindex="3" />
						<label for="url">
							<strong><?php _e('Website', 'k2'); ?></strong>
						</label>
					</p>			
				</div><!-- comment-personaldetails -->
			<?php endif; // If not logged in ?>

				<!--<p><?php printf( __('<strong>XHTML:</strong> You can use these tags: <code>%s</code>', 'k2'), allowed_tags() ); ?></p>-->
		
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
					<div><input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit', 'k2'); ?>" /></div>

					<?php if ( function_exists('comment_id_fields') ) : comment_id_fields(); else: ?>
						<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
					<?php endif; ?>

				</p>
			</form>

		<?php endif; // If registration required and not logged in ?>
	
		<div class="clear"></div>
	</div> <!-- .commentformbox -->

	<?php endif; // Reply Form ?>
