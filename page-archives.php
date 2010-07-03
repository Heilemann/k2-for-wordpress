<?php
/**
 * The template for displaying pages with page slug archives.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

if ( is_page_template( 'page-archives.php' ) ) :

/*
Template Name: Archives (Do Not Use Manually)
*/

/**
 * Counts the posts, pages, comments, categories and tags on your site.
 * see: app/classes/archive.php
 */
$count_posts	= K2Archive::count('post');
$count_pages	= K2Archive::count('page');
$count_comments	= K2Archive::count('comment');
$count_cats	= K2Archive::count('category');
$count_tags	= K2Archive::count('tag');

get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>

	<div class="primary">
		<a name="startcontent"></a>

		<div class="content hfeed">

			<?php the_post(); ?>

			<div id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-header">
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link(__('Edit', 'k2'), '<span class="entry-edit">', '</span>'); ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div> <!-- .entry-header -->

				<div class="entry-content">

					<p class="archivetext"><?php /* translators: 1: blog name, 2: post count, 3: page count 4: comment count, 5: category count, 6: tag count */
						printf( __('This is the frontpage of the %1$s archives. Currently the archives are spanning %2$s, %3$s and %4$s, contained within the meager confines of %5$s and %6$s. Through here, you will be able to move down into the archives by way of time or category. If you are looking for something specific, perhaps you should try the search on the sidebar.', 'k2'),
						get_bloginfo('name'), $count_posts, $count_pages, $count_comments, $count_cats, $count_tags );
					?></p>

					<?php
					$tag_cloud = get_terms( 'post_tag' );
					if ( $tag_cloud ) :
					?>
						<h3><?php _e('Tag Cloud', 'k2'); ?></h3>
						<div id="tag-cloud">
							<?php wp_tag_cloud('number=0'); ?>
						</div>
					<?php endif; ?>

					<h3><?php _e('Browse by Month', 'k2'); ?></h3>
					<ul class="archive-list">
						<?php wp_get_archives('show_post_count=1'); ?>
					</ul>

					<br class="clear" />

					<h3><?php _e('Browse by Category', 'k2'); ?></h3>
					<ul class="archive-list">
						<?php wp_list_categories( array( 'hierarchical' => true, 'show_count' => 1, 'title_li' => '' ) ); ?>
					</ul>

					<br class="clear" />

				</div> <!-- .entry-content -->

				<div class="entry-footer">
					<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:', 'k2') . '</span>', 'after' => '</div>' ) ); ?>

					<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
				</div> <!-- .entry-footer -->
			</div> <!-- #entry-ID -->

			<?php if ( get_post_custom_values('comments') ): ?>
			<div class="comments">
				<?php comments_template(); ?>
			</div> <!-- .comments -->
			<?php endif; ?>

		</div> <!-- .content .hfeed -->

	</div> <!-- .primary -->

	<?php if ( ! get_post_custom_values('sidebarless') ) get_sidebar(); ?>

	<?php if ( is_active_sidebar('widgets-bottom') ) : ?>
	<div id="widgets-bottom" class="widgets">
		<?php dynamic_sidebar('widgets-bottom'); ?>
	</div>
	<?php endif; ?>

</div> <!-- .wrapper -->

<?php get_footer(); ?>

<?php else :

	locate_template( array('page.php'), true );

endif; ?>
