<?php
	$k2_image_link = false;

	function k2_gallery_link($output) {
		global $k2_image_link;

		switch ($k2_image_link) {
			case 'prev':
				$output = str_replace('</a>', '<span>' . __('&laquo; Previous', 'k2') . '</span></a>', $output);
				break;

			case 'next':
				$output = str_replace('</a>', '<span>' . __('Next &raquo;', 'k2') . '</span></a>', $output);
				break;
		}

		return $output;
	}

	add_filter('wp_get_attachment_link', 'k2_gallery_link');
?>

<?php get_header(); ?>

<div class="wrapper template-image">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>

	<div id="primary">
		<a name="startcontent"></a>

		<div id="content" class="hfeed">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

			<?php if ( ! empty($post->post_parent) ): ?>
			<div class="navigation">
				<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post-header">
					<h1 class="post-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link( __('Edit', 'k2'), '<span class="entry-edit">', '</span>' ); ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div> <!-- .post-header -->

				<div class="post-content">
					<div class="attachment-image">
						<a href="<?php echo wp_get_attachment_url($post->ID); ?>" class="image-link"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a>

						<?php if ( !empty($post->post_excerpt) ): ?>
						<div class="caption"><?php the_excerpt(); ?></div>
						<?php endif; ?>
					</div>

					<?php if ( !empty($post->post_content) ) the_content(sprintf(__('Continue reading \'%s\'', 'k2'), the_title('', '', false))); ?>
				</div> <!-- .post-content -->

				<div class="post-footer">
					<h5><?php _e('Photo Information', 'k2'); ?></h5>
					<ul class="image-meta">
						<li class="dimensions">
							<span><?php _e('Dimensions:', 'k2'); ?></span>
							<?php
								list($width, $height) = getimagesize( get_attached_file($post->ID) );
								/* translators: 1: image width, 2: image height */ 
								printf( __('%1$s &times; %2$s pixels', 'k2'), $width, $height );
							?>
						</li>
						<li class="file-size">
							<span><?php _e('File Size:', 'k2'); ?></span>
							<?php echo size_format( filesize( get_attached_file($post->ID) ) ); ?>
						</li>
						<li class="uploaded">
							<span><?php _e('Uploaded on:', 'k2'); ?></span>
							<?php
								if ( function_exists('time_since') ):
									printf( __('%s ago', 'k2'),
										'<abbr class="published" title="' . get_the_time('Y-m-d\TH:i:sO') . '">' . time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()) . '</abbr>');
								else:
							?><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO'); ?>"><?php the_time( get_option('date_format') ); ?></abbr><?php endif; ?>
						</li>

						<?php /* K2 Hook */ do_action('k2_image_meta', $post->ID); ?>
					</ul>

					<div id="gallery-nav" class="navigation">
						<div class="nav-previous">
							<?php $k2_image_link = 'prev'; previous_image_link(); $k2_image_link = false; ?>
						</div>
						<div class="nav-next">
							<?php $k2_image_link = 'next'; next_image_link(); $k2_image_link = false; ?>
						</div>
						<div class="clear"></div>
					</div>
				</div><!-- .post-footer -->
			</div> <!-- #post-ID -->

			<div class="comments">
				<?php comments_template(); ?>
			</div> <!-- .comments -->

			<?php if ( ! empty($post->post_parent) ): ?>
			<div class="navigation">
				<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

		<?php endwhile; else: ?>

			<div class="hentry four04">

				<div class="post-header">
					<h3 class="center"><?php _e('Not Found', 'k2'); ?></h3>
				</div>

				<div class="post-content">
					<p><?php _e('Oh no! You\'re looking for something which just isn\'t here! Fear not however, errors are to be expected, and luckily there are tools on the sidebar for you to use in your search for what you need.', 'k2'); ?></p>
				</div>

			</div> <!-- .hentry .four04 -->

		<?php endif; ?>

		</div> <!-- #content -->

	</div> <!-- #primary -->
	
	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>

</div> <!-- .wrapper -->
	
<?php get_footer(); ?>