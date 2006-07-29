<?php

function toppage_nav_sidebar_module($args) {
	extract($args);

	echo($before_module);
	?>
	<ul>
		<?php wp_list_pages('sort_column=menu_order&depth=1&title_li=' . $before_title . $title . $after_title); ?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module('Top pages navigation module', 'toppage_nav_sidebar_module', 'sb-navigation');

?>
