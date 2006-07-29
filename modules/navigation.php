<?php

function nav_sidebar_module($args) {
	extract($args);

	echo($before_module);
	?>
	<ul>
		<?php wp_list_pages('sort_column=menu_order&title_li=' . $before_title . $title . $after_title); ?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module('Navigation module', 'nav_sidebar_module', 'sb-navigation');

?>
