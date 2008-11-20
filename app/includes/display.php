<?php
	// Prevent users from directly loading this class file
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

	function k2_header_menu() {
	?>

	<ul class="menu">
		<?php if ( get_option('show_on_front') != 'page' ): ?>
		<li class="<?php if (
							is_home()
							or is_archive()
							or is_single()
							or is_paged()
							or is_search()
							or ( function_exists('is_tag') and is_tag() )
						): ?>current_page_item<?php else: ?>page_item<?php endif; ?>"><a href="<?php echo get_option('home'); ?>/" title="<?php echo get_option('k2blogornoblog'); ?>"><?php echo get_option('k2blogornoblog'); ?></a></li>
		<?php endif; ?>

		<?php /* K2 Hook */ do_action('template_header_menu'); ?>

		<?php wp_list_pages( apply_filters('k2_menu_list_pages', 'sort_column=menu_order&depth=1&title_li=') ); ?>

		<?php wp_register('<li class="admintab">','</li>'); ?>
	</ul> <!-- .menu -->

<?php
	}

	add_action('template_header', 'k2_header_menu');

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


	function k2_comment_start_el($comment, $args = array(), $depth = 1) {
		global $comment_index;

		$GLOBALS['comment'] = $comment;
		$comment_index++;

		extract($args, EXTR_SKIP);
?>

<li id="comment-<?php comment_ID(); ?>" <?php if ( function_exists('comment_class') ): comment_class(); else: echo 'class="' . k2_comment_class( $comment_index, false ) . '"'; endif; ?>>
	<div class="comment">

		<div class="comment-head">
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

			<a href="#comment-<?php comment_ID(); ?>" class="counter" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>"><?php echo $comment_index; ?></a>
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

				<?php if ( function_exists('quoter_comment') ): quoter_comment(); endif; ?>

				<?php
					if ( function_exists('jal_edit_comment_link') ):
						jal_edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">','</span>', '<em>(Editing)</em>');
					else:
						edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">', '</span>');
					endif;
				?>
			</div><!-- .comment-meta -->
		</div><!-- .comment-head -->

		<div class="comment-content">
			<?php comment_text(); ?> 

			<?php if ( ! $comment->comment_approved ): ?>
				<p class="comment-moderation alert">
					<strong><?php _e('Your comment is awaiting moderation.','k2_domain'); ?></strong>
				</p>
			<?php endif; ?>
		</div><!-- .comment-content -->

		<?php if ( function_exists('comment_reply_link') ): ?>
		<div id="comment-reply-<?php comment_ID(); ?>" class="comment-reply">
			<?php comment_reply_link(array_merge( $args, array('add_below' => 'comment-reply', 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</div>
		<?php endif; ?>

	</div><!-- #div-comment -->
<?php
	}

	function k2_comment_item($comment) {
		k2_comment_start_el($comment, array('style' => 'ul') );
		echo '</li>';
	}

	function k2_ping_start_el($comment, $args = array(), $depth = 1) {
		global $comment_index;
		$GLOBALS['comment'] = $comment;	?>

		<li id="comment-<?php comment_ID(); ?>" class="<?php k2_comment_class( ++$comment_index ); ?>">
			<?php if ( function_exists('comment_favicon') ): ?>
				<span class="favatar"><?php comment_favicon(); ?></span>
			<?php endif ?>

			<a href="#comment-<?php comment_ID(); ?>" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>" class="counter"><?php echo $comment_index; ?></a>
			<span class="comment-author"><?php comment_author_link(); ?></span>

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
			</div><!-- .comment-meta -->
<?php
	}

	function k2_ping_item($comment) {
		k2_ping_start_el($comment, array('style' => 'ul') );
		echo '</li>';
	}
?>