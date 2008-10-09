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

	<?php if ( !isset($comments_by_type) ): $comments_by_type = k2_seperate_comments($comments); endif; ?>

		<h4><?php printf( __('%1$s %2$s to &#8220;%3$s&#8221;', 'k2_domain'), '<span id="comments">' . count($comments_by_type['comment']) . '</span>', count($comments_by_type['comment']) ? __('Responses', 'k2_domain'): __('Response','k2_domain'), the_title('', '', false) ); ?></h4>

		<div class="metalinks">
			<span class="commentsrsslink"><?php comments_rss_link(__('Feed for this Entry','k2_domain')); ?></span>
			<?php if ('open' == $post->ping_status): ?><span class="trackbacklink"><a href="<?php trackback_url(); ?>" title="<?php _e('Copy this URI to trackback this entry.','k2_domain'); ?>"><?php _e('Trackback Address','k2_domain'); ?></a></span><?php endif; ?>
		</div>

		<hr />

	<?php if ( !empty($comments_by_type['comment']) ): $GLOBALS['comment_index'] = 0; ?>
		<ul id="commentlist">
			<?php if ( function_exists('wp_list_comments') ): ?>
				<?php wp_list_comments('type=comment&callback=k2_comment_start_el'); ?>
			<?php else: ?>
				<?php foreach ($comments_by_type['comment'] as $comment): ?>
					<?php k2_comment_item( $comment ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>

		<?php if ( function_exists('wp_list_comments') ): ?>
		<div class="navigation">
			<div class="nav-previous"><?php previous_comments_link() ?></div>
			<div class="nav-next"><?php next_comments_link() ?></div>
		</div>
		<?php endif; ?>
	<?php elseif ( comments_open() ): ?>
		<ul id="commentlist">
			<li id="leavecomment">
				<?php _e('No Comments','k2_domain'); ?>
			</li>
		</ul>
	<?php endif; // If there are comments ?>

	<?php if ( !empty($comments_by_type['pings']) ): $GLOBALS['comment_index'] = 0; ?>
		<ul id="pinglist">
			<?php if ( function_exists('wp_list_comments') ): ?>
				<?php wp_list_comments( 'type=pings&callback=k2_ping_item' ); ?>
			<?php else: ?>
				<?php foreach ($comments_by_type['pings'] as $comment): ?>
					<?php k2_ping_item($comment); ?>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
	<?php endif; // If there are trackbacks / pingbacks ?>
		
	<?php /* Comments closed */ if ( !comments_open() and is_single() ): ?>
		<div id="comments-closed-msg"><?php _e('Comments are currently closed.','k2_domain'); ?></div>
	<?php endif; ?>

	<?php /* Reply Form */ if ( comments_open() ): ?>
	<div id="respond">
		<h4 class="reply"><?php
				if ( isset( $_GET['jal_edit_comments'] ) ):
					_e('Edit Your Comment','k2_domain');
				elseif ( function_exists('comment_form_title') ):
					comment_form_title( __('Leave a Reply', 'k2_domain'), __('Leave a Reply to %s', 'k2_domain') );
				else:
					_e('Leave a Reply','k2_domain');
				endif;
		?></h4>
		
		<?php if ( function_exists('cancel_comment_reply_link') ): ?>
		<div class="cancel-comment-reply">
			<small><?php cancel_comment_reply_link(); ?></small>
		</div>
		<?php endif; ?>

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
					<?php if ( function_exists('comment_parent_field') ): comment_parent_field(); endif; ?>
					<span id="commentload"></span>
				</p>
				
				<div class="clear"></div>

				<?php do_action('comment_form', $post->ID); ?>

			</form>

		<?php endif; // If registration required and not logged in ?>
	
	</div> <!-- .commentformbox -->

	<?php endif; // Reply Form ?>
