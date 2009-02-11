<?php
	// Load Rolling Archives?
	if ( '1' == get_option('k2rollingarchives') ): 
?>

<div id="rollingarchives" style="display:none;">
	<div id="texttrimmer">
		<div id="trimmertrackwrap"><div id="trimmertrack"><div id="trimmerhandle"></div></div></div>
		
		<div id="trimmerless"><span><?php _e('Less','k2_domain'); ?></span></div>
		<div id="trimmermore"><span><?php _e('More','k2_domain'); ?></span></div>
		<div id="trimmertrim"><span><?php _e('Trim','k2_domain'); ?></span></div>
		<div id="trimmeruntrim"><span><?php _e('Untrim','k2_domain'); ?></span></div>
	</div>

	<div id="rollnavigation">
		<div id="pagetrackwrap"><div id="pagetrack"><div id="pagehandle"><div id="rollhover"><div id="rolldates"></div></div></div></div></div>

		<div id="rollpages"></div>
		
		<a id="rollprevious" title="<?php _e('Older','k2_domain'); ?>" href="#">
			<span>&laquo;</span> <?php _e('Older','k2_domain'); ?>
		</a>
		<div id="rollhome" title="<?php _e('Home','k2_domain'); ?>">
			<span><?php _e('Home','k2_domain'); ?></span>
		</div>
		<div id="rollload" title="<?php _e('Loading','k2_domain'); ?>">
			<span><?php _e('Loading','k2_domain'); ?></span>
		</div>
		<a id="rollnext" title="<?php _e('Newer','k2_domain'); ?>" href="#">
			<?php _e('Newer','k2_domain'); ?> <span>&raquo;</span>
		</a>

	</div> <!-- #rollnavigation -->
</div> <!-- #rollingarchives -->

<?php if ( !isset($_GET['k2dynamic']) ): ?>
<noscript>
	<?php k2_navigation('nav-above'); ?> 
</noscript>
<?php endif; ?>

<?php K2::setup_rolling_archives(); ?>

<?php endif; ?>

<div id="rollingcontent" class="hfeed">
	<?php include(TEMPLATEPATH . '/app/display/theloop.php'); ?>
</div><!-- #rollingcontent .hfeed -->
