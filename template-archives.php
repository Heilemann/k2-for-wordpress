<?php
/**
 * The template for displaying pages with page slug archives.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 1.5
 */

/*
Template Name: Archives
*/

// Counts the posts, pages, comments, categories and tags on your site.
$count_posts    = wp_count_posts( 'post' );
$count_pages    = wp_count_posts( 'page' );
$count_comments	= wp_count_comments();
$count_cats	    = wp_count_terms( 'category', array( 'hide_empty' => true ) );
$count_tags	    = wp_count_terms( 'post_tag', array( 'hide_empty' => true ) );

get_header(); ?>

<div class="wrapper">

	<?php if ( is_active_sidebar('widgets-top') ) : ?>
	<div id="widgets-top" class="widgets">
		<?php dynamic_sidebar('widgets-top'); ?>
	</div>
	<?php endif; ?>

	<div class="primary">
		<div id="content" class="content">

			<?php the_post(); ?>

			<article id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</header> <!-- .entry-header -->

				<div class="entry-content">

					<p class="archivetext">
					<?php
						/* translators: 1: blog name, 2: post count, 3: page count 4: comment count, 5: category count, 6: tag count */
						printf( __('This is the frontpage of the %1$s archives. Currently the archives are spanning %2$s, %3$s and %4$s, contained within the meager confines of %5$s and %6$s. Through here, you will be able to move down into the archives by way of time or category. If you are looking for something specific, perhaps you should try the search on the sidebar.', 'k2'),
							get_bloginfo('name'),
							sprintf( _n( '%d post', '%d posts', $count_posts->publish, 'k2' ), number_format_i18n( $count_posts->publish ) ),
							sprintf( _n( '%d page', '%d pages', $count_pages->publish, 'k2' ), number_format_i18n( $count_posts->publish ) ),
							sprintf( _n( '%d comment', '%d comments', $count_comments->approved, 'k2' ), number_format_i18n( $count_comments->approved ) ),
							sprintf( _n( '%d category', '%d categories', $count_cats, 'k2' ), number_format_i18n( $count_cats ) ),
							sprintf( _n( '%d tag', '%d tags', $count_tags, 'k2' ), number_format_i18n( $count_tags ) )
						);
					?>
					</p>

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

				<footer class="entry-footer">
					<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:', 'k2') . '</span>', 'after' => '</div>' ) ); ?>

					<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
				</footer> <!-- .entry-footer -->
			</article> <!-- #entry-ID -->

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
