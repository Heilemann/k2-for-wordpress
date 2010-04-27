<?php
	// This is the loop, which fetches entries from your database.
	// It is a very delicate piece of machinery. Be gentle!

	global $wp_query;

	// array for loading loop templates
	$templates = array();
	$page_head = '';
	
	if ( is_home() ) {
		$templates[] = 'blocks/k2-loop-home.php';

	} elseif ( is_archive() ) {
		if ( is_date() ) {
			the_post();

			if ( is_day() ) {
				$templates[] = 'blocks/k2-loop-archive-day.php';
				$page_head = sprintf( __('Daily Archive for %s','k2_domain'), get_the_time( __('F jS, Y','k2_domain') ) );

			} elseif ( is_month() ) {
				$templates[] = 'blocks/k2-loop-archive-month.php';
				$page_head = sprintf( __('Monthly Archive for %s','k2_domain'), get_the_time( __('F, Y','k2_domain') ) );

			} elseif ( is_year() ) {
				$templates[] = 'blocks/k2-loop-archive-year.php';
				$page_head = sprintf( __('Yearly Archive for %s','k2_domain'), get_the_time( __('Y','k2_domain') ) );
			}

			$templates[] = 'blocks/k2-loop-archive-date.php';

			rewind_posts();
		} elseif ( is_category() ) {
			$templates[] = 'blocks/k2-loop-category-' . absint( get_query_var('cat') ) . '.php';
			$templates[] = 'blocks/k2-loop-category.php';
			$page_head = sprintf( __('Archive for the \'%s\' Category','k2_domain'), single_cat_title('', false) );
			
		} elseif ( is_tag() ) {
			$templates[] = 'blocks/k2-loop-tag-' . get_query_var('tag') . '.php';
			$templates[] = 'blocks/k2-loop-tag.php';
			$page_head = sprintf( __('Tag Archive for \'%s\'','k2_domain'), single_tag_title('', false) );
			
		} elseif ( is_author() ) {
			$templates[] = 'blocks/k2-loop-author.php';
			$page_head = sprintf( __('Author Archive for %s','k2_domain'), get_author_name( get_query_var('author') ) );
		}
		
		$templates[] = 'blocks/k2-loop-archive.php';
	} elseif ( is_search() ) {
		$templates[] = 'blocks/k2-loop-search.php';
		$page_head = sprintf( __('Search Results for \'%s\'','k2_domain'), esc_attr( get_search_query() ) );
	}

	$templates[] = 'blocks/k2-loop.php';
?>

	<?php /* Top Navigation */ k2_navigation('nav-above'); ?>

	<?php if ( ! empty($page_head) ): ?>
		<div class="page-head">
			<h1><?php echo $page_head; ?></h1>

			<?php if ( is_paged() ): ?>
				<h2 class="archivepages"><?php printf( __('Page %1$s of %2$s', 'k2_domain'), intval( get_query_var('paged')), $wp_query->max_num_pages); ?></h2>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php /* Check if there are posts */ if ( have_posts() ): ?>

		<?php /* Load the loop templates */ locate_template( $templates, true ); ?>
	
	<?php /* If there is nothing to loop */ else: define('K2_NOT_FOUND', true); ?>

		<?php locate_template( array('blocks/k2-404.php'), true ); ?>

	<?php endif; /* End Loop Init  */ ?>

	<?php /* Bottom Navigation */ k2_navigation('nav-below'); ?> 
