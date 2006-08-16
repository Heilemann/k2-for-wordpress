<?php

function text_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);
	echo('<p>' . nl2br(wp_specialchars(htmlspecialchars(sbm_get_option('text'), ENT_QUOTES), 1)) . '</p>');
	echo($after_module);
}

function text_sidebar_module_control() {
	if(isset($_POST['text_module_text'])) {
		sbm_update_option('text', stripslashes($_POST['text_module_text']));
	}

	?>
		<p><label for="text-module-text">Module's text content:</label><br /><textarea id="text-module-text" name="text_module_text" rows="6" cols="30"><?php echo(wp_specialchars(htmlspecialchars(sbm_get_option('text'), ENT_QUOTES), 1)); ?></textarea></p>
	<?php
}

register_sidebar_module('Text module', 'text_sidebar_module', 'sb-text');
register_sidebar_module_control('Text module', 'text_sidebar_module_control');

?>
