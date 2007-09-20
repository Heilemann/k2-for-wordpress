<?php
	$modules = K2SBM::get_installed_modules();
	$base_module = $k2sbm_registered_modules[$module->type];
?>

<div id="optionstab-content" class="tabcontent">
	<p id="type-container">
		<span><?php _e('Type:', 'k2_domain'); ?></span>
		<?php echo($modules[$module->type]['name']); ?>
	</p>

	<p id="name-container">
		<label for="module-name" class="titlelabel"><?php _e('Title', 'k2_domain'); ?></label>
		<input id="module-name" name="module_name" type="text" value="<?php echo attribute_escape($module->name); ?>" />

		<input id="output-show-title" name="output[show_title]" type="checkbox"<?php if($module->output['show_title']) { ?> checked="checked"<?php } ?> />
		<label for="output-show-title" class="showtitlelabel"><?php _e('Display Title', 'k2_domain'); ?></label>
	</p>

<?php
	if(function_exists($base_module['control_callback'])) {
		// Call the control callback
		call_user_func($base_module['control_callback']);
	}
?>

</div><!-- #optionstab-content -->

<div id="advancedtab-content" class="tabcontent">
	<p>
		<label for="output-css-file"><?php _e('Load the following CSS file with this module', 'k2_domain'); ?>:</label><br/>
		<input id="output-css-file" name="output[css_file]" type="text" value="<?php echo attribute_escape($module->output['css_file']); ?>" />
	</p>
</div><!-- #advancedtab-content -->


<div id="displaytab-content" class="tabcontent">
<p>
<fieldset>
	<!--<legend><?php _e('Display Module On', 'k2_domain'); ?></legend>-->
	<div><input id="display-home" name="display[home]" type="checkbox"<?php if($module->display['home']) { ?> checked="checked"<?php } ?> /> <label for="display-home"><?php _e('Homepage', 'k2_domain'); ?></label></div>

	<div><input id="display-archives" name="display[archives]" type="checkbox"<?php if($module->display['archives']) { ?> checked="checked"<?php } ?> /> <label for="display-archives"><?php _e('Archives', 'k2_domain'); ?></label></div>

	<div><input id="display-post" name="display[post]" type="checkbox"<?php if($module->display['post']) { ?> checked="checked"<?php } ?> /> <label for="display-post"><?php _e('Single posts', 'k2_domain'); ?></label><!-- &raquo; <a id="toggle-specific-posts" href="#"><?php _e('Select Individual Posts', 'k2_domain'); ?></a>--></div>

	<!--<div id="specific-posts" class="toggle-item"></div>-->

	<div><input id="display-search" name="display[search]" type="checkbox"<?php if($module->display['search']) { ?> checked="checked"<?php } ?> /> <label for="display-search"><?php _e('Search results', 'k2_domain'); ?></label></div>

	<div><input id="display-error" name="display[error]" type="checkbox"<?php if($module->display['error']) { ?> checked="checked"<?php } ?> /> <label for="display-error"><?php _e('Error page', 'k2_domain'); ?></label></div>

	<div><input id="display-pages" name="display[pages]" type="checkbox"<?php if($module->display['pages']) { ?> checked="checked"<?php } ?> /> <label for="display-pages"><?php _e('Static pages', 'k2_domain'); ?></label><!-- &raquo; <a id="toggle-specific-pages" href="#"><?php _e('Select Individual Pages', 'k2_domain'); ?></a>--></div>

	<div id="specific-pages" class="toggle-item"></div>
</fieldset>
</p>
</div><!-- #displaytab-content -->
