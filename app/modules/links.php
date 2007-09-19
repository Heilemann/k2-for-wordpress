<?php

function links_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	?>
	<ul>
		<?php wp_list_bookmarks('title_before=<h4>&title_after=</h4>'); ?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module('Links', 'links_sidebar_module', 'sb-links');

?>
