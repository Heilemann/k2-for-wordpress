<?php
	$modules = K2SBM::get_installed_modules();
?>

<p id="module-update-success" class="success"><?php _e('Module\'s options updated', 'k2_domain'); ?></p>
<p id="module-update-error" class="error"></p>

<p>
	<label for="module-name"><strong><?php _e('Module\'s title:', 'k2_domain'); ?></strong></label><br />
	<input id="module-name" name="module_name" type="text" value="<?php echo attribute_escape($module->name); ?>" /><br />
	<input id="output-show-title" name="output[show_title]" type="checkbox"<?php if($module->output['show_title']) { ?> checked="checked"<?php } ?> /> <label for="output-show-title"><?php _e('Display module\'s title', 'k2_domain'); ?></label>
</p>

<p>
	<strong><?php _e('Module\'s type:', 'k2_domain'); ?></strong><br />
	<?php echo($modules[$module->type]['name']); ?>
</p>

<p>
	<a id="toggle-advanced-output-options" href="#"><?php _e('Advanced options', 'k2_domain'); ?></a>
	<div id="advanced-output-options" class="toggle-item">
		<p>
			<label for="output-css-file"><?php _e('Related CSS file', 'k2_domain'); ?>:</label><br />
			<input id="output-css-file" name="output[css_file]" type="text" value="<?php echo attribute_escape($module->output['css_file']); ?>" />
		</p>
	</div>
</p>

<fieldset>
<legend><strong><?php _e('Display on:', 'k2_domain'); ?></strong></legend>
	<input id="display-home" name="display[home]" type="checkbox"<?php if($module->display['home']) { ?> checked="checked"<?php } ?> /> <label for="display-home"><?php _e('Homepage', 'k2_domain'); ?></label><br />

	<input id="display-archives" name="display[archives]" type="checkbox"<?php if($module->display['archives']) { ?> checked="checked"<?php } ?> /> <label for="display-archives"><?php _e('Archives', 'k2_domain'); ?></label><br />

	<input id="display-post" name="display[post]" type="checkbox"<?php if($module->display['post']) { ?> checked="checked"<?php } ?> /> <label for="display-post"><?php _e('Single posts', 'k2_domain'); ?></label> &raquo; <a id="toggle-specific-posts" href="#"><?php _e('Detailed options', 'k2_domain'); ?></a><br />

	<div id="specific-posts" class="toggle-item"></div>

	<input id="display-search" name="display[search]" type="checkbox"<?php if($module->display['search']) { ?> checked="checked"<?php } ?> /> <label for="display-search"><?php _e('Search results', 'k2_domain'); ?></label><br />

	<input id="display-pages" name="display[pages]" type="checkbox"<?php if($module->display['pages']) { ?> checked="checked"<?php } ?> /> <label for="display-pages"><?php _e('Static pages', 'k2_domain'); ?></label> &raquo; <a id="toggle-specific-pages" href="#"><?php _e('Detailed options', 'k2_domain'); ?></a><br />

	<div id="specific-pages" class="toggle-item"></div>

	<input id="display-error" name="display[error]" type="checkbox"<?php if($module->display['error']) { ?> checked="checked"<?php } ?> /> <label for="display-error"><?php _e('Error page', 'k2_domain'); ?></label>
</fieldset>
