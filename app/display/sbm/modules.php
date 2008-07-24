<?php
	$modules = K2SBM::get_installed_modules();
	$sidebars = K2SBM::get_sidebars();
	$disabled = K2SBM::get_disabled();
?>

<?php if ($restored) { ?>
<div id="message2" class="updated fade">
	<p><?php _e('Your sidebar was restored', 'k2_domain'); ?></p>
</div>
<?php } ?>

<?php if ($error) { ?>
<div id="sbm-warning" class="updated fade-ff0000">
	<p><?php _e('Invalid SBM backup file', 'k2_domain'); ?></p>
</div>
<?php } ?>

<div id="optionswindow" style="visibility:hidden;">
	<a href="#" id="closelink" title="<?php _e('Close', 'k2_domain'); ?>"></a>

	<table cellspacing="0" cellpadding="0">
	<tr>
		<td class="opttl">&nbsp;</td>
		<td class="optt">&nbsp;</td>
		<td class="opttr">&nbsp;</td>
	</tr>

	<tr>
		<td class="optl" rowspan="3">&nbsp;</td>
		<td class="opttabs">
	
			<div class="tabbg">
				<div class="tabs">
					<a href="#" id="optionstab" class="selected" title="<?php _e('Options for this module type', 'k2_domain'); ?>"><?php _e('Options', 'k2_domain'); ?></a>
					<a href="#" id="advancedtab"><?php _e('Advanced', 'k2_domain'); ?></a>
					<a href="#" id="displaytab" title="<?php _e('Where to display this module', 'k2_domain'); ?>"><?php _e('Display', 'k2_domain'); ?></a>
				</div>
			</div>

		</td>
		<td class="optr" rowspan="3">&nbsp;</td>
	</tr>

	<tr>
		<td class="optcontents">
			<form id="module-options-form">

				<div id="options">
				</div>

				<!--<p class="optionkeys"><?php _e('\'Enter\' saves, \'Escape\' closes.', 'k2_domain'); ?></p>-->

			</form>
		</td>
	</tr>
	<tr>
		<td class="optbuttons">
			<input type="submit" id="submit" value="<?php echo attribute_escape(__('Save', 'k2_domain')); ?>" />
			<input type="submit" id="submitclose" value="<?php echo attribute_escape(__('Save &amp; Close', 'k2_domain')); ?>" />
		</td>
	</tr>

	<tr>
		<td class="optbl">&nbsp;</td>
		<td class="optb">&nbsp;</td>
		<td class="optbr">&nbsp;</td>
	</tr>
	</table>
</div><!-- #optionswindow -->

