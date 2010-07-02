<?php
/**
 * This class holds all the code for creating, deleting and setting up the pre-made archives page.
 *
 * @package WordPress
 * @subpackage K2
 * @since K2 unknown
 */

class K2Archive {
	function install() {
		if ( '1' == get_option('k2archives') ) {
			K2Archive::create_archive();
		}
	}

	function create_archive() {
		global $wpdb;

		$archives_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-archives.php' LIMIT 1");

		if ( empty($archives_id) ) {
			$archives_page = array();
			$archives_page['post_content'] = __('Do not edit this page', 'k2');
			$archives_page['post_excerpt'] = __('Do not edit this page', 'k2');
			$archives_page['post_title'] = __('Archives', 'k2');
			$archives_page['post_name'] = 'archivepage';
			$archives_page['post_status'] = 'publish';
			$archives_page['post_type'] = 'page';
			$archives_page['page_template'] = 'page-archives.php';

			// For WordPress 2.6+
			if ( ! function_exists('get_page_templates') )
				require_once(ABSPATH . 'wp-admin/includes/theme.php');

			wp_insert_post($archives_page);
		}
	}

	function delete_archive() {
		global $wpdb;

		$archives_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-archives.php' LIMIT 1");

		if (!empty($archives_id)) {
			wp_delete_post($archives_id);
		}
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
				$total = sprintf(_n('%d post', '%d posts', $posts->publish, 'k2'), number_format_i18n($posts->publish));
				break;
			case 'page' :
				$pages = wp_count_posts('page');
				$total = sprintf(_n('%d page', '%d pages', $pages->publish, 'k2'), number_format_i18n($pages->publish));
				break;
			case 'comment' :
				$comments = wp_count_comments();
				$total = sprintf(_n('%d comment', '%d comments', $comments->approved, 'k2'), number_format_i18n($comments->approved));
				break;
			case 'category' :
				$categories = wp_count_terms('category');
				$total = sprintf(_n('%d category', '%d categories', $categories, 'k2'), number_format_i18n($categories));
				break;
			case 'tag' :
				$tags = wp_count_terms('post_tag');
				$total = sprintf(_n('%d tag', '%d tags', $tags, 'k2'), number_format_i18n($tags));
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

		if ((get_post_type() == 'page') && (get_post_meta($post->ID, '_wp_page_template', true) == 'page-archives.php')) {
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

add_action('k2_install', array('K2Archive', 'install'));
add_action('k2_uninstall', array('K2Archive', 'delete_archive'));
add_filter('the_content', array('K2Archive', 'excerpt'));
?>
