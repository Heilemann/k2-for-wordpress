<?php
	$prefix = '';

	// Get core WP functions if needed
	if (isset($_GET['k2dynamic'])) {
		require (dirname(__FILE__).'/../../../wp-blog-header.php');
		$prefix = 'nested_';

		$query = k2_parse_query($_GET);
		query_posts($query);
	}

	// Load Rolling Archives?
	if ( (get_option('k2rollingarchives') == 1) and ($wp_query->max_num_pages > 1) ) { 

		// Parse the query
		if ( is_array($wp_query->query) ) {
			$rolling_query = http_build_query($wp_query->query);
		} else {
			$rolling_query = $wp_query->query;
		}

		// Get list of page dates
		$page_dates = get_rolling_page_dates($wp_query);

		// Debugging
		if ( isset($_GET['k2debug']) ) {
			$rolling_query .= '&k2debug=1';
		}
?>

<div id="<?php echo $prefix; ?>rollingarchives">
	<div id="<?php echo $prefix; ?>texttrimmer">
		<div id="<?php echo $prefix; ?>trimmertrackwrap"><div id="<?php echo $prefix; ?>trimmertrack"><div id="<?php echo $prefix; ?>trimmerhandle"></div></div></div>
		
		<div id="<?php echo $prefix; ?>trimmerless"><span><?php _e('Less','k2_domain'); ?></span></div>
		<div id="<?php echo $prefix; ?>trimmermore"><span><?php _e('More','k2_domain'); ?></span></div>
	</div> <!-- #<?php echo $prefix; ?>texttrimmer -->

	<div id="<?php echo $prefix; ?>rollnavigation">
		<div id="<?php echo $prefix; ?>pagetrackwrap"><div id="<?php echo $prefix; ?>pagetrack"><div id="<?php echo $prefix; ?>pagehandle"><div id="<?php echo $prefix; ?>rollhover"><div id="<?php echo $prefix; ?>rollpages"></div><div id="<?php echo $prefix; ?>rolldates"></div></div></div></div></div>

		<div id="<?php echo $prefix; ?>rollprevious" title="<?php _e('Older','k2_domain'); ?>">
			<span>&laquo;</span> <?php _e('Older','k2_domain'); ?>
		</div>
		<div id="<?php echo $prefix; ?>rollhome" title="<?php _e('Home','k2_domain'); ?>">
			<span><?php _e('Home','k2_domain'); ?></span>
		</div>
		<div id="<?php echo $prefix; ?>rollload" title="<?php _e('Loading','k2_domain'); ?>">
			<span><?php _e('Loading','k2_domain'); ?></span>
		</div>
		<div id="<?php echo $prefix; ?>rollnext" title="<?php _e('Newer','k2_domain'); ?>">
			<?php _e('Newer','k2_domain'); ?> <span>&raquo;</span>
		</div>

	</div> <!-- #<?php echo $prefix; ?>rollnavigation -->

	<div id="<?php echo $prefix; ?>rollnotices"></div>
</div> <!-- #<?php echo $prefix; ?>rollingarchives -->

<script type="text/javascript">
// <![CDATA[
	var <?php echo $prefix; ?>rolling = new RollingArchives(
		"<?php echo $prefix; ?>", "rollingarchives", "primarycontent", "<?php echo attribute_escape(__('Page %1$d of %2$d',k2_domain)); ?>", <?php echo $wp_query->max_num_pages; ?>,
		<?php output_javascript_url('theloop.php'); ?>,
		"<?php echo $rolling_query; ?>",
		<?php output_javascript_array($page_dates); ?>
	);
// ]]>
</script>

<?php } ?>
<div id="<?php echo $prefix; ?>primarycontent" class="hfeed">
	<?php include (TEMPLATEPATH . '/theloop.php'); ?>
</div><!-- #<?php echo $prefix; ?>primarycontent .hfeed -->
