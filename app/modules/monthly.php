<?php

function months_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
?>
	<ul>
		<?php wp_get_archives('type=monthly'); ?>
	</ul>
<?php
	echo($after_module);
}

register_sidebar_module('Monthly Archive', 'months_sidebar_module', 'sb-months');

?>
