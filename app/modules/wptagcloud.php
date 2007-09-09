<?php
if (function_exists('wp_tag_cloud')) {


function wptagcloud_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);

	if ( function_exists('UTW_ShowWeightedTagSetAlphabetical') ) {
		echo('test1234');
		UTW_ShowWeightedTagSetAlphabetical("coloredsizedtagcloud");
	} else if ( function_exists('wp_tag_cloud') ) {
		wp_tag_cloud('format=list');
	}

	echo($after_module);
}

function wptagcloud_sidebar_module_control() {
	if (isset($_POST['wptagcloud_module_blurp'])) {
		sbm_update_option('wptagcloud', $_POST['wptagclod_module_blurp']);
	}
}

register_sidebar_module('Tag Cloud', 'wptagcloud_sidebar_module', 'sb-wptagcloud', array('wptagcloud' => ''));
register_sidebar_module_control('Tag Cloud', 'wptagcloud_sidebar_module_control');

}
?>