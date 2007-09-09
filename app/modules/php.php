<?php

function php_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	eval(' ?>' . sbm_get_option('code') . '<?php ');
	echo($after_module);
}

function php_sidebar_module_control() {
	if(isset($_POST['php_module_code'])) {
		sbm_update_option('code', stripslashes($_POST['php_module_code']));
	} ?>
		<p>
			<label for="php-module-code"><?php _e('Module\'s code:', 'k2_domain'); ?></label><br />
			<textarea id="php-module-code" name="php_module_code" rows="6" cols="30"><?php echo(wp_specialchars(htmlspecialchars(sbm_get_option('code'), ENT_QUOTES), 1)); ?></textarea>
		</p>
	<?php
}

register_sidebar_module('Text, HTML and PHP', 'php_sidebar_module', 'sb-php');
register_sidebar_module_control('Text, HTML and PHP', 'php_sidebar_module_control');

?>