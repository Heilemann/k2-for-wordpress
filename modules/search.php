<?php

function search_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	get_search_form();
	echo($after_module);
}

register_sidebar_module('Search', 'search_sidebar_module', 'sb-search');

?>
