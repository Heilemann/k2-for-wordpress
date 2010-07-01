<?php
/**
 * This is the loop, which fetches entries from your database.
 * It is a very delicate piece of machinery. Be gentle!
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

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
				$page_head = /* translators: daily archive date format (example: Monday, January 1st, 1970), see http://php.net/date */ sprintf( __('Daily Archive for %s', 'k2'), get_the_time( __('l, F jS, Y', 'k2') ) );

			} elseif ( is_month() ) {
				$templates[] = 'blocks/k2-loop-archive-month.php';
				$page_head = /* translators: monthly archive date format (example: January, 1970), see http://php.net/date */ sprintf( __('Monthly Archive for %s', 'k2'), get_the_time( __('F, Y', 'k2') ) );

			} elseif ( is_year() ) {
				$templates[] = 'blocks/k2-loop-archive-year.php';
				$page_head = /* translators: yearly archive date format (example: 1970), see http://php.net/date */ sprintf( __('Yearly Archive for %s', 'k2'), get_the_time('Y') );
			}

			$templates[] = 'blocks/k2-loop-archive-date.php';

			rewind_posts();
		} elseif ( is_category() ) {
			$cat = $wp_query->get_queried_object();

			$templates[] = 'blocks/k2-loop-category-' . $cat->slug . '.php';
			$templates[] = 'blocks/k2-loop-category-' . $cat->cat_ID . '.php';
			$templates[] = 'blocks/k2-loop-category.php';
			$page_head = sprintf( __('Archive for the &#8216;%s&#8217; Category', 'k2'), single_cat_title('', false) );

		} elseif ( is_tag() ) {
			$templates[] = 'blocks/k2-loop-tag-' . get_query_var('tag') . '.php';
			$templates[] = 'blocks/k2-loop-tag.php';
			$page_head = sprintf( __('Tag Archive for &#8216;%s&#8217;', 'k2'), single_tag_title('', false) );

		} elseif ( is_author() ) {
			$templates[] = 'blocks/k2-loop-author.php';
			$page_head = sprintf( __('Author Archive for %s', 'k2'), get_the_author_meta( 'display_name', get_query_var('author') ) );
		}

		$templates[] = 'blocks/k2-loop-archive.php';
	} elseif ( is_search() ) {
		$templates[] = 'blocks/k2-loop-search.php';
		$page_head = sprintf( __('Search Results for &#8216;%s&#8217;', 'k2'), get_search_query() );
	}

	$page_head = apply_filters('k2_section_title', $page_head);
	$templates[] = 'blocks/k2-loop.php';
?>

	<?php if ( ! empty($page_head) ): ?>
		<div class="page-head">
			<h1><?php echo $page_head; ?></h1>

			<?php if ( is_paged() ): ?>
				<h2 class="archivepages"><?php /* translators: 1: current page, 2: total pages */ printf( __('Page %1$s of %2$s', 'k2'), intval( get_query_var('paged')), $wp_query->max_num_pages ); ?></h2>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php /* Check if there are posts */ if ( have_posts() ): ?>

		<?php /* Load the loop templates */ locate_template( $templates, true ); ?>

	<?php /* If there is nothing to loop */ else: define('K2_NOT_FOUND', true); ?>

		<?php locate_template( array('blocks/k2-404.php'), true ); ?>

	<?php endif; /* End Loop Init  */ ?>
