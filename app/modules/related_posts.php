<?php

function related_posts_sidebar_module($args) {
	global $notfound;

	extract($args);

	if(is_single() and ($notfound != '1')) {
		echo($before_module . $before_title . $title . $after_title);
		?>
			<ul>
				<?php related_posts(); ?>
			</ul>
		<?php
		echo($after_module);
	}
}

if(function_exists('related_posts')) {
	register_sidebar_module('Related posts module', 'related_posts_sidebar_module', 'sb-related');
}

?>
