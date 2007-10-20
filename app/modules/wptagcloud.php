<?php

function wptagcloud_sidebar_module($args) {
	extract($args);

	echo($before_module . $before_title . $title . $after_title);

	wp_tag_cloud(
		'smallest='	.sbm_get_option('smallest').
		'&largest='	.sbm_get_option('largest').
		'&unit='	.sbm_get_option('unit').
		'&number='	.sbm_get_option('number').
		'&format='	.sbm_get_option('format').
		'&orderby='	.sbm_get_option('orderby').
		'&order='	.sbm_get_option('sortorder')
	);

	echo($after_module);
}

function wptagcloud_sidebar_module_control() {
	if (isset($_POST['wptagcloud_module'])) {
		sbm_update_option( 'smallest', (int) preg_replace('/^.*?(\d+).*?$/', '$1', $_POST['wptagcloud_module']['smallest']) );

		sbm_update_option( 'largest', (int) preg_replace('/^.*?(\d+).*?$/', '$1', $_POST['wptagcloud_module']['largest']) );

		sbm_update_option( 'number', (int) preg_replace('/^.*?(\d+).*?$/', '$1', $_POST['wptagcloud_module']['number']) );

		sbm_update_option( 'orderby', $_POST['wptagcloud_module']['orderby'] );
		sbm_update_option( 'sortorder', $_POST['wptagcloud_module']['sortorder'] );
	}
	?>
		<p>
			<label for="wptagcloud-module-smallest"><?php _e('Smallest font size:','k2_domain'); ?></label>
			<input id="wptagcloud-module-smallest" name="wptagcloud_module[smallest]" type="text" value="<?php echo attribute_escape(sbm_get_option('smallest')); ?>" size="2" />
		</p>

		<p>
			<label for="wptagcloud-module-largest"><?php _e('Largest font size:','k2_domain'); ?></label>
			<input id="wptagcloud-module-largest" name="wptagcloud_module[largest]" type="text" value="<?php echo attribute_escape(sbm_get_option('largest')); ?>" size="2" />
		</p>

		<p>
			<label for="wptagcloud-module-number"><?php _e('Number of tags:','k2_domain'); ?></label>
			<input id="wptagcloud-module-number" name="wptagcloud_module[number]" type="text" value="<?php echo attribute_escape(sbm_get_option('number')); ?>" size="2" /><br />
			<small><?php _e('Set to 0 to display all tags.','k2-domain'); ?></small>
		</p>

		<!--
		<p>
			<label for="wptagcloud-module-format"><?php _e('Format of cloud display:','k2_domain'); ?></label>
			<select id="wptagcloud-module-format" name="wptagcloud_module[format]">
				<option value="flat" <?php selected(sbm_get_option('format'), 'flat'); ?>><?php _e('Flat','k2_domain'); ?></option>
				<option value="list" <?php selected(sbm_get_option('format'), 'list'); ?>><?php _e('List','k2_domain'); ?></option>
				<option value="array" <?php selected(sbm_get_option('format'), 'array'); ?>><?php _e('Array','k2_domain'); ?></option>
			</select>
		</p>
		-->

		<p>
			<label for="wptagcloud-module-orderby"><?php _e('Order by:','k2_domain'); ?></label>
			<select id="wptagcloud-module-orderby" name="wptagcloud_module[orderby]">
				<option value="name" <?php selected(sbm_get_option('orderby'), 'name'); ?>><?php _e('Name','k2_domain'); ?></option>
				<option value="count" <?php selected(sbm_get_option('orderby'), 'count'); ?>><?php _e('Count','k2_domain'); ?></option>
			</select>
		</p>

		<p>
			<label for="wptagcloud-module-sortorder"><?php _e('Sort Order:','k2_domain'); ?></label>
			<select id="wptagcloud-module-sortorder" name="wptagcloud_module[sortorder]">
				<option value="ASC" <?php selected(sbm_get_option('sortorder'), 'ASC'); ?>><?php _e('Ascending','k2_domain'); ?></option>
				<option value="DESC" <?php selected(sbm_get_option('sortorder'), 'DESC'); ?>><?php _e('Descending','k2_domain'); ?></option>
			</select>
		</p>

	<?php
}

if (function_exists('wp_tag_cloud')) {
	register_sidebar_module('Tag Cloud', 'wptagcloud_sidebar_module', 'sb-wptagcloud',
		array('smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45, 'format' => 'list', 'orderby' => 'name', 'sortorder' => 'ASC')
	);

	register_sidebar_module_control('Tag Cloud', 'wptagcloud_sidebar_module_control');
}
?>