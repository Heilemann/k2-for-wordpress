<?php
	$k2_image_link = false;

	function k2_gallery_link($output) {
		global $k2_image_link;

		switch ($k2_image_link) {
			case 'prev':
				$output = str_replace('</a>', '<span>&laquo; Previous</span></a>', $output);
				break;

			case 'next':
				$output = str_replace('</a>', '<span>Next &raquo;</span></a>', $output);
				break;
		}

		return $output;
	}

	add_filter('wp_get_attachment_link', 'k2_gallery_link');
?>

<?php get_header(); ?>

<div class="content template-image">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

			<?php if ( ! empty($post->post_parent) ): ?>
			<div class="navigation">
				<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-head">
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link( __('Edit','k2_domain'), '<span class="entry-edit">', '</span>' ); ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div> <!-- .entry-head -->

				<div class="entry-content">
					<div class="attachment-image">
						<a href="<?php echo wp_get_attachment_url($post->ID); ?>" class="image-link"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a>

						<?php if ( !empty($post->post_excerpt) ): ?>
						<div class="caption"><?php the_excerpt(); ?></div>
						<?php endif; ?>
					</div>

					<?php if ( !empty($post->post_content) ) the_content(sprintf(__('Continue reading \'%s\'', 'k2_domain'), the_title('', '', false))); ?>
				</div> <!-- .entry-content -->

				<div class="entry-foot">
					<h5><?php _e('Photo Information', 'k2_domain'); ?></h5>
					<ul class="image-meta">
						<li class="dimensions">
							<span><?php _e('Dimensions:','k2_domain'); ?></span>
							<?php
								list($width, $height) = getimagesize( get_attached_file($post->ID) );
								printf( _x('%1$s &times; %2$s pixels', 'k2_image', 'k2_domain'), $width, $height );
							?>
						</li>
						<li class="file-size">
							<span><?php _e('File Size:','k2_domain'); ?></span>
							<?php echo size_format( filesize( get_attached_file($post->ID) ) ); ?>
						</li>
						<li class="uploaded">
							<span><?php _e('Uploaded on:','k2_domain'); ?></span>
							<?php
								if ( function_exists('time_since') ):
									printf( __('%s ago','k2_domain'),
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
				</div><!-- .entry-foot -->
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

</div> <!-- .content -->
	
<?php get_footer(); ?>