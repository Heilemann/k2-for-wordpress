<?php get_header(); ?>

<div class="content">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class(); ?>">

				<div class="entry-head">
					<h3 class="entry-title"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo; <a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), attribute_escape(get_the_title()) ); ?>'><?php the_title(); ?></a></h3>

					<div class="entry-meta">
						<?php
							printf(	__('<span class="meta-start">Published</span> %1$s %2$s<span class="meta-end">.</span>','k2_domain'),

								'<div class="entry-author">' .
								sprintf(  __('<span class="meta-prep">by</span> %s','k2_domain'),
									'<address class="vcard author"><a href="' . get_author_posts_url(get_the_author_ID()) .'" class="url fn" title="'. sprintf(__('View all posts by %s','k2_domain'), attribute_escape(get_the_author())) .'">' . get_the_author() . '</a></address>'
								) . '</div>',

								'<div class="entry-date">' .
								( function_exists('time_since') ?
										sprintf(__('%s ago','k2_domain'),
											'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">' . time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . '</abbr>') :
										sprintf(__('<span class="meta-prep">on</span> %s','k2_domain'),
											'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">'. get_the_time( get_option('date_format') ) . '</abbr>')
								) . '</div>'
							);
						?>

						<div class="entry-comments">
							<a class="commentslink" href="#comments">
								<?php /* Comments */ comments_number('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>'); ?>
							</a>
						</div>

						<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
					</div> <!-- .entry-meta -->
				</div> <!-- .entry-head -->

				<div class="entry-content">
					<div class="attachment">
						<a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a>
					</div>
	                <div class="caption"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?></div>

					<?php the_content(sprintf(__('Continue reading \'%s\'', 'k2_domain'), the_title('', '', false))); ?>

				</div> <!-- .entry-content -->

			</div> <!-- #post-ID -->

			<div id="gallery-nav" class="navigation">
				<div class="nav-previous">
					<?php previous_image_link('%link', '<span class="meta-nav">&laquo;</span> %title'); ?>
				</div>
				<div class="nav-next">
					<?php next_image_link('%link', '%title <span class="meta-nav">&raquo;</span>'); ?>
				</div>
				<div class="clear"></div>
			</div>

			<?php comments_template(); ?>
		<?php endwhile; else: ?>

			<div class="hentry four04">

				<div class="entry-head">
					<h3 class="center"><?php _e('Not Found','k2_domain'); ?></h3>
				</div>

				<div class="entry-content">
					<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.','k2_domain'); ?></p>
				</div>

			</div> <!-- .hentry .four04 -->

		<?php endif; ?>

		</div> <!-- #current-content -->

		<div id="dynamic-content"></div>
	</div> <!-- #primary -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->
	
<?php get_footer(); ?>