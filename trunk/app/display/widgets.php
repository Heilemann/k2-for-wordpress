<div class="wrap">
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

	<div id="sbmheader">
		<?php if ( function_exists('screen_icon') ) screen_icon(); ?>
		<h2><?php _e('K2 Widgets Manager', 'k2_domain') ?></h2>

	<!--
		<span class="backuprestore">
			<a href="" id="restoresbm"><?php _e('Restore', 'k2_domain') ?></a>
			<a href="" id="backupsbm"><?php _e('Backup', 'k2_domain') ?></a>
		</span>
	-->
		<div class="updated">
			<p>Please use <a href="widgets.php">Widgets</a> to add multi-widgets (those in blue).</p>
		</div>

		<a href="#" id="undo"><?php _e('Undo', 'k2_domain') ?> <span id="levels"></span></a>
	</div><!-- .sbmheader -->

	<div class="containerwrap">

		<div class="initloading"><?php _e('Loading', 'k2_domain'); ?></div>

		<div id="availablemodulescontainer" class="container">
			<h3><?php _e('Available Widgets', 'k2_domain') ?></h3>

			<div>
				<ul id="available-widgets">
				<?php K2SBM::list_available_widgets(); ?>
				</ul>
			</div>
		</div><!-- #availablemodulescontainer -->


		<?php foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ): ?>
			<div id="<?php echo $sidebar_id; ?>-container" class="container">
				<h3><?php echo $sidebar['name']; ?></h3>

				<div class="droppable">
					<ul id="<?php echo $sidebar_id; ?>" class="sortable">
					<?php K2SBM::list_sidebar_widgets($sidebar_id); ?>
					</ul>
				</div>
			</div><!-- .container -->
		<?php endforeach; ?>

	</div><!-- .containerwrap -->

	<div class="clear"></div>
</div><!-- .wrap -->

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
			<h4 id="widget-name"></h4>
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
			<input type="submit" id="submit" value="<?php echo attribute_escape(__('Save', 'k2_domain')); ?>" class="button-secondary" />
			<input type="submit" id="submitclose" value="<?php echo attribute_escape(__('Save &amp; Close', 'k2_domain')); ?>" class="button-primary" />
		</td>
	</tr>

	<tr>
		<td class="optbl">&nbsp;</td>
		<td class="optb">&nbsp;</td>
		<td class="optbr">&nbsp;</td>
	</tr>
	</table>
</div><!-- #optionswindow -->

<div id="overlay"></div>
