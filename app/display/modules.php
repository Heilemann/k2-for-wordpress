<?php
	$modules = K2SBM::get_installed_modules();
	$sidebars = K2SBM::get_sidebars();
?>

<div class="wrap">
	<h2><?php _e('K2 Sidebar Modules', 'k2_domain') ?></h2>

	<div id="sbm-options" class="tab-box right">
		<ul class="tab-bar">
			<li id="show-tab-module-add"><a href="#"><?php _e('Add module', 'k2_domain'); ?></a></li>
			<li id="show-tab-module-options"><a href="#"><?php _e('Module\'s options', 'k2_domain'); ?></a></li>
		</ul>

		<div id="tab-module-add" class="tab">
			<h3><?php _e('Add module', 'k2_domain') ?></h3>

			<form id="module-add">
				<p id="module-add-error" class="error"></p>

				<p>
					<label for="add-name"><strong><?php _e('Module\'s title:', 'k2_domain'); ?></strong></label><br />
					<input id="add-name" name="add_name" type="text" />
				</p>

				<p>
					<label for="add-type"><?php _e('Module\'s type', 'k2_domain'); ?>:</label><br />
					<select id="add-type" name="add_type" size="10">
						<?php $selected = false; foreach($modules as $id => $module): ?>
							<option value="<?php echo attribute_escape($id); ?>" <?php if(!$selected) { ?> selected="selected"<?php $selected = true; } ?>><?php echo($module['name']); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p>
					<label for="add-sidebar"><?php _e('Add to', 'k2_domain'); ?>:</label><br />
					<select id="add-sidebar" name="add_sidebar">
						<?php $selected = false; foreach($sidebars as $id => $sidebar): ?>
							<option value="<?php echo attribute_escape($id); ?>" <?php if(!$selected) { ?> selected="selected"<?php $selected = true; } ?>><?php echo($sidebar->name); ?></option>
						<?php endforeach; ?>
						<option value="disabled"><?php _e('Disabled modules', 'k2_domain'); ?></option>
					</select>
				</p>

				<p class="submit">
					<input type="submit" value="<?php echo attribute_escape(__('Add &raquo;', 'k2_domain')); ?>" />
				</p>
			</form>
		</div>

		<div id="tab-module-options" class="tab">
			<h3><?php _e('Module\'s options', 'k2_domain') ?></h3>

			<div id="module-options">
				<span id="module-options-desc"><?php _e('Select a module to view it\'s options here', 'k2_domain'); ?></span>

				<form id="module-options-form">
					<div id="module-options-custom">
					</div>

					<p class="submit">
						<input class="remove" type="button" value="<?php echo attribute_escape(__('Remove &raquo;', 'k2_domain')); ?>" /> <input type="submit" value="<?php echo attribute_escape(__('Save &raquo;', 'k2_domain')); ?>" />
					</p>
				</form>
			</div>
		</div>
	</div>

	<div id="sbm-dnd" class="sbm-block">
		<?php $sidebar_count = 1; foreach($sidebars as $id => $sidebar) { ?>
			<div class="module-list">
				<h4>
					<?php echo($sidebar->name); ?>
					<?php if ($sidebar_count++ > get_option('k2sidebarnumber')) { ?>
						<span><?php _e('(Inactive)','k2_domain'); ?></span>
					<?php } ?>
				</h4>

				<ul id="<?php echo($id); ?>">
				</ul>
			</div>
		<?php } ?>

		<div class="module-list">
			<h4><?php _e('Disabled modules', 'k2_domain'); ?></h4>

			<ul id="disabled">
			</ul>
		</div>

		<div class="clear"></div>
	</div>

	<div class="clear"></div>
</div>

<div class="wrap">
	<p style="text-align: center;"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://k2.stikipad.com/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ) ?></p>
</div>
