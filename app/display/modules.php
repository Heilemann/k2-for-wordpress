<?php
	// Update
	$update = K2Options::update();

	$column_number = get_option('k2columns');
	$column_options = array(
		1 => __('One Column','k2_domain'),
		__('Two Columns', 'k2_domain'),
		__('Three Columns', 'k2_domain')
	);

	$modules = K2SBM::get_installed_modules();
	$sidebars = K2SBM::get_sidebars();
	$disabled = K2SBM::get_disabled();
	$next_id = get_option('k2sbm_modules_next_id');
?>

<div id="optionswindow">
	<a href="#" id="closelink"></a>

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
		<a href="#" id="optionstab" class="selected">Options</a>
		<a href="#" id="advancedtab">Advanced</a>
		<a href="#" id="displaytab">Display</a>
	</div>
	</div>

	<form id="module-options-form">

		<div id="options">
		</div>

		<p class="optionkeys">'Enter' saves, 'Escape' closes.</p>

		<p class="submitbuttons">
			<input type="submit" id="submit" value="<?php echo attribute_escape(__('Save', 'k2_domain')); ?>" />
			<input type="submit" id="submitclose" value="<?php echo attribute_escape(__('Save &amp; Close', 'k2_domain')); ?>" />
		</p>
	</form>
</div>

<div id="parentwrapper">

	<h2><?php _e('K2 Sidebar Modules', 'k2_domain') ?></h2>


	<form id="columnsform" name="columnsform" action="<?php echo attribute_escape($update); ?>" method="post" enctype="multipart/form-data">

		<select id="k2-columns" name="k2[columns]" onchange="this.form.submit();">
		<?php foreach ($column_options as $option => $label) { ?>
			<option value="<?php echo $option; ?>" <?php selected($column_number, $option); ?>><?php echo $label; ?></option>
		<?php } ?>
		</select>
	</form>



	<div id="next_id" style="display: none;"><?php echo $next_id; ?></div>

	<div class="wrap">

		<div id="availablemodulescontainer" class="container">
			<h3><?php _e('Available Modules', 'k2_domain') ?></h3>

			<ul id="availablemodules">
				<?php foreach($modules as $id => $module) { ?>
					<li id="<?php echo attribute_escape($id); ?>" class="availablemodule">
						<span class="name"><?php echo(attribute_escape($module['name'])); ?></span>
					</li>
				<?php } ?>
			</ul>
		</div>


		<div id="sidebarscontainer">
		<?php foreach ($sidebars as $id => $sidebar) { ?>
		<div id="<?php echo($id); ?>container" class="container">
			<h3><?php echo($sidebar->name); ?></h3>

			<div class="droppable">
				<ul id="<?php echo($id); ?>" class="sortable reorderable">

					<?php foreach ($sidebar->modules as $id => $module) { ?>

						<li id="<?php print attribute_escape($module->id); ?>" class="module">
							<div>
								<span class="name"><?php print $module->name; ?></span>
								<span class="type"><?php echo($modules[$module->type]['name']); ?></span>
								<a href="#" class="optionslink"> </a>
							</div>
						</li>

					<?php } ?>

				</ul>
			</div>
		</div>
		<?php } ?>
		</div>



		<div id="disabledcontainer" class="container">
			<h3><?php _e('Disabled', 'k2_domain'); ?></h3>

			<div class="droppable">
				<ul id="disabled" class="sortable reorderable">

					<?php foreach ($disabled as $id => $module) { ?>

						<li id="<?php print attribute_escape($module->id); ?>" class="module">
							<div>
								<span class="name"><?php print $module->name; ?></span>
								<span class="type"><?php echo($modules[$module->type]['name']); ?></span>
								<a href="#" class="optionslink"> </a>
							</div>
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

		<div class="clear"></div>
	</div>
</div>

<div>
	<p style="text-align: center;"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://k2.stikipad.com/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ) ?></p>
</div>

<div id="overlay"></div>