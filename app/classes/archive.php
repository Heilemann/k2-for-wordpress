<?php
/* This class holds all the code for creating, deleting and setting up the pre-made archives page */

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
			$archives_page['post_content'] = __('Do not edit this page', 'k2_domain');
			$archives_page['post_excerpt'] = __('Do not edit this page', 'k2_domain');
			$archives_page['post_title'] = __('Archives', 'k2_domain');
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
}

add_action('k2_install', array('K2Archive', 'install'));
add_action('k2_uninstall', array('K2Archive', 'delete_archive'));
?>
