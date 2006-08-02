<?php

function php_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	eval(' ?>' . stripslashes(sbm_get_option('code')) . '<?php ');
	echo($after_module);
}

function php_sidebar_module_control() {
	if(isset($_POST['php_module_code'])) {
		sbm_update_option('code', $_POST['php_module_code']);
	}

	?>
		<p><label for="php-module-code">Module's code:</label><br /><textarea id="php-module-code" name="php_module_code" rows="6" cols="30"><?php echo(stripslashes(sbm_get_option('code'))); ?></textarea></p>
	<?php
}

register_sidebar_module('PHP module', 'php_sidebar_module', 'sb-php');
register_sidebar_module_control('PHP module', 'php_sidebar_module_control');

?>
