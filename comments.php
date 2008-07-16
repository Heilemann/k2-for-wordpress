<?php
	// Do not access this file directly
	if ( 'comments.php' == basename( $_SERVER['SCRIPT_FILENAME'] ) ):
		die( __('Please do not load this page directly. Thanks!', 'k2_domain') );
	endif;

 	// Password Protection
	if ( ! empty( $post->post_password ) ):
		if ( $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password ): ?>

	<p class="nopassword"><?php _e('This post is password protected. Enter the password to view comments.','k2_domain'); ?></p>

	<?php return; endif; endif; ?>

		<h4><?php printf(__('%1$s %2$s to &#8220;%3$s&#8221;','k2_domain'), '<span id="comments">' . get_comments_number() . '</span>', (1 == $post->comment_count) ? __('Response','k2_domain'): __('Responses','k2_domain'), the_title('', '', false)); ?></h4>

		<div class="metalinks">
			<span class="commentsrsslink"><?php comments_rss_link(__('Feed for this Entry','k2_domain')); ?></span>
			<?php if ('open' == $post->ping_status): ?><span class="trackbacklink"><a href="<?php trackback_url(); ?>" title="<?php _e('Copy this URI to trackback this entry.','k2_domain'); ?>"><?php _e('Trackback Address','k2_domain'); ?></a></span><?php endif; ?>
		</div>

		<hr />

	<?php if ( count($comments) > 0 ): ?>
		<ol id="commentlist">

		<?php $index = 0; foreach ($comments as $comment): ?>

			<li id="comment-<?php comment_ID(); ?>" class="<?php k2_comment_class( ++$index ); ?>">

			<?php /* WordPress 2.5 Avatar */ if ( function_exists('get_avatar') and get_option('show_avatars') ): ?>
				<span class="gravatar">
					<?php echo get_avatar( $comment, 32 ); ?>
				</span>
			<?php /* Gravatar 2.x Plugin */ elseif ( function_exists('gravatar_image_link') ): ?>
				<?php gravatar_image_link(); ?>
			<?php /* Gravatar 1.x Plugin */ elseif ( function_exists('gravatar') ): ?>
				<a href="http://www.gravatar.com/" title="<?php _e('What is this?','k2_domain'); ?>">
					<img src="<?php gravatar('X', 32, get_bloginfo('template_url') . '/images/defaultgravatar.jpg' ); ?>" class="gravatar" alt="<?php _e('Gravatar Icon','k2_domain'); ?>" />
				</a>
			<?php /* End Gravatar Check */ endif; ?>

				<a href="#comment-<?php comment_ID(); ?>" class="counter" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>"><?php echo $index; ?></a>
				<span class="commentauthor"><?php comment_author_link(); ?></span>

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

					<?php if ( function_exists('quoter_comment') ): quoter_comment(); endif; ?>

					<?php
						if ( function_exists('jal_edit_comment_link') ):
							jal_edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">','</span>', '<em>(Editing)</em>');
						else:
							edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">', '</span>');
						endif;
					?>
				</div><!-- .comment-meta -->

				<div class="comment-content">
					<?php comment_text(); ?> 
				</div><!-- .comment-content -->

				<?php if ( ! $comment->comment_approved ): ?>
					<p class="comment-moderation alert">
						<strong><?php _e('Your comment is awaiting moderation.','k2_domain'); ?></strong>
					</p>
				<?php endif; ?>
			</li>

		<?php endforeach; ?>

		</ol><!-- #commentlist -->
	<?php elseif ( comments_open() ): ?>
		<ol id="commentlist">
			<li id="leavecomment">
				<?php _e('No Comments','k2_domain'); ?>
			</li>
		</ol>
	<?php endif; // If there are comments ?>

	<?php global $trackbacks; if ( count($trackbacks) > 0 ): ?>
		<ol id="pinglist">

		<?php $index = 0; foreach ($trackbacks as $comment): ?>

			<li id="comment-<?php comment_ID(); ?>" class="<?php k2_comment_class( ++$index ); ?>">
				<?php if ( function_exists('comment_favicon') ): ?>
					<span class="favatar">
						<?php comment_favicon(); ?>
					</span>
				<?php endif ?>

				<a href="#comment-<?php comment_ID(); ?>" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>" class="counter"><?php echo $index; ?></a>
				<span class="commentauthor"><?php comment_author_link(); ?></span>

				<div class="comment-meta">				
				<?php
					printf(__('%1$s on %2$s','k2_domain'), 
						'<span class="pingtype">' . get_k2_ping_type(__('Trackback','k2_domain'), __('Pingback','k2_domain')) . '</span>',
						sprintf('<a href="#comment-%1$s" title="%2$s">%3$s</a>',
							get_comment_ID(),	
							(function_exists('time_since')?
								sprintf(__('%s ago.','k2_domain'),
									time_since(abs(strtotime($comment->comment_date_gmt . " GMT")), time())
								):
								__('Permanent Link to this Comment','k2_domain')
							),
							sprintf(__('%1$s at %2$s','k2_domain'),
								get_comment_date(__('M jS, Y','k2_domain')),
								get_comment_time()
							)			
						)
					);
				?>				
				<?php if ($user_ID) { edit_comment_link(__('Edit','k2_domain'),'<span class="comment-edit">','</span>'); } ?>
				</div>
			</li>

			<?php endforeach; ?>

		</ol> <!-- #pinglist -->
	<?php endif; // If there are trackbacks / pingbacks ?>
		
	<?php /* Comments closed */ if ( !comments_open() and is_single() ): ?>
		<div id="comments-closed-msg"><?php _e('Comments are currently closed.','k2_domain'); ?></div>
	<?php endif; ?>

	<?php /* Reply Form */ if ( comments_open() ): ?>
	<div id="commentformbox">
		<h4 id="respond" class="reply"><?php if (isset($_GET['jal_edit_comments'])): _e('Edit Your Comment','k2_domain'); else: _e('Leave a Reply','k2_domain'); endif; ?></h4>
		
		<?php if ( get_option('comment_registration') and !$user_ID ): ?>
			<p>
				<?php printf(__('You must <a href="%s">login</a> to post a comment.','k2_domain'), get_option('siteurl') . '/wp-login.php?redirect_to=' . get_permalink()); ?>
			</p>
		<?php else: ?>
			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

			<?php
				if ( isset($_GET['jal_edit_comments']) ):
					$jal_comment = jal_edit_comment_init();

					if ( ! $jal_comment ):
						return;
					endif;
			?>
			<?php elseif ( $user_ID ): ?>
		
				<p class="comment-login">
					<?php printf( __('Logged in as %s.','k2_domain'), '<a href="' . get_option('siteurl') . '/wp-admin/profile.php">' . $user_identity . '</a>' ); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account','k2_domain'); ?>"><?php _e('Logout','k2_domain'); ?> &raquo;</a>
				</p>
	
			<?php elseif ( '' != $comment_author ): ?>

				<p class="comment-welcomeback"><?php printf(__('Welcome back <strong>%s</strong>','k2_domain'), $comment_author); ?>
				
				<a href="javascript:toggleCommentAuthorInfo();" id="toggle-comment-author-info">
					<?php _e('(Change)','k2_domain'); ?>
				</a>

				<script type="text/javascript" charset="utf-8">
				//<![CDATA[
					var changeMsg = "<?php echo  js_escape( __('(Change)','k2_domain') ); ?>";
					var closeMsg = "<?php echo js_escape( __('(Close)','k2_domain') ); ?>";
					
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
			
			<?php if ( ! $user_ID ): ?>
				<div id="comment-author-info">
					<p>
						<input type="text" name="author" id="author" value="<?php echo attribute_escape($comment_author); ?>" size="22" tabindex="1" />
						<label for="author">
							<strong><?php _e('Name','k2_domain'); ?></strong> <?php if ( $req ): _e('(required)','k2_domain'); endif; ?>
						</label>
					</p>
					
					<p>
						<input type="text" name="email" id="email" value="<?php echo attribute_escape($comment_author_email); ?>" size="22" tabindex="2" />
						<label for="email">
							<strong><?php _e('Mail','k2_domain'); ?></strong> (<?php _e('will not be published','k2_domain'); ?>) <?php if ( $req ): _e('(required)', 'k2_domain'); endif; ?>
						</label>
					</p>
					
					<p>
						<input type="text" name="url" id="url" value="<?php echo attribute_escape($comment_author_url); ?>" size="22" tabindex="3" />
						<label for="url">
							<strong><?php _e('Website','k2_domain'); ?></strong>
						</label>
					</p>			
				</div><!-- comment-personaldetails -->
			<?php endif; // If not logged in ?>

				<!--<p><?php printf(__('<strong>XHTML:</strong> You can use these tags: %s','k2_domain'), allowed_tags()) ?></p>-->
		
				<p>
					<textarea name="comment" id="comment" cols="100%" rows="10" tabindex="4"><?php
						if ( function_exists('jal_edit_comment_link') ):
							jal_comment_content($jal_comment);
						endif;

						if ( function_exists('quoter_comment_server') ):
							quoter_comment_server();
						endif;
					?></textarea>
					<span id="commenterror"></span>
				</p>
		
				<?php if ( function_exists('show_subscription_checkbox') ): show_subscription_checkbox(); endif; ?>
				<?php if ( function_exists('quoter_page') ): quoter_page(); endif; ?>

				<p>
					<input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit','k2_domain'); ?>" />
					<input type="hidden" name="comment_count" value="<?php echo $num_comments; ?>" />
					<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
					<span id="commentload"></span>
				</p>
				
				<div class="clear"></div>

				<?php do_action('comment_form', $post->ID); ?>

			</form>

		<?php endif; // If registration required and not logged in ?>
	
	</div> <!-- .commentformbox -->

	<?php endif; // Reply Form ?>
