<?php get_header(); ?>

<div class="content">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
			<div id="post-<?php the_ID(); ?>" class="<?php k2_post_class(); ?>">

				<div class="entry-head">
					<h3 class="entry-title">
					<?php if ( ! empty($post->post_parent) ): ?>
						<a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a> &raquo;
					<?php endif; ?>
					<a href="<?php the_permalink(); ?>" rel="bookmark" title='<?php printf( __('Permanent Link to "%s"','k2_domain'), attribute_escape(get_the_title()) ); ?>'><?php the_title(); ?></a>
					</h3>

					<div class="entry-meta">
						<div class="entry-comments">
							<a class="commentslink" href="#comments">
								<?php /* Comments */ comments_number('0&nbsp;<span>'.__('Comments','k2_domain').'</span>', '1&nbsp;<span>'.__('Comment','k2_domain').'</span>', '%&nbsp;<span>'.__('Comments','k2_domain').'</span>'); ?>
							</a>
						</div>

						<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">','</span>'); ?>
					</div> <!-- .entry-meta -->
				</div> <!-- .entry-head -->

			<?php if ( get_wp_version() > 2.4 ): // WP 2.5+ ?>
				<div class="entry-content">
					<div class="image-attachment">
						<a href="<?php echo wp_get_attachment_url($post->ID); ?>"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a>
					</div>

	                <div class="image-caption"><?php echo $post->post_excerpt; ?></div>

					<div class="image-description">
						<?php the_content(sprintf(__('Continue reading \'%s\'', 'k2_domain'), the_title('', '', false))); ?>
					</div>
				</div> <!-- .entry-content -->

				<div class="additional-info">
					<h4>Additional Info</h4>
					<ul class="image-meta">
						<li class="dimensions">
							<span><?php _e('Dimensions:','k2_domain'); ?></span>
							<?php list($width, $height) = getimagesize( get_attached_file($post->ID) ); printf( _c('%1$s px &times; %2$s px|1: width, 2: height','k2_domain'), $width, $height ); ?>
						</li>
						<li class="file-size">
							<span><?php _e('File Size:','k2_domain'); ?></span>
							<?php echo size_format( filesize( get_attached_file($post->ID) ) ); ?>
						</li>
						<li class="uploaded">
							<span><?php _e('Uploaded on:','k2_domain'); ?></span>
							<?php if ( function_exists('time_since') ):
									printf( __('%s ago','k2_domain'),
										'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">' . time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . '</abbr>');
								else: ?>
								<abbr class="published" title="<?php get_the_time('Y-m-d\TH:i:sO'); ?>"><?php the_time( get_option('date_format') ); ?></abbr>
							<?php endif; ?>
						</li>

						<?php /* K2 Hook */ do_action('k2_image_meta', $post->ID); ?>
					</ul>
				</div>
			<?php else: // WP < 2.5 ?>
				<div class="entry-content">
					<div class="image-attachment">
						<?php $attachment_link = get_the_attachment_link($post->ID, true, array(450, 800)); // This also populates the iconsize for the next line ?>
						<?php $_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>
						<p class="<?php echo $classname; ?>"><?php echo $attachment_link; ?><br /><?php echo basename($post->guid); ?></p>
					</div>

	                <div class="image-caption"><?php echo $post->post_excerpt; ?></div>

					<div class="image-description">
						<?php the_content(sprintf(__('Continue reading \'%s\'', 'k2_domain'), the_title('', '', false))); ?>
					</div>
				</div> <!-- .entry-content -->
			<?php endif; ?>

			</div> <!-- #post-ID -->

			<?php if ( get_wp_version() > 2.4 ): ?>
			<div id="gallery-nav" class="navigation">
				<div class="nav-previous">
					<?php previous_image_link('%link', '<span class="meta-nav">&laquo;</span> %title'); ?>
				</div>
				<div class="nav-next">
					<?php next_image_link('%link', '%title <span class="meta-nav">&raquo;</span>'); ?>
				</div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

			<div class="entry-comments comments">
				<?php comments_template(); ?>
			</div> <!-- .entry-comments -->

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
</div> <!-- #primary-wrapper -->

	<?php get_sidebar(); ?>

</div> <!-- .content -->
	
<?php get_footer(); ?>