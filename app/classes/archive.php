<?php
/* This class holds all the code for creating, deleting and setting up the archives, powered by Extended Live Archives: http://www.sonsofskadi.net/extended-live-archive/ */

class K2Archive {
	function install() {
		if ( '1' == get_option('k2archives') ) {
			K2Archive::create_archive();
		}
	}

	function create_archive() {
		global $wpdb;

		$archives_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-archives.php' LIMIT 1");

		$archives_page = array();
		$archives_page['ID'] = $archives_id;
		$archives_page['post_content'] = __('Do not edit this page', 'k2_domain');
		$archives_page['post_excerpt'] = __('Do not edit this page', 'k2_domain');
		$archives_page['post_title'] = __('Archives', 'k2_domain');
			$archives_page['post_name'] = 'archivepage';

		if (get_wp_version() < 2.1) {
			// WP 2.0
			$archives_page['post_status'] = 'static';
		} else {
			// WP 2.1+
			$archives_page['post_status'] = 'publish';
			$archives_page['post_type'] = 'page';
		}
		$archives_page['page_template'] = 'page-archives.php';

		wp_insert_post($archives_page);
	}

	function delete_archive() {
		global $wpdb;

		$archives_id = $wpdb->get_var("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-archives.php' LIMIT 1");

		if (!empty($archives_id)) {
			wp_delete_post($archives_id);
		}
	}

	function setup_archive() {
		// No point doing this unless ELA is actually installed
		if (function_exists('af_ela_set_config')) {
			// Easier way to check for UTW
			if(class_exists('UltimateTagWarriorCore')) {
				$menu_order="chrono,tags,cats";
			} else {
				$menu_order="chrono,cats";
			}

			$initSettings = array(
				// we always set the character set from the blog settings
				'newest_first' => 0,
				'num_entries' => 1,
				'num_entries_tagged' => 0,
				'num_comments' => 1,
				'fade' => 1,
				'hide_pingbacks_and_trackbacks' => 1,
				'use_default_style' => 1,
				'paged_posts' => 1,
				'selected_text' => '',
				'selected_class' => 'selected',
				'comment_text' => '<span>%</span>',
				'number_text' => '<span>%</span>',
				'number_text_tagged' => '(%)',
				'closed_comment_text' => '<span>%</span>',
				'day_format' => 'jS',
				'error_class' => 'alert',

				// allow truncating of titles
				'truncate_title_length' => 0,
				'truncate_cat_length' => 25,
				'truncate_title_text' => '&#8230;',
				'truncate_title_at_space' => 1,
				'abbreviated_month' => 1,
				'tag_soup_cut' => 0,
				'tag_soup_X' => 0,

				// paged posts related stuff
				'paged_post_num' => 15,
				'paged_post_next' => __('next 15 posts &raquo;','k2_domain'),
				'paged_post_prev' => __('&laquo; previous 15 posts','k2_domain'),

				// default text for the tab buttons
				'menu_order' => $menu_order,
				'menu_month' => __('Chronology','k2_domain'),
				'menu_cat' => __('Taxonomy','k2_domain'),
				'menu_tag' => __('Folksonomy','k2_domain'),
				'before_child' => '&nbsp;&nbsp;&nbsp;',
				'after_child' => '',
				'loading_content' => '<img src="'.get_bloginfo('template_url').'/images/spinner.gif" class="elaload" alt="Spinner" />',
				'idle_content' => '',
				'excluded_categories' => '0'
			);

			$ret = af_ela_set_config($initSettings);
		}

		return $ret;
	}
}

add_action('k2_install', array('K2Archive', 'install'));
add_action('k2_uninstall', array('K2Archive', 'delete_archive'));
?>
