<?php
	$modules = K2SBM::get_installed_modules();
?>

<div id="optionstab-content" class="tabcontent">
<p id="type-container">
	<span><?php _e('Type:', 'k2_domain'); ?></span>
	<?php echo($modules[$module->type]['name']); ?>
</p>

<p id="name-container">
	<label for="module-name" class="titlelabel"><?php _e('Title:', 'k2_domain'); ?></label>
	<input id="module-name" name="module_name" type="text" value="<?php echo attribute_escape($module->name); ?>" />

	<input id="output-show-title" name="output[show_title]" type="checkbox"<?php if($module->output['show_title']) { ?> checked="checked"<?php } ?> />
	<label for="output-show-title" class="showtitlelabel"><?php _e('Display Title', 'k2_domain'); ?></label>
</p>
</div>

<div id="advancedtab-content" class="tabcontent">
	<p>
		<label for="output-css-file"><?php _e('Related CSS file', 'k2_domain'); ?>:</label>
		<input id="output-css-file" name="output[css_file]" type="text" value="<?php echo attribute_escape($module->output['css_file']); ?>" />
	</p>
</div>


<div id="displaytab-content" class="tabcontent">
<fieldset>
<legend><strong><?php _e('Display Module On:', 'k2_domain'); ?></strong></legend>
	<input id="display-home" name="display[home]" type="checkbox"<?php if($module->display['home']) { ?> checked="checked"<?php } ?> /> <label for="display-home"><?php _e('Homepage', 'k2_domain'); ?></label><br />

	<input id="display-archives" name="display[archives]" type="checkbox"<?php if($module->display['archives']) { ?> checked="checked"<?php } ?> /> <label for="display-archives"><?php _e('Archives', 'k2_domain'); ?></label><br />

	<input id="display-post" name="display[post]" type="checkbox"<?php if($module->display['post']) { ?> checked="checked"<?php } ?> /> <label for="display-post"><?php _e('Single posts', 'k2_domain'); ?></label> &raquo; <a id="toggle-specific-posts" href="#"><?php _e('Detailed options', 'k2_domain'); ?></a><br />

	<div id="specific-posts" class="toggle-item"></div>

	<input id="display-search" name="display[search]" type="checkbox"<?php if($module->display['search']) { ?> checked="checked"<?php } ?> /> <label for="display-search"><?php _e('Search results', 'k2_domain'); ?></label><br />

	<input id="display-pages" name="display[pages]" type="checkbox"<?php if($module->display['pages']) { ?> checked="checked"<?php } ?> /> <label for="display-pages"><?php _e('Static pages', 'k2_domain'); ?></label> &raquo; <a id="toggle-specific-pages" href="#"><?php _e('Detailed options', 'k2_domain'); ?></a><br />

	<div id="specific-pages" class="toggle-item"></div>

	<input id="display-error" name="display[error]" type="checkbox"<?php if($module->display['error']) { ?> checked="checked"<?php } ?> /> <label for="display-error"><?php _e('Error page', 'k2_domain'); ?></label>
</fieldset>
</div>