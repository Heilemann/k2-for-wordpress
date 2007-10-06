<?php
	// Update Columns Number
	if ( isset($_POST['columns_number']) ) {
		update_option('k2columns',$_POST['columns_number']);
	}

	$column_number = get_option('k2columns');
	$column_options = array(
		1 => __('One Column','k2_domain'),
		__('Two Columns', 'k2_domain'),
		__('Three Columns', 'k2_domain')
	);

	$modules = K2SBM::get_installed_modules();
	$sidebars = K2SBM::get_sidebars();
	$disabled = K2SBM::get_disabled();
?>

<?php include('header.php'); ?>

<div id="optionswindow">
	<a href="#" id="closelink" title="Close"></a>

	<div class="opttl"> </div>
	<div class="optt"> </div>
	<div class="opttr"> </div>

	<div class="optl"> </div>
	<div class="optr"> </div>

	<div class="optbl"> </div>
	<div class="optb"> </div>
	<div class="optbr"> </div>

	<div class="tabbg">
	<div class="tabs">
		<a href="#" id="optionstab" class="selected" title="<?php _e('Options for this module type'); ?>">Options</a>
		<a href="#" id="advancedtab">Advanced</a>
		<a href="#" id="displaytab" title="<?php _e('Where to display this module'); ?>">Display</a>
	</div>
	</div>

	<form id="module-options-form">

		<div id="options">
		</div>

		<!--<p class="optionkeys"><?php _e('\'Enter\' saves, \'Escape\' closes.'); ?></p>-->

		<p class="submitbuttons">
			<input type="submit" id="submit" value="<?php echo attribute_escape(__('Save', 'k2_domain')); ?>" />
			<input type="submit" id="submitclose" value="<?php echo attribute_escape(__('Save &amp; Close', 'k2_domain')); ?>" />
		</p>
	</form>
</div>

<div id="parentwrapper">

	<div class="sbmheader">
		<h2><?php _e('K2 Sidebar Manager', 'k2_domain') ?></h2>

		<span class="backuprestore">
			<a href="" id="backupsbm">Backup</a>
			<a href="" id="restoresbm">Restore</a>
		</span>
	
		<form id="columnsform" name="columnsform" action="" method="post" enctype="multipart/form-data">
			<select id="columns-number" name="columns_number" onchange="this.form.submit();">
			<?php foreach ($column_options as $option => $label) { ?>
				<option value="<?php echo $option; ?>" <?php selected($column_number, $option); ?>><?php echo $label; ?></option>
			<?php } ?>
			</select>
		</form>
	</div>

	<div id="backupsbmwindow" style="display: none;">

		<?php if($restored) { ?>
<!--		<div id="message2" class="updated fade">
			<p><?php _e('Your sidebar was restored', 'k2_domain'); ?></p>
		</div>
-->		<?php } ?>

		<?php if($error) { ?>
<!--		<div id="sbm-warning" class="updated fade-ff0000">
			<p><?php _e('Invalid SBM backup file', 'k2_domain'); ?></p>
		</div>
-->		<?php } ?>

		<div class="configstuff">
			<form action="<?php bloginfo('template_url'); ?>/app/includes/sbm-direct.php" method="post" id="backupform" style="display: none;">
				<h3><?php _e('Backup current sidebar:', 'k2_domain'); ?></h3>
				<p><input type="submit" value="<?php _e('Create sidebar backup &raquo;', 'k2_domain'); ?>" /></p>

				<input type="hidden" name="action" value="backup" />
			</form>

			<form action="" method="post" enctype="multipart/form-data" id="restoreform">
				<p style="text-align: center;"><input type="file" name="backup" />
				<button><?php _e('Restore', 'k2_domain'); ?></button></p>

				<input type="hidden" name="action" value="restore" />
			</form>
		</div>
	</div>


	<div class="wrap">
		
		<div class="initloading"><?php _e('Loading'); ?></div>

		<div id="availablemodulescontainer" class="container">
			<h3><?php _e('Available Modules', 'k2_domain') ?></h3>

			<div>
			<ul id="availablemodules">
				<?php foreach($modules as $id => $module) { ?>
					<li id="<?php echo attribute_escape($id); ?>" class="module availablemodule">
						<span class="modulewrapper">
							<span class="name"><?php echo(attribute_escape($module['name'])); ?></span>
						</span>
					</li>
				<?php } ?>
			</ul>
			</div>
		</div>


		<?php foreach ($sidebars as $id => $sidebar) { ?>
		<div id="<?php echo($id); ?>container" class="container">
			<h3><?php echo($sidebar->name); ?></h3>

			<div class="droppable">
				<ul id="<?php echo($id); ?>" class="sortable reorderable">

					<?php foreach ($sidebar->modules as $id => $module) { ?>

						<li id="<?php print attribute_escape($module->id); ?>" class="module">
							<span class="modulewrapper">
								<span class="name"><?php print $module->name; ?></span>
								<span class="type"><?php echo($modules[$module->type]['name']); ?></span>
								<a href="#" class="optionslink"> </a>
							</span>
						</li>

					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>



		<div id="disabledcontainer" class="container">
			<h3><?php _e('Disabled', 'k2_domain'); ?></h3>

			<div class="droppable">
				<ul id="disabled" class="sortable reorderable">

					<?php foreach ($disabled as $id => $module) { ?>

						<li id="<?php print attribute_escape($module->id); ?>" class="module">
							<span class="modulewrapper">
								<span class="name"><?php print $module->name; ?></span>
								<span class="type"><?php echo($modules[$module->type]['name']); ?></span>
								<a href="#" class="optionslink"> </a>
							</span>
						</li>

					<?php } ?>

				</ul>
			</div>
		</div>

		<div id="trashcontainer" class="container">
			<h3><?php _e('Trash', 'k2_domain'); ?></h3>

			<ul id="trash" class="sortable">
			</ul>
		</div>

		<div class="clear">&nbsp;</div>

	</div>

	<p style="text-align: center;" class="helptext"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://k2.stikipad.com/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ) ?></p>
</div>


<div id="overlay"></div>
