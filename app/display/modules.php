<?php // Print_R for HTML
function print_r_html($data,$return_data=false)
{
    $data = print_r($data,true);
    $data = str_replace( " ","&nbsp;", $data);
    $data = str_replace( "\r\n","<br>\r\n", $data);
    $data = str_replace( "\r","<br>\r", $data);
    $data = str_replace( "\n","<br>\n", $data);

    if (!$return_data)
        echo $data;   
    else
        return $data;
} ?>

<?php
	global $wpdb;

	// Update
	$update = K2Options::update();

	$sidebar_number = get_option('k2sidebarnumber');
	$modules = K2SBM::get_installed_modules();
	$sidebars = K2SBM::get_sidebars();
	$disabled = K2SBM::get_disabled();
	$next_id = get_option('k2sbm_modules_next_id');
?>

<div id="optionswindow">
<form id="module-options-form">
	<div class="tabs">
		<a href="#" id="optionstab" class="selected">Options</a>
		<a href="#" id="advancedtab">Advanced</a>
		<a href="#" id="displaytab">Display</a>
		<a href="#" id="closelink"></a>
	</div>

	<div id="options">
	</div>

	<p class="submit">
		<input type="submit" id="submit" value="<?php echo attribute_escape(__('Save', 'k2_domain')); ?>" />
		<input type="submit" id="submitclose" value="<?php echo attribute_escape(__('Save &amp; Close', 'k2_domain')); ?>" />
	</p>
</form>
</div>

<div id="parentwrapper">

	<h2><?php _e('K2 Sidebar Modules', 'k2_domain') ?></h2>

	<div id="msg">
		<?php if(isset($_POST['submit'])) {
			_e('Updated sidebar options','k2_domain');
		} ?>
	</div>

	<div id="next_id" style="display: none;"><?php echo $next_id; ?></div>

	<form name="dofollow" id="dofollow" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="<?php echo attribute_escape($update); ?>" />
		<input type="hidden" name="page_options" value="'dofollow_timeout'" />

		<?php if (function_exists('dynamic_sidebar')) { ?>
		<select id="k2-sidebarnumber" name="k2[sidebarnumber]">
			<option value="0" <?php selected($sidebar_number, '0'); ?>><?php _e('No Sidebars','k2_domain'); ?></option>
			<option value="1" <?php selected($sidebar_number, '1'); ?>><?php _e('One Sidebar','k2_domain'); ?></option>
			<option value="2" <?php selected($sidebar_number, '2'); ?>><?php _e('Two Sidebars','k2_domain'); ?></option>
		</select>
		<?php } ?>

		<input type="submit" name="submitclose" id="submitclose" value="<?php echo attribute_escape(__('Update','k2_domain')); ?>" />
	</form>


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
		<?php $sidebar_count = 1; foreach($sidebars as $id => $sidebar) { ?>
		<div id="<?php echo($id); ?>container" class="container">
			<h3>
				<?php echo($sidebar->name); ?>
				<?php if ($sidebar_count++ > get_option('k2sidebarnumber')) { ?>
					<span><?php _e('(Inactive)','k2_domain'); ?></span>
				<?php } ?>
			</h3>

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
			<h3><?php _e('Disabled Modules', 'k2_domain'); ?></h3>

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
			<h3><?php _e('Trash Modules', 'k2_domain'); ?></h3>

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