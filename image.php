<?php
/**
 * The template used to display image type attachments.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

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

	<div class="primary">
		<a name="startcontent"></a>

		<?php /* K2 Hook */ do_action('template_primary_begin'); ?>

		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

			<?php if ( ! empty($post->post_parent) ): ?>
			<div class="navigation">
				<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
				<div class="clear"></div>
			</div>
			<?php endif; ?>

			<div class="content hfeed">

				<div id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-header">
						<h1 class="entry-title">
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
						</h1>

						<?php /* Edit Link */ edit_post_link( __('Edit', 'k2'), '<span class="entry-edit">', '</span>' ); ?>

						<?php /* K2 Hook */ do_action('template_entry_head'); ?>
					</div> <!-- .entry-header -->

					<div class="entry-content">
						<div class="attachment-image">
							<a href="<?php echo wp_get_attachment_url($post->ID); ?>" class="image-link"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a>

							<?php if ( !empty($post->post_excerpt) ): ?>
							<div class="caption"><?php the_excerpt(); ?></div>
							<?php endif; ?>
						</div>
					</div> <!-- .entry-content -->

					<div class="entry-footer">
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
								<?php echo k2_entry_date(); ?>
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
					</div><!-- .entry-footer -->
				</div> <!-- #entry-ID -->

				<div class="comments">
					<?php comments_template(); ?>
				</div> <!-- .comments -->

				<?php if ( ! empty($post->post_parent) ): ?>
				<div class="navigation">
					<div class="nav-previous"><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><span>&laquo;</span> <?php echo get_the_title($post->post_parent); ?></a></div>
					<div class="clear"></div>
				</div>
				<?php endif; ?>

			<?php endwhile; else: define('K2_NOT_FOUND', true); ?>

				<?php locate_template( array('blocks/k2-404.php'), true ); ?>

			<?php endif; ?>

		</div> <!-- .content -->

	</div> <!-- .primary -->

	<?php if ( ! get_post_custom_values('sidebarless') ) get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>

</div> <!-- .wrapper -->

<?php get_footer(); ?>
