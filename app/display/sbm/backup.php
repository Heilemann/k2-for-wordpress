<?php include('header.php'); ?>

<style type="text/css">
	h3 {
		font: normal 1.8em Georgia;
		margin: 30px 0 0;
		color: #222;
	}

	small {
		color: #666;
	}

	.configstuff {
		width: 700px;
		margin: 0 auto 30px;
		font-size: 1.1em;
	}
</style>

<div class="wrap">
	<div class="configstuff">
		<form action="" method="post">
			<h3><?php _e('Backup current sidebar:', 'k2_domain'); ?></h3>
			<p><small><?php _e('This will create a backup of your current sidebar configuration.', 'k2_domain'); ?><br /><?php _e('Keep in a safe place in case of disaster.', 'k2_domain'); ?></small></p>
			<p class="submit" style="text-align: center;"><input type="submit" value="<?php _e('Create sidebar backup &raquo;', 'k2_domain'); ?>" /></p>
		</form>

		<form action="" method="post">
			<h3><?php _e('Restore sidebar:', 'k2_domain'); ?></h3>
			<p><small><?php _e('Did it fail? Never fear, restore is here!', 'k2_domain'); ?><br /><?php _e('I hope you kept that file safe...', 'k2_domain'); ?></small></p>
			<p style="text-align: center;"><input type="file" /></p>
			<p class="submit narrow"><input type="submit" value="<?php _e('Restore sidebar &raquo;', 'k2_domain'); ?>" /></p>
		</form>
	</div>
</div>
