<?php

function categories_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	?>
	<ul>
		<?php
		if (function_exists('wp_list_categories')) {
			wp_list_categories('title_li=&show_count=1&hierarchical=0');
		} else {
			list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','','');
		}
		?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module('Category List', 'categories_sidebar_module', 'sb-categories');

?>
