<?php
/**
 * This class holds all the code for adding, deleting and setting up the pre-made archives page.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

class K2Archive {

	/**
	 * Get the page ID that uses template page-archives.php.
	 *
	 * @since K2 1.1
	 */
	function get_page_id() {
		global $wpdb;

		$page_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-archives.php' LIMIT 1");

		return $page_id;
	}

	/**
	 * Add the archives page.
	 *
	 * @since K2 1.1
	 */
	function add() {
		if ( '1' == get_option('k2archives') ) {
			$page_id = K2Archive::get_page_id();

			if ( !$page_id ) {
				$data = array(
					'post_title' => __('Archives', 'k2'),
					'post_name' => 'archives',
					'post_status' => 'publish',
					'post_type' => 'page',
					'page_template' => 'page-archives.php',
					'comment_status' => 'closed',
					'ping_status' => 'closed'
				);

				wp_insert_post($data);
			}
		}
	}

	/**
	 * Delete the archives page.
	 *
	 * @since K2 1.1
	 */
	function delete() {
		$page_id = K2Archive::get_page_id();

		if ( $page_id )
			wp_delete_post($page_id, true);
	}

	/**
	 * Counts the posts, pages, comments, categories and tags on your site.
	 *
	 * @since K2 1.1
	 */
	function count($type) {
		global $post;

		switch($type) :
			case 'post' :
				$posts = wp_count_posts('post');
				$total = sprintf( _n('%d post', '%d posts', $posts->publish, 'k2'), number_format_i18n($posts->publish) );
				break;
			case 'page' :
				$pages = wp_count_posts('page');
				$total = sprintf( _n('%d page', '%d pages', $pages->publish, 'k2'), number_format_i18n($pages->publish) );
				break;
			case 'comment' :
				$comments = wp_count_comments();
				$total = sprintf( _n('%d comment', '%d comments', $comments->approved, 'k2'), number_format_i18n($comments->approved) );
				break;
			case 'category' :
				//$categories = wp_count_terms('category', array('hide_empty' => true));
				$categories = count(get_terms('category'));
				$total = sprintf( _n('%d category', '%d categories', $categories, 'k2'), number_format_i18n($categories) );
				break;
			case 'tag' :
				$tags = wp_count_terms('post_tag', array('hide_empty' => true));
				$total = sprintf( _n('%d tag', '%d tags', $tags, 'k2'), number_format_i18n($tags) );
				break;
		endswitch;

		return $total;
	}

	/**
	 * Archives excerpt for search results.
	 *
	 * @since K2 1.1
	 */
	function excerpt($content) {
		global $post;

		if ( (get_post_type() == 'page') && (get_post_meta($post->ID, '_wp_page_template', true) == 'page-archives.php') ) {
			$count_posts	= K2Archive::count('post');
			$count_pages	= K2Archive::count('page');
			$count_comments	= K2Archive::count('comment');
			$count_cats	= K2Archive::count('category');
			$count_tags	= K2Archive::count('tag');

			/* translators: 1: post count, 2: page count 3: comment count, 4: category count, 5: tag count */
			printf( '<p>' . __('Currently the archives are spanning %1$s, %2$s and %3$s, contained within the meager confines of %4$s and %5$s.', 'k2') . '</p>',
				 $count_posts, $count_pages, $count_comments, $count_cats, $count_tags
			);
		} else {
			return $content;
		}
	}

}

add_action('k2_install', array('K2Archive', 'add'));
add_action('k2_uninstall', array('K2Archive', 'delete'));
add_filter('the_content', array('K2Archive', 'excerpt'));
