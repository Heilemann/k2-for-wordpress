<?php
/**
 * Default Loop Template
 *
 * This file is loaded by multiple files and used for generating the loop
 *
 * @package K2
 * @subpackage Templates
 */
?>

<?php if ( have_posts() ) : ?>

	<?php if ( is_archive() || is_search() ) : ?>
		<header class="page-header">

			<h1 class="page-title">
			<?php
				if ( is_date() ) {
					the_post();
				
					if ( is_day() ) {
						printf( __( 'Daily Archives: <span>%s</span>', 'k2' ), get_the_date() );
					} elseif ( is_month() ) {
						printf( __( 'Monthly Archives: <span>%s</span>', 'k2' ), get_the_date( 'F Y' ) );
					} elseif ( is_year() ) {
						printf( __( 'Yearly Archives: <span>%s</span>', 'k2' ), get_the_date( 'Y' ) );
					} else {
						_e( 'Archives', 'k2' );
					}
				
					rewind_posts();
				} elseif ( is_category() ) {
					printf( __('Archive for the &#8216;%s&#8217; Category', 'k2'), single_cat_title('', false) );
				} elseif ( is_tag() ) {
					printf( __('Tag Archive for &#8216;%s&#8217;', 'k2'), single_tag_title('', false) );
				} elseif ( is_author() ) {
					printf( __('Author Archive for %s', 'k2'), get_the_author_meta( 'display_name', get_query_var('author') ) );
				} elseif ( is_search() ) {
					printf( __('Search Results for &#8216;%s&#8217;', 'k2'), get_search_query() );
				} else {
					_e( 'Archives', 'k2' );
				}
			?>
			</h1>

			<?php if ( $wp_query->max_num_pages > 1 ): ?>
			<h2 class="archivepages">
				<?php
					printf( __('Page %1$s of %2$s', 'k2'),
						isset( $wp_query->query_vars['paged'] ) ? $wp_query->query_vars['paged'] : 1,
						$wp_query->max_num_pages
					);
				?>
			</h2>
			<?php endif; ?>

		</header>
	<?php endif; ?>

	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="nav-above" class="navigation">
			<h1 class="section-heading"><?php _e( 'Post navigation', 'k2' ); ?></h1>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'k2' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'k2' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif; ?>

	<?php /* Start the Loop */ while ( have_posts() ) : the_post(); ?>

		<article id="entry-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php get_template_part('blocks/k2-post' , function_exists('get_post_format') ? get_post_format( $post->ID ) : '' ); ?>
		</article><!-- #entry-ID -->

	<?php endwhile; ?>

	<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="nav-below" class="navigation">
			<h1 class="section-heading"><?php _e( 'Post navigation', 'k2' ); ?></h1>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'k2' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'k2' ) ); ?></div>
		</nav><!-- #nav-below -->
	<?php endif; ?>

<?php else: get_template_part( 'blocks/k2-404' ); endif; ?>