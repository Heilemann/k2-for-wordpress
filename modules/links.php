<?php

function links_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	?>
	<ul>
		<?php get_links_list(); ?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module('Links module', 'links_sidebar_module', 'sb-links');

?>
