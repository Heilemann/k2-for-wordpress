<?php

function categories_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	?>
	<ul>
		<?php list_cats(0, '', 'name', 'asc', '', 1, 0, 1, 1, 1, 1, 0,'','','','',''); ?>
	</ul>
	<?php
	echo($after_module);
}

register_sidebar_module(__('Category list module', 'k2_domain'), 'categories_sidebar_module', 'sb-categories');

?>
