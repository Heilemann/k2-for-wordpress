<?php /*
	Template Name: Archives (Do Not Use Manually)
*/ ?>

<?php /* Counts the posts, comments and categories on your blog */
	$numpostsarray	= wp_count_posts('post');
	$numposts		= $numpostsarray->publish;
	
	$numcommsarray	= wp_count_comments();
	$numcomms		= $numcommsarray->approved;
	
	$numcats = count(get_all_category_ids());
?>

<?php get_header(); ?>

<div class="content">

<div id="primary-wrapper">
	<div id="primary">
		<div id="notices"></div>
		<a name="startcontent" id="startcontent"></a>

		<div id="current-content" class="hfeed">

			<?php the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="entry-head">
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php k2_permalink_title(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php /* Edit Link */ edit_post_link(__('Edit','k2_domain'), '<span class="entry-edit">', '</span>'); ?>

					<?php /* K2 Hook */ do_action('template_entry_head'); ?>
				</div><!-- .entry-head -->

				<div class="entry-content">

					<p class="archivetext"><?php printf(__('This is the frontpage of the %1$s archives. Currently the archives are spanning %2$s posts and %3$s comments, contained within the meager confines of %4$s categories. Through here, you will be able to move down into the archives by way of time or category. If you are looking for something specific, perhaps you should try the search on the sidebar.','k2_domain'), get_bloginfo('name'), $numposts, $numcomms, $numcats); ?></p>

					<h3><?php _e('Tag Cloud','k2_domain'); ?></h3>
					<div id="tag-cloud">
					<?php wp_tag_cloud('number=0'); ?>
					</div>

					<h3><?php _e('Browse by Month','k2_domain'); ?></h3>
					<ul class="archive-list">
						<?php wp_get_archives('show_post_count=1'); ?>
					</ul>

					<br class="clear" />

					<h3><?php _e('Browse by Category','k2_domain'); ?></h3>
					<ul class="archive-list">
						<?php wp_list_cats('hierarchical=0&optioncount=1'); ?>
					</ul>

					<br class="clear" />
						
				</div><!-- .entry-content -->

				<div class="entry-foot">
					<?php wp_link_pages( array('before' => '<div class="entry-pages"><span>' . __('Pages:','k2_domain') . '</span>', 'after' => '</div>' ) ); ?>

					<?php /* K2 Hook */ do_action('template_entry_foot'); ?>
				</div><!-- .entry-foot -->
			</div><!-- #post-ID -->

			<?php if ( get_post_custom_values('comments') ): ?>
			<div class="comments">
				<?php comments_template(); ?>
			</div><!-- .comments -->
			<?php endif; ?>

		</div><!-- #current-content .hfeed -->

		<div id="dynamic-content"></div>
	</div><!-- #primary -->
</div><!-- #primary-wrapper -->

<?php if ( ! get_post_custom_values('sidebarless') ) get_sidebar(); ?>

</div> <!-- .content -->
	
<?php get_footer(); ?>