<div id="parentwrapper">

	<div class="sbmheader">
		<h2><?php _e('K2 Sidebar Manager', 'k2_domain') ?></h2>

		<span class="backuprestore">
			<a href="" id="restoresbm"><?php _e('Restore', 'k2_domain') ?></a>
			<a href="" id="backupsbm"><?php _e('Backup', 'k2_domain') ?></a>
		</span>

		<a href="#" id="undo"><?php _e('Undo', 'k2_domain') ?> <span id="levels"></span></a>
	</div><!-- .sbmheader -->

	<div id="backupsbmwindow" style="display: none;">

		<div class="configstuff">
			<form action="<?php bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php" method="POST" id="backupform" style="display: none;">
				<h3><?php _e('Backup current sidebar:', 'k2_domain'); ?></h3>
				<!--<p><small><?php _e('This will create a backup of your current sidebar configuration.', 'k2_domain'); ?><br /><?php _e('Keep in a safe place in case of disaster.', 'k2_domain'); ?></small></p>-->
				<p><input type="submit" value="<?php _e('Create sidebar backup &raquo;', 'k2_domain'); ?>" /></p>

				<input type="hidden" name="sbm_action" value="backup" />
				<input type="hidden" name="action" value="k2sbm" />
			</form>

			<form action="" method="post" enctype="multipart/form-data" id="restoreform">
				<!--<h3><?php _e('Restore sidebar:', 'k2_domain'); ?></h3>-->
				<!--<p><small><?php _e('Did it fail? Never fear, restore is here!', 'k2_domain'); ?><br /><?php _e('I hope you kept that file safe...', 'k2_domain'); ?></small></p>-->
				<p style="text-align: center;"><input type="file" name="backup" />
				<button><?php _e('Restore', 'k2_domain'); ?></button></p>

				<input type="hidden" name="sbm_action" value="restore" />
				<input type="hidden" name="action" value="k2sbm" />
			</form>
		</div>
	</div><!-- #backupsbmwindow -->


	<div class="wrap">
		
		<div class="containerwrap">

			<div class="initloading"><?php _e('Loading', 'k2_domain'); ?></div>

			<div id="availablemodulescontainer" class="container">
				<h3><?php _e('Available Modules', 'k2_domain') ?></h3>

				<div>
				<ul id="availablemodules">
				<?php foreach($modules as $id => $module) { ?>
					<li id="<?php echo attribute_escape($id); ?>" class="module availablemodule">
						<div class="slidingdoor">
							<span class="name"><?php echo(attribute_escape($module['name'])); ?></span>
						</div>
					</li>
				<?php } ?>
				</ul>
				</div>
			</div><!-- #availablemodulescontainer -->


		<?php foreach ($sidebars as $sidebar) { ?>
			<div id="<?php echo($sidebar->id); ?>-container" class="container">
				<h3><?php echo($sidebar->name); ?></h3>

				<div class="droppable">
					<ul id="<?php echo($sidebar->id); ?>" class="sortable reorderable">
					<?php foreach ($sidebar->modules as $module) { ?>

						<li id="<?php print attribute_escape($module->id); ?>" class="module">
							<div class="slidingdoor">
								<span class="modulewrapper">
									<span class="name"><?php print $module->name; ?></span>
									<span class="type"><?php echo($modules[$module->type]['name']); ?></span>
								</span>
								<a href="#" class="optionslink" alt="<?php _e('Module Options', 'k2_domain') ?>"> </a>
								<a href="#" class="deletelink" alt="<?php _e('Delete Module', 'k2_domain') ?><"> </a>
							</div>
						</li>

					<?php } ?>
					</ul>
				</div>
			</div><!-- .container -->
		<?php } ?>


			<div id="disabledcontainer" class="container">
				<h3><?php _e('Disabled', 'k2_domain'); ?></h3>

				<div class="droppable">
					<ul id="disabled" class="sortable reorderable">

						<?php foreach ($disabled as $module) { ?>

							<li id="<?php print attribute_escape($module->id); ?>" class="module">
								<div class="slidingdoor">
									<span class="modulewrapper">
										<span class="name"><?php print $module->name; ?></span>
										<span class="type"><?php echo($modules[$module->type]['name']); ?></span>
									</span>
									<a href="#" class="optionslink" alt="<?php _e('Module Options', 'k2_domain') ?>"> </a>
									<a href="#" class="deletelink" alt="<?php _e('Delete Module', 'k2_domain') ?><"> </a>
								</div>
							</li>

						<?php } ?>

					</ul>
				</div>
			</div><!-- #disabledcontainer -->


			<div id="trashcontainer" class="container">
				<h3><?php _e('Trash', 'k2_domain'); ?></h3>

				<ul id="trash" class="sortable">
				</ul>
			</div><!-- #trashcontainer -->

		</div><!-- .containerwrap -->

	</div><!-- .wrap -->

	<p style="text-align: center;" class="helptext"><?php printf(__('Help to be had at the %1$s or in the %2$s.','k2_domain'), '<a href="http://getk2.com/forum/" title="' .__('K2 Support Forums','k2_domain') . '">' .__('K2 Support Forums','k2_domain') . '</a>', '<a href="http://k2.stikipad.com/" title="' .__('K2 Documentation','k2_domain') . '">' .__('K2 Documentation','k2_domain') . '</a>' ) ?></p>

</div><!-- #parentwrapper -->


<div id="overlay"></div>
