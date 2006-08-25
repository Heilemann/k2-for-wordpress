<?php

function search_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	include(TEMPLATEPATH . '/searchform.php');
	echo($after_module);
}

register_sidebar_module(__('Search module', 'k2_domain'), 'search_sidebar_module', 'sb-search');

?>