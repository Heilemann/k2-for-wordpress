<?php

function calendar_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	get_calendar();
	echo($after_module);
}

register_sidebar_module('Calendar', 'calendar_sidebar_module', 'sb-calendar');

?>
