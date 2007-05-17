<?php
	// Get core WP functions if needed
	if (isset($_GET['k2dynamic'])) {
		require (dirname(__FILE__).'/../../../wp-blog-header.php');

		$query = k2_parse_query($_GET);
		query_posts($query);

		unset($_GET['k2dynamic']);
	}

	// Load Rolling Archives?
	if ( get_option('k2rollingarchives') == 1 ) { 

		// Parse the query
		if ( is_array($wp_query->query) ) {
			$rolling_query = http_build_query($wp_query->query);
		} else {
			$rolling_query = $wp_query->query;
		}

		// Get list of page dates
		if ( !is_page() and !is_single() ) {
			$page_dates = get_rolling_page_dates($wp_query);
		}

		// Get the current page
		$rolling_page = get_query_var('paged');
		if ( empty($rolling_page) ) {
			$rolling_page = 1;
		}

		// Debugging
		if ( isset($_GET['k2debug']) ) {
			$rolling_query .= '&k2debug=1';
		}
?>

<div id="rollingarchives" style="visibility: hidden;">
	<div id="texttrimmer">
		<div id="trimmertrackwrap"><div id="trimmertrack"><div id="trimmerhandle"></div></div></div>
		
		<div id="trimmerless"><span><?php _e('Less','k2_domain'); ?></span></div>
		<div id="trimmermore"><span><?php _e('More','k2_domain'); ?></span></div>
	</div> <!-- #texttrimmer -->

	<div id="rollnavigation">
		<div id="pagetrackwrap"><div id="pagetrack"><div id="pagehandle"><div id="rollhover"><div id="rolldates"></div></div></div></div></div>

		<div id="rollpages"></div>
		
		<div id="rollprevious" title="<?php _e('Older','k2_domain'); ?>">
			<span>&laquo;</span> <?php _e('Older','k2_domain'); ?>
		</div>
		<div id="rollhome" title="<?php _e('Home','k2_domain'); ?>">
			<span><?php _e('Home','k2_domain'); ?></span>
		</div>
		<div id="rollload" title="<?php _e('Loading','k2_domain'); ?>">
			<span><?php _e('Loading','k2_domain'); ?></span>
		</div>
		<div id="rollnext" title="<?php _e('Newer','k2_domain'); ?>">
			<?php _e('Newer','k2_domain'); ?> <span>&raquo;</span>
		</div>

	</div> <!-- #rollnavigation -->

	<div id="rollnotices"></div>
</div> <!-- #rollingarchives -->

<script type="text/javascript">
// <![CDATA[
	K2.RollingArchives.setRollingState(<?php echo $rolling_page; ?>, <?php echo $wp_query->max_num_pages; ?>, "<?php echo $rolling_query; ?>", <?php output_javascript_array($page_dates); ?>);
// ]]>
</script>

<?php } ?>
<div id="rollingcontent" class="hfeed">
	<?php include (TEMPLATEPATH . '/theloop.php'); ?>
</div><!-- #rollingcontent .hfeed -->
